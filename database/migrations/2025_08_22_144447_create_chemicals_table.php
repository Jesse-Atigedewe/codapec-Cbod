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
            //formular quantity to calculate with hectares
            $table->decimal('formula_quantity', 10, 6)->nullable();
            $table->foreignId('type_id')->nullable();
            $table->enum('unit', ['litres', 'kg', 'bottles', 'sachets'])->default('liters');
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
