<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    // --- VIEWS ---
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    // --- LOGIC ---
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('anime.index'));
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'is_admin' => (User::count() == 0) ? 1 : 0, 
        ]);

        Auth::attempt($request->only('email', 'password'));
        return redirect(route('anime.index'))->with('success', 'Registrasi berhasil! Anda telah login.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'))->with('success', 'Anda telah berhasil logout!');
    }

    // -- Fungsi Reset Password --
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        Log::info('--- PROSES RESET PASSWORD DIMULAI (TES BACA TOKEN) ---');
        $request->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ], [
            'phone_number.exists' => 'Nomor WhatsApp tidak terdaftar.'
        ]);

        $phone_number = $request->phone_number;
        $user = User::where('phone_number', $phone_number)->first();

        if (!$user) {
            Log::warning('Gagal: Nomor HP tidak ditemukan di database.');
            return back()->withErrors(['phone_number' => 'Nomor WhatsApp tidak terdaftar.']);
        }

        Log::info('Sukses: Pengguna ditemukan -> ' . $user->email);

        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );
        Log::info('Token reset telah dibuat dan disimpan untuk ' . $user->email);

        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        try {
            $token_fonnte = env('FONNTE_TOKEN');
            if (empty($token_fonnte)) {
                Log::error('Gagal: FONNTE_TOKEN kosong di .env');
                return back()->withErrors(['phone_number' => 'Konfigurasi server error.']);
            }
            
            Log::info('Token yang dibaca dari .env untuk dikirim: ' . $token_fonnte);

            $target_number = $user->phone_number;
            if (str_starts_with($target_number, '08')) {
                $target_number = substr($target_number, 1);
            }

            Log::info('Mencoba mengirim WA ke ' . $target_number . ' via Fonnte...');

            $response = Http::withHeaders([
                'Authorization' => $token_fonnte
            ])->post('https://api.fonnte.com/send', [
                'target' => $target_number,
                'message' => "Halo " . $user->name . "! Klik link ini untuk reset password kamu:\n" . $resetUrl . "\n\nLink ini hanya valid selama 60 menit.",
                'countryCode' => '62',
            ]);

            if ($response->failed() || $response->json('status') == false) {
                Log::error('Gagal kirim WA. Respon Fonnte: ' . $response->body());
                return back()->withErrors(['phone_number' => 'Gagal mengirim pesan WhatsApp. Pastikan Device Fonnte Anda "Connected".']);
            }

            Log::info('Sukses: Pesan WA berhasil dikirim. Respon Fonnte: ' . $response->body());

        } catch (\Exception $e) {
            Log::error('Exception saat mengirim WA: ' . $e->getMessage());
            return back()->withErrors(['phone_number' => 'Gagal terhubung ke server WhatsApp.']);
        }

        return back()->with('status', 'Link reset password telah dikirim ke WhatsApp Anda.');
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        $email = $request->email;
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)->first();

        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['email' => 'Token reset password tidak valid.']);
        }

        if (now()->subMinutes(60)->gt($tokenData->created_at)) {
            return back()->withErrors(['email' => 'Token reset password telah kedaluwarsa.']);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password Anda berhasil direset! Silakan login.');
    }
}