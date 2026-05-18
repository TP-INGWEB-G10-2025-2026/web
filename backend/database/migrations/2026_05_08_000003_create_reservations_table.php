<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Enseignant qui fait la demande
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->restrictOnDelete();

            // Matériel assigné à la validation (null en attente)
            $table->foreignUuid('material_id')
                  ->nullable()
                  ->constrained('materials')
                  ->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', [
                'pending',    // en attente
                'validated',  // validée
                'rejected',   // rejetée
                'cancelled',  // annulée
            ])->default('pending');

            // Raison du rejet (optionnelle)
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
