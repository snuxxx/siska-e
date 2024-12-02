<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'tanggal_absensi',
        'jam_check_in',
        'jam_check_out',
        'latitude',
        'longitude',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
