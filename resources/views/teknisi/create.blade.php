@extends('layouts.app')
@section('title','Tambah Tim Teknisi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
  }

  body{
    background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed;
  }

  /* Header & card khusus halaman ini */
  .app-header{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 0;
    color:#111;
  }
  .app-header .logo{
    height:36px;
  }
  .app-header .sub{
    opacity:.75;
    font-size:.9rem;
  }

  .card-like{
    background:rgba(255,255,255,.96);
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.14);
  }

  /* Tombol back bulat */
  .btn-back-fixed{
    position:fixed;
    top:10px;
    left:14px;
    width:56px;
    height:56px;
    border-radius:50%;
    background:#fff;
    border:4px solid #03A9F4;
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:2000;
    text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-back-fixed .chev{
    color:#03A9F4;
    font-size:28px;
    font-weight:700;
  }
  .btn-back-fixed:hover{
    transform:translateY(-1px);
    box-shadow:0 8px 18px rgba(0,0,0,.22);
  }
</style>
@endpush

@section('content')

<a href="{{ route('teknisi.index') }}" class="btn-back-fixed" aria-label="Back">
  <span class="chev">&lsaquo;</span>
</a>

<div class="app-header mb-3">
  <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
  <div>
    <h5 class="mb-0">Tambah Tim Teknisi</h5>
    <small class="sub">Buat tim baru & atur NIK teknisi.</small>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<div class="card-like">
  <form method="POST" action="{{ route('teknisi.store') }}" class="row g-3">
    @csrf

    <div class="col-lg-4">
      <label class="form-label">STO</label>
      <select name="sto_code" id="sto_code" class="form-select" required>
        <option value="">— pilih STO —</option>
        @foreach($stoOpts as $s)
          <option value="{{ $s }}" @selected(old('sto_code') == $s)>{{ $s }}</option>
        @endforeach
      </select>
    </div>

    <div class="col-lg-4">
      <label class="form-label">Nama Tim</label>
      <input
        type="text"
        name="nama_tim"
        id="nama_tim"
        class="form-control"
        value="{{ old('nama_tim') }}"
        placeholder="— pilih STO dulu —"
        list="teamSuggestions"
        disabled
        required
      >
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

@endsection

@push('scripts')
<script>
  // Saran nama tim berdasar STO (BET01..BET30 dst)
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

    // Inisialisasi dari old() kalau validasi gagal
    const initSto = @json(old('sto_code'));
    if(initSto){
      fillSuggestions(initSto);
    }
  })();
</script>
@endpush
