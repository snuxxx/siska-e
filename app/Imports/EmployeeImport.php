<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\SalaryComponent;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeeImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // Cek apakah tanggal masuk adalah format serial Excel
            if (is_numeric($row['tanggal_masuk'])) {
                $tanggalMasuk = Date::excelToDateTimeObject($row['tanggal_masuk'])->format('Y-m-d');
            } else {
                // Jika sudah dalam format tanggal, gunakan Carbon untuk parsing
                $tanggalMasuk = \Carbon\Carbon::parse($row['tanggal_masuk'])->format('Y-m-d');
            }
    
            // Tentukan kode_karyawan berdasarkan format yang diinginkan
            $kodePerusahaan = strtoupper($row['kode_perusahaan']);
            $kodeDivisi = strtoupper($row['kode_divisi']);
            $tahunBergabung = \Carbon\Carbon::parse($tanggalMasuk)->year;
    
            // Cari nomor urut berdasarkan tahun dan kode perusahaan/divisi
            $lastEmployee = Employee::where('kode_perusahaan', $kodePerusahaan)
                                    ->where('kode_divisi', $kodeDivisi)
                                    ->whereYear('tanggal_masuk', $tahunBergabung)
                                    ->latest('id') // Ambil ID terbaru
                                    ->first();
    
            // Tentukan nomor urut, jika tidak ada data sebelumnya maka mulai dari 1
            $nomorUrut = $lastEmployee ? (intval(substr($lastEmployee->kode_karyawan, -7, 3)) + 1) : 1;
    
            // Format nomor urut menjadi 3 digit
            $nomorUrutFormatted = str_pad($nomorUrut, 3, '0', STR_PAD_LEFT);
    
            // Gabungkan menjadi kode_karyawan dalam format yang diinginkan
            $kodeKaryawan = $kodePerusahaan . '-' . $kodeDivisi . '-' . $nomorUrutFormatted . '-' . $tahunBergabung;
    
            // Simpan data karyawan ke tabel employees
            $employee = Employee::create([
                'kode_perusahaan' => $kodePerusahaan,
                'kode_divisi' => $kodeDivisi,
                'nama_lengkap' => $row['nama_lengkap'],
                'email' => $row['email'],
                'no_telepon' => $row['no_telepon'],
                'jabatan' => $row['jabatan'],
                'divisi' => $row['divisi'],
                'tanggal_masuk' => $tanggalMasuk,  // Menyimpan tanggal masuk yang sudah diparsing
                'status' => $row['status'],
                'kode_karyawan' => $kodeKaryawan,  // Menyimpan kode karyawan yang sudah terformat
            ]);
    
            // Simpan data salary component ke tabel salarycomponents
            SalaryComponent::create([
                'employee_id' => $employee->id,  // Menghubungkan ke karyawan yang baru saja disimpan
                'gaji_pokok' => $row['gaji_pokok'],
                'tunjangan' => $row['tunjangan'],
                'potongan' => $row['potongan'],
            ]);
    
            return $employee;
    
        } catch (\Exception $e) {
            // Jika terjadi kesalahan, log error
            Log::error('Error processing row: ' . json_encode($row));
            Log::error('Exception: ' . $e->getMessage());
            return null;
        }
    }

}
