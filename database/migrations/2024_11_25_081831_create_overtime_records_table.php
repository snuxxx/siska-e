<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->date('tanggal_lembur');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('tarif_per_jam', 15, 2);
            $table->decimal('total_lembur', 15, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('overtime_records');
    }
};