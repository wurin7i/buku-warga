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
        Schema::create('occupants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')
                ->references('id')->on('people')
                ->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()
                ->references('id')->on('properties')
                ->cascadeOnDelete();
            $table->boolean('is_resident');
            $table->timestamp('moved_in_at')->nullable();
            $table->timestamp('moved_out_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('occupants');
    }
};
