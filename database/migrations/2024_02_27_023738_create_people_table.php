<?php

use App\Enums\PersonAttributes\BloodType;
use App\Enums\PersonAttributes\Citizenship;
use App\Enums\PersonAttributes\Gender;
use App\Enums\PersonAttributes\Marriage;
use App\Enums\PersonAttributes\Religion;
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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique();
            $table->char('kk_number', 16)->nullable();
            $table->string('name');
            $table->enum('gender', Gender::getValues());
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('deceased_date')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->enum('religion', Religion::getValues())->nullable();
            $table->enum('marriage', Marriage::getValues())->nullable();
            $table->unsignedSmallInteger('occupation')->nullable();
            $table->enum('blood_type',  BloodType::getValues())->nullable();
            $table->enum('citizenship', Citizenship::getValues())->nullable();
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
