<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('id_report');
            $table->enum('tipe_report', ['Kehadiran', 'Cuti', 'Gaji']);
            $table->json('data_report');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};

// create_roles_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id('id');
            $table->enum('tipe_report', ['Kehadiran', 'Cuti', 'Gaji']);
            $table->json('data_report');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
};