@extends('layouts.app')
@section('title','Tambah Teknisi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{ --brand-red:#b41111; --glass:rgba(255,255,255,.84); --psb:#0ea5e9; --psb-soft:#e6f6fe; --sb-w:300px; --sb-mini:72px; }
  body{ background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed; }
  .layout{ display:flex; gap:16px; } .content{ flex:1 1 auto; min-width:0; }
  .sidebar{ position:relative; width:var(--sb-w); background:var(--glass); border-radius:16px; padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.16), 0 0 0 1px rgba(2,8,23,.06) inset; transition:width .24s; backdrop-filter:blur(8px); }
  .sidebar.mini{ width:var(--sb-mini); } .sb-section{ transition:opacity .18s; }
  .sidebar.mini .sb-section{ opacity:0; pointer-events:none; position:absolute; inset:16px; }
  .sb-toggle{ position:absolute; top:10px; right:10px; z-index:2; border-radius:999px; padding:6px 10px; }
  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff; display:grid; place-items:center; font-weight:700; font-size:20px; }
  .logout-wrap{ display:flex; justify-content:center; } .logout-wrap .btn{ min-width:140px; }
  .menu-title{ font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; margin:12px 0 8px; }
  .menu-item{ display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background:rgba(255,255,255,.5)!important; border:1px solid rgba(0,0,0,.08); backdrop-filter:blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset; }
  .menu-item::before{ content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px; background:var(--psb); opacity:.9; }
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{height:36px} .card-like{ background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px; box-shadow:0 10px 26px rgba(2,8,23,.14); }
</style>
@endpush

@section('content')
@php
  $role = auth()->user()->role ?? '';
@endphp

<div class="container-fluid py-4">
  <div class="layout">
    {{-- SIDEBAR --}}
    <aside id="sidebar" class="sidebar">
      <button id="sbToggle" class="sb-toggle btn btn-light btn-sm"><i class="bi bi-chevron-left"></i></button>
      <div class="sb-section w-100">
        <div class="d-flex flex-column align-items-center text-center mb-3">
          <div class="avatar mb-2">{{ strtoupper(auth()->user()->name[0] ?? 'U') }}</div>
          <div class="user-name fw-semibold">{{ auth()->user()->name ?? 'User' }}</div>
          <div class="user-email text-muted small">{{ auth()->user()->email ?? '' }}</div>
          <div class="badge bg-light text-dark mt-2">{{ $role ?: '—' }}</div>
        </div>
        <div class="logout-wrap mb-3">
          <form action="{{ route('logout') }}" method="POST">@csrf
            <button class="btn btn-outline-danger btn-sm">Logout</button>
          </form>
        </div>

        <div class="menu-title">MENU</div>
        <div class="d-flex flex-column gap-2">
          <a href="{{ route('detail-order-psb.index') }}" class="menu-item" data-menu="psb">
            <span class="mi-left"><i class="bi bi-file-earmark-text"></i></span>
            <span class="mi-text">Detail Order <strong>PSB</strong></span>
            <i class="bi bi-chevron-right ms-auto"></i>
          </a>
          <a href="{{ route('registrasi-teknisi.index') }}" class="menu-item" data-menu="psb">
            <span class="mi-left"><i class="bi bi-person-plus"></i></span>
            <span class="mi-text">Registrasi Teknisi</span>
            <i class="bi bi-chevron-right ms-auto"></i>
          </a>
        </div>
      </div>
    </aside>

    {{-- CONTENT --}}
    <main class="content">
      <div class="app-header mb-2">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
        <div>
          <h5 class="title mb-0">Tambah Teknisi</h5>
          <small class="text-muted">Form registrasi master teknisi.</small>
        </div>
      </div>

      @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif

      <form method="POST" action="{{ route('registrasi-teknisi.store') }}" class="card-like" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">NIK <span class="text-danger">*</span></label>
            <input type="text" name="nik" value="{{ old('nik') }}" class="form-control" required>
          </div>
          <div class="col-md-5">
            <label class="form-label">Nama <span class="text-danger">*</span></label>
            <input type="text" name="nama" value="{{ old('nama') }}" class="form-control" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Base STO</label>
            <select name="base_sto" class="form-select">
              <option value="">— pilih STO —</option>
             @foreach(($stoOpts ?? $stoOptions ?? []) as $s)
                <option value="{{ $s }}" @selected(old('base_sto')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Mitra</label>
            <input type="text" name="mitra" value="{{ old('mitra') }}" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
              <option value="AKTIF"    @selected(old('status','AKTIF')==='AKTIF')>AKTIF</option>
              <option value="NONAKTIF" @selected(old('status')==='NONAKTIF')>NONAKTIF</option>
            </select>
          </div>
          <!-- <div class="col-md-4">
            <label class="form-label">Foto (jpg/png/webp, max 2MB)</label>
            <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="form-control">
          </div> -->
        </div>

        <div class="mt-3 d-flex gap-2">
          <a href="{{ route('registrasi-teknisi.index') }}" class="btn btn-outline-secondary">Batal</a>
          <button class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </main>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sb=document.getElementById('sidebar'), btn=document.getElementById('sbToggle'); if(!sb||!btn) return;
    const KEY='sidebar-mini';
    const setMini=on=>{ sb.classList.toggle('mini',on); localStorage.setItem(KEY,on?'1':'0');
      btn.innerHTML = on? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-left"></i>'; };
    setMini(localStorage.getItem(KEY)==='1'); btn.addEventListener('click',()=>setMini(!sb.classList.contains('mini')));
  });
</script>
@endpush
@endsection
