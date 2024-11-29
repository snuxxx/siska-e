<?php

namespace App\Http\Controllers;

use App\Models\ShiftTemplate;
use Illuminate\Http\Request;

class ShiftTemplateController extends Controller
{
    // Menampilkan semua template
    public function index()
    {
        return response()->json(ShiftTemplate::all());
    }

    // Membuat template baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_template' => 'required|string',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ]);

        $template = ShiftTemplate::create($request->all());

        return response()->json($template, 201);
    }

    public function show($id)
{
    $shiftTemplate = ShiftTemplate::find($id);

    // Jika template tidak ditemukan
    if (!$shiftTemplate) {
        return response()->json(['message' => 'Shift Template not found'], 404);
    }

    // Jika ditemukan, kembalikan data
    return response()->json($shiftTemplate);
}


    // Memperbarui template
    public function update(Request $request, $id)
    {
        $template = ShiftTemplate::findOrFail($id);

        $request->validate([
            'nama_template' => 'string',
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
        ]);

        $template->update($request->all());

        return response()->json($template);
    }

    // Menghapus template
    public function destroy($id)
    {
        $template = ShiftTemplate::findOrFail($id);
        $template->delete();

        return response()->json(['message' => 'Template deleted successfully.']);
    }
}
