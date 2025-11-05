<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

    // Menampilkan form untuk memasukkan nomor HP.
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['phone_number' => 'required|string|exists:users,phone_number']);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return back()->withErrors(['phone_number' => 'Nomor WhatsApp tidak terdaftar.']);
        }

        // Buat token
        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        // Buat URL reset
        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);

        // Kirim pesan via Fonnte
        try {
            $response = Http::post('https://api.fonnte.com/send', [
                'target' => $user->phone_number,
                'message' => "Halo! Klik link ini untuk reset password Anda: " . $resetUrl . "\n\nLink ini hanya valid selama 60 menit.",
                'countryCode' => '62',
            ], [
                'Authorization' => env('FONNTE_TOKEN')
            ]);

            if ($response->failed()) {
                return back()->withErrors(['phone_number' => 'Gagal mengirim pesan WhatsApp. Coba lagi nanti.']);
            }

        } catch (\Exception $e) {
            return back()->withErrors(['phone_number' => 'Gagal terhubung ke server WhatsApp.']);
        }

        return back()->with('status', 'Link reset password telah dikirim ke WhatsApp Anda.');
    }

    // Menampilkan form reset password (password baru).
    public function showResetPasswordForm(Request $request, $token = null)
    {
        $email = $request->email;
        return view('auth.reset-password', ['token' => $token, 'email' => $email]);
    }

    // Memproses reset password.
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

        // Update password user
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Password Anda berhasil direset! Silakan login.');
    }
}