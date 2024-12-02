<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees');
            $table->date('tanggal_absensi');
            $table->time('jam_check_in')->nullable();
            $table->time('jam_check_out')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('status', ['Hadir', 'Terlambat', 'Alpha', 'Izin']);
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
