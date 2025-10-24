<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dco_received_chemicals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('dispatch_id')->constrained('dispatches')->cascadeOnDelete();
            // dco that approved the distribution
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_received', 12, 2);
            $table->decimal('quantity_distributed', 12, 2)->default(0);
            $table->dateTime('received_at');
            $table->timestamps();
        });
    }    public function down(): void
    {
        Schema::dropIfExists('dco_received_chemicals');
    }
};
