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
            $table->char('nik', 16)->nullable()->index();
            $table->char('kk_number', 16)->nullable()->index();
            $table->string('name')->index();
            $table->identityAttribute(ReferenceType::Gender);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->boolean('is_deceased')->default(false);
            $table->string('address')->nullable();
            $table->char('sub_region', 7)->nullable()->comment('RT-RW in XXX/YYY format');
            $table->foreignId('region_id')->nullable()
                ->constrained('ref_regions');
            $table->identityAttribute(ReferenceType::Religion);
            $table->identityAttribute(ReferenceType::Marital);
            $table->identityAttribute(ReferenceType::Occupation);
            $table->identityAttribute(ReferenceType::BloodType);
            $table->identityAttribute(ReferenceType::Citizenship);
            $table->foreignId('creator_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();
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
