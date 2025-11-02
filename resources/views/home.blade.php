@extends('layouts.app')

@push('styles')
<style>
  /* contoh style khusus halaman (tanpa styling sidebar) */
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{ height:36px; }
  .app-header .title{ margin:0; font-weight:700; line-height:1.2; }
  .app-header .sub{ display:block; opacity:.75; font-size:.85rem; }

  .metric{ border-radius:14px; padding:16px; color:#fff; }
  .metric .big{ font-size:28px; font-weight:700; line-height:1; }
  .metric .label{ opacity:.9; font-weight:500; }
  .metric .icon{ font-size:28px; opacity:.95; }
  .metric.blue{background:#0d6efd;} .metric.green{background:#20c997;}
  .metric.indigo{background:#6610f2;} .metric.cyan{background:#0dcaf0; color:#103;}
  .metric.orange{background:#fd7e14;} .metric.red{background:#dc3545;}
  .metric.gray{background:#6c757d;} .metric.yellow{background:#ffc107; color:#432;}

  .table-activity td{ padding:8px 10px; vertical-align:middle; }
  .badge-status{ font-size:.8rem; }
</style>
@endpush

@section('content')
@php
  $role = auth()->user()->role ?? '';
  $canUploadRekap = in_array($role, ['Super Admin','Team Leader']);
@endphp

  <div class="app-header mb-3">
    <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
    <div>
      <h5 class="title mb-0">Dashboard PSB</h5>
      <small class="sub">Ringkasan order, status, dan aktivitas terbaru.</small>
    </div>

    <div class="ms-auto d-flex align-items-center gap-2">
      <a href="{{ route('rekap-foto.index') }}" class="btn btn-warning btn-sm"><i class="bi bi-camera me-1"></i> Rekap Foto</a>
      @if($canUploadRekap)
        <a href="{{ route('rekap-foto.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-upload me-1"></i> Upload</a>
      @endif
    </div>
  </div>

  {{-- Metrik --}}
  <div class="row g-3">
    <div class="col-md-3 col-sm-6"><div class="metric blue d-flex justify-content-between align-items-center"><div><div class="big">{{ $totalUsers }}</div><div class="label">User</div></div><i class="bi bi-people-fill icon"></i></div></div>
    <div class="col-md-3 col-sm-6"><div class="metric indigo d-flex justify-content-between align-items-center"><div><div class="big">{{ $teamCount }}</div><div class="label">Team</div></div><i class="bi bi-diagram-3 icon"></i></div></div>
    <div class="col-md-3 col-sm-6"><div class="metric green d-flex justify-content-between align-items-center"><div><div class="big">{{ $customerCount }}</div><div class="label">Pelanggan</div></div><i class="bi bi-person-badge icon"></i></div></div>
    <div class="col-md-3 col-sm-6"><div class="metric red d-flex justify-content-between align-items-center"><div><div class="big">{{ $totalOrders }}</div><div class="label">Order PSB</div></div><i class="bi bi-card-checklist icon"></i></div></div>
  </div>

  {{-- Ringkasan status --}}
  <div class="row g-3 mt-1">
    <div class="col-md-2 col-6"><div class="metric yellow text-dark"><div class="big">{{ $openCount }}</div><div class="label">OPEN</div></div></div>
    <div class="col-md-2 col-6"><div class="metric cyan"><div class="big">{{ $surveiCount }}</div><div class="label">SURVEI</div></div></div>
    <div class="col-md-2 col-6"><div class="metric blue"><div class="big">{{ $progresCount }}</div><div class="label">PROGRES</div></div></div>
    <div class="col-md-2 col-6"><div class="metric green"><div class="big">{{ $acCount }}</div><div class="label">AC</div></div></div>
    <div class="col-md-2 col-6"><div class="metric gray"><div class="big">{{ $closeCount }}</div><div class="label">CLOSE</div></div></div>
    <div class="col-md-2 col-6"><div class="metric orange"><div class="big">{{ $kendalaPelanggan + $kendalaTeknik + $kendalaSistem + $kendalaLainnya }}</div><div class="label">Total Kendala</div></div></div>
  </div>

  {{-- Aktivitas Terbaru --}}
  <div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="mb-0">Aktivitas Terbaru</h6>
      <a href="{{ route('detail-order-psb.index') }}" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Detail
      </a>
    </div>
    <div class="table-responsive">
      <table class="table table-borderless table-activity mb-0">
        <tbody>
          @forelse($recentOrders as $r)
            <tr>
              <td class="text-muted" style="width:180px;">{{ optional($r->date_created)->format('d-m-Y H:i') }}</td>
              <td class="text-muted" style="width:120px;"><span class="badge bg-light text-dark">WO: {{ $r->workorder }}</span></td>
              <td>{{ $r->customer_name }}</td>
              <td class="text-end" style="width:160px;"><span class="badge bg-secondary">{{ $r->order_status }}</span></td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-muted">Belum ada aktivitas.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
@endsection
