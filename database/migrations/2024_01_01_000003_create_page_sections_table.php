<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->foreignId('navigation_item_id')
                  ->nullable()
                  ->constrained('navigation_items')
                  ->nullOnDelete();
            $table->foreignId('template_id')
                  ->nullable()
                  ->constrained('section_templates')
                  ->nullOnDelete();
            $table->string('section_name');
            $table->string('section_key', 100)->nullable();
            $table->string('data_source', 50)->default('static');
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['hotel_id', 'order']);
            $table->index(['hotel_id', 'navigation_item_id']);
            $table->index(['hotel_id', 'is_visible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};
