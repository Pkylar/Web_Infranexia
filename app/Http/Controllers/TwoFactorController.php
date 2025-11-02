<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class TwoFactorController extends Controller
{
    // =================== FORM OTP (sudah ada di versi sebelumnya) ===================
    public function index()
    {
        if (!session()->has('2fa:user:id')) {
            return redirect()->route('login');
        }
        return view('auth.twofactor');
    }

    public function verify(Request $request)
    {
        $request->validate(['one_time_password' => 'required|string']);

        $userId = $request->session()->get('2fa:user:id');
        if (!$userId) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi 2FA berakhir. Silakan login lagi.']);
        }

        $user = User::find($userId);
        if (!$user || empty($user->two_factor_secret)) {
            return redirect()->route('login')->withErrors(['email' => '2FA tidak aktif untuk akun ini.']);
        }

        $google2fa = $this->google2fa();
        $valid     = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

        if (!$valid) {
            return back()->withErrors(['one_time_password' => 'Kode OTP tidak valid.']);
        }

        Auth::login($user);
        $request->session()->forget('2fa:user:id');

        return redirect()->intended('/home');
    }

    // =================== PENGATURAN 2FA DI PROFIL ===================

    public function settings(Request $request)
    {
        $user    = $request->user();
        $enabled = !empty($user->two_factor_secret);

        $secret  = null;
        $inlineQr = null;   // URL gambar QR (untuk <img src="...">)
        $otpauth = null;    // otpauth:// URI (opsional ditampilkan sebagai teks)

        if (!$enabled) {
            $secret = $request->session()->get('2fa:setup:secret');
            if (!$secret) {
                $g2fa   = $this->google2fa();
                $secret = $g2fa->generateSecretKey();
                $request->session()->put('2fa:setup:secret', $secret);
            }

            $appName = config('app.name', 'Infranexia');
            $g2fa    = $this->google2fa();
            $otpauth = $g2fa->getQRCodeUrl($appName, $user->email, $secret);

            // Buat QR via layanan publik
            $inlineQr = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);
            // Alternatif:
            // $inlineQr = 'https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl=' . urlencode($otpauth);
        }

        $recoveryCodes = [];
        if ($enabled && !empty($user->two_factor_recovery_codes)) {
            $decoded = json_decode($user->two_factor_recovery_codes, true);
            if (is_array($decoded)) $recoveryCodes = $decoded;
        }

        return view('profile.security', compact('enabled', 'secret', 'inlineQr', 'recoveryCodes', 'otpauth'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|string',
        ]);

        $user = $request->user();
        if (!empty($user->two_factor_secret)) {
            return redirect()->route('2fa.settings')->with('status', '2FA sudah aktif.');
        }

        $secret = $request->session()->get('2fa:setup:secret');
        if (!$secret) {
            return redirect()->route('2fa.settings')->withErrors(['2fa' => 'Sesi setup 2FA tidak ditemukan.']);
        }

        $g2fa  = $this->google2fa();
        $valid = $g2fa->verifyKey($secret, $request->one_time_password);

        if (!$valid) {
            return back()->withErrors(['one_time_password' => 'Kode OTP tidak valid.']);
        }

        // simpan secret ke user
        $user->two_factor_secret = $secret;

        // generate recovery codes (8 buah)
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::upper(Str::random(10));
        }
        $user->two_factor_recovery_codes = json_encode($codes);

        $user->save();

        // bersihkan secret sementara
        $request->session()->forget('2fa:setup:secret');

        return redirect()->route('2fa.settings')->with('status', 'Two-Factor Authentication berhasil diaktifkan.');
    }

    public function disable(Request $request)
    {
        $user = $request->user();

        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        // bersihkan juga secret sementara kalau ada
        $request->session()->forget('2fa:setup:secret');

        return redirect()->route('2fa.settings')->with('status', 'Two-Factor Authentication telah dinonaktifkan.');
    }

    public function regenerateRecovery(Request $request)
    {
        $user = $request->user();

        if (empty($user->two_factor_secret)) {
            return redirect()->route('2fa.settings')->withErrors(['2fa' => 'Aktifkan 2FA dulu sebelum membuat recovery codes.']);
        }

        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::upper(Str::random(10));
        }
        $user->two_factor_recovery_codes = json_encode($codes);
        $user->save();

        return redirect()->route('2fa.settings')->with('status', 'Recovery codes telah dibuat ulang.');
    }

    private function google2fa()
    {
        $classV8 = '\\PragmaRX\\Google2FA\\Google2FA';                 // v8
        $classV9 = '\\PragmaRX\\Google2FA\\Google2FA\\Google2FA';      // v9

        if (class_exists($classV8)) {
            return new $classV8();
        }

        if (class_exists($classV9)) {
            return new $classV9();
        }

        throw new \RuntimeException(
            'Google2FA class not found. Pastikan paket terpasang & autoload dimuat: ' .
            'composer require pragmarx/google2fa && composer dump-autoload -o'
        );
    }
}
