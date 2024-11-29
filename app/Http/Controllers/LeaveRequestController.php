<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveBalance;
use App\Models\LeaveDocument;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UrgentLeaveRequestNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeaveRequestsExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpParser\Node\Stmt\Switch_;

class LeaveRequestController extends Controller
{

    // Mengajukan cuti
    public function store(Request $request)
{
    // Validasi inputan
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'jumlah_cuti' => 'required|numeric|min:1',
        'jenis_pengajuan' => 'required|in:Cuti Tahunan,Cuti Darurat',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai', // pastikan tanggal selesai >= tanggal mulai
    ]);

    try {
        // Cari data saldo cuti karyawan
        $leaveBalance = LeaveBalance::where('employee_id', $request->employee_id)->first();

        if (!$leaveBalance) {
            return response()->json(['message' => 'Data saldo cuti tidak ditemukan.'], 404);
        }

        // Cek apakah saldo cuti cukup
        $jumlahCuti = $request->jumlah_cuti;
        if (!$leaveBalance->useLeave($jumlahCuti)) {
            return response()->json(['message' => 'Saldo cuti tidak mencukupi.'], 400);
        }

        // Membuat permintaan cuti
        $leaveRequest = LeaveRequest::create([
            'employee_id' => $request->employee_id,
            'jumlah_cuti' => $jumlahCuti,
            'jenis_pengajuan' => $request->jenis_pengajuan,  // Menyimpan jenis pengajuan
            'tanggal_mulai' => $request->tanggal_mulai,      // Menyimpan tanggal mulai
            'tanggal_selesai' => $request->tanggal_selesai,  // Menyimpan tanggal selesai
            'status' => 'Menunggu',                          // Status default
            'alasan' => $request->alasan ?? null,            // Alasan jika ada
        ]);

        // Kirim respons sukses
        return response()->json([
            'message' => 'Permintaan cuti berhasil diajukan.',
            'data' => $leaveRequest
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan saat mengajukan cuti.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    // Menyetujui atau menolak cuti
    public function update(Request $request, $id)
    {
        // Validasi input status
        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Disetujui,Ditolak,Persetujuan Sementara',
        ]);
    
        // Cari data pengajuan cuti berdasarkan ID
        $leaveRequest = LeaveRequest::findOrFail($id);
        $jenisPengajuan = $leaveRequest->jenis_pengajuan;
    
        // Update status pengajuan cuti
        $leaveRequest->status = $request->status;
    
        // Jika status disetujui dan jenis pengajuan adalah Cuti Tahunan, baru kurangi jatah cuti
        if ($request->status === 'Disetujui') {
            // Menghitung durasi cuti dalam hari
            $duration = Carbon::parse($leaveRequest->tanggal_mulai)->diffInDays(Carbon::parse($leaveRequest->tanggal_selesai)) + 1;
    
            // Ambil saldo cuti karyawan
            $leaveBalance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)->first();
    
            // Cek apakah saldo cuti cukup
            if ($leaveBalance && $leaveBalance->sisa_cuti >= $duration) {
                // Kurangi sisa cuti jika cukup
                $leaveBalance->sisa_cuti -= $duration;
                $leaveBalance->total_cuti += $duration; // Tambah total cuti sesuai durasi
                $leaveBalance->save();
            } else {
                return response()->json(['message' => 'Saldo cuti tidak mencukupi.'], 400);
            }

            if ($jenisPengajuan === 'Cuti Tahunan') {
                // Tambahkan cuti tahunan
                $leaveBalance->cuti_tahunan += $duration;
                $leaveBalance->save();
            }
            // Logika jika jenis pengajuan adalah "Cuti Darurat"
            if ($jenisPengajuan === 'Cuti Darurat') {
                // Tambahkan cuti darurat
                $leaveBalance->cuti_darurat += $duration;
                $leaveBalance->save();
            }
        }
    
        // Simpan perubahan status pengajuan
        $leaveRequest->save();
    
        return response()->json(['message' => 'Status pengajuan berhasil diperbarui.']);
    }
    



public function destroy($id)
{
    try {
        // Temukan data berdasarkan ID
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Hapus data
        $leaveRequest->delete();

        // Berikan respon sukses
        return response()->json([
            'message' => 'Data berhasil dihapus',
        ], 200);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Data tidak ditemukan
        return response()->json([
            'message' => 'Data tidak ditemukan',
        ], 404);
    } catch (\Exception $e) {
        // Tangani error lain
        return response()->json([
            'message' => 'Terjadi kesalahan saat menghapus data',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function index()
    {
        // Mengambil semua data pengajuan cuti dan relasi karyawan
        $leaveRequests = LeaveRequest::with('employee')->get(); // Pastikan ada relasi employee

        return response()->json([
            'success' => true,
            'data' => $leaveRequests,
        ], 200);
    }

    public function show($id)
    {
        // Cari data berdasarkan ID
        $leaveRequest = LeaveRequest::with('employee')->find($id);

        // Jika data tidak ditemukan, kembalikan error
        if (!$leaveRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengajuan cuti tidak ditemukan.',
            ], 404);
        }

        // Kembalikan data
        return response()->json([
            'success' => true,
            'data' => $leaveRequest,
        ], 200);
    }

    public function exportToExcel()
{
    return Excel::download(new LeaveRequestsExport, 'leave_requests.xlsx');
}

public function exportToPDF()
{
    $leaveRequests = LeaveRequest::all();
    $pdf = Pdf::loadView('leave_requests.pdf', compact('leaveRequests'));
    return $pdf->download('leave_requests.pdf');
}
    
}
