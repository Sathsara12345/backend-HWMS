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
        Schema::create('nav_items', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('href')->nullable();        // null if it has children
            $table->string('icon')->nullable();        // store icon name as string e.g "LayoutDashboard"
            $table->string('section')->nullable();     // "Dashboards", "Pages" etc
            $table->unsignedBigInteger('parent_id')->nullable(); // null = top level
            $table->foreign('parent_id')->references('id')->on('nav_items')->onDelete('cascade');
            $table->integer('order')->default(0);      // for sorting
            $table->boolean('is_active')->default(true);
            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nav_items');
    }
};
