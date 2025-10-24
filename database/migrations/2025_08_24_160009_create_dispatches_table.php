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
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chemical_request_id')->constrained()->cascadeOnDelete();
               $table->foreignId('request_id')->nullable()->constrained('requests') 
        ->nullOnDelete();
            $table->foreignId('chemical_id')->constrained()->cascadeOnDelete();
              $table->string('driver_name')->nullable()->after('chemical_id');
            $table->string('driver_phone')->nullable()->after('driver_name');
            $table->string('driver_license')->nullable()->after('driver_phone');
            $table->string('vehicle_number')->nullable()->after('driver_license');
            $table->integer('quantity')->default(0)->after('vehicle_number');
            $table->boolean('trip_complete')->default(false)->after('quantity');
        

            // shipment status
            $table->enum('status', ['pending',  'delivered'])->default('pending');
            $table->date('dispatched_at')->nullable();
            $table->date('delivered_at')->nullable();

            // approvals
            $table->boolean('dco_approved')->default(false);
            $table->foreignId('dco_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('dco_approved_at')->nullable();

            $table->boolean('auditor_approved')->default(false);
            $table->foreignId('auditor_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('auditor_approved_at')->nullable();

            $table->boolean('regional_manager_approved')->default(false);
            $table->foreignId('regional_manager_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('regional_manager_approved_at')->nullable();

            // other
            $table->string('waybill')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
