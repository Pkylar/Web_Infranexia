@extends('layouts.app')
@section('title','Tambah Teknisi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --glass:rgba(255,255,255,.84);
    --psb:#0ea5e9;          /* biru brand */
    --psb-soft:#e6f6fe;
  }

  body{
    background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed;
  }

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

  .card-like{
    background:rgba(255,255,255,.96);
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.14);
  }

  /* Paksa strip sidebar selalu biru */
  .sidebar .menu-item::before{
    background:#0ea5e9 !important;
  }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
  <div class="app-header mb-2">
    <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
    <div>
      <h5 class="title mb-0">Tambah Teknisi</h5>
      <small class="text-muted">Form registrasi master teknisi.</small>
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
      {{-- Foto teknisi kalau mau diaktifkan lagi
      <div class="col-md-4">
        <label class="form-label">Foto (jpg/png/webp, max 2MB)</label>
        <input type="file" name="foto" accept=".jpg,.jpeg,.png,.webp" class="form-control">
      </div>
      --}}
    </div>

    <div class="mt-3 d-flex gap-2">
      <a href="{{ route('registrasi-teknisi.index') }}" class="btn btn-outline-secondary">Batal</a>
      <button class="btn btn-primary">Simpan</button>
    </div>
  </form>
</div>
@endsection
