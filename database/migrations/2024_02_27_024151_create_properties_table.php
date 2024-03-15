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
            $table->foreignId('owner_id')->nullable()
                ->constrained('people');
            $table->boolean('has_building')->default(true);
            $table->foreignId('sub_region_id')
                ->constrained('areas');
            $table->foreignId('cluster_id')->nullable()
                ->constrained('areas');
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
