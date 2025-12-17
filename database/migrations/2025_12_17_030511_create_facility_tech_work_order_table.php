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
        Schema::create('facility_tech_work_order', function (Blueprint $table) {
            $table->id();

            // Relasi ke Tiket (Tetap)
            $table->foreignId('work_order_facility_id')
                ->constrained('work_order_facilities')
                ->onDelete('cascade');

            // Relasi ke Teknisi (UBAH KE 'facility_teches')
            $table->foreignId('facility_tech_id')
                ->constrained('facility_teches') // <--- PERBAIKAN DISINI
                ->onDelete('cascade');

            $table->unique(['work_order_facility_id', 'facility_tech_id'], 'tech_wo_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_tech_work_order');
    }
};
