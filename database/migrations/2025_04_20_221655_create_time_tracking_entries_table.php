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
        Schema::create('time_tracking_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['work', 'break']);
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->unsignedBigInteger('break_reason_id')->nullable();
            $table->foreign('break_reason_id')->references('id')->on('break_reasons')->onDelete('set null');
            $table->unsignedBigInteger('work_type_id')->nullable();
            $table->foreign('work_type_id')->references('id')->on('work_types')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_tracking_entries');
    }
};
