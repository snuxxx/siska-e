<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// create_users_global_table.php
return new class extends Migration
{
    public function up()
    {
        Schema::create('users_global', function (Blueprint $table) {
            $table->id('id');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('tenant_id')->nullable()->constrained('tenants', 'id');
            $table->enum('role_global', ['Admin', 'HRD', 'Karyawan'])->default('Karyawan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_global');
    }
};
