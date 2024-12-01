<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shift_templates', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama_template');
            $table->time('jam_masuk');
            $table->time('jam_keluar');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_templates');
    }
};
