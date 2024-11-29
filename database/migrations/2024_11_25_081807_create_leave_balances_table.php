<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->integer('total_cuti')->default(0);
            $table->integer('sisa_cuti')->default(12);
            $table->integer('cuti_tahunan')->default(0); // Menambahkan kolom cuti tahunan
            $table->integer('cuti_darurat')->default(0); // Menambahkan kolom cuti darurat
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_balances');
    }
};
