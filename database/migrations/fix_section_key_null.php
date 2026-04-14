<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// ── Run this migration to fix any existing sections where section_key is NULL ─
// These were created via the frontend before the fix was applied.
// Without section_key, media uploads stored files at "hotels/1//filename.jpg"
// instead of "hotels/1/hero/filename.jpg" — the double slash broke Storage::url().

return new class extends Migration
{
    public function up(): void
    {
        // Copy section_name into section_key wherever section_key is empty/null
        DB::statement("
            UPDATE page_sections
            SET section_key = section_name
            WHERE section_key IS NULL OR section_key = ''
        ");
    }

    public function down(): void
    {
        // Non-reversible data fix
    }
};
