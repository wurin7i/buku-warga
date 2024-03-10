<?php

use App\Enums\AreaType;
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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('label');
            $table->foreignId('parent_id')->nullable()
                ->references('id')->on('areas')
                ->cascadeOnDelete();
            $table->foreignId('base_area_id')->nullable()
                ->references('id')->on('areas')
                ->nullOnDelete();
            $table->foreignId('region_id')->nullable()
                ->references('id')->on('ref_regions');
            $table->enum('type', array_map(fn (AreaType $areaType) => $areaType->value, AreaType::cases()))
                ->default(AreaType::Locale);
            $table->unsignedTinyInteger('level')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
