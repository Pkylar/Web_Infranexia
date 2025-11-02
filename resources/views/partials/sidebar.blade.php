{{-- resources/views/partials/sidebar.blade.php --}}
@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    // ---- user & identity (boleh null ketika guest / render awal)
    $user     = Auth::user();
    $name     = trim($user->name ?? 'User');
    $email    = $user->email ?? '';
    $role     = $user->role  ?? '';
    $initial  = strtoupper(mb_substr($name, 0, 1));

    // ---- role flags
    $isSuperAdmin = $role === 'Super Admin';
    $isHdTa       = $role === 'HD TA';
    $isTeamLeader = $role === 'Team Leader';
    $isHdMitra    = $role === 'HD Mitra';
    $canPSB       = $isSuperAdmin || $isHdTa || $isTeamLeader || $isHdMitra;

    // ---- route flags
    $is = fn (string $pat) => request()->routeIs($pat);
    $isPresensi  = $is('presensi.*');
    $isPSB       = $is('detail-order-psb.*');
    $isSecurity  = $is('2fa.*') || $is('profile.*') || $is('security.*');
    $isTim       = $is('teknisi.*');
    $isRegTek    = $is('registrasi-teknisi.*');

    // ---- status 2FA (aman terhadap $user === null)
    $has2FA = false;
    if ($user) {
        // Fortify default
        $has2FA = !empty($user->two_factor_secret);
        // Jika ada helper method, cek dengan aman
        if (!$has2FA && method_exists($user, 'hasEnabledTwoFactorAuthentication')) {
            try { $has2FA = (bool) $user->hasEnabledTwoFactorAuthentication(); } catch (\Throwable $e) { /* ignore */ }
        }
    }

    // ---- tentukan halaman pengaturan 2FA yang tersedia
    $candidates = ['2fa.settings','twofactor.index','profile.security','security.index','profile.show'];
    $link2fa    = null;
    foreach ($candidates as $r) {
        if (Route::has($r)) {
            $link2fa = $r === 'profile.show'
                ? route('profile.show') . '#two-factor-authentication'
                : route($r);
            break;
        }
    }
    $link2fa = $link2fa ?? url('/profile');

    // ---- submenu teknisi expanded?
    $tekExpanded = $isTim || $isRegTek;
@endphp

