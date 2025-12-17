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
        Schema::table('work_order_facilities', function (Blueprint $table) {
            $table->date('target_completion_date')->nullable()->change(); // Pastikan nullable
            $table->date('start_date')->nullable()->after('target_completion_date'); // Kolom Baru
            $table->date('actual_completion_date')->nullable()->change(); // Pastikan nullable
        });
    }

    public function down(): void
    {
        Schema::table('work_order_facilities', function (Blueprint $table) {
            $table->dropColumn('start_date');
        });
    }
};
