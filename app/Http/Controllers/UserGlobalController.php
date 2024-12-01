<?php

namespace App\Http\Controllers;

use App\Models\UserGlobal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserGlobalController extends Controller
{
    public function index()
    {
        return response()->json(UserGlobal::all(), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users_global,email',
            'password' => 'required|string|min:6',
            'role_global' => 'in:Admin,HRD,Karyawan',
        ]);

        $user = UserGlobal::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_global' => $request->role_global ?? 'Karyawan',
        ]);

        return response()->json(['message' => 'User created successfully', 'data' => $user], 201);
    }

    public function show($id)
    {
        $user = UserGlobal::all()->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = UserGlobal::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = UserGlobal::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }
}
