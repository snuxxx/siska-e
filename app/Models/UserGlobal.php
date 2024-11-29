<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGlobal extends Model
{
    use HasFactory;

    protected $table = 'users_global';

    protected $fillable = [
        'email',
        'password',
        'tenant_id',
        'role_global',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}

