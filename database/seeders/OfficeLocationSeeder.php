<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OfficeLocation;

class OfficeLocationSeeder extends Seeder
{
    public function run()
    {
        OfficeLocation::create([
            'tenant_id' => 1,              // Sesuaikan dengan tenant_id Anda
            'latitude' => -6.200000,       // Ganti dengan latitude lokasi kantor
            'longitude' => 106.816666,    // Ganti dengan longitude lokasi kantor
            'radius' => 100,              // Radius valid dalam meter
        ]);
    }
}
