<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('id');
            $table->string('kode_karyawan')->nullable();
            $table->string('kode_perusahaan')->nullable();
            $table->string('kode_divisi')->nullable();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('no_telepon')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('divisi')->nullable();
            $table->date('tanggal_masuk');
            $table->enum('status', ['Aktif', 'Non-Aktif', 'Resign'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};