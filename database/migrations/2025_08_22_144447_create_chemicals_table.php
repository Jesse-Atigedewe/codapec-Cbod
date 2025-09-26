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
        Schema::create('chemicals', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    // usage type: insecticide, fungicide, herbicide, fertilizer
    $table->foreignId('type_id')->nullable()->nullOnDelete();
    $table->enum('state', ['granular', 'solid', 'liquid', 'powder'])->nullable();
    // unit of measure
    $table->enum('unit', ['liters', 'kg', 'bottles'])->default('liters');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemicals');
    }
};
