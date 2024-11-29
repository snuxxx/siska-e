<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal_shift',
        'jam_masuk',
        'jam_keluar',
    ];

    // Relasi ke Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Cast kolom untuk waktu
    protected $casts = [
        'jam_masuk' => 'datetime:H:i:s',
        'jam_keluar' => 'datetime:H:i:s',
        'tanggal_shift' => 'date:Y-m-d',
    ];
}
