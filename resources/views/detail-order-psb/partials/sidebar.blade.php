@php
  $user       = auth()->user();
  $initial    = strtoupper(substr($user->name ?? 'U', 0, 1));
  $isPresensi = request()->routeIs('presensi.*');
  $isPSB      = request()->routeIs('detail-order-psb.*');
  $isSecurity = request()->routeIs('2fa.settings');   // <-- menu baru
  $has2FA     = !empty($user?->two_factor_secret);    // indikator status 2FA
@endphp

<aside class="sb-panel">
  <div class="sb-avatar">{{ $initial }}</div>
  <div class="sb-name">{{ $user->name ?? 'User' }}</div>
  <div class="sb-email">{{ $user->email ?? '' }}</div>

  {{-- indikator kecil (optional) --}}
  <div class="small text-muted mb-2">
    2FA: <strong class="{{ $has2FA ? 'text-success' : 'text-danger' }}">
      {{ $has2FA ? 'Aktif' : 'Nonaktif' }}
    </strong>
  </div>

  <div class="sb-logout">
    <form action="{{ route('logout') }}" method="POST">@csrf
      <button class="btn btn-outline-danger btn-sm">Logout</button>
    </form>
  </div>

  <div class="sb-title">MENU</div>

  {{-- === Menu baru: Keamanan Akun / Profile (2FA) === --}}
  <a href="{{ route('2fa.settings') }}" class="mi {{ $isSecurity ? 'active' : '' }}">
    <span class="ico"><i class="bi bi-shield-lock"></i></span>
    <span>Keamanan Akun <strong>(2FA)</strong></span>
    <span class="chev">›</span>
  </a>

  <a href="{{ route('presensi.checkin') }}" class="mi {{ $isPresensi ? 'active' : '' }}">
    <span class="ico"><i class="bi bi-clipboard-check"></i></span>
    <span>Presensi</span>
    <span class="chev">›</span>
  </a>

  <a href="{{ route('detail-order-psb.index') }}" class="mi {{ $isPSB ? 'active' : '' }}">
    <span class="ico"><i class="bi bi-file-earmark-text"></i></span>
    <span>Detail Order <strong>PSB</strong></span>
    <span class="chev">›</span>
  </a>
</aside>
