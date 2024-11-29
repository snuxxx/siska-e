<?php

// create_tenants_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama_perusahaan');
            $table->string('db_name');
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_user');
            $table->string('db_password');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};