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
            // Menambahkan kolom facility_tech_id, boleh null (karena saat baru dibuat belum ada teknisi)
            $table->foreignId('facility_tech_id')->nullable()->constrained('facility_teches')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_facilities', function (Blueprint $table) {
            $table->dropForeign(['facility_tech_id']);
            $table->dropColumn('facility_tech_id');
        });
    }
};
