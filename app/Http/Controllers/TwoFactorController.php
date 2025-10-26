<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use PragmaRX\Google2FAQRCode\Google2FA as Google2FAQRCode;

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

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->one_time_password);

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
        $user = $request->user();
        $enabled = !empty($user->two_factor_secret);

        $secret = null;
        $inlineQr = null;     // akan berisi URL gambar QR
        $otpauth = null;      // simpan juga kalau mau tampilkan sebagai teks

        if (!$enabled) {
            $secret = $request->session()->get('2fa:setup:secret');
            if (!$secret) {
                $secret = (new Google2FA())->generateSecretKey();
                $request->session()->put('2fa:setup:secret', $secret);
            }

            $appName = config('app.name', 'Infranexia');
            $otpauth = (new Google2FA())->getQRCodeUrl($appName, $user->email, $secret);

            // Pakai layanan QR publik (pilih salah satu):
            // api.qrserver.com
            $inlineQr = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpauth);
            // atau Google Chart API (juga oke):
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

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

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
}
