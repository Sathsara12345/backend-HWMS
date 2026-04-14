<?php

namespace App\Http\Controllers\WebManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\WebManagement\StoreSectionContentRequest;
use App\Http\Requests\WebManagement\UploadSectionMediaRequest;
use App\Http\Resources\WebManagement\SectionContentResource;
use App\Models\PageSection;
use App\Models\SectionContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class SectionContentController extends Controller
{
    // ── GET /v1/admin/sections/{pageSection}/contents ─────────
    public function index(PageSection $pageSection): AnonymousResourceCollection
    {
        $this->authorize('update', $pageSection);

        $contents = $pageSection->contents()->get();

        return SectionContentResource::collection($contents);
    }

    // ── PUT /v1/admin/sections/{pageSection}/contents ─────────
    // Body: { "fields": { "title": "New Title", "subtitle": "New Sub" } }
    public function updateFields(StoreSectionContentRequest $request, PageSection $pageSection): JsonResponse
    {
        $this->authorize('update', $pageSection);

        foreach ($request->input('fields') as $key => $value) {
            SectionContent::updateOrCreate(
                ['section_id' => $pageSection->id, 'field_key' => $key],
                ['field_value' => $value]
            );
        }

        $contents = $pageSection->fresh()->contents()->get();

        return response()->json([
            'message'  => 'Content updated successfully.',
            'contents' => SectionContentResource::collection($contents),
        ]);
    }

    // ── POST /v1/admin/sections/{pageSection}/contents/media ──
    // Body: multipart/form-data  →  file + key
    public function uploadMedia(UploadSectionMediaRequest $request, PageSection $pageSection): JsonResponse
    {
        $this->authorize('update', $pageSection);

        $key     = $request->input('key');
        $file    = $request->file('file');
        $isVideo = str_starts_with($file->getMimeType(), 'video/');
        $type    = $isVideo ? 'video' : 'image';

        // Delete old file if replacing
        $existing = SectionContent::where('section_id', $pageSection->id)
                                   ->where('field_key', $key)
                                   ->first();

        if ($existing?->field_value) {
            Storage::disk('public')->delete($existing->field_value);
        }

        // Store new file under hotels/{hotel_id}/{section_key}/
        $path = $file->store(
            "hotels/{$pageSection->hotel_id}/{$pageSection->section_key}",
            'public'
        );

        $content = SectionContent::updateOrCreate(
            ['section_id' => $pageSection->id, 'field_key' => $key],
            ['field_value' => $path, 'type' => $type]
        );

        return response()->json([
            'message' => 'Media uploaded successfully.',
            'content' => new SectionContentResource($content),
        ]);
    }

    // ── DELETE /v1/admin/sections/{pageSection}/contents/media/{key}
    public function deleteMedia(PageSection $pageSection, string $key): JsonResponse
    {
        $this->authorize('update', $pageSection);

        $content = SectionContent::where('section_id', $pageSection->id)
                                  ->where('field_key', $key)
                                  ->first();

        if (!$content?->field_value) {
            return response()->json(['message' => "No media found for key '{$key}'."], 404);
        }

        Storage::disk('public')->delete($content->field_value);
        $content->update(['field_value' => null]);

        return response()->json(['message' => "'{$key}' removed successfully."]);
    }
}