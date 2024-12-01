<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees', 'id');
            $table->enum('jenis_pengajuan', ['Cuti Tahunan','Cuti Darurat']);
            $table->date('tanggal_mulai');  // Pastikan tanggal_mulai ada
            $table->date('tanggal_selesai'); // Pastikan tanggal_selesai ada
            $table->text('alasan')->nullable();
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Persetujuan Sementara'])->default('Menunggu');
            $table->timestamps();
        });
        

        // Add foreign key to leave_documents table
        Schema::table('leave_documents', function (Blueprint $table) {
            $table->foreign('leave_request_id')->references('id')->on('leave_requests');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
};
