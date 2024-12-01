<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Tenant;
use App\Models\User;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('tenant:create {id}', function ($id) {
    // Membuat tenant
    $tenant = Tenant::create([
        'id' => $id,
    ]);

    // Menambahkan domain ke tenant
    $tenant->domains()->create([
        'domain' => $id, // Asumsikan domain sama dengan ID tenant
    ]);


    // Jalankan migrasi khusus tenant
    $tenant->run(function () {
        Artisan::call('migrate', ['--path' => 'database/migrations/tenant']);
    });

    $this->info("Tenant {$id} berhasil dibuat dengan domain {$id}.");
});