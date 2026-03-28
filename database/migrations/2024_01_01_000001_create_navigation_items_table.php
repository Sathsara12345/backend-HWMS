<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('label');
            $table->string('slug', 100)->nullable();
            $table->string('url');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);

            // ── SEO ───────────────────────────────────────────
            $table->string('meta_title',       60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords',    255)->nullable();
            $table->string('og_title',         60)->nullable();
            $table->string('og_description',   160)->nullable();
            $table->string('og_image',         255)->nullable();
            $table->string('canonical_url',    255)->nullable();
            $table->boolean('is_indexable')->default(true);

            $table->timestamps();

            $table->index(['hotel_id', 'order']);
            $table->index(['hotel_id', 'is_active']);
            $table->index(['hotel_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
    }
};
