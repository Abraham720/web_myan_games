<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $supabaseUrl;
    protected $supabaseAnonKey;

    public function __construct()
    {
        // Kita pakai ANON key untuk auth user (bukan service role)
        $this->supabaseUrl = config('services.supabase.url');
        $this->supabaseAnonKey = env('SUPABASE_ANON_KEY'); 
    }

    // 1. Tampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // 2. Proses Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Panggil Supabase Auth API
        $response = Http::withHeaders([
            'apikey' => $this->supabaseAnonKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->supabaseUrl}/auth/v1/token?grant_type=password", [
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if ($response->successful()) {
            $user = $response->json();
            
            // SIMPAN UUID KE SESSION LARAVEL
            // Ini kunci agar Laravel "tahu" siapa yang login
            session([
                'supabase_user' => [
                    'id' => $user['user']['id'], // UUID
                    'email' => $user['user']['email'],
                    'username' => $user['user']['user_metadata']['username'] ?? 'User',
                ]
            ]);

            return redirect()->route('feed')->with('success', 'Login berhasil!');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    // 3. Tampilkan Form Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // 4. Proses Register
    public function register(Request $request)
{
    // 1. Validasi input (HAPUS unique:auth_users_check)
    $request->validate([
        'username' => 'required|string|max:50',
        'email' => 'required|email',  // ✅ HAPUS "unique:..." 
        'password' => 'required|min:6|confirmed',
    ]);

    // 2. Panggil Supabase Sign Up API
    $response = Http::withHeaders([
        'apikey' => $this->supabaseAnonKey,
        'Content-Type' => 'application/json',
    ])->post("{$this->supabaseUrl}/auth/v1/signup", [
        'email' => $request->email,
        'password' => $request->password,
        'data' => [
            'username' => $request->username
        ]
    ]);

    // 3. Handle Response dari Supabase
    if ($response->successful()) {
        $user = $response->json();
        
        // Auto login setelah register (opsional)
        session([
            'supabase_user' => [
                'id' => $user['user']['id'],
                'email' => $user['user']['email'],
                'username' => $request->username,
            ]
        ]);

        return redirect()->route('feed')->with('success', 'Akun berhasil dibuat! Silakan lengkapi profilmu.');
    }

    // 4. Handle Error dari Supabase (misal: email sudah terdaftar)
    $errorData = $response->json();
    $errorMessage = $errorData['msg'] ?? 'Registrasi gagal. Silakan coba lagi.';
    
    // Jika email sudah dipakai, Supabase return status 400 dengan pesan spesifik
    if ($response->status() === 400 && str_contains(strtolower($errorMessage), 'email')) {
        $errorMessage = 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.';
    }

    return back()->withErrors(['email' => $errorMessage])->withInput();
}

    // 5. Logout
    public function logout()
    {
        // Opsional: Panggil API Supabase untuk revoke token
        session()->forget('supabase_user');
        return redirect()->route('feed')->with('success', 'Logout berhasil.');
    }
}