@extends('layouts.app')
@section('title','Tambah Tim Teknisi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
    --sb-w: 300px;
    --sb-mini: 72px;
  }
  body{ background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed; }

  /* Layout & sidebar: konsisten dengan page lain */
  .layout{ display:flex; gap:16px; }
  .content{ flex:1 1 auto; min-width:0; }
  .sidebar{
    position:relative; width:var(--sb-w);
    background:var(--glass); border-radius:16px; padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.16), 0 0 0 1px rgba(2,8,23,.06) inset;
    transition: width .24s ease, box-shadow .24s ease, background .24s ease;
    backdrop-filter: blur(8px);
  }
  .sidebar.mini{ width:var(--sb-mini); }
  .sb-section{ transition: opacity .18s ease; }
  .sidebar.mini .sb-section{ opacity:0; pointer-events:none; position:absolute; inset:16px; }
  .sb-rail{ display:flex; flex-direction:column; gap:12px; align-items:center; }
  .sb-toggle{
    position:absolute; top:10px; right:10px; z-index:2;
    border-radius:999px; padding:6px 10px; line-height:1;
    background:#fff; border:1px solid #e5e7eb;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
  }
  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff; display:grid; place-items:center; font-weight:700; font-size:20px; }
  .logout-wrap{ display:flex; justify-content:center; }
  .logout-wrap .btn{ min-width:140px; }

  .menu-title{ font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; margin:12px 0 8px; }
  .menu-item{
    display:flex; align-items:center; gap:12px;
    padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background: rgba(255,255,255,.5) !important;
    border:1px solid rgba(0,0,0,.08);
    backdrop-filter: blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset;
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .menu-item::before{ content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px; background: var(--brand-red); opacity:.9; }
  .menu-item .mi-left{ width:36px; height:36px; display:grid; place-items:center; border-radius:10px; background: rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.08); }
  .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; transition: transform .15s ease, color .15s ease; }
  .menu-item:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(2,8,23,.16), 0 0 0 1px rgba(255,255,255,.45) inset; }

  /* Biru seperti item lain */
  .menu-item[data-menu="psb"]{ background: var(--psb-soft) !important; border:1px solid rgba(14,165,233,.35); }
  .menu-item[data-menu="psb"]::before{ background: var(--psb); }
  .menu-item[data-menu="psb"] .mi-left{ background: rgba(14,165,233,.10); border-color: rgba(14,165,233,.25); }
  .menu-item[data-menu="psb"] .mi-left i{ color: var(--psb); }
  .menu-item[data-menu="psb"] .mi-chevron{ color: var(--psb); opacity:.9; }

  .sidebar.mini .menu-item{ justify-content:center; padding:12px 10px; }
  .sidebar.mini .menu-item .mi-chevron,
  .sidebar.mini .menu-title,
  .sidebar.mini .user-name,
  .sidebar.mini .user-email,
  .sidebar.mini .logout-wrap{ display:none !important; }
  .sidebar.mini .menu-item .mi-text{ display:none; }
  .sidebar.mini .menu-item .mi-left{ width:40px; height:40px; }

  /* Header */
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{height:36px}
  .app-header .title{margin:0;font-weight:700;line-height:1.2}
  .app-header .sub{display:block;opacity:.75;font-size:.9rem}

  /* Card */
  .card-like{ background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px; box-shadow:0 10px 26px rgba(2,8,23,.14); }

  /* Back FAB (biru) */
  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center;
    z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-back-fixed:hover{ transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,.22); }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }
</style>
@endpush

@section('content')
{{-- Back ke daftar tim --}}
<a href="{{ route('teknisi.index') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>

@php
  $role = auth()->user()->role ?? '';
  $showTeknisi = in_array($role, ['Super Admin','HD TA','Team Leader']);
@endphp

<div class="container-fluid py-4">
  <div class="layout">

    <!-- {{-- ===== SIDEBAR ===== --}}
   @include('partials.sidebar') -->


    {{-- ===== KONTEN ===== --}}
    <main class="content">
      {{-- Header --}}
      <div class="app-header mb-3">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
        <div>
          <h5 class="title mb-0">Tambah Tim Teknisi</h5>
          <small class="sub">Buat tim baru & atur NIK teknisi.</small>
        </div>
      </div>

      {{-- Error --}}
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif

      {{-- Form --}}
      <div class="card-like">
        <form method="POST" action="{{ route('teknisi.store') }}" class="row g-3">
          @csrf

          <div class="col-lg-4">
            <label class="form-label">STO</label>
            <select name="sto_code" id="sto_code" class="form-select" required>
              <option value="">— pilih STO —</option>
              @foreach($stoOpts as $s)
                <option value="{{ $s }}" @selected(old('sto_code')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-lg-4">
            <label class="form-label">Nama Tim</label>
            {{-- pakai datalist agar ada saran (BET01..BET30) sesuai STO --}}
            <input type="text" name="nama_tim" id="nama_tim" class="form-control"
                   value="{{ old('nama_tim') }}" placeholder="— pilih STO dulu —" list="teamSuggestions" disabled required>
            <datalist id="teamSuggestions"></datalist>
          </div>

          <div class="col-lg-4">
            <label class="form-label">NIK Teknisi 1</label>
            <input type="text" name="nik_teknisi1" class="form-control" value="{{ old('nik_teknisi1') }}">
          </div>

          <div class="col-lg-4">
            <label class="form-label">NIK Teknisi 2</label>
            <input type="text" name="nik_teknisi2" class="form-control" value="{{ old('nik_teknisi2') }}">
          </div>

          <div class="col-12 d-flex gap-2 mt-2">
            <a href="{{ route('teknisi.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>

@push('scripts')
<script>
  // Toggle sidebar (konsisten)
  document.addEventListener('DOMContentLoaded', () => {
    const sb  = document.getElementById('sidebar');
    const btn = document.getElementById('sbToggle');
    if(!sb || !btn) return;
    const KEY = 'sidebar-mini';
    const refresh = () => btn.innerHTML = sb.classList.contains('mini')
      ? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-left"></i>';
    const setMini = (on) => { sb.classList.toggle('mini', on); localStorage.setItem(KEY, on?'1':'0'); refresh(); }
    setMini(localStorage.getItem(KEY) === '1');
    btn.addEventListener('click', () => setMini(!sb.classList.contains('mini')));
  });

  // Saran Nama Tim berdasar STO
  (function(){
    const stoSel  = document.getElementById('sto_code');
    const nameInp = document.getElementById('nama_tim');
    const list    = document.getElementById('teamSuggestions');

    function pad2(n){ return String(n).padStart(2,'0'); }
    function fillSuggestions(sto){
      list.innerHTML = '';
      if(!sto){
        nameInp.value = '';
        nameInp.placeholder = '— pilih STO dulu —';
        nameInp.disabled = true;
        return;
      }
      for(let i=1;i<=30;i++){
        const opt = document.createElement('option');
        opt.value = sto + pad2(i);
        list.appendChild(opt);
      }
      nameInp.disabled = false;
      if(!nameInp.value){
        nameInp.placeholder = 'Contoh: ' + sto + '01';
      }
    }

    stoSel?.addEventListener('change', e => fillSuggestions(e.target.value));

    // Inisialisasi dari old() (ketika validasi gagal)
    const initSto  = @json(old('sto_code'));
    if(initSto){ fillSuggestions(initSto); }
  })();
</script>
@endpush
@endsection
