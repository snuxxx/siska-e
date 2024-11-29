<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_template',
        'jam_masuk',
        'jam_keluar',
    ];

    // Cast kolom waktu
    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
    ];
}
