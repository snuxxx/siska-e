<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->date('tanggal_absensi');
            $table->time('jam_check_in')->nullable();
            $table->time('jam_check_out')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['Hadir', 'Terlambat', 'Absen'])->default('Hadir');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance');
    }
};