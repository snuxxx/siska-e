<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Notifications\Notifiable;

class Employee extends Model 
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'kode_karyawan','kode_perusahaan', 'kode_divisi', 'nama_lengkap', 'email', 'no_telepon', 'jabatan', 'divisi',
        'tanggal_masuk', 'status',
    ];

    public static function generateEmployeeCode($kodePerusahaan, $kodeDivisi)
    {
        $year = date('Y');
        $lastEmployee = self::where('kode_perusahaan', $kodePerusahaan)
                             ->where('kode_divisi', $kodeDivisi)
                             ->orderBy('id', 'desc')
                             ->first();

        $number = $lastEmployee ? ((int) substr($lastEmployee->kode_karyawan, -3)) + 1 : 1;
        $number = str_pad($number, 3, '0', STR_PAD_LEFT);

        return "$kodePerusahaan-$kodeDivisi-$number-$year";
    }

    public function salaryComponent()
{
    return $this->hasOne(SalaryComponent::class);
}

    public function users()
{
    return $this->hasMany(Employee::class, 'id');
}

public function officeLocation()
    {
        return $this->hasOne(OfficeLocation::class);
    }

}
