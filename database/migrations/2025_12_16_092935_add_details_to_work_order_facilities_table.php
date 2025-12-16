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
            // Hapus ->after('...') agar aman dari error posisi

            $table->date('report_date')->nullable();
            $table->time('report_time')->nullable();
            $table->string('shift', 20)->nullable();

            // Relasi mesin (Hapus after('plant'))
            $table->foreignId('machine_id')->nullable()->constrained('machines')->onDelete('set null');

            // OPSIONAL: Jika Anda yakin kolom 'plant' belum ada dan ingin membuatnya sekarang:
            // $table->string('plant')->nullable(); 
        });
    }

    public function down(): void
    {
        Schema::table('work_order_facilities', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum kolomnya
            $table->dropForeign(['machine_id']);

            // Drop kolom
            $table->dropColumn(['report_date', 'report_time', 'shift', 'machine_id']);
        });
    }
};
