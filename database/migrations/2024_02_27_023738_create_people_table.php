<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use WuriN7i\IdRefs\Enums\ReferenceType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->nullable()->unique();
            $table->char('kk_number', 16)->nullable();
            $table->string('name');
            $table->reference(ReferenceType::Gender);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_deceased')->default(false);
            $table->string('address')->nullable();
            $table->foreignId('region_id')->nullable()
                ->references('id')->on('ref_regions');
            $table->reference(ReferenceType::Religion);
            $table->reference(ReferenceType::Marital);
            $table->reference(ReferenceType::Occupation);
            $table->reference(ReferenceType::BloodType);
            $table->reference(ReferenceType::Citizenship);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
