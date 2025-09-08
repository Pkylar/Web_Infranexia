@php
  $user = auth()->user();
  $initial = strtoupper(substr($user->name ?? 'U', 0, 1));
  $isPresensi = request()->routeIs('presensi.*');
  $isPSB      = request()->routeIs('detail-order-psb.*');
@endphp

<aside class="sb-panel">
  <div class="sb-avatar">{{ $initial }}</div>
  <div class="sb-name">{{ $user->name ?? 'User' }}</div>
  <div class="sb-email">{{ $user->email ?? '' }}</div>

  <div class="sb-logout">
    <form action="{{ route('logout') }}" method="POST">@csrf
      <button class="btn btn-outline-danger btn-sm">Logout</button>
    </form>
  </div>

  <div class="sb-title">MENU</div>

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
