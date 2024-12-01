<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Menampilkan semua tenant.
     */
    public function index()
    {
        return response()->json(Tenant::with('domains')->get());
    }

    /**
     * Membuat tenant baru.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'domain' => 'required|string|max:255|unique:domains,domain',
    ]);

    try {
        // Buat tenant baru
        $tenant = Tenant::create([
            'name' => $validated['name'], // Ambil name dari input JSON
        ]);

        // Buat domain untuk tenant
        $tenant->domains()->create([
            'domain' => $validated['domain'],
        ]);

        return response()->json(['message' => 'Tenant created successfully', 'tenant' => $tenant], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    /**
     * Menampilkan tenant berdasarkan ID.
     */
    public function show($id)
    {
        $tenant = Tenant::with('domains')->find($id);
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        return response()->json($tenant);
    }

    /**
     * Memperbarui tenant.
     */
    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        $tenant->update($validated);
        return response()->json(['message' => 'Tenant updated successfully', 'data' => $tenant]);
    }

    /**
     * Menghapus tenant.
     */
    public function destroy($id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $tenant->delete();
        return response()->json(['message' => 'Tenant deleted successfully']);
    }
}
