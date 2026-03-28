<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                  ->constrained('page_sections')
                  ->cascadeOnDelete();
            $table->string('field_key', 100);
            $table->text('field_value')->nullable();
            $table->string('type', 50)->default('text'); // text|image|video|json|number
            $table->timestamps();

            $table->index(['section_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_contents');
    }
};
