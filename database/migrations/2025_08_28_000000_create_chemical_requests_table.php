<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chemical_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // requester
            $table->foreignId('warehouse_rep_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('chemical_id')->constrained()->cascadeOnDelete();
            $table->foreignId('haulage_company_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->enum('status', ['pending', 'approved', 'dispatched'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chemical_requests');
    }
};
