@extends('layouts.app')
@section('title', 'Add Data - Detail Order PSB')

@push('styles')
<style>
  body{background:url('{{ asset('images/bg.jpg') }}') no-repeat center center fixed;background-size:cover;}
  .card-custom{background:rgba(255,255,255,.92);border-radius:12px;}

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
  .btn-back-fixed .chev{ color:#0ea5e9; font-size:28px; font-weight:700; line-height:1; transform:translateY(-1px); }

  .dropdown-menu.sto-menu { max-height:260px; overflow:auto; width:100%; }

  .overlay{position:fixed;inset:0;background:rgba(0,0,0,.35);display:none;align-items:center;justify-content:center;z-index:1050}
  .overlay.show{display:flex}
  .ov-card{width:680px;max-width:92vw;background:#fff;border-radius:12px;box-shadow:0 12px 30px rgba(0,0,0,.2)}
  .ov-head{padding:14px 18px;border-bottom:1px solid #eee;font-weight:700}
  .ov-body{padding:16px 18px}
  .ov-foot{padding:14px 18px;border-top:1px solid #eee;text-align:right}
  .mono{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:13px;background:#f8f9fa;border:1px solid #eee;border-radius:8px;padding:10px}

  .form-label{font-weight:600; font-size:.9rem;}
  .is-disabled{background:#f7f7f7; pointer-events:none; opacity:.8;}
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
    {{-- ===== SIDEBAR ===== --}}
    @include('partials.sidebar')

    {{-- ===== CONTENT ===== --}}
    <main class="content">
      <a href="{{ route('detail-order-psb.index') }}" class="btn-back-fixed" title="Back" aria-label="Back">‹</a>

      <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Add Data - PSB</h5>
        </div>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <div class="card card-custom shadow-sm">
          <div class="card-body">
            <form id="add-form" method="POST" action="{{ route('detail-order-psb.store') }}">
              @csrf
              <input type="hidden" name="replace_id" id="replace_id" value="">

              <div class="alert alert-info py-2">
                <strong>Catatan:</strong> <em>Date Created</em> & <em>Work Log</em> akan terisi otomatis saat klik <b>Simpan Data</b>. <br>
                <b>Status Order</b> tidak perlu diisi—sistem otomatis menyetelnya menjadi <b>OPEN</b>.
              </div>

              <div class="row g-3">
                {{-- Identitas order --}}
                <div class="col-md-3">
                  <label class="form-label">Workorder <span class="text-danger">*</span></label>
                  <input type="text" name="workorder" class="form-control" value="{{ old('workorder') }}" placeholder="Workorder" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">SC Order No / Track ID / CSRM No</label>
                  <input type="text" name="sc_order_no" class="form-control" value="{{ old('sc_order_no') }}" placeholder="SC Order No / Track ID / CSRM No">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Service No.</label>
                  <input type="text" name="service_no" class="form-control" value="{{ old('service_no') }}" placeholder="Service No.">
                </div>

                {{-- Kategori wilayah & klasifikasi --}}
                <div class="col-md-3">
                  <label class="form-label">Sub District</label>
                  <select id="sub_district" name="sub_district" class="form-select">
                    <option value="">Sub District — Semua</option>
                    @foreach(($subDistrictOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('sub_district')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Service Area</label>
                  <select id="service_area" name="service_area" class="form-select">
                    <option value="">Service Area — Semua</option>
                    @foreach(($serviceAreaOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('service_area')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Branch</label>
                  <select id="branch" name="branch" class="form-select">
                    <option value="">Branch — Semua</option>
                    @foreach(($branchOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('branch')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">WOK</label>
                  <select id="wok" name="wok" class="form-select">
                    <option value="">WOK — Semua</option>
                    @foreach(($wokOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('wok')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- STO multi --}}
                <div class="col-md-6">
                  <label class="form-label">STO (boleh pilih lebih dari 1)</label>
                  <div id="stoDD" class="dropdown">
                    <button class="btn btn-light w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                      <span id="stoDropdownLabel">STO — Pilih</span><span class="ms-2">▾</span>
                    </button>
                    <div class="dropdown-menu sto-menu px-3 py-2" id="stoMenu">
                      <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <a href="#" id="stoSelectAll" class="text-decoration-none">Select all</a>
                        <a href="#" id="stoClear" class="text-decoration-none">Clear</a>
                      </div>
                      @foreach(($stoOpts ?? []) as $code)
                        <div class="form-check sto-item">
                          <input class="form-check-input sto-check" type="checkbox"
                                id="sto-{{ $code }}" name="sto[]"
                                value="{{ $code }}"
                                {{ in_array($code, (array)old('sto', []), true) ? 'checked':'' }}>
                          <label class="form-check-label" for="sto-{{ $code }}">{{ $code }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                  <div class="form-text">STO bisa lebih dari satu.</div>
                </div>

                {{-- Produk & Transaksi --}}
                <div class="col-md-3">
                  <label class="form-label">Produk</label>
                  <select name="produk" class="form-select">
                    <option value="">Produk — Semua</option>
                    @foreach(($produkOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('produk')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Transaksi</label>
                  <select name="transaksi" class="form-select">
                    <option value="">Transaksi — Semua</option>
                    @foreach(($transaksiOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('transaksi')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Status bima & alamat --}}
                <div class="col-md-3">
                  <label class="form-label">Status bima</label>
                  <input type="text" name="status_bima" class="form-control" value="{{ old('status_bima') }}" placeholder="Status bima">
                </div>
                <div class="col-md-9">
                  <label class="form-label">Address</label>
                  <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Address">
                </div>

                {{-- Customer / kontak / tim --}}
                <div class="col-md-4">
                  <label class="form-label">Customer Name</label>
                  <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name') }}" placeholder="Customer Name">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Contact Number</label>
                  <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number') }}" placeholder="Contact Number">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Team Name</label>
                  <select id="team_name" name="team_name" class="form-select" disabled>
                    <option value="">— pilih tim —</option>
                  </select>
                  <div class="form-text">Pilih STO dulu agar daftar tim muncul.</div>
                </div>

                {{-- Koordinat, Validasi, Validator, Keterangan, ID Valins --}}
                <div class="col-md-4">
                  <label class="form-label">Koordinat Survei</label>
                  <input type="text" name="koordinat_survei" class="form-control" value="{{ old('koordinat_survei') }}" placeholder="Koordinat Survei">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Validasi Eviden Kendala</label>
                  <select name="validasi_eviden_kendala" class="form-select">
                    <option value="">Validasi Eviden Kendala — Semua</option>
                    @foreach($validasiOptions as $v)
                      <option value="{{ $v }}" @selected(old('validasi_eviden_kendala')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nama Validator Kendala</label>
                  <input type="text" name="nama_validator_kendala" class="form-control" value="{{ old('nama_validator_kendala') }}" placeholder="Nama Validator Kendala">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Validasi Failwa / Invalid</label>
                  <select name="validasi_failwa_invalid" class="form-select">
                    <option value="">Validasi Failwa / Invalid — Semua</option>
                    @foreach($validasiOptions as $v)
                      <option value="{{ $v }}" @selected(old('validasi_failwa_invalid')===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nama Validator Failwa</label>
                  <input type="text" name="nama_validator_failwa" class="form-control" value="{{ old('nama_validator_failwa') }}" placeholder="Nama Validator Failwa">
                </div>
                <div class="col-md-4">
                  <label class="form-label">ID Valins</label>
                  <input type="text" name="id_valins" class="form-control" value="{{ old('id_valins') }}" placeholder="ID Valins">
                </div>

                {{-- Deskripsi & Keterangan Non Valid --}}
                <div class="col-md-12">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3" placeholder="Description">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Keterangan Non Valid</label>
                  <textarea name="keterangan_non_valid" class="form-control" rows="3" placeholder="Keterangan Non Valid">{{ old('keterangan_non_valid') }}</textarea>
                </div>
              </div>

              <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Simpan Data</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Overlay konfirmasi duplikat --}}
      <div id="dup-overlay" class="overlay" aria-hidden="true">
        <div class="ov-card" role="dialog" aria-modal="true" aria-labelledby="dup-title">
          <div class="ov-head" id="dup-title">Konfirmasi Duplikat</div>
          <div class="ov-body">
            <p class="mb-2">Ditemukan data dengan kombinasi <b>Workorder / SC Order No / Service No</b> yang sudah ada.</p>
            <pre class="mono mb-2" id="dup-summary">—</pre>
            <p class="mb-0">Pilih <b>Replace</b> untuk menimpa data lama, atau <b>Cancel</b> untuk kembali mengubah isian.</p>
          </div>
          <div class="ov-foot">
            <button type="button" class="btn btn-secondary me-2" id="btn-dup-cancel">Cancel</button>
            <button type="button" class="btn btn-danger" id="btn-dup-replace">Replace</button>
          </div>
        </div>
      </div>
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

<script>
(function(){
  /* ===== JSON mapping dari controller ===== */
  const TERR = @json($territories ?? []);
  window.TERR = TERR; // expose untuk debug devtools jika perlu

  /* ===== util ===== */
  const uniq = (arr) => Array.from(new Set(arr.filter(v => v !== null && String(v).trim() !== ''))).sort();
  const by   = (k, v) => (o) => (v === '' ? true : String(o[k] ?? '') === String(v));

  function setDisabled(sel, yes) {
    sel.classList.toggle('is-disabled', !!yes);
    sel.disabled = !!yes;
  }

  function setOptions(sel, items, placeholder) {
    const cur = sel.value;
    sel.innerHTML = '';
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = placeholder;
    sel.appendChild(opt0);
    items.forEach(v => {
      const o = document.createElement('option');
      o.value = v; o.textContent = v;
      sel.appendChild(o);
    });
    if (items.includes(cur)) sel.value = cur;
  }

  function currentState() {
    const sd = document.getElementById('sub_district').value || '';
    const sa = document.getElementById('service_area').value || '';
    const br = document.getElementById('branch').value || '';
    const wk = document.getElementById('wok').value || '';
    return {sd, sa, br, wk};
  }

  function calcMode({sa, br, wk}) {
    if (sa) return 'by_sa';
    if (br || wk) return 'by_bw';
    return 'neutral';
  }

  /* ===== apply filter ke 4 dropdown + STO ===== */
  function applyFilters() {
    if (!Array.isArray(TERR) || TERR.length === 0) return; // guard

    const sdSel = document.getElementById('sub_district');
    const saSel = document.getElementById('service_area');
    const brSel = document.getElementById('branch');
    const wkSel = document.getElementById('wok');

    const {sd, sa, br, wk} = currentState();

    // base: persempit oleh Sub-District bila ada
    let base = TERR.slice();
    if (sd) base = base.filter(by('sub_district', sd));

    const mode = calcMode({sa, br, wk});

    // kandidat
    let saCand  = base.slice();
    let brCand  = base.slice();
    let wkCand  = base.slice();
    let stoCand = base.slice();

    if (mode === 'by_sa') {
      brCand  = brCand.filter(by('service_area', sa));
      wkCand  = wkCand.filter(by('service_area', sa));
      stoCand = stoCand.filter(by('service_area', sa));
    } else if (mode === 'by_bw') {
      if (br) { wkCand  = wkCand.filter(by('branch', br)); stoCand = stoCand.filter(by('branch', br)); }
      if (wk) { brCand  = brCand.filter(by('wok',   wk));  stoCand = stoCand.filter(by('wok',   wk)); }
    }

    const saList  = uniq(saCand.map(r => r.service_area));
    const brList  = uniq(brCand.map(r => r.branch));
    const wkList  = uniq(wkCand.map(r => r.wok));
    const stoList = uniq(stoCand.map(r => r.sto));

    setOptions(saSel, saList, 'Service Area — Semua');
    setOptions(brSel, brList, 'Branch — Semua');
    setOptions(wkSel, wkList, 'WOK — Semua');

    // lock rules
    if (mode === 'by_sa') {
      setDisabled(brSel, true); brSel.value = '';
      setDisabled(wkSel, true); wkSel.value = '';
      setDisabled(saSel, false);
    } else if (mode === 'by_bw') {
      setDisabled(saSel, true); saSel.value = '';
      setDisabled(brSel, false);
      setDisabled(wkSel, false);
    } else {
      setDisabled(saSel, false);
      setDisabled(brSel, false);
      setDisabled(wkSel, false);
    }

    // filter STO checkbox
    const allowed = new Set(stoList);
    document.querySelectorAll('.sto-item').forEach(wrap => {
      const chk = wrap.querySelector('.sto-check');
      const ok  = (allowed.size === 0) || allowed.has(chk.value);
      wrap.style.display = ok ? '' : 'none';
      if (!ok) chk.checked = false;
    });

    updateStoLabel();
  }

  /* ===== STO UI helpers ===== */
  function updateStoLabel(){
    const dd = document.getElementById('stoDD');
    if(!dd) return;
    const label = dd.querySelector('#stoDropdownLabel');
    const checks= dd.querySelectorAll('.sto-check');
    const all = Array.from(checks).filter(c => c.closest('.sto-item').style.display !== 'none');
    const selected = all.filter(c=>c.checked).map(c=>c.value);
    if(selected.length===0) label.textContent='STO — Pilih';
    else if(selected.length===all.length && all.length>0) label.textContent='STO — All';
    else if(selected.length<=4) label.textContent='STO — '+selected.join(', ');
    else label.textContent='STO — '+selected.length+' selected';
  }

  document.getElementById('stoMenu')?.addEventListener('change', (e)=>{
    if(e.target.classList.contains('sto-check')) updateStoLabel();
  });
  document.getElementById('stoSelectAll')?.addEventListener('click', (e)=>{
    e.preventDefault();
    document.querySelectorAll('.sto-item').forEach(wrap=>{
      if(wrap.style.display==='none') return;
      const c = wrap.querySelector('.sto-check'); c.checked = true;
    });
    updateStoLabel();
    fillTeamSelect_Create(); // panggil ulang
  });
  document.getElementById('stoClear')?.addEventListener('click', (e)=>{
    e.preventDefault();
    document.querySelectorAll('.sto-check').forEach(c=>c.checked=false);
    updateStoLabel();
    fillTeamSelect_Create(); // panggil ulang
  });

  /* ===== DUP CHECK (tetap) ===== */
  const form   = document.getElementById('add-form');
  const ov     = document.getElementById('dup-overlay');
  const btnYes = document.getElementById('btn-dup-replace');
  const btnNo  = document.getElementById('btn-dup-cancel');
  const sumEl  = document.getElementById('dup-summary');
  const replaceInput = document.getElementById('replace_id');

  function show(){ ov.classList.add('show'); ov.removeAttribute('aria-hidden'); }
  function hide(){ ov.classList.remove('show'); ov.setAttribute('aria-hidden','true'); }

  form.addEventListener('submit', async (e) => {
    if (replaceInput.value) return;
    e.preventDefault();

    const fd = new FormData(form);
    const token = fd.get('_token') || '';

    const body = new FormData();
    body.append('workorder',   fd.get('workorder')   || '');
    body.append('sc_order_no', fd.get('sc_order_no') || '');
    body.append('service_no',  fd.get('service_no')  || '');
    body.append('_token',      token);

    try{
      const res = await fetch('{{ route('detail-order-psb.dup-check') }}', {
        method: 'POST',
        headers: {'Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
        body
      });

      if (!res.ok) throw new Error('dup-check HTTP '+res.status);
      const json = await res.json();

      if (json.duplicate) {
        const s = json.summary || {};
        sumEl.textContent =
          `WO : ${s.workorder || '-'}\nSC : ${s.sc_order_no || '-'}\nSV : ${s.service_no || '-'}\n`+
          `Status: ${s.status || '-'}\nDate  : ${s.date || '-'}`;

        btnYes.onclick = () => { replaceInput.value = json.id; hide(); form.submit(); };
        btnNo.onclick  = () => { hide(); };
        show();
      } else {
        form.submit();
      }
    } catch(err){
      form.submit();
    }
  });

  /* ===== event changes untuk filter mapping ===== */
  ['sub_district','service_area','branch','wok'].forEach(id=>{
    document.getElementById(id)?.addEventListener('change', applyFilters);
  });

  // init pertama
  applyFilters();

  /* =======================================================================
     TEAM NAME (CREATE) — ambil dari API berdasarkan STO terpilih
     ======================================================================= */

  // helper: cek visible
  function isVisible(el){ return !!(el && (el.offsetWidth || el.offsetHeight || el.getClientRects().length)); }

  // Ambil STO terpilih dari checkbox / select / hidden CSV
  function getStoSelected_Create(){
    const checks = Array.from(document.querySelectorAll('input[name="sto[]"], .sto-check'));
    const picked = checks
      .filter(c => c.checked && (c.closest('.sto-item') ? isVisible(c.closest('.sto-item')) : isVisible(c)))
      .map(c => c.value);
    if (picked.length) return picked;

    const selMulti = document.querySelector('select[name="sto[]"]');
    if (selMulti) {
      const arr = Array.from(selMulti.selectedOptions).map(o=>o.value).filter(Boolean);
      if (arr.length) return arr;
    }

    const hiddenCsv = document.querySelector('input[name="sto"]');
    if (hiddenCsv && hiddenCsv.value) {
      return hiddenCsv.value.split(',').map(s=>s.trim()).filter(Boolean);
    }
    return [];
  }

  // ==== Fallback generator tim dari STO (STO01..STO10) ====
  function generateTeamsFromSto(stos, max=10){
    const out = [];
    (stos||[]).forEach(code=>{
      for(let i=1;i<=max;i++){
        out.push(`${code}${String(i).padStart(2,'0')}`);
      }
    });
    return out;
  }

  // ==== util untuk isi <select> tim ====
  function applyTeamOptions(sel, names, placeholder='— pilih tim —', current=''){
    sel.innerHTML = `<option value="">${placeholder}</option>`;
    const seen = new Set();
    names.forEach(name=>{
      if(!name || seen.has(name)) return;
      sel.appendChild(new Option(name, name));
      seen.add(name);
    });
    sel.disabled = false;
    if(current) sel.value = current;
  }

  /* ===== Isi Team Name (create) dari STO yang dipilih ===== */
  async function fillTeamSelect_Create(){
    const sel = document.getElementById('team_name');
    if(!sel) return;

    sel.disabled = true;
    sel.innerHTML = '<option value="">— pilih tim —</option>';

    // STO yang terpilih & terlihat
    const stos = Array.from(document.querySelectorAll('.sto-check'))
      .filter(c => c.checked && c.closest('.sto-item').style.display !== 'none')
      .map(c => c.value);

    if (stos.length === 0) return;

    // enable supaya terasa aktif
    sel.disabled = false;

    // coba ambil dari API; kalau kosong/gagal → fallback generator
    let names = [];
    try{
      // kirim CSV + array
      const params = new URLSearchParams();
      params.set('sto', stos.join(','));
      stos.forEach(s=>params.append('sto[]', s));
      const url = `{{ route('api.teams.by-sto') }}?${params.toString()}`;

      const res = await fetch(url, {headers:{'Accept':'application/json'}});
      if(res.ok){
        const data = await res.json();
        const list = Array.isArray(data) ? data : (data.data || data.items || []);
        names = list.map(it => it.text || it.name || it.team_name).filter(Boolean);
      }
    }catch(e){ /* diem aja, lanjut fallback */ }

    if (names.length === 0) names = generateTeamsFromSto(stos, 10);

    const oldVal = @json(old('team_name',''));
    applyTeamOptions(sel, names, '— pilih tim —', oldVal);
  }

  document.addEventListener('change', (e)=>{
    if (e.target.matches('input[name="sto[]"], .sto-check, select[name="sto[]"]')) {
      fillTeamSelect_Create();
    }
  });

  // Bungkus applyFilters supaya tiap mapping berubah → isi ulang tim
  const _applyFiltersOrig = applyFilters;
  applyFilters = function(){
    _applyFiltersOrig();
    fillTeamSelect_Create();
  };

  // Trigger saat checkbox STO (di menu) berubah
  document.getElementById('stoMenu')?.addEventListener('change', e=>{
    if(e.target.classList.contains('sto-check')) fillTeamSelect_Create();
  });

  // Init awal
  fillTeamSelect_Create();
})();
</script>
@endsection
