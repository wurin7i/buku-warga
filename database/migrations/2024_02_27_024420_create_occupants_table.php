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
                ->constrained('people')
                ->cascadeOnDelete();
            $table->foreignId('building_id')->nullable()
                ->constrained('properties')
                ->cascadeOnDelete();
            $table->boolean('is_resident');
            $table->date('moved_in_date')->nullable();
            $table->date('moved_out_date')->nullable();
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
