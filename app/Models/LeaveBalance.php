<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\LeaveRequestController;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'total_cuti',
        'sisa_cuti',
        'cuti_tahunan',
        'cuti_darurat',
    ];

    // Relasi ke Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    // Metode untuk mengurangi sisa cuti
    public function useLeave($days)
    {
        // Pastikan sisa_cuti cukup untuk jumlah yang diminta
        if ($this->sisa_cuti >= $days) {
            // Kurangi sisa cuti
            $this->sisa_cuti -= $days;
            $this->sisa_cuti += $days;

            // Sesuaikan total cuti jika sisa_cuti berkurang
            if ($this->sisa_cuti < 0) {
                $this->total_cuti += $this->sisa_cuti;
                $this->sisa_cuti = 0; // Pastikan sisa cuti tidak negatif
            }

            // Simpan perubahan ke database
            $this->save();

            return true;
        }

        // Jika sisa cuti tidak cukup
        return false;
    }
}

