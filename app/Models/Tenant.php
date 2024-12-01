<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{

    use HasDatabase, HasDomains;

    public static function boot()
{
    parent::boot();

    static::created(function ($user) {
        // Pastikan tenant sudah ada sebelum membuatnya
        $tenant = Tenant::find($user->id); // Menemukan tenant berdasarkan user ID atau cara lain

        if (!$tenant) {
            // Membuat tenant baru jika belum ada
            
            static::creating(function ($tenant) {
                if (!isset($tenant->id)) {
                    $tenant->id = null; // ID akan diisi oleh database (auto increment)
                }
            });
        }
    });
}

}
