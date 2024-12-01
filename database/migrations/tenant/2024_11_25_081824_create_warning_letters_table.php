<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('warning_letters', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->text('alasan');
            $table->enum('status', ['Sementara', 'Permanen'])->default('Sementara');
            $table->date('tanggal_diberikan')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('warning_letters');
    }
};