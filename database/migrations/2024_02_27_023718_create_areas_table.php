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
            $table->string('name');
            $table->foreignId('parent_id')->nullable()
                ->constrained('areas')
                ->cascadeOnDelete();
            $table->foreignId('base_id')->nullable()
                ->constrained('areas')
                ->nullOnDelete();
            $table->foreignId('region_id')->nullable()
                ->constrained('ref_regions');
            $table->enum('type', array_map(fn (AreaType $areaType) => $areaType->value, AreaType::cases()))
                ->default(AreaType::SubRegion);
            $table->unsignedTinyInteger('level')->nullable();
            $table->foreignId('holder_id')->nullable()
                ->constrained('organizations');
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
