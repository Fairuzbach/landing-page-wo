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
        Schema::create('work_order_general_affairs', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_num'); //woGA-YYYMMDD-000
            $table->foreignId('requester_id')->constrained('users');
            $table->string('requester_name');

            $table->string('plant');
            $table->string("department");

            $table->text('description');
            $table->string('category');
            $table->string('parameter_permintaan');

            $table->string('status')->default('pending');
            $table->string('status_permintaan');

            $table->date('target_completion_date')->nullable();
            $table->dateTime('actual_completion_date')->nullable();

            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_general_affair');
    }
};
