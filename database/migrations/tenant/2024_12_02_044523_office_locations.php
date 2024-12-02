<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 10, 8);     // Lokasi kantor (latitude)
            $table->decimal('longitude', 11, 8);    // Lokasi kantor (longitude)
            $table->float('radius')->default(50);   // Radius valid (dalam meter)
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('office_locations');
    }
};

