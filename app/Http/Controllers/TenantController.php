<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        return response()->json(Tenant::with('users')->get());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_perusahaan' => 'required|string|max:255',
        'db_name' => 'required|string|max:255',
        'db_user' => 'required|string|max:255',
        'db_password' => 'required|string|max:255',
    ]);

    try {
        $tenant = Tenant::create($validated);
         return response()->json($tenant, 201);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function show($id)
    {
        $tenant = Tenant::with('users')->find($id);
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }
        return response()->json($tenant);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::find($id);
        if (!$tenant) {
            return response()->json(['message' => 'Tenant not found'], 404);
        }

        $tenant->update($request->all());
        return response()->json(['message' => 'Tenant updated successfully', 'data' => $tenant]);
    }

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
