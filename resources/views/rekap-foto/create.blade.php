@extends('layouts.app')
@section('title','Upload Foto Rekapan')

@push('styles')
<style>
  body{ background:url('{{ asset('images/bg.jpg') }}') center/cover fixed no-repeat; }
  .layout{ display:flex; gap:16px; } .content{ flex:1 1 auto; min-width:0; }
  .card-like{
    background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.14);
  }

   /* Back FAB */
  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center; z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
  }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }
</style>
@endpush

@section('content')
<a href="{{ url('/home') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>
@php
  $uName = auth()->user()->name ?? '';
@endphp

<div class="container-fluid py-4">
  <div class="layout">
    @include('partials.sidebar')

    <main class="content">
      <div class="d-flex align-items-center gap-2 mb-3">
        <img src="{{ asset('images/logo.png') }}" class="me-2" style="height:36px" alt="logo">
        <h5 class="mb-0">Upload Foto Rekapan</h5>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
        </div>
      @endif

      <div class="card-like">
        <form method="POST" action="{{ route('rekap-foto.store') }}" enctype="multipart/form-data" class="row g-3">
          @csrf

          <div class="col-md-6">
            <label class="form-label">Foto <span class="text-danger">*</span></label>
            <input type="file" name="photo" class="form-control" accept="image/*" required>
            <div class="form-text">Maks 15 MB. Format: JPG, PNG, WEBP, HEIC/HEIF.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">STO</label>
            <select name="sto" class="form-select">
              <option value="">— pilih STO —</option>
              @foreach($stoOpts as $s)
                <option value="{{ $s }}" @selected(old('sto')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">NIK Teknisi (opsional)</label>
            <input type="text" name="teknisi_nik" value="{{ old('teknisi_nik') }}" class="form-control" placeholder="opsional">
          </div>

          {{-- Nama teknisi otomatis dari user yang login --}}
          <div class="col-md-4">
            <label class="form-label">Nama Pengunggah</label>
            <input type="text" class="form-control" value="{{ $uName }}" disabled>
            <input type="hidden" name="teknisi_nama" value="{{ $uName }}">
          </div>

          <div class="col-md-4">
            <label class="form-label">Keterangan</label>
            <input type="text" name="note" value="{{ old('note') }}" class="form-control" placeholder="opsional">
          </div>

          <div class="col-12 d-flex justify-content-end gap-2">
            <a href="{{ route('rekap-foto.index') }}" class="btn btn-outline-secondary">Batal</a>
            <button class="btn btn-primary">
              <i class="bi bi-cloud-arrow-up me-1"></i> Upload
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>
@endsection
