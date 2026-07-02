<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Auth API untuk mobile client (Android).
 *
 * Memakai Laravel Sanctum personal access token:
 * - POST /api/login  -> menukar email+password dengan token Bearer
 * - POST /api/logout -> mencabut token yang sedang dipakai
 * - GET  /api/me     -> profil user pemilik token
 *
 * Auth web (session + CSRF) TIDAK tersentuh — dua jalur ini terpisah.
 */
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'       => 'required|email',
            'password'    => 'required|string',
            // Nama perangkat untuk label token, mis. "Pixel 9 Pro XL"
            'device_name' => 'nullable|string|max:100',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            AuditLog::record('api_failed_login', 'Percobaan login API gagal', [
                'email' => $request->email,
            ]);

            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $token = $user->createToken($request->device_name ?? 'mobile')->plainTextToken;

        AuditLog::record('api_login', 'Login API berhasil', ['email' => $user->email]);

        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
                'level' => $user->level,
                'xp'    => $user->xp,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Cabut HANYA token yang dipakai request ini (perangkat lain tetap login)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Token dicabut. Sampai jumpa!',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'level'  => $user->level,
                'xp'     => $user->xp,
                'streak' => $user->streak,
                'title'  => $user->title,
            ],
        ]);
    }
}
