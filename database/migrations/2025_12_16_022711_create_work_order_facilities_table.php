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
        Schema::create('work_order_facilities', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_num')->unique();
            $table->foreignId('requester_id')->constrained('users');
            $table->string('requester_name');
            $table->string('location_details');
            $table->string('description');
            $table->string('category');
            $table->string('status')->default('pending');
            $table->string('photo_path')->nullable();

            //date fields
            $table->date('target_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();

            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->string('processed_by_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_facilities');
    }
};
