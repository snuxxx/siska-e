<?php

namespace App\Http\Controllers;

use App\Models\User; // Gunakan model yang relevan, misalnya Employee jika berbeda
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Temukan pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Validasi password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial tidak valid'], 401);
        }

        // Buat token personal
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token autentikasi pengguna saat ini
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil'], 200);
    }
}
