<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('work_order_engineerings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id'); // ID user pelapor
            $table->string('ticket_num')->unique();     // Format: engIO-20231024-001

            // Info Laporan Awal
            $table->date('report_date');
            $table->string('report_time');              // String atau Time
            // $table->string('shift');                 // SUDAH DIHAPUS
            $table->string('plant');
            $table->string('machine_name');
            $table->string('damaged_part');             // Bagian request
            $table->string('improvement_status');       // PENGGANTI production_status
            $table->string('improvement_parameters');       // PENGGANTI production_status
            $table->string('kerusakan');                // Biasanya copy dari damaged_part
            $table->text('kerusakan_detail');
            $table->string('priority')->default('medium');
            $table->string('work_status')->default('pending'); // pending, in_progress, completed
            $table->string('photo_path')->nullable();

            // Info Pengerjaan (Update)
            $table->date('finished_date')->nullable();
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('engineer_tech')->nullable(); // PENGGANTI technician
            $table->text('maintenance_note')->nullable();
            $table->text('repair_solution')->nullable();
            $table->string('sparepart')->nullable();

            $table->timestamps();

            // Opsional: Foreign Key jika table users ada
            // $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_order_engineerings');
    }
};
