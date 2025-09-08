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
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        opacity: 0.75; /* 75% background opacity */
        z-index: 0;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        max-width: 400px;
        width: 100%;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .login-logo {
        max-width: 220px;
        margin: 0 auto 20px auto;
    }

    .form-group {
        margin-bottom: 20px;
        max-width: 340px;
        margin-left: auto;
        margin-right: auto;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        text-align: left;
    }

    .form-icon-wrapper {
        position: relative;
    }

    .form-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        font-size: 14px;
    }

    .form-control {
        border-radius: 10px;
        padding-left: 40px;
        padding-right: 10px;
        height: 44px;
        font-size: 14px;
        border: 1px solid #ccc;
        width: 100%;
        box-sizing: border-box;
    }

    .btn-login {
        background-color: #002456;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: bold;
        width: 100%;
        max-width: 340px;
        margin: 0 auto;
        display: block;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
    }

    .btn-login:hover {
        background-color: #001c3a;
    }

    .register-link {
        margin-top: 15px;
    }

    .register-link a {
        color: #002456;
        font-weight: 600;
    }

    .text-danger {
        color: #e3342f;
        font-size: 13px;
        margin-top: 4px;
        text-align: left;
        max-width: 340px;
        margin-left: auto;
        margin-right: auto;
    }
</style>

<div class="login-bg">
    <div class="login-card">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="login-logo">
        <h2>Welcome!</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-envelope"></i></span>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="Enter email">
                </div>
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="form-icon-wrapper">
                    <span class="form-icon"><i class="fas fa-lock"></i></span>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="current-password" placeholder="Enter password">
                </div>
                @error('password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-login">Login</button>

            <div class="register-link mt-3">
                <span>Don’t have an account? <a href="{{ route('register') }}">Register here</a></span>
            </div>
        </form>
    </div>
</div>
@endsection
