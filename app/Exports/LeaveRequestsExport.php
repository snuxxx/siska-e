<?php

namespace App\Exports;

use App\Models\LeaveRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LeaveRequestsExport implements FromCollection, WithHeadings
{
    /**
     * Mengambil koleksi data dari leave_request dan SalaryComponent
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Ambil data leave requests dengan relasi leaveBalance dan employee
        $leave_requests = LeaveRequest::with('leaveBalance', 'employee')->get();

        // Cek jika data kosong
        if ($leave_requests->isEmpty()) {
            throw new \Exception('Tidak ada data cuti karyawan untuk diekspor.');
        }

        // Map data leave_requests ke format array untuk Excel
        return $leave_requests->map(function ($leave_request, $index) {
            return [
                $index + 1, // Nomor urut
                $leave_request->employee->nama_lengkap, // Mengakses relasi 'employee'
                $leave_request->employee->email,
                $leave_request->employee->no_telepon,
                $leave_request->employee->jabatan,
                $leave_request->leaveBalance->total_cuti ?? 0, // Mengakses relasi 'leaveBalance'
                $leave_request->leaveBalance->sisa_cuti ?? 0,
                $leave_request->leaveBalance->cuti_tahunan ?? 0,
                $leave_request->leaveBalance->cuti_darurat ?? 0,
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
            'No',
            'Nama Lengkap',
            'Email',
            'No Telepon',
            'Jabatan',
            'Total Cuti',
            'Sisa Cuti',
            'Cuti Tahunan',
            'Cuti Darurat',
        ];
    }
}
