@extends('layouts.app')

@section('content')
<style>
    .login-bg {
        position: relative;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 30px;
        overflow: hidden;
    }
    .login-bg::before {
        content: "";
        background: url('{{ asset('images/bg.jpg') }}') center center / cover no-repeat;
        position: absolute; inset: 0;
        opacity: 0.75;
        z-index: 0;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        max-width: 400px; width: 100%;
        text-align: center; position: relative; z-index: 1;
    }
    .login-logo { max-width: 220px; margin: 0 auto 20px auto; }
    .form-group { margin-bottom: 20px; max-width: 340px; margin-left: auto; margin-right: auto; }
    .form-label { display: block; font-weight: 600; color: #333; margin-bottom: 6px; text-align: left; }
    .form-icon-wrapper { position: relative; }
    .form-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; font-size: 14px; }
    .form-control {
        border-radius: 10px; padding-left: 40px; padding-right: 10px;
        height: 44px; font-size: 14px; border: 1px solid #ccc; width: 100%; box-sizing: border-box;
    }
    .btn-login {
        background-color: #002456; color: white; border: none; border-radius: 10px; padding: 12px; font-weight: bold;
        width: 100%; max-width: 340px; margin: 0 auto; display: block; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
    }
    .btn-login:hover { background-color: #001c3a; }
    .register-link { margin-top: 15px; }
    .register-link a { color: #002456; font-weight: 600; }
    .text-danger { color: #e3342f; font-size: 13px; margin-top: 4px; text-align: left; max-width: 340px; margin-left: auto; margin-right: auto; }
</style>

<div class="login-bg">
    <div class="login-card">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
        <h2>Create Account</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Name</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-user"></i></span>
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           name="name" value="{{ old('name') }}" required autofocus placeholder="Enter name">
                </div>
                @error('name') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email') }}" required placeholder="Enter email">
                </div>
                @error('email') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" required placeholder="Enter password">
                </div>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password-confirm" class="form-label">Confirm Password</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-lock"></i></span>
                    <input id="password-confirm" type="password" class="form-control"
                           name="password_confirmation" required placeholder="Confirm password">
                </div>
            </div>

            {{-- ROLE SELECT --}}
            <div class="form-group">
                <label for="role" class="form-label">Role</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-user-tag"></i></span>
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                        <option value="">— pilih role —</option>
                        <option value="Super Admin" {{ old('role')==='Super Admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="HD TA"        {{ old('role')==='HD TA' ? 'selected' : '' }}>HD TA</option>
                        <option value="HD Mitra"     {{ old('role')==='HD Mitra' ? 'selected' : '' }}>HD Mitra</option>
                        <option value="Team Leader"  {{ old('role')==='Team Leader' ? 'selected' : '' }}>Team Leader</option>
                    </select>
                </div>
                @error('role') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-login">Register</button>

            <div class="register-link mt-3">
                <span>Already have an account? <a href="{{ route('login') }}">Login</a></span>
            </div>
        </form>
    </div>
</div>
@endsection
