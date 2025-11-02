@extends('layouts.app')
@section('title','Import Data - Detail Order PSB')

@push('styles')
<style>
  body { background: url('{{ asset('images/bg.jpg') }}') no-repeat center center fixed; background-size: cover; }
  .card-custom { background: rgba(255,255,255,.94); border-radius: 12px; }
  .hint{ font-size: 13px; color:#6b7280; }
  .errors-box{ max-height: 260px; overflow:auto; background:#fff7f7; border:1px solid #fca5a5; border-radius:8px; padding:10px; }
  .btn-wide{ min-width: 160px; }

  .btn-back-fixed{
    position: fixed; top: 12px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background: #fff; border: 4px solid #0ea5e9;
    display:flex; align-items:center; justify-content:center;
    text-decoration: none; z-index: 2000;
    box-shadow: 0 6px 18px rgba(0,0,0,.18);
    transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  }
  .btn-back-fixed:hover{ transform: translateY(-1px); box-shadow: 0 10px 22px rgba(0,0,0,.25); background:#f5fbff; }
  .btn-back-fixed .chev{ color:#0ea5e9; font-size:28px; font-weight:700; line-height:1; transform: translateY(-1px); }

  .confirm-overlay{ position: fixed; inset:0; background: rgba(0,0,0,.35);
    display:flex; align-items:center; justify-content:center; z-index: 1050; }
  .confirm-card{ width: 680px; max-width: 92vw; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,.2); }
  .confirm-card .head{ padding:16px 18px; border-bottom:1px solid #eee; font-weight:600; }
  .confirm-card .body{ padding:16px 18px; }
  .confirm-card .foot{ padding:14px 18px; text-align:right; border-top:1px solid #eee; }
</style>
@endpush

{{-- ADDED: Sidebar styles (copy dari Home) --}}
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

  .menu-item[data-menu="psb"]{
    background: var(--psb-soft) !important;
    border:1px solid rgba(14,165,233,.35);
  }
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
{{-- /ADDED --}}

@section('content')
<div class="container-fluid py-4">
  <div class="layout">
    <!-- {{-- ===== SIDEBAR ===== --}}
    @include('partials.sidebar') -->


    {{-- ===== CONTENT ===== --}}
    <main class="content">
      <a href="{{ route('detail-order-psb.index') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>

      <div class="container">
        @if(session('success')) <div class="alert alert-success mt-4">{{ session('success') }}</div> @endif
        @if(session('info'))    <div class="alert alert-info mt-4">{{ session('info') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger mt-4">{{ session('error') }}</div>   @endif

        <div class="card card-custom shadow-sm mt-3">
          <div class="card-body">
            <h5 class="mb-2">Import Data (CSV)</h5>
            <p class="hint mb-3">
              Unggah file <strong>.csv</strong> atau <strong>Excel (.xlsx/.xls/.ods)</strong> dengan <em>header</em> kolom.
              <strong>Urutan kolom boleh bebas</strong> (header fleksibel).
              <br>Yang <strong>wajib ada & terisi</strong> pada setiap baris adalah <code>workorder</code>.
              Kolom lain <em>opsional</em> (termasuk <code>order_status</code> — bila kosong akan dianggap <code>OPEN</code> saat disimpan).
              <br><code>date_created</code> juga opsional; jika kosong akan otomatis diisi waktu proses import.
            </p>

            <div class="mb-3">
              <a href="{{ route('detail-order-psb.import.template') }}" class="btn btn-outline-secondary btn-wide">Download Template CSV</a>
            </div>

            {{-- FORM upload --}}
            <form method="POST" action="{{ route('detail-order-psb.import.store') }}" enctype="multipart/form-data" class="mt-2">
              @csrf
              <div class="row g-3 align-items-end">
                <div class="col-md-6">
                  <label class="form-label">File CSV/Excel</label>
                  <input type="file" name="file"
                    class="form-control @error('file') is-invalid @enderror"
                    accept=".csv,.xlsx,.xls,.ods,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                  @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                  {{-- DROPDOWN di bawah input file --}}
                  <div class="mt-3">
                    <label class="form-label">Set Produk untuk semua baris (opsional)</label>
                    <select name="override_produk" class="form-select">
                      <option value="">— Jangan set (biarkan dari CSV) —</option>
                      @foreach(($produkOpts ?? []) as $p)
                        <option value="{{ $p }}" @selected(old('override_produk')===$p)>{{ $p }}</option>
                      @endforeach
                    </select>
                    <div class="form-text">Jika dipilih, kolom <b>produk</b> pada semua baris akan di-override.</div>
                  </div>
                </div>

                <div class="col-md-6 d-flex">
                  <button type="submit" class="btn btn-primary btn-wide ms-auto">Import Sekarang</button>
                </div>
              </div>
            </form>

            @if(session('import_errors'))
              <hr>
              <h6 class="mb-2 text-danger">Detail Error ({{ count(session('import_errors')) }})</h6>
              <div class="errors-box">
                <ul class="mb-0">
                  @foreach(session('import_errors') as $e)
                    <li>{{ $e }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <hr class="my-4">
            <details>
              <summary class="fw-semibold">Kolom yang dikenali</summary>
              <div class="mt-2">
                <code>{{ implode(', ', $importable ?? []) }}</code>
              </div>
            </details>
          </div>
        </div>
      </div>

      {{-- Overlay konfirmasi (hanya muncul jika $confirm == true) --}}
      @if(!empty($confirm))
        <div class="confirm-overlay">
          <div class="confirm-card">
            <div class="head">Konfirmasi Import</div>
            <div class="body">
              <p class="mb-1">Total baris pada file: <strong>{{ $confirmStats['total'] ?? 0 }}</strong></p>
              <p class="text-danger">Terdeteksi duplikat: <strong>{{ $confirmStats['dup'] ?? 0 }}</strong> baris.</p>
              <p class="mb-0">Pilih <strong>Replace</strong> untuk menimpa data lama, atau <strong>Cancel</strong> untuk membatalkan import.</p>
              <small class="text-muted d-block mt-2">Jika memilih <em>Set Produk</em>, pilihan itu akan diterapkan saat proses replace juga.</small>
            </div>
            <div class="foot">
              <form method="POST" action="{{ route('detail-order-psb.import.store') }}" class="d-inline">
                @csrf
                <input type="hidden" name="confirm_action" value="replace">
                <button class="btn btn-danger">Replace</button>
              </form>
              <form method="POST" action="{{ route('detail-order-psb.import.store') }}" class="d-inline ms-2">
                @csrf
                <input type="hidden" name="confirm_action" value="cancel">
                <button class="btn btn-secondary">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      @endif
    </main>
  </div>
</div>

{{-- ADDED: sidebar toggle + remember state --}}
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
{{-- /ADDED --}}
@endsection
