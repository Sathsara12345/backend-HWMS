<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->json('images')->nullable();
            $table->boolean('availability')->default(true);
            $table->timestamps();

            $table->index(['hotel_id', 'availability']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
