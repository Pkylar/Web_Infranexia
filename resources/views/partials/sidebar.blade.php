{{-- resources/views/partials/sidebar.blade.php --}}
@php
  $role         = auth()->user()->role ?? '';
  $isSuperAdmin = ($role === 'Super Admin');
  $isHdTa       = ($role === 'HD TA');
  $isTeamLeader = ($role === 'Team Leader');
  $isHdMitra    = ($role === 'HD Mitra');
  $canPSB       = $isSuperAdmin || $isHdTa || $isHdMitra || $isTeamLeader;
@endphp

<aside id="sidebar" class="sidebar">
  <button id="sbToggle" class="sb-toggle btn btn-light btn-sm" aria-label="Toggle sidebar" type="button">
    <i class="bi bi-chevron-left"></i>
  </button>

  <div class="sb-section w-100">
    <div class="d-flex flex-column align-items-center text-center mb-3">
      <div class="avatar mb-2">{{ strtoupper(auth()->user()->name[0] ?? 'U') }}</div>
      <div class="user-name fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
      <div class="user-email text-muted small">{{ auth()->user()->email ?? '' }}</div>
      @if($role)<div class="badge bg-light text-dark mt-2">{{ $role }}</div>@endif
    </div>

    <div class="logout-wrap mb-3 d-flex justify-content-center">
      <form action="{{ route('logout') }}" method="POST">@csrf
        <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
      </form>
    </div>

    <div class="menu-title">MENU</div>
    <div class="d-flex flex-column gap-2">

      {{-- Detail Order PSB: SA + HD TA + HD Mitra + TL --}}
      @if($canPSB)
        <a href="{{ route('detail-order-psb.index') }}" class="menu-item" data-menu="psb" title="Detail Order PSB">
          <span class="mi-left"><i class="bi bi-file-earmark-text"></i></span>
          <span class="mi-text">Detail Order <strong>PSB</strong></span>
          <i class="bi bi-chevron-right mi-chevron"></i>
        </a>
      @endif

      {{-- Presensi: SA + TL --}}
      @if($isSuperAdmin || $isTeamLeader)
        <a href="{{ route('presensi.checkin') }}" class="menu-item" data-menu="psb" title="Presensi">
          <span class="mi-left"><i class="bi bi-clipboard-check"></i></span>
          <span class="mi-text">Presensi</span>
          <i class="bi bi-chevron-right mi-chevron"></i>
        </a>
      @endif

      {{-- Teknisi submenu: SA + TL --}}
      @if($isSuperAdmin || $isTeamLeader)
        <button type="button" class="menu-item js-tek-toggle" data-menu="psb" title="Teknisi" aria-expanded="false">
          <span class="mi-left"><i class="bi bi-people"></i></span>
          <span class="mi-text">Teknisi</span>
          <i class="bi bi-chevron-down mi-chevron js-tek-caret"></i>
        </button>

        <div class="submenu js-tek-sub">
          <a href="{{ route('teknisi.index') }}" class="menu-subitem" title="Tim">
            <span class="mi-left"><i class="bi bi-diagram-3"></i></span>
            <span class="mi-text">Tim</span>
            <i class="bi bi-chevron-right mi-chevron"></i>
          </a>
          <a href="{{ route('registrasi-teknisi.index') }}" class="menu-subitem" title="Registrasi">
            <span class="mi-left"><i class="bi bi-person-plus"></i></span>
            <span class="mi-text">Registrasi</span>
            <i class="bi bi-chevron-right mi-chevron"></i>
          </a>
        </div>
      @endif

      @hasSection('sidebar_extra')
        @yield('sidebar_extra')
      @endif
    </div>
  </div>
</aside>

@once
@push('styles')
<style>
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
    --sb-w:300px;
    --sb-mini:72px;
  }

  /* ===== Sidebar container ===== */
  .sidebar{
    position:relative; width:var(--sb-w);
    background:var(--glass); border-radius:16px; padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.16), 0 0 0 1px rgba(2,8,23,.06) inset;
    transition: width .24s ease, box-shadow .24s ease, background .24s ease;
    backdrop-filter: blur(8px);
    z-index:20;
  }
  .sidebar.mini{ width:var(--sb-mini); }
  .sb-section{ transition: opacity .18s ease; }
  .sidebar.mini .sb-section{ opacity:0; pointer-events:none; position:absolute; inset:16px; }

  .sb-toggle{
    position:absolute; top:10px; right:10px; z-index:21;
    border-radius:999px; padding:6px 10px; line-height:1;
    background:#fff; border:1px solid #e5e7eb;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
  }

  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff;
           display:grid; place-items:center; font-weight:700; font-size:20px; }
  .logout-wrap .btn{ min-width:140px; }

  .menu-title{ font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; margin:12px 0 8px; }

  /* ===== Item utama ===== */
  .menu-item{
    display:flex; align-items:center; gap:12px;
    padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background: rgba(255,255,255,.5) !important;
    border:1px solid rgba(0,0,0,.08);
    backdrop-filter: blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .menu-item::before{
    content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px;
    background: var(--psb); opacity:.9;
  }
  .menu-item .mi-left{
    width:36px; height:36px; display:grid; place-items:center; border-radius:10px;
    background: rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.08);
  }
  .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; transition: transform .15s ease, color .15s ease; }
  .menu-item:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(2,8,23,.16), 0 0 0 1px rgba(255,255,255,.45) inset; }
  .menu-item:hover .mi-chevron{ transform: translateX(3px); color:#333; }

  /* ===== Submenu Teknisi ===== */
  .submenu{ display:none; padding-left:12px; }
  .submenu.show{ display:block; }
  .menu-subitem{
    display:flex; align-items:center; gap:12px;
    padding:12px 14px; border-radius:14px; text-decoration:none; color:#111; position:relative;
    background: rgba(255,255,255,.6)!important; border:1px solid rgba(14,165,233,.25);
    backdrop-filter: blur(8px);
    box-shadow:0 6px 18px rgba(2,8,23,.10), 0 0 0 1px rgba(255,255,255,.30) inset;
    margin-top:6px;
  }
  .menu-subitem::before{
    content:""; position:absolute; inset:0 auto 0 0; width:4px; border-radius:14px 0 0 14px; background:#0ea5e9; opacity:.9;
  }
  .menu-subitem .mi-left{
    width:32px; height:32px; display:grid; place-items:center; border-radius:8px;
    background: rgba(14,165,233,.10); border:1px solid rgba(14,165,233,.25);
  }
  .menu-subitem .mi-left i{ color:#0ea5e9; }

  /* ===== Responsive ===== */
  @media (max-width: 991.98px){
    .sidebar{ width:var(--sb-mini); }
    .sidebar .sb-section{ opacity:0; pointer-events:none; }
    .sidebar.mini .sb-section{ opacity:0; }
  }
  .sidebar.mini .menu-item{ justify-content:center; padding:12px 10px; }
  .sidebar.mini .menu-item .mi-chevron,
  .sidebar.mini .menu-title,
  .sidebar.mini .user-name,
  .sidebar.mini .user-email,
  .sidebar.mini .logout-wrap{ display:none !important; }
  .sidebar.mini .menu-item .mi-text{ display:none; }
  .sidebar.mini .menu-item .mi-left{ width:40px; height:40px; }
  .sidebar.mini .submenu{ display:none !important; }
</style>
@endpush
@endonce
