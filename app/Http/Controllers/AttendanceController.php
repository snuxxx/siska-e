<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    // Lokasi kantor dan radius absensi (dapat dikonfigurasi di .env atau config/attendance.php)
    private $officeLatitude = -6.200000; // Latitude kantor
    private $officeLongitude = 106.816666; // Longitude kantor
    private $radius = 50; // Radius validasi dalam meter

    // Check-in absensi
    public function checkIn(Request $request)
    {
        $employeeId = $request->user()->id; // ID karyawan berdasarkan token
        $currentTime = Carbon::now(); // Waktu sekarang
        $officeStartTime = Carbon::createFromTime(9, 0, 0); // Jam mulai kerja (misal pukul 09:00)
        $tolerance = 5; // Toleransi dalam menit

        // Validasi input data
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Validasi lokasi (dalam radius yang diperbolehkan)
        if (!$this->isWithinRadius($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'message' => 'Lokasi Anda berada di luar radius absensi.',
            ], 422);
        }

        // Tentukan status (Hadir atau Terlambat)
        $status = ($currentTime->diffInMinutes($officeStartTime, false) > $tolerance) 
            ? 'Terlambat' 
            : 'Hadir';

        // Simpan data ke database
        Attendance::create([
            'employee_id' => $employeeId,
            'tanggal_absensi' => $currentTime->toDateString(),
            'jam_check_in' => $currentTime->toTimeString(),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $status,
        ]);

        return response()->json(['message' => 'Check-in berhasil', 'status' => $status], 200);
    }

    // Check-out absensi
    public function checkOut(Request $request)
    {
        $employeeId = $request->user()->id; // ID karyawan berdasarkan token
        $currentTime = Carbon::now(); // Waktu sekarang
        $officeEndTime = Carbon::createFromTime(17, 0, 0); // Jam selesai kerja (misal pukul 17:00)
        $tolerance = 10; // Toleransi dalam menit

        // Validasi input data
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Validasi lokasi (dalam radius yang diperbolehkan)
        if (!$this->isWithinRadius($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'message' => 'Lokasi Anda berada di luar radius absensi.',
            ], 422);
        }

        // Tentukan status (Hadir atau Pulang Awal)
        $status = ($currentTime->diffInMinutes($officeEndTime, false) < -$tolerance) 
            ? 'Pulang Awal' 
            : 'Hadir';

        // Perbarui data absensi di database
        $attendance = Attendance::where('employee_id', $employeeId)
            ->where('tanggal_absensi', $currentTime->toDateString())
            ->first();

        if ($attendance) {
            $attendance->update([
                'jam_check_out' => $currentTime->toTimeString(),
                'status' => $status,
            ]);

            return response()->json(['message' => 'Check-out berhasil', 'status' => $status], 200);
        }

        return response()->json(['message' => 'Data absensi tidak ditemukan'], 404);
    }

    // Fungsi validasi radius lokasi
    private function isWithinRadius($latitude, $longitude)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latDelta = deg2rad($latitude - $this->officeLatitude);
        $lonDelta = deg2rad($longitude - $this->officeLongitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->officeLatitude)) * cos(deg2rad($latitude)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance <= $this->radius; // True jika dalam radius, False jika tidak
    }
}
