@extends('layouts.app')
@section('title','Detail Order PSB')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  body{background:url('{{ asset('images/bg.jpg') }}') no-repeat center center fixed;background-size:cover}

  :root{
    --psb-surface: rgba(255,255,255,.96);
    --psb-outline:#e2e8f0;
    --psb-ring: rgba(2,8,23,.08);
    --psb-primary:#0ea5e9;
    --psb-primary-600:#0284c7;
    --psb-primary-700:#0369a1;
    --psb-head-bg:var(--psb-primary-700);
    --psb-head-text:#fff;
    --psb-row-alt:#f8fafc;
  }

  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid var(--psb-primary);
    display:flex; align-items:center; justify-content:center; z-index:2000; text-decoration:none;
    box-shadow:0 4px 14px rgba(0,0,0,.15); transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-back-fixed:hover{ transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,.22); }
  .btn-back-fixed .chev{ color:var(--psb-primary); font-size:28px; font-weight:700; line-height:1; transform:translateY(-1px); }

  .page-head .logo{height:40px}
  .btn-add{background:var(--psb-primary);border-color:var(--psb-primary);color:#fff}
  .btn-add:hover{background:var(--psb-primary-600);border-color:var(--psb-primary-600);color:#fff}

  .toolbar{
    background:linear-gradient(180deg, #38bdf8 0%, var(--psb-primary-600) 100%);
    color:#f8fafc;border-radius:12px;padding:14px;
    box-shadow:0 1px 0 0 var(--psb-ring), 0 8px 24px rgba(2,8,23,.18);
  }
  .toolbar .row>[class*="col-"]{padding-left:8px;padding-right:8px}
  .toolbar .form-control,.toolbar .form-select,.toolbar .btn{height:40px}
  .toolbar .form-control,.toolbar .form-select{border:1px solid var(--psb-outline)}
  .toolbar .btn-outline-light{--bs-btn-color:#ffffff;--bs-btn-border-color:#e2e8f0}
  .toolbar .btn-light{background:#fff;color:var(--psb-primary-700);border-color:#cbd5e1}
  .toolbar .btn-light:hover{background:#f8fafc;color:var(--psb-primary-600)}
  .placeholder-like::placeholder{color:#334155;opacity:.85}

  .sto-dd .btn{
    width:100%; text-align:left; background:#fff; border:1px solid var(--psb-outline);
    position:relative; padding-right:40px; color:#111827;
  }
  .sto-dd .btn:after{
    content:""; position:absolute; right:12px; top:50%; width:12px; height:12px; pointer-events:none;
    transform:translateY(-50%);
    background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23666' viewBox='0 0 16 16'%3e%3cpath d='M3.204 5h9.592L8 10.481 3.204 5z'/%3e%3c/svg%3e");
    background-size:12px 12px; background-repeat:no-repeat; background-position:center;
  }
  .dropdown-menu.sto-menu{
    max-height:300px;overflow:auto;width:100%;
    border:1px solid var(--psb-outline); box-shadow:0 10px 24px rgba(2,8,23,.14);
  }
  .sto-menu .form-check{padding:6px 2px;display:flex;align-items:center;gap:.5rem}
  .sto-menu .form-check-input{margin:0 0 0 .1rem}
  .sto-menu .form-check-label{color:#111827 !important;opacity:1 !important;user-select:none}
  .sto-menu a{text-decoration:none}

  .listbar{background:var(--psb-surface);border-radius:12px;padding:10px 14px;border:1px solid var(--psb-outline)}

  .scroll-x{overflow-x:auto}
  table.psb-table{min-width:3520px;table-layout:fixed;background:#fff;border-color:#e5e7eb}
  .psb-table th,.psb-table td{font-size:14px;padding:12px 14px;vertical-align:top;overflow-wrap:anywhere}
  .psb-table thead th{position:sticky;top:0;z-index:2;background:var(--psb-head-bg);color:var(--psb-head-text);text-align:left}
  .psb-table tbody tr:nth-child(even){background:var(--psb-row-alt)}
  .worklog-cell{white-space:pre-wrap}

  .psb-table th:nth-child(1),  .psb-table td:nth-child(1){width:160px}
  .psb-table th:nth-child(2),  .psb-table td:nth-child(2){width:140px}
  .psb-table th:nth-child(3),  .psb-table td:nth-child(3){width:220px}
  .psb-table th:nth-child(4),  .psb-table td:nth-child(4){width:150px}
  .psb-table th:nth-child(5),  .psb-table td:nth-child(5){width:260px}
  .psb-table th:nth-child(6),  .psb-table td:nth-child(6){width:140px}
  .psb-table th:nth-child(7),  .psb-table td:nth-child(7){width:280px}
  .psb-table th:nth-child(8),  .psb-table td:nth-child(8){width:180px}
  .psb-table th:nth-child(9),  .psb-table td:nth-child(9){width:170px}
  .psb-table th:nth-child(10), .psb-table td:nth-child(10){width:160px}
  .psb-table th:nth-child(11), .psb-table td:nth-child(11){width:180px}
  .psb-table th:nth-child(12), .psb-table td:nth-child(12){width:220px}
  .psb-table th:nth-child(13), .psb-table td:nth-child(13){width:360px}
  .psb-table th:nth-child(14), .psb-table td:nth-child(14){width:180px}
  .psb-table th:nth-child(15), .psb-table td:nth-child(15){width:220px}
  .psb-table th:nth-child(16), .psb-table td:nth-child(16){width:240px}
  .psb-table th:nth-child(17), .psb-table td:nth-child(17){width:200px}
  .psb-table th:nth-child(18), .psb-table td:nth-child(18){width:260px}
  .psb-table th:nth-child(19), .psb-table td:nth-child(19){width:140px}
  .psb-table th:nth-child(20), .psb-table td:nth-child(20){width:180px}
  .psb-table th:nth-child(21), .psb-table td:nth-child(21){width:160px}
  .psb-table th:nth-child(22), .psb-table td:nth-child(22){width:160px}
  .psb-table th:nth-child(23), .psb-table td:nth-child(23){width:220px}
  .psb-table th:nth-child(24), .psb-table td:nth-child(24){width:140px}
  .psb-table th:nth-child(25), .psb-table td:nth-child(25){width:150px}
  .psb-table th:nth-child(26), .psb-table td:nth-child(26){width:140px}
  .psb-table th:nth-child(27), .psb-table td:nth-child(27){width:160px}
  .psb-table th:nth-child(28), .psb-table td:nth-child(28){width:130px}

  .btn-xxs{padding:6px 12px;font-size:12px;border-radius:8px;min-width:100px}
  .btn-edit{background:#0d6efd;color:#fff}
  .btn-del{background:#fff;color:#c1121f;border:1px solid #c1121f}
  .btn-qe {background:#10b981;color:#fff}
</style>
@endpush

@push('styles')
<style>
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
    --sb-w: 300px;
    --sb-mini: 72px;
  }
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
  .avatar{
    width:56px; height:56px; border-radius:50%;
    background:#6c757d; color:#fff; display:grid; place-items:center;
    font-weight:700; font-size:20px;
  }
  .logout-wrap{ display:flex; justify-content:center; }
  .logout-wrap form{ width:auto; }
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
  .menu-item::before{
    content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px;
    background: var(--brand-red); opacity:.9;
  }
  .menu-item .mi-left{
    width:36px; height:36px; display:grid; place-items:center; border-radius:10px;
    background: rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.08);
  }
  .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; transition: transform .15s ease, color .15s ease; }
  .menu-item:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(2,8,23,.16), 0 0 0 1px rgba(255,255,255,.45) inset; }
  .menu-item:hover .mi-chevron{ transform: translateX(3px); color:#333; }
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

  @media (max-width: 991.98px){ .sidebar{ width:var(--sb-mini); } .sidebar .sb-section{ opacity:0; pointer-events:none; } }
</style>
@endpush

@section('content')

@php
  $role = auth()->user()->role ?? '';
  $canManage   = in_array($role, ['Super Admin','HD TA']);     // create/edit/update/import/export
  $canDelete   = ($role === 'Super Admin');                     // destroy only super admin
  $canQuickEdit= in_array($role, ['Super Admin','HD TA','HD Mitra','Team Leader']);
@endphp

<a href="{{ url('/home') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>

<div class="container-fluid py-4">
  <div class="layout">
    
    <!-- @include('partials.sidebar') -->

    <main class="content">

  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 page-head">
    <div class="d-flex align-items-center gap-2">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
      <div>
        <h5 class="mb-0">Detail Order PSB</h5>
        <small>Kelola data order di sini.</small>
      </div>
    </div>
    <div class="page-actions mt-2 mt-md-0">
      @if($canManage)
        <a href="{{ route('detail-order-psb.export.csv', request()->query()) }}" class="btn btn-outline-secondary me-2">
          <i class="bi bi-download me-1"></i> Download File
        </a>
        <a href="{{ route('detail-order-psb.create') }}" class="btn btn-add me-2">
          <i class="bi bi-plus-circle me-1"></i> Add Data
        </a>
        <a href="{{ route('detail-order-psb.import.form') }}" class="btn btn-outline-secondary">
          Import Data
        </a>
      @endif
    </div>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif
  @if(session('import_errors'))
    <div class="alert alert-warning">
      <div class="fw-semibold mb-1">Catatan Import:</div>
      <ul class="mb-0">@foreach(session('import_errors') as $err)<li>{{ $err }}</li>@endforeach</ul>
    </div>
  @endif

  @php
    $orderStatusReq = request('order_status');
    // fix: definisikan $isKendala agar tidak undefined
    $isKendala = is_string($orderStatusReq) && str_starts_with($orderStatusReq, 'KENDALA ');
    $initialCat = $isKendala ? 'KENDALA' : ($orderStatusReq ?? '');
    $initialSub = $isKendala ? $orderStatusReq : '';
    $stoFilter  = (array)request()->input('sto', []);
  @endphp

  <form id="filterForm" method="GET" action="{{ route('detail-order-psb.index') }}" class="toolbar mb-2">
    <input type="hidden" name="order_status" id="order_status_final" value="{{ $orderStatusReq }}">

    <div class="row g-2 align-items-end">
      <div class="col-xl-3 col-md-6">
        <input type="text" name="sc_order_no" id="sc_order_no" autocomplete="off"
               value="{{ request('sc_order_no') }}" class="form-control placeholder-like"
               placeholder="SC Order No/Track ID/CSRM No">
      </div>

      <div class="col-xl-3 col-md-6">
        <select name="sub_district" id="sub_district" class="form-select">
          <option value="">Sub District — Semua —</option>
          @foreach(($subDistrictOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('sub_district')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-xl-3 col-md-6">
        <select name="service_area" id="service_area" class="form-select" {{ !empty($disableSA) ? 'disabled' : '' }}>
          <option value="">Service Area — Semua —</option>
          @foreach(($serviceAreaOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('service_area')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-xl-3 col-md-6">
        <select name="branch" id="branch" class="form-select" {{ !empty($disableBW) ? 'disabled' : '' }}>
          <option value="">Branch — Semua —</option>
          @foreach(($branchOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('branch')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-xl-3 col-md-6">
        <select name="wok" id="wok" class="form-select" {{ !empty($disableBW) ? 'disabled' : '' }}>
          <option value="">WOK — Semua —</option>
          @foreach(($wokOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('wok')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-xl-3 col-md-6">
        <div class="sto-dd dropdown">
          <button class="btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <span id="stoFilterLabel">STO (Filter) — Semua STO</span>
          </button>
          <div class="dropdown-menu sto-menu px-3 py-2">
            <div class="d-flex justify-content-between align-items-center mb-2 small">
              <a href="#" id="stoFilterAll">All</a>
              <a href="#" id="stoFilterClear">Clear</a>
            </div>
            @php $allSto = $stoOpts ?? []; @endphp
            @foreach($allSto as $code)
              <div class="form-check">
                <input class="form-check-input sto-filter-check" type="checkbox"
                       id="sto-filter-{{ $code }}" name="sto[]" value="{{ $code }}"
                       {{ in_array($code, $stoFilter, true) ? 'checked' : '' }}>
                <label class="form-check-label" for="sto-filter-{{ $code }}">{{ $code }}</label>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6">
        <select name="produk" class="form-select">
          <option value="">Produk — Semua —</option>
          @foreach(($produkOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('produk')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-xl-3 col-md-6">
        <select name="transaksi" class="form-select">
          <option value="">Transaksi — Semua —</option>
          @foreach(($transaksiOpts ?? []) as $v)
            <option value="{{ $v }}" @selected(request('transaksi')===$v)>{{ $v }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-12 d-flex justify-content-end gap-2 mt-2" id="topControls">
        <a href="{{ route('detail-order-psb.index') }}" class="btn btn-outline-light">Reset</a>
        <button class="btn btn-light fw-semibold" type="submit">Filter</button>
        <button class="btn btn-outline-light" id="btnShowAll" type="button">Show All Filters</button>
      </div>
    </div>

      {{-- Filter tambahan (hidden) --}}
    <div id="moreFilters" class="mt-2 d-none" aria-hidden="true">
      <div class="row g-2 align-items-end">
        <div class="col-lg-3"><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Date Created (from)"></div>
        <div class="col-lg-3"><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Date Created (to)"></div>
        <div class="col-lg-3"><input type="text" name="workorder" class="form-control" value="{{ request('workorder') }}" placeholder="Workorder"></div>
        <div class="col-lg-3"><input type="text" name="service_no" class="form-control" value="{{ request('service_no') }}" placeholder="Service No."></div>

        <div class="col-lg-3"><input type="text" name="description" class="form-control" value="{{ request('description') }}" placeholder="Description"></div>
        <div class="col-lg-3"><input type="text" name="status_bima" class="form-control" value="{{ request('status_bima') }}" placeholder="Status bima"></div>
        <div class="col-lg-6"><input type="text" name="address" class="form-control" value="{{ request('address') }}" placeholder="Address"></div>

        <div class="col-lg-3"><input type="text" name="customer_name" class="form-control" value="{{ request('customer_name') }}" placeholder="Customer Name"></div>
        <div class="col-lg-3"><input type="text" name="contact_number" class="form-control" value="{{ request('contact_number') }}" placeholder="Contact Number"></div>
        <div class="col-lg-3"><input type="text" name="team_name" class="form-control" value="{{ request('team_name') }}" placeholder="Team Name"></div>

        {{-- Order Status (7 opsi) + Sub Kendala conditional --}}
        <div class="col-lg-3">
          <select id="order_status_main" class="form-select">
            <option value="">Order Status — Semua —</option>
            @foreach(['OPEN','SURVEI','REVOKE SC','PROGRES','KENDALA','AC','CLOSE'] as $opt)
              <option value="{{ $opt }}" @selected(($isKendala?'KENDALA':request('order_status'))===$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-lg-6 {{ ($isKendala?'KENDALA':request('order_status'))==='KENDALA' ? '' : 'd-none' }}" id="sub_kendala_wrap">
          <select id="sub_kendala" class="form-select">
            <option value="">Sub Kendala — Semua —</option>
            @foreach(($subKendalaOpts ?? []) as $opt)
              <option value="{{ $opt }}" @selected($isKendala && request('order_status')===$opt)>{{ $opt }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-lg-3"><input type="text" name="work_log" class="form-control" value="{{ request('work_log') }}" placeholder="Work Log (contains)"></div>
        <div class="col-lg-3"><input type="text" name="koordinat_survei" class="form-control" value="{{ request('koordinat_survei') }}" placeholder="Koordinat Survei"></div>

        <div class="col-lg-3">
          <select name="validasi_eviden_kendala" class="form-select">
            <option value="">Validasi Eviden Kendala — Semua —</option>
            @foreach(($validasiOptions ?? []) as $v)
              <option value="{{ $v }}" @selected(request('validasi_eviden_kendala')===$v)>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-lg-3"><input type="text" name="nama_validator_kendala" class="form-control" value="{{ request('nama_validator_kendala') }}" placeholder="Nama Validator Kendala"></div>

        <div class="col-lg-3">
          <select name="validasi_failwa_invalid" class="form-select">
            <option value="">Validasi Failwa / Invalid Survey — Semua —</option>
            @foreach(($validasiOptions ?? []) as $v)
              <option value="{{ $v }}" @selected(request('validasi_failwa_invalid')===$v)>{{ $v }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-lg-3"><input type="text" name="nama_validator_failwa" class="form-control" value="{{ request('nama_validator_failwa') }}" placeholder="Nama Validator Failwa"></div>

        <div class="col-lg-6"><input type="text" name="keterangan_non_valid" class="form-control" value="{{ request('keterangan_non_valid') }}" placeholder="Keterangan Non Valid"></div>
        <div class="col-lg-3"><input type="text" name="id_valins" class="form-control" value="{{ request('id_valins') }}" placeholder="ID Valins"></div>

        <div class="col-12 d-flex justify-content-end gap-2">
          <button class="btn btn-outline-light" id="btnHideAll" type="button">Hide Filters</button>
          <a href="{{ route('detail-order-psb.index') }}" class="btn btn-outline-light">Reset</a>
          <button class="btn btn-light fw-semibold" type="submit">Filter</button>
        </div>
      </div>
    </div>
  </form>

  <div id="tableWrap">
    <div class="listbar d-flex flex-wrap align-items-center justify-content-between mb-2">
      <form method="GET" action="{{ route('detail-order-psb.index') }}" class="d-flex align-items-center gap-2 listbar-form">
        @foreach(request()->except(['per_page','page']) as $k => $v)
          @if(is_array($v))
            @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
          @else
            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
          @endif
        @endforeach
        <span class="me-1">Show</span>
        <select name="per_page" class="form-select perpage" style="width:120px">
          @foreach($allowedPerPage as $opt)
            <option value="{{ $opt }}" {{ (int)($perPage ?? 10) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
          @endforeach
        </select>
        <span>rows</span>
      </form>
      <div class="ms-auto">
        {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="scroll-x">
        <table class="table table-bordered psb-table mb-2">
          <thead>
            <tr>
              @foreach($columns as $col)<th>{{ $col }}</th>@endforeach
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $r)
              <tr id="row-{{ $r->id }}"
                  data-id="{{ $r->id }}"
                  data-sto="{{ $r->sto }}"
                  data-team="{{ $r->team_name }}"
                  data-status="{{ $r->order_status }}"
                  data-subkendala="{{ $r->sub_kendala }}"
                  data-desc="{{ $r->description }}">
                <td>{{ optional($r->date_created)->format('Y-m-d H:i') }}</td>
                <td>{{ $r->workorder }}</td>
                <td>{{ $r->sc_order_no }}</td>
                <td>{{ $r->service_no }}</td>
                <td class="td-description">{{ $r->description }}</td>
                <td>{{ $r->status_bima }}</td>
                <td>{{ $r->address }}</td>
                <td>{{ $r->customer_name }}</td>
                <td>{{ $r->contact_number }}</td>
                <td class="td-team">{{ $r->team_name }}</td>
                <td class="td-status">{{ $r->order_status }}</td>
                <td class="td-subkendala">{{ $r->sub_kendala }}</td>
                <td class="worklog-cell td-worklog">{{ $r->work_log }}</td>
                <td>{{ $r->koordinat_survei }}</td>
                <td>{{ $r->validasi_eviden_kendala }}</td>
                <td>{{ $r->nama_validator_kendala }}</td>
                <td>{{ $r->validasi_failwa_invalid }}</td>
                <td>{{ $r->nama_validator_failwa }}</td>
                <td>{{ $r->keterangan_non_valid }}</td>
                <td>{{ $r->sub_district }}</td>
                <td>{{ $r->service_area }}</td>
                <td>{{ $r->branch }}</td>
                <td>{{ $r->wok }}</td>
                <td>{{ $r->sto }}</td>
                <td>{{ $r->produk }}</td>
                <td>{{ $r->transaksi }}</td>
                <td>{{ $r->id_valins }}</td>
                <td>
                  <div class="d-flex flex-column gap-1">
                    @if($canQuickEdit)
                      <button type="button" class="btn btn-xxs btn-qe w-100" onclick="window.openQuickFromRow(this)">Quick Edit</button>
                    @endif

                    @if($canManage)
                      <a href="{{ route('detail-order-psb.edit',$r->id) }}" class="btn btn-xxs btn-edit w-100">Edit</a>
                    @endif

                    @if($canDelete)
                      <form action="{{ route('detail-order-psb.destroy',$r->id) }}" method="POST" onsubmit="return confirm('Hapus baris ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-xxs btn-del w-100">Delete</button>
                      </form>
                    @endif
                  </div>
                </td>
              </tr>
            @empty
              <tr><td colspan="{{ count($columns)+1 }}" class="text-center text-muted">Belum ada data</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="d-flex justify-content-end">
        {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

    </main>
  </div>
</div>

{{-- ===== QUICK EDIT MODAL ===== --}}
<div class="modal fade" id="qeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <form id="qe-form" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Quick Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="qe-id">
        <div class="mb-3">
          <label class="form-label">STO</label>
          <input type="text" id="qe-sto" class="form-control" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Tim</label>
          <select id="qe-team" class="form-select">
            <option value="">— pilih tim —</option>
          </select>
          <div class="form-text">Wajib pilih tim sebelum ubah status.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Order Status</label>
          <select id="qe-status" class="form-select" disabled>
            <option value="">— pilih status —</option>
            <option value="OPEN">OPEN</option>
            <option value="SURVEI">SURVEI</option>
            <option value="REVOKE SC">REVOKE SC</option>
            <option value="PROGRES">PROGRES</option>
            <option value="KENDALA PELANGGAN">KENDALA PELANGGAN</option>
            <option value="KENDALA TEKNIK">KENDALA TEKNIK</option>
            <option value="KENDALA SISTEM">KENDALA SISTEM</option>
            <option value="KENDALA LAINNYA">KENDALA LAINNYA</option>
            <option value="AC">AC</option>
            <option value="CLOSE">CLOSE</option>
          </select>
        </div>

        <div class="mb-3 d-none" id="qe-sub-wrap">
          <label class="form-label">Sub Kendala</label>
          <select id="qe-subkendala" class="form-select">
            <option value="">— pilih sub kendala —</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea id="qe-desc" class="form-control" rows="3" placeholder="Tulis catatan jika perlu"></textarea>
        </div>

        <div id="qe-error" class="alert alert-danger d-none"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- ======= Scripts ======= --}}
<script>
(function(){
  const TERR = {!! json_encode($territories ?? []) !!};
  const SUBS = {!! json_encode($subKendalaOpts ?? []) !!};
  const KEN_CATS = ['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'];

  const $ = (sel,ctx=document)=>ctx.querySelector(sel);
  const $$= (sel,ctx=document)=>Array.from(ctx.querySelectorAll(sel));

  /* ===== STO filter label (toolbar) ===== */
  (function(){
    const stoDD = document.querySelector('.sto-dd');
    if(!stoDD) return;
    const label = stoDD.querySelector('#stoFilterLabel');
    const checks= stoDD.querySelectorAll('.sto-filter-check');
    const allBtn= stoDD.querySelector('#stoFilterAll');
    const clrBtn= stoDD.querySelector('#stoFilterClear');

    function updateLabel(){
      const all = Array.from(checks);
      const selected = all.filter(c=>c.checked).map(c=>c.value);
      if(selected.length===0) label.textContent = 'STO (Filter) — Semua STO';
      else if(selected.length===all.length) label.textContent = 'STO (Filter) — All STO';
      else if(selected.length<=3) label.textContent = 'STO (Filter) — ' + selected.join(', ');
      else label.textContent = 'STO (Filter) — ' + selected.length + ' selected';
    }
    function triggerAjax(){ fetchAndSwap(currentUrlWithQuery()); }

    updateLabel();
    stoDD.addEventListener('change', e=>{
      if(e.target.classList.contains('sto-filter-check')) { updateLabel(); triggerAjax(); }
    });
    allBtn?.addEventListener('click', e=>{ e.preventDefault(); checks.forEach(c=>c.checked=true); updateLabel(); triggerAjax(); });
    clrBtn?.addEventListener('click', e=>{ e.preventDefault(); checks.forEach(c=>c.checked=false); updateLabel(); triggerAjax(); });
  })();

  /* ===== Territory chained selects (toolbar) ===== */
  const selSub = $('#sub_district'), selSA=$('#service_area'), selBr=$('#branch'), selWok=$('#wok');
  const uniq = arr => Array.from(new Set(arr)).filter(Boolean);
  function fillOptions(select, items, firstLabel){
    if(!select) return;
    const cur = select.value;
    select.innerHTML = '';
    select.appendChild(new Option(firstLabel,''));
    items.forEach(v=>select.appendChild(new Option(v,v)));
    if(items.includes(cur)) select.value = cur;
  }
  function deriveBranches(sd){ return uniq((sd?TERR.filter(x=>x.sub_district===sd):TERR).map(x=>x.branch)); }
  function deriveWoks(sd, br){ let rows=TERR; if(sd) rows=rows.filter(x=>x.sub_district===sd); if(br) rows=rows.filter(x=>x.branch===br); return uniq(rows.map(x=>x.wok)); }
  function deriveServiceAreas(sd, br, wk){ let rows=TERR; if(sd) rows=rows.filter(x=>x.sub_district===sd); if(br) rows=rows.filter(x=>x.branch===br); if(wk) rows=rows.filter(x=>x.wok===wk); return uniq(rows.map(x=>x.service_area)); }
  function deriveStosBy(sa, sd, br, wk){ let rows=TERR; if(sd) rows=rows.filter(x=>x.sub_district===sd); if(sa) rows=rows.filter(x=>x.service_area===sa); else { if(br) rows=rows.filter(x=>x.branch===br); if(wk) rows=rows.filter(x=>x.wok===wk);} return uniq(rows.map(x=>x.sto)); }
  function applyDisableLogic(){ if (selBr) selBr.disabled = !!selSA.value; if (selWok) selWok.disabled = !!selSA.value; if (selSA) selSA.disabled = !!(selBr.value||selWok.value); }
  function setStoChecks(allowed){
    const dd = document.querySelector('.sto-dd'); if(!dd) return;
    $$('.sto-filter-check', dd).forEach(ch=>{
      const ok = allowed.length ? allowed.includes(ch.value) : true;
      ch.closest('.form-check').style.display = ok ? '' : 'none';
      if(!ok) ch.checked = false;
    });
    dd.dispatchEvent(new Event('change'));
  }
  function onTerritoryChange(){
    const sd = selSub?.value || '';
    fillOptions(selBr, deriveBranches(sd), 'Branch — Semua —');
    fillOptions(selWok, deriveWoks(sd, selBr.value), 'WOK — Semua —');
    fillOptions(selSA, deriveServiceAreas(sd, selBr.value, selWok.value), 'Service Area — Semua —');
    setStoChecks(deriveStosBy(selSA.value, sd, selBr.value, selWok.value));
    applyDisableLogic();
  }
  selSub?.addEventListener('change', ()=>{ onTerritoryChange(); triggerAjax(); });
  selBr ?.addEventListener('change', ()=>{ onTerritoryChange(); triggerAjax(); });
  selWok?.addEventListener('change', ()=>{ onTerritoryChange(); triggerAjax(); });
  selSA ?.addEventListener('change', ()=>{ onTerritoryChange(); triggerAjax(); });
  onTerritoryChange();

  /* ===== AJAX table swap for filters/pagination ===== */
  const form = $('#filterForm'); const tableWrap = $('#tableWrap'); let ctrl;
  function serializeForm(frm){ const fd=new FormData(frm); return new URLSearchParams(fd).toString(); }
  async function fetchAndSwap(url){
    if(ctrl) ctrl.abort(); ctrl=new AbortController(); tableWrap.style.opacity=.6;
    const res = await fetch(url, {signal:ctrl.signal, headers:{'X-Requested-With':'fetch'}});
    const html=await res.text(); const doc=new DOMParser().parseFromString(html,'text/html');
    const newWrap = doc.querySelector('#tableWrap');
    if(newWrap){ tableWrap.innerHTML = newWrap.innerHTML; bindDynamicInsideWrap(); }
    tableWrap.style.opacity=1;
  }
  function currentUrlWithQuery(){ const qs=serializeForm(form); const url=location.pathname+'?'+qs; history.replaceState({},'',url); return url; }
  function triggerAjax(){ fetchAndSwap(currentUrlWithQuery()); }
  $('#sc_order_no')?.addEventListener('input', triggerAjax);
  form?.addEventListener('submit', (e)=>{ e.preventDefault(); triggerAjax(); });

  function bindDynamicInsideWrap(){
    tableWrap.querySelectorAll('select.perpage').forEach(sel=>{
      sel.addEventListener('change', ()=>{
        const qs=new URLSearchParams(serializeForm(form)); qs.set('per_page', sel.value); qs.delete('page');
        fetchAndSwap(location.pathname + '?' + qs.toString());
      });
    });
    tableWrap.querySelectorAll('.pagination a.page-link').forEach(a=>{
      a.addEventListener('click', (e)=>{ e.preventDefault(); const href=a.getAttribute('href'); if(href) fetchAndSwap(href); });
    });
  }
  bindDynamicInsideWrap();

  /* ===== Order Status (7) + Sub Kendala → hidden order_status ===== */
  const osMain = $('#order_status_main');
  const osFinal= $('#order_status_final');
  const subWrap= $('#sub_kendala_wrap');
  const subSel = $('#sub_kendala');

  function syncOrderStatus(){
    if(!osMain || !osFinal) return;
    const isKen = osMain.value === 'KENDALA';
    subWrap?.classList.toggle('d-none', !isKen);
    osFinal.value = isKen ? (subSel?.value || '') : (osMain.value || '');
  }
  osMain?.addEventListener('change', ()=>{ syncOrderStatus(); triggerAjax(); });
  subSel ?.addEventListener('change', ()=>{ syncOrderStatus(); triggerAjax(); });

  // init: pastikan hidden terisi benar saat load
  syncOrderStatus();

  /* ===== Quick Edit modal logic (tetap) ===== */
  const SUBS_RAW = SUBS.slice();
  const KEN = KEN_CATS;

  const csrf = @json(csrf_token());
  const updateUrlBase = @json(route('detail-order-psb.quick-update', ['order' => '__ID__']));

  function refs(){
    window.qe = {
      modalEl: document.getElementById('qeModal'),
      form: document.getElementById('qe-form'),
      id: document.getElementById('qe-id'),
      sto: document.getElementById('qe-sto'),
      team: document.getElementById('qe-team'),
      status: document.getElementById('qe-status'),
      subWrap: document.getElementById('qe-sub-wrap'),
      sub: document.getElementById('qe-subkendala'),
      desc: document.getElementById('qe-desc'),
      err: document.getElementById('qe-error')
    };
  }
  refs();

  function fillTeams(stoCsv, current=''){
    const sel = qe.team; sel.innerHTML = '<option value="">— pilih tim —</option>'; sel.disabled = false;
    const stos = (stoCsv||'').split(',').map(s=>s.trim()).filter(Boolean);
    const names = [];
    stos.forEach(code=>{ for(let i=1;i<=10;i++){ names.push(`${code}${String(i).padStart(2,'0')}`); }});
    const seen = new Set();
    names.forEach(n=>{ if(!seen.has(n)){ sel.add(new Option(n,n)); seen.add(n);} });
    if(current) sel.value = current;
  }

  function showSub(cat, selected=''){
    qe.sub.innerHTML = '<option value="">— pilih sub kendala —</option>';
    SUBS_RAW.filter(v=>v.startsWith(cat+'|')).forEach(full=>{
      const label = full.split('|').slice(1).join(' | ');
      qe.sub.add(new Option(label, full, false, full===selected));
    });
    qe.subWrap.classList.remove('d-none');
  }
  function hideSub(){
    qe.subWrap.classList.add('d-none');
    qe.sub.innerHTML = '<option value="">— pilih sub kendala —</option>';
  }

  window.openQuickFromRow = (btn)=>{
    refs();
    const row = btn.closest('tr');
    const id   = row.dataset.id;
    const sto  = row.dataset.sto || '';
    const team = row.dataset.team || '';
    const stat = row.dataset.status || '';
    const desc = row.dataset.desc || '';

    qe.err.classList.add('d-none'); qe.err.textContent = '';
    qe.id.value = id; qe.sto.value = sto; qe.desc.value = desc;

    fillTeams(sto, team);
    qe.status.disabled = !team;

    if (stat.startsWith('KENDALA ')) {
      const cat = KEN.find(c => stat.startsWith(c)) || '';
      qe.status.value = cat || '';
      showSub(qe.status.value, stat);
    } else {
      qe.status.value = stat || '';
      hideSub();
    }

    qe.team.onchange = ()=>{ qe.status.disabled = !qe.team.value; };
    qe.status.onchange = ()=>{
      const v = qe.status.value || '';
      if (KEN.includes(v)) showSub(v, '');
      else hideSub();
    };

    qe.form.onsubmit = async (e)=>{
      e.preventDefault();
      qe.err.classList.add('d-none'); qe.err.textContent = '';

      const id = qe.id.value;
      const url = updateUrlBase.replace('__ID__', id);
      const team = qe.team.value || '';
      const st   = qe.status.value || '';
      const sub  = KEN.includes(st) ? (qe.sub.value || '') : '';
      const desc = qe.desc.value || '';

      if (KEN.includes(st) && !sub){
        qe.err.textContent = 'Sub Kendala wajib dipilih.';
        qe.err.classList.remove('d-none');
        return;
      }

      const body = new URLSearchParams();
      body.append('_method','PATCH');
      if (team) body.append('team_name', team);
      if (st)   body.append('order_status', st);
      if (sub)  body.append('sub_kendala', sub);
      body.append('description', desc);

      try{
        const res = await fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body});
        const data = await res.json().catch(()=>({}));
        if(!res.ok || !data.ok){
          qe.err.textContent = data.message || 'Gagal menyimpan.';
          qe.err.classList.remove('d-none');
          return;
        }

        const tr = document.getElementById('row-'+id);
        if(tr){
          tr.querySelector('.td-team')?.replaceChildren(document.createTextNode(data.row.team_name || ''));
          tr.querySelector('.td-status')?.replaceChildren(document.createTextNode(data.row.order_status || ''));
          tr.querySelector('.td-subkendala')?.replaceChildren(document.createTextNode(data.row.sub_kendala || ''));
          tr.querySelector('.td-description')?.replaceChildren(document.createTextNode(data.row.description || ''));
          if (data.row.work_log) tr.querySelector('.td-worklog')?.replaceChildren(document.createTextNode(data.row.work_log));

          tr.dataset.team = data.row.team_name || '';
          tr.dataset.status = data.row.order_status || '';
          tr.dataset.subkendala = data.row.sub_kendala || '';
          tr.dataset.desc = data.row.description || '';
        }

        bootstrap.Modal.getOrCreateInstance(qe.modalEl).hide();
      }catch(err){
        qe.err.textContent = 'Gagal menyimpan (network).';
        qe.err.classList.remove('d-none');
      }
    };

    bootstrap.Modal.getOrCreateInstance(qe.modalEl).show();
  };

  /* ===== Show/Hide extra filter ===== */
  const more   = document.getElementById('moreFilters');
  const topCtl = document.getElementById('topControls');
  document.getElementById('btnShowAll')?.addEventListener('click', ()=>{
    more.classList.remove('d-none'); more.setAttribute('aria-hidden','false'); topCtl.classList.add('d-none');
  });
  document.getElementById('btnHideAll')?.addEventListener('click', ()=>{
    more.classList.add('d-none'); more.setAttribute('aria-hidden','true'); topCtl.classList.remove('d-none');
  });

})();
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sb   = document.getElementById('sidebar');
    const btn  = document.getElementById('sbToggle');
    if(!sb || !btn) return;
    const KEY  = 'sidebar-mini';
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
