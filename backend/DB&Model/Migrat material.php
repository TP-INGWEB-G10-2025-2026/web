<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->foreignUuid('category_id')->constrained('categories')->onDelete('restrict');
            $table->enum('status', ['available', 'in_use', 'broken', 'maintenance'])->default('available');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};

?>