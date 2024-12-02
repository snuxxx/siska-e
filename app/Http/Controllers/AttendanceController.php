<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
    
        $employee = Employee::find($validated['employee_id']);
        $today = Carbon::now()->toDateString();
        $now = Carbon::now();
    
        // Cari shift untuk employee hari ini
        $shift = Shift::where('employee_id', $employee->id)
                      ->where('tanggal_shift', $today)
                      ->first();
    
        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan untuk hari ini.'], 404);
        }
    
        $shiftStart = Carbon::createFromTimeString($shift->jam_masuk);
        $shiftEnd = Carbon::createFromTimeString($shift->jam_keluar);
    
        // Cek apakah check-in terlalu awal (lebih dari 1 jam sebelum shift)
        if ($now->lt($shiftStart->subHour())) {
            return response()->json(['message' => 'Check-in terlalu awal. Anda hanya dapat check-in dalam waktu 1 jam sebelum shift.'], 400);
        }
    
        // Cek izin
        $leave = LeaveRequest::where('employee_id', $employee->id)
                             ->where('status', 'Disetujui')
                             ->where('tanggal_mulai', '<=', $today)
                             ->where('tanggal_selesai', '>=', $today)
                             ->exists();
    
        if ($leave) {
            return response()->json(['message' => 'Status absensi: Izin.'], 200);
        }
    
        // Tentukan status absensi
        $status = 'Hadir';
        $minutesLate = $now->diffInMinutes($shiftStart, false); // Hitung selisih waktu
    
        if ($minutesLate > 30) {
            $status = 'Alpha'; // Terlambat lebih dari 30 menit
        } elseif ($minutesLate > 5) {
            $status = 'Terlambat'; // Terlambat lebih dari 5 menit
        } elseif ($minutesLate < -60) {
            return response()->json(['message' => 'Check-in terlalu awal.'], 400); // Jika check-in terlalu dini
        }
    
        // Simpan absensi
        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'tanggal_absensi' => $today,
            'jam_check_in' => $now->toTimeString(),
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'status' => $status,
        ]);
    
        return response()->json([
            'message' => 'Check-in berhasil.',
            'data' => $attendance,
        ]);
    }
    
    

    public function checkOut(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::find($validated['employee_id']);
        $today = Carbon::now()->toDateString();
        $now = Carbon::now();

        // Cari absensi hari ini
        $attendance = Attendance::where('employee_id', $employee->id)
                                 ->where('tanggal_absensi', $today)
                                 ->first();

        if (!$attendance) {
            return response()->json(['message' => 'Anda belum melakukan check-in hari ini.'], 400);
        }

        // Cari shift untuk employee hari ini
        $shift = Shift::where('employee_id', $employee->id)
                      ->where('tanggal_shift', $today)
                      ->first();

        if (!$shift) {
            return response()->json(['message' => 'Shift tidak ditemukan untuk hari ini.'], 404);
        }

        $shiftEnd = Carbon::createFromTimeString($shift->jam_keluar);

        // Cek apakah checkout terlalu awal
        if ($now->lt($shiftEnd)) {
            return response()->json(['message' => 'Anda tidak dapat checkout sebelum shift selesai.'], 400);
        }

        // Update absensi dengan jam check-out
        $attendance->update([
            'jam_check_out' => $now->toTimeString(),
        ]);

        return response()->json([
            'message' => 'Check-out berhasil.',
            'data' => $attendance,
        ]);
    }

    // Fungsi untuk menghitung jarak antara dua koordinat
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
