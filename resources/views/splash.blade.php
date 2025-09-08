@extends('layouts.app')

@section('content')
<style>
    .splash-container { position: relative; height: 100vh; overflow: hidden; text-align: center; color: white; }
    .splash-background { position: absolute; width: 100%; height: 100%; background: url('{{ asset('images/bg.jpg') }}') center center / cover no-repeat; opacity: 0.25; z-index: 1; }
    .splash-content { position: relative; z-index: 2; top: 35%; padding: 0 20px; }
    .logo { max-width: 70%; width: 300px; height: auto; margin-bottom: 30px; }
    .get-started-btn { display: inline-block; background-color: #002456; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-size: 1.2rem; text-decoration: none; transition: background-color 0.3s ease; }
    .get-started-btn:hover { background-color: #001c3a; color: #fff; }
    @media (max-width: 768px) { .logo { width: 60%; } .get-started-btn { font-size: 1rem; padding: 10px 24px; } }
    @media (max-width: 480px) { .logo { width: 80%; } .get-started-btn { font-size: 0.9rem; padding: 8px 20px; } }
</style>

<div class="splash-container">
    <div class="splash-background"></div>
    <div class="splash-content">
        <img src="{{ asset('images/logo.png') }}" alt="Infralexia Logo" class="logo">
        <br>
        <a href="{{ Auth::check() ? route('home') : route('login') }}" class="get-started-btn">Get Started</a>
    </div>
</div>
@endsection
