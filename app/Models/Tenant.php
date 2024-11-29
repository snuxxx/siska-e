<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $table = 'tenants';

    protected $fillable = [
        'nama_perusahaan',
        'db_name',
        'db_host',
        'db_user',
        'db_password',
    ];

    public function users()
    {
        return $this->hasMany(UserGlobal::class, 'tenant_id', 'id');
    }
}

