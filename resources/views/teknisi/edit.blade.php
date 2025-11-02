@extends('layouts.app')
@section('title','Edit Tim Teknisi')

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
  .sb-toggle{ position:absolute; top:10px; right:10px; z-index:2; }
  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff; display:grid; place-items:center; font-weight:700; font-size:20px; }
  .menu-item{ display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative; background: rgba(255,255,255,.5) !important; border:1px solid rgba(0,0,0,.08); }
  .menu-item::before{ content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px; background: var(--psb); opacity:.9; }
  .menu-item[data-menu="psb"]{ background: var(--psb-soft) !important; border:1px solid rgba(14,165,233,.35); }

  .card-like{ background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px; box-shadow:0 10px 26px rgba(2,8,23,.14); }

  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center;
    z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
  }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }
</style>
@endpush

@section('content')
<a href="{{ route('teknisi.index') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>

<div class="container-fluid py-4">
  <div class="layout">

    <!-- {{-- Sidebar ringkas biar konsisten --}}
    @include('partials.sidebar') -->


    <main class="content">
      <div class="d-flex align-items-center gap-2 mb-3">
        <img src="{{ asset('images/logo.png') }}" style="height:36px" alt="logo">
        <div>
          <h5 class="mb-0">Edit Tim Teknisi</h5>
          <small class="text-muted">Perbarui data tim & anggota.</small>
        </div>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
      @endif
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form method="POST" action="{{ route('teknisi.update', $row->id) }}" class="card-like">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">STO</label>
            <select name="sto_code" class="form-select" required>
              <option value="">— pilih STO —</option>
              @foreach($stoOpts as $s)
                <option value="{{ $s }}" @selected(old('sto_code',$row->sto_code)===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-5">
            <label class="form-label">Nama Tim</label>
            <input type="text" name="nama_tim" class="form-control"
                   value="{{ old('nama_tim',$row->nama_tim) }}" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">NIK Teknisi 1</label>
            <input type="text" name="nik_teknisi1" class="form-control"
                   value="{{ old('nik_teknisi1',$row->nik_teknisi1) }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">NIK Teknisi 2</label>
            <input type="text" name="nik_teknisi2" class="form-control"
                   value="{{ old('nik_teknisi2',$row->nik_teknisi2) }}">
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <a href="{{ route('teknisi.index') }}" class="btn btn-outline-secondary">Batal</a>
          <button class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </main>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sb  = document.getElementById('sidebar');
    const btn = document.getElementById('sbToggle');
    if(!sb || !btn) return;
    const KEY = 'sidebar-mini';
    function refreshIcon(){
      btn.innerHTML = sb.classList.contains('mini')
        ? '<i class="bi bi-chevron-right"></i>'
        : '<i class="bi bi-chevron-left"></i>';
    }
    function setMini(on){
      sb.classList.toggle('mini', on);
      localStorage.setItem(KEY, on ? '1' : '0');
      refreshIcon();
    }
    setMini(localStorage.getItem(KEY) === '1');
    btn.addEventListener('click', () => setMini(!sb.classList.contains('mini')));
  });
</script>
@endsection
