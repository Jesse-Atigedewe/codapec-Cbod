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
        Schema::create('farmers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cooperative_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('operational_area');
            $table->string('farmer_id')->unique();
            $table->string('farmer_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('id_card_number')->nullable();
            $table->string('farm_location')->nullable();
            $table->string('year_established')->nullable();
            $table->string('farm_code')->nullable();
            $table->string('cocoa_type')->nullable();
            $table->decimal('hectares', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmers');
    }
};
