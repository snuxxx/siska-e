<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryComponent;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EmployeeImport;
use App\Exports\EmployeeExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\LeaveBalance;

class EmployeeController extends Controller
{
    function formatRupiah($number)
{
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

public function index()
{
    try {
        $employees = Employee::with('salaryComponent')->get()->map(function ($employee) {
            $salaryComponent = $employee->salaryComponent;
            return [
                'id' => $employee->id,
                'Nama Lengkap' => $employee->nama_lengkap,
                'Kode Karyawan' => $employee->kode_karyawan,
                'Email' => $employee->email,
                'No Telepon' => $employee->no_telepon,
                'Tanggal Masuk' => $employee->tanggal_masuk,
                'Gaji Pokok' => $this->formatRupiah($salaryComponent->gaji_pokok),
                'Tunjangan' => $this->formatRupiah($salaryComponent->tunjangan),
                'Potongan' => $this->formatRupiah($salaryComponent->potongan),
            ];
        });

        return response()->json([
            'message' => 'Berikut adalah data semua Karyawan',
            'data' => $employees,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat mengambil data karyawan.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

public function store(Request $request)
{
    // Validasi input dari request
    $request->validate([
        // Validasi untuk data karyawan
        'kode_perusahaan' => 'required',
        'kode_divisi' => 'required',
        'nama_lengkap' => 'required',
        'email' => 'required|email|unique:employees',
        'tanggal_masuk' => 'required|date',

        // Validasi untuk komponen gaji
        'gaji_pokok' => 'required|numeric|min:0',
        'tunjangan' => 'nullable|numeric|min:0',
        'potongan' => 'nullable|numeric|min:0',
    ]);

    try {
        // Generate kode karyawan unik
        $kodeKaryawan = Employee::generateEmployeeCode($request->kode_perusahaan, $request->kode_divisi);

        // Buat data karyawan
        $employee = Employee::create(array_merge($request->all(), ['kode_karyawan' => $kodeKaryawan]));

        // Tambahkan komponen gaji
        $salaryComponent = SalaryComponent::create([
            'employee_id' => $employee->id,
            'gaji_pokok' => $request->gaji_pokok,
            'tunjangan' => $request->tunjangan ?? 0,
            'potongan' => $request->potongan ?? 0,
        ]);

        // Tambahkan data LeaveBalance untuk karyawan yang baru
        LeaveBalance::create([
            'employee_id' => $employee->id,
            'total_cuti' => 0, // Total cuti
            'sisa_cuti' => 12,  // Sisa cuti
            'cuti_tahunan' => 0, // Cuti tahunan
            'cuti_darurat' => 0, // Cuti darurat
        ]);

        // Respon jika berhasil
        return response()->json([
            'message' => 'Karyawan berhasil ditambahkan.',
            'data' => [
                'employee' => $employee,
                'gaji_pokok' => $this->formatRupiah($salaryComponent->gaji_pokok),
                'tunjangan' => $this->formatRupiah($salaryComponent->tunjangan),
                'potongan' => $this->formatRupiah($salaryComponent->potongan),
            ]
        ], 201);
    } catch (\Exception $e) {
        // Respon jika terjadi kesalahan
        return response()->json([
            'message' => 'Karyawan gagal ditambahkan.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
public function show($id)
{
    $employee = Employee::with('salaryComponent')->find($id);
    if (!$employee) {
        return response()->json(['message' => 'Employee not found'], 404);
    }

    $salaryComponent = $employee->salaryComponent;

    return response()->json([
            'id' => $employee->id,
            'Nama Lengkap' => $employee->nama_lengkap,
            'Kode Karyawan' => $employee->kode_karyawan,
            'Email' => $employee->email,
            'No Telepon' => $employee->no_telepon,
            'Tanggal Masuk' => $employee->tanggal_masuk,
            'Gaji Pokok' => $this->formatRupiah($salaryComponent->gaji_pokok),
            'Tunjangan' => $this->formatRupiah($salaryComponent->tunjangan),
            'Potongan' => $this->formatRupiah($salaryComponent->potongan),
    ]);
}

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'kode_perusahaan' => 'required',
            'kode_divisi' => 'required',
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'tanggal_masuk' => 'required|date',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangan' => 'nullable|numeric|min:0',
            'potongan' => 'nullable|numeric|min:0',
        ]);
    
        try {
            $employee->update($request->all());
    
            $salaryComponent = $employee->salaryComponent;
            $salaryComponent->update($request->only(['gaji_pokok', 'tunjangan', 'potongan']));
    
            return response()->json([
                'message' => 'Karyawan berhasil diperbarui.',
                'data' => [
                    'employee' => $employee,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memperbarui karyawan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function destroy(Employee $employee)
{
    try {
        $employee->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat menghapus karyawan.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls', // Pastikan ekstensi file yang valid
    ]);

    try {
        // Ambil file yang di-upload
        $file = $request->file('file');

        // Tentukan format pembaca (reader type)
        Excel::import(new EmployeeImport, $file->getRealPath());

        return response()->json([
            'message' => 'Data karyawan berhasil diimpor.',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat mengimpor file.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function export()
{
    try {
        // Menggunakan EmployeeExport untuk mendownload file Excel
        return Excel::download(new EmployeeExport, 'employees.xlsx');
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 404);
    }
}

}
