<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // Tujuan default setelah login (kalau tidak ada intended)
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Dipanggil SETELAH kredensial valid dan Auth::login() sukses oleh trait.
     * Jika user mengaktifkan 2FA, kita keluarkan dulu (logout),
     * simpan user id di session, lalu arahkan ke halaman verifikasi OTP.
     */
    protected function authenticated(Request $request, $user)
    {
        if (!empty($user->two_factor_secret)) {
            Auth::logout();
            $request->session()->put('2fa:user:id', $user->id);
            return redirect()->route('2fa.verify');
        }

        // Jika user tidak mengaktifkan 2FA, lanjut normal
        return redirect()->intended($this->redirectTo);
    }
}
