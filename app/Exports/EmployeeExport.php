<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeExport implements FromCollection, WithHeadings
{
    /**
     * Mengambil koleksi data dari Employee dan SalaryComponent
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
{
    $employees = Employee::with('salaryComponent')->get();

    // Cek jika data kosong
    if ($employees->isEmpty()) {
        throw new \Exception('Tidak ada data karyawan untuk diekspor.');
    }

    return $employees->map(function ($employee) {
        return [
            $employee->kode_perusahaan,
            $employee->kode_divisi,
            $employee->kode_karyawan,
            $employee->nama_lengkap,
            $employee->email,
            $employee->no_telepon,
            $employee->jabatan,
            $employee->divisi,
            $employee->tanggal_masuk,
            $employee->status,
            $employee->salaryComponent->gaji_pokok ?? 0,
            $employee->salaryComponent->tunjangan ?? 0,
            $employee->salaryComponent->potongan ?? 0,
        ];
    });
}


    /**
     * Menyediakan Heading untuk setiap kolom di Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Kode Perusahaan',
            'Kode Divisi',
            'Kode Karyawan',
            'Nama Lengkap',
            'Email',
            'No Telepon',
            'Jabatan',
            'Divisi',
            'Tanggal Masuk',
            'Status',
            'Gaji Pokok',
            'Tunjangan',
            'Potongan',
        ];
    }
}