<aside id="sidebar" class="sidebar">
  <button id="sbToggle" class="btn btn-light btn-sm sb-toggle" aria-label="Toggle sidebar" type="button">
    <i class="bi bi-chevron-left"></i>
  </button>

  <div class="sb-section w-100">
    {{-- Profile --}}
    <div class="d-flex flex-column align-items-center text-center mb-3">
      <div class="avatar mb-2">{{ $initial }}</div>
      <div class="user-name fw-semibold">{{ $name }}</div>
      @if($email)<div class="user-email text-muted small">{{ $email }}</div>@endif
      @if($role)<div class="badge bg-light text-dark mt-2">{{ $role }}</div>@endif
      <div class="small text-muted mt-2">
        2FA:
        <strong class="{{ $has2FA ? 'text-success' : 'text-danger' }}">
          {{ $has2FA ? 'Aktif' : 'Nonaktif' }}
        </strong>
      </div>
    </div>

    {{-- Logout --}}
    <div class="logout-wrap mb-3 d-flex justify-content-center">
      <form action="{{ route('logout') }}" method="POST">@csrf
        <button class="btn btn-outline-danger btn-sm" type="submit">Logout</button>
      </form>
    </div>

    {{-- Menu --}}
    <div class="menu-title">MENU</div>
    <div class="d-flex flex-column gap-2">

      {{-- Keamanan / 2FA --}}
      <a href="{{ $link2fa }}" class="menu-item {{ $isSecurity ? 'active' : '' }}" title="Keamanan Akun (2FA)">
        <span class="mi-left"><i class="bi bi-shield-lock"></i></span>
        <span class="mi-text">Keamanan Akun <strong>(2FA)</strong></span>
        <i class="bi bi-chevron-right mi-chevron"></i>
      </a>

      {{-- Detail Order PSB --}}
      @if($canPSB)
      <a href="{{ route('detail-order-psb.index') }}" class="menu-item {{ $isPSB ? 'active' : '' }}" title="Detail Order PSB">
        <span class="mi-left"><i class="bi bi-file-earmark-text"></i></span>
        <span class="mi-text">Detail Order <strong>PSB</strong></span>
        <i class="bi bi-chevron-right mi-chevron"></i>
      </a>
      @endif

      {{-- Presensi --}}
      @if($isSuperAdmin || $isTeamLeader)
      <a href="{{ route('presensi.checkin') }}" class="menu-item {{ $isPresensi ? 'active' : '' }}" title="Presensi">
        <span class="mi-left"><i class="bi bi-clipboard-check"></i></span>
        <span class="mi-text">Presensi</span>
        <i class="bi bi-chevron-right mi-chevron"></i>
      </a>
      @endif

      {{-- Teknisi (submenu) --}}
      @if($isSuperAdmin || $isTeamLeader)
        <button
          type="button"
          class="menu-item js-tek-toggle {{ $tekExpanded ? 'active' : '' }}"
          title="Teknisi"
          aria-expanded="{{ $tekExpanded ? 'true' : 'false' }}"
          data-key="submenu-teknisi"
        >
          <span class="mi-left"><i class="bi bi-people"></i></span>
          <span class="mi-text">Teknisi</span>
          <i class="bi bi-chevron-{{ $tekExpanded ? 'up' : 'down' }} mi-chevron js-tek-caret"></i>
        </button>

        <div class="submenu js-tek-sub" style="display: {{ $tekExpanded ? 'block' : 'none' }};">
          <a href="{{ route('teknisi.index') }}" class="menu-subitem {{ $isTim ? 'active' : '' }}" title="Tim">
            <span class="mi-left"><i class="bi bi-diagram-3"></i></span>
            <span class="mi-text">Tim</span>
            <i class="bi bi-chevron-right mi-chevron"></i>
          </a>
          <a href="{{ route('registrasi-teknisi.index') }}" class="menu-subitem {{ $isRegTek ? 'active' : '' }}" title="Registrasi">
            <span class="mi-left"><i class="bi bi-person-plus"></i></span>
            <span class="mi-text">Registrasi</span>
            <i class="bi bi-chevron-right mi-chevron"></i>
          </a>
        </div>
      @endif

      {{-- Slot opsional dari halaman --}}
      @hasSection('sidebar_extra') @yield('sidebar_extra') @endif

    </div>
  </div>
</aside>

@push('scripts')
<script>
  // --- Sidebar mini toggle + remember (localStorage)
  (function(){
    const sb  = document.getElementById('sidebar');
    const btn = document.getElementById('sbToggle');
    if(!sb || !btn) return;
    const KEY='sidebar-mini';
    const setMini = on => {
      sb.classList.toggle('mini', !!on);
      localStorage.setItem(KEY, on ? '1':'0');
      btn.innerHTML = on ? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-left"></i>';
    };
    setMini(localStorage.getItem(KEY) === '1');
    btn.addEventListener('click', () => setMini(!sb.classList.contains('mini')));
  })();

  // --- Submenu Teknisi toggle + remember (sessionStorage)
  (function(){
    const toggle = document.querySelector('.js-tek-toggle');
    const sub    = document.querySelector('.js-tek-sub');
    const caret  = document.querySelector('.js-tek-caret');
    if(!toggle || !sub) return;

    const KEY = 'submenu-teknisi-open';
    const setOpen = (on) => {
      sub.style.display = on ? 'block' : 'none';
      toggle.setAttribute('aria-expanded', on ? 'true' : 'false');
      caret && (caret.className = 'bi ' + (on ? 'bi-chevron-up' : 'bi-chevron-down') + ' mi-chevron js-tek-caret');
      sessionStorage.setItem(KEY, on ? '1' : '0');
    };

    // init from server-side state or stored state
    const serverOpen = toggle.getAttribute('aria-expanded') === 'true';
    const stored = sessionStorage.getItem(KEY);
    const initial = stored === null ? serverOpen : stored === '1';
    setOpen(initial);

    toggle.addEventListener('click', () => setOpen(sub.style.display === 'none'));
  })();
</script>
@endpush
