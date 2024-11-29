<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->constrained('users_global', 'id');
            $table->string('action');
            $table->json('data_sebelum')->nullable();
            $table->json('data_sesudah')->nullable();
            $table->timestamp('timestamp')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};