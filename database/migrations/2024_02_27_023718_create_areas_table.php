<?php

use App\Enums\AreaAttributes\Type as AreaType;
use App\Enums\AreaAttributes\Level;
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
            $table->foreignId('parent_id')->nullable()->references('id')->on('areas')
                ->cascadeOnDelete();
            $table->foreignId('administrative_area_id')->nullable()->references('id')->on('areas')
                ->nullOnDelete();
            $table->enum('type', AreaType::getValues())->default(AreaType::Administrative);
            $table->unsignedTinyInteger('level')->nullable();
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
