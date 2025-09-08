<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Infralexia - PL TSEL')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Jika kamu punya CSS sendiri, tetap dipakai --}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body { background: url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed; }
        .layout-wrapper { backdrop-filter: blur(2px); }
        .sidebar { width: 300px; min-height: 100vh; background: rgba(240, 248, 255, 0.75); }
        .sidebar .brand { padding: 24px 20px; display:flex; align-items:center; gap:12px; }
        .sidebar .brand img { height: 44px; }
        .sidebar .menu a { display:block; padding:12px 18px; border-radius:12px; text-decoration:none; margin-bottom:8px; color:#000; }
        .sidebar .menu a.active, .sidebar .menu a:hover { background: rgba(0,0,0,0.06); }
        .content { flex: 1; padding: 24px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="layout-wrapper d-flex">
        <aside class="sidebar p-3">
            <div class="brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
                <div>
                    <div class="fw-bold">Infralexia</div>
                    <small>SEGEMEN PL-TSEL</small>
                </div>
            </div>

            @include('partials.sidebar')
        </aside>

        <main class="content">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
