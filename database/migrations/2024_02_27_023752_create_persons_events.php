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
        Schema::create('persons_events', function (Blueprint $table) {
            $table->foreignId('person_id')
                ->references('id')->on('people')
                ->cascadeOnDelete();
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                ->references('id')->on('events')
                ->cascadeOnDelete();
            $table->string('notes')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons_events');
    }
};
