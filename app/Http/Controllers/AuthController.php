<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // OWASP A07 — Authentication: session regenerate + audit log
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate(); // cegah session fixation

            // OWASP A09 — catat login berhasil
            AuditLog::record('login', 'Login berhasil', ['email' => $credentials['email']]);

            return redirect()->intended('/schedules');
        }

        // OWASP A09 — catat login gagal (tanpa password!)
        AuditLog::record('failed_login', 'Percobaan login gagal', ['email' => $credentials['email']]);

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    // OWASP A07 — session invalidate saat logout
    public function logout(Request $request)
    {
        AuditLog::record('logout', 'User logout');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // OWASP A07 — Password complexity rules
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()       // minimal 1 huruf
                    ->mixedCase()     // minimal 1 huruf besar + 1 kecil
                    ->numbers()       // minimal 1 angka
                    ->symbols()       // minimal 1 simbol
                    ->uncompromised(), // tidak ada di database breach (HaveIBeenPwned)
            ],
        ]);

        $role = User::count() === 0 ? 'admin' : 'user';

        $user = User::create([
            'name'     => $validatedData['name'],
            'email'    => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role'     => $role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        // OWASP A09 — catat registrasi
        AuditLog::record('register', 'Akun baru dibuat', ['email' => $user->email, 'role' => $role]);

        return redirect()->intended('/schedules')->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name);
    }
}
