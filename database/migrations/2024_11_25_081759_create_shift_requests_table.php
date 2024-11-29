<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shift_requests', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('shift_id')->constrained('shifts', 'id');
            $table->foreignId('requester_id')->constrained('employees', 'id');
            $table->foreignId('requested_to_id')->constrained('employees', 'id');
            $table->enum('status', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('shift_requests');
    }
};