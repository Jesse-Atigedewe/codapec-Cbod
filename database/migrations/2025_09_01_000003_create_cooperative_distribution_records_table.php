<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('cooperative_distribution_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dco_received_chemical_id')->constrained('dco_received_chemicals')->cascadeOnDelete();
            $table->foreignId('cooperative_id')->constrained('cooperatives')->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->foreignId('distributed_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('distributed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cooperative_distribution_records');
    }
};
