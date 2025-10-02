<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->foreignId('farmer_group_id')->nullable()->constrained('farmer_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('farmers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('farmer_group_id');
        });
    }
};
