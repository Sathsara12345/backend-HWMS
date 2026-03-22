<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('navigation_item_id')
                  ->nullable()
                  ->constrained('navigation_items')
                  ->nullOnDelete();
            $table->string('section_name');
            $table->string('title')->nullable();
            $table->string('sub_title')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('image_url')->nullable();
            $table->string('video_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('poster_url')->nullable();
            $table->string('background_url')->nullable();            
            $table->index(['hotel_id', 'order']);
            $table->index(['hotel_id', 'navigation_item_id']);
            $table->index(['hotel_id', 'is_visible']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
