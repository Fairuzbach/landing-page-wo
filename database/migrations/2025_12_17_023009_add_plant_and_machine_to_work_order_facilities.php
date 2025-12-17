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
            // 1. Tambah kolom 'plant' (String) jika belum ada
            if (!Schema::hasColumn('work_order_facilities', 'plant')) {
                $table->string('plant')->nullable()->after('requester_name');
            }

            // 2. Tambah kolom 'machine_id' (Foreign Key) jika belum ada
            // Kita pakai machine_id, bukan machine_name, agar datanya berelasi
            if (!Schema::hasColumn('work_order_facilities', 'machine_id')) {
                $table->foreignId('machine_id')->nullable()->constrained('machines')->onDelete('set null')->after('plant');
            }

            // 3. Tambah kolom 'machine_name' (String) agar nama mesin juga tersimpan langsung
            if (!Schema::hasColumn('work_order_facilities', 'machine_name')) {
                $table->string('machine_name')->nullable()->after('machine_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('work_order_facilities', function (Blueprint $table) {
            // Drop the added columns/foreign keys if they exist (safe rollback)
            if (Schema::hasColumn('work_order_facilities', 'machine_name')) {
                $table->dropColumn('machine_name');
            }

            if (Schema::hasColumn('work_order_facilities', 'machine_id')) {
                $table->dropForeign(['machine_id']);
                $table->dropColumn('machine_id');
            }

            if (Schema::hasColumn('work_order_facilities', 'plant')) {
                $table->dropColumn('plant');
            }
        });
    }
};
