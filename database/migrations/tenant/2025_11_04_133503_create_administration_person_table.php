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
        Schema::create('administration_person', function (Blueprint $table) {
            $table->id();
            $table->string('administration_id'); // FK ke administrations (tenants)
            $table->unsignedBigInteger('person_id'); // FK ke people
            $table->timestamp('managed_from')->nullable(); // NULL = pending approval
            $table->timestamp('managed_until')->nullable(); // NULL = masih aktif (jika managed_from not null)
            $table->string('reason')->nullable(); // Alasan (pindah, meninggal, dll)
            $table->string('status')->default('pending'); // pending, approved, rejected, ended
            $table->json('metadata')->nullable(); // Data tambahan
            $table->timestamps();

            // Foreign keys - note: FK to central tenants table will be handled differently
            $table->foreign('person_id')->references('id')->on('people')->onDelete('cascade');

            // Indexes untuk performance
            $table->index(['administration_id', 'status']); // Pending approvals
            $table->index(['person_id', 'managed_from']); // Person history
            $table->index(['status', 'created_at']); // Approval queue
            $table->index('managed_from');
            $table->index('managed_until');

            // Constraint: satu person hanya bisa punya satu pending request per administration
            $table->unique(['administration_id', 'person_id', 'status'], 'unique_pending_request');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administration_person');
    }
};
