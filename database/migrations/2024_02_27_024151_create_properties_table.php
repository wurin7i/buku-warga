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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->foreignId('owner_id')->nullable()->references('id')->on('people');
            $table->boolean('has_building')->default(true);
            $table->foreignId('administrative_area_id')->references('id')->on('areas');
            $table->foreignId('area_id')->nullable()->references('id')->on('areas');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
