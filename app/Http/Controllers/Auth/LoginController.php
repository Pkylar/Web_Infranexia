<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    // jaga-jaga kalau dipakai oleh trait
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // override: setelah login SELALU ke /home (abaikan intended)
    protected function authenticated(Request $request, $user)
    {
        return redirect()->route('home');
    }
}
