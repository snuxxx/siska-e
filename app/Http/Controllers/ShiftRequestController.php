<?php

namespace App\Http\Controllers;

use App\Models\ShiftRequest;
use App\Models\Employee;
use App\Models\Shift;
use Illuminate\Http\Request;
use App\Notifications\ShiftRequestNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\LeaveRequest;


class ShiftRequestController extends Controller
{
    // Mengajukan permohonan pertukaran shift
    public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'shift_id' => 'required|exists:shifts,id',
        'requester_id' => 'required|exists:employees,id',
        'requested_to_id' => 'required|exists:employees,id',
        'alasan' => 'nullable|string',
    ]);

    // Mendapatkan data karyawan yang mengajukan permintaan dan yang diminta
    $requester = Employee::findOrFail($request->requester_id);
    $requestedTo = Employee::findOrFail($request->requested_to_id);

    // Validasi divisi dan jabatan yang sama
    if ($requester->division_id !== $requestedTo->division_id || $requester->position_id !== $requestedTo->position_id) {
        return response()->json(['message' => 'Karyawan harus memiliki divisi dan jabatan yang sama untuk melakukan pertukaran shift.'], 400);
    }

    // Buat permintaan pertukaran shift
    $shiftRequest = ShiftRequest::create([
        'shift_id' => $request->shift_id,
        'requester_id' => $request->requester_id,
        'requested_to_id' => $request->requested_to_id,
        'alasan' => $request->alasan,
        'status' => 'Pending', // status awal adalah pending
    ]);

    // Kirim notifikasi kepada karyawan yang diminta
    Notification::send($requestedTo, new ShiftRequestNotification($shiftRequest));

    // Kirim notifikasi ke HRD
    $hrd = Employee::where('role', 'HRD')->first(); // asumsikan HRD memiliki role khusus
    if ($hrd) {
        Notification::send($hrd, new ShiftRequestNotification($shiftRequest));
    }

    return response()->json([
        'message' => 'Permintaan pertukaran shift diajukan',
        'data' => $shiftRequest
    ], 201);
}


    // Menyetujuinya atau menolaknya

    public function update(Request $request, $id)
{
    $leaveRequest = LeaveRequest::find($id);

    if (!$leaveRequest) {
        return response()->json([
            'success' => false,
            'message' => 'Pengajuan cuti tidak ditemukan.',
        ], 404);
    }

    $validated = $request->validate([
        'status' => 'required|in:Menunggu,Disetujui,Ditolak,Persetujuan Sementara',
    ]);

    $leaveRequest->update($validated);

    return response()->json([
        'success' => true,
        'message' => 'Status pengajuan berhasil diperbarui.',
        'data' => $leaveRequest,
    ]);
}

public function destroy($id)
{
    $leaveRequest = LeaveRequest::find($id);

    if (!$leaveRequest) {
        return response()->json([
            'success' => false,
            'message' => 'Pengajuan cuti tidak ditemukan.',
        ], 404);
    }

    $leaveRequest->delete();

    return response()->json([
        'success' => true,
        'message' => 'Pengajuan cuti berhasil dihapus.',
    ]);
}


    
    // Menampilkan riwayat permintaan pertukaran shift
    public function index()
    {
        return response()->json(ShiftRequest::with(['requester', 'requestedTo', 'shift'])->get());
    }

    public function show($id)
{
    // Cari shift request berdasarkan ID
    $shiftRequest = ShiftRequest::with(['requester', 'requestedTo', 'shift'])->find($id);

    // Jika data tidak ditemukan, kembalikan respons error
    if (!$shiftRequest) {
        return response()->json([
            'message' => 'Shift request tidak ditemukan.'
        ], 404);
    }

    // Kembalikan data shift request jika ditemukan
    return response()->json([
        'message' => 'Shift request ditemukan.',
        'data' => $shiftRequest
    ], 200);
}



}
