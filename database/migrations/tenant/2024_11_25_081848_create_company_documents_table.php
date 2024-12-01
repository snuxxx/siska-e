<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('company_documents', function (Blueprint $table) {
            $table->id('id');
            $table->string('nama_dokumen');
            $table->string('path_dokumen');
            $table->foreignId('uploaded_by')->constrained('employees', 'id');
            $table->timestamp('uploaded_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_documents');
    }
};