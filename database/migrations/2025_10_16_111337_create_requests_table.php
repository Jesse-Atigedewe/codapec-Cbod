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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            //user that made the request
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            //district and region for filtering and access control
            $table->foreignId('district_id')->constrained()->nullOnDelete();
            $table->foreignId('region_id')->constrained()->nullOnDelete();
            //cooperative requested
            $table->foreignId('cooperative_id')->constrained()->nullOnDelete();
            // Chemical selected for the request
            $table->foreignId('chemical_id')->constrained()->nullOnDelete();
            // status 
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            //regional manager approval
            $table->boolean('regional_manager_approved')->default(false);
            //admin approval
            $table->boolean('admin_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
