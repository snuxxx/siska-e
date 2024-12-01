<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_documents', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->foreignId('leave_request_id')->nullable(); // Will be updated after leave_requests table creation
            $table->string('nama_dokumen');
            $table->string('path_dokumen');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_documents');
    }
};