<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reservation_id')
                  ->nullable()
                  ->constrained()
                  ->nullOnDelete();

            $table->foreignUuid('user_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignUuid('material_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('loan_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();

            $table->enum('return_status', ['good', 'damaged', 'lost'])->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'loan_date']);
            $table->index(['material_id', 'actual_return_date']);
            $table->index('expected_return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
