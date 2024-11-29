<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    // Menampilkan semua shift
    public function index()
    {
        return response()->json(Shift::with('employee')->get());
    }

    // Membuat shift baru
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_shift' => 'required|date',
            'jam_masuk' => 'required|date_format:H:i:s',
            'jam_keluar' => 'required|date_format:H:i:s',
        ]);

        $shift = Shift::create($request->all());

        return response()->json([
            'message' => 'Shift created successfully',
            'data' => $shift,
        ], 201);
    }

    public function show($id)
{
    // Cari shift berdasarkan ID dan sertakan data employee
    $shift = Shift::with('employee')->find($id);

    // Jika data tidak ditemukan
    if (!$shift) {
        return response()->json(['message' => 'Shift not found'], 404);
    }

    // Jika ditemukan, kembalikan data
    return response()->json($shift);
}


    // Memperbarui shift
    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'employee_id' => 'exists:employees,id',
            'tanggal_shift' => 'date',
            'jam_masuk' => 'date_format:H:i:s',
            'jam_keluar' => 'date_format:H:i:s',
        ]);

        $shift->update($request->all());

        return response()->json([
            'message' => 'Shift updated successfully',
            'data' => $shift,
        ]);
    }

    // Menghapus shift
    public function destroy($id)
    {
        $shift = Shift::findOrFail($id);
        $shift->delete();

        return response()->json(['message' => 'Shift deleted successfully']);
    }
}
