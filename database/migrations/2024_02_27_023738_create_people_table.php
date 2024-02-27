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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->unique();
            $table->char('kk_number', 16)->nullable();
            $table->string('name');
            $table->char('gender');
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('deceased_date')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedTinyInteger('religion')->nullable();
            $table->unsignedTinyInteger('marriage')->nullable();
            $table->unsignedSmallInteger('occupation')->nullable();
            $table->unsignedTinyInteger('blood_type')->nullable();
            $table->char('citizenship', 3)->nullable();
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
