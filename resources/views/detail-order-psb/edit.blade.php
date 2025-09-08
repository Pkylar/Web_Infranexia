@extends('layouts.app')
@section('title', 'Edit Data - Detail Order PSB')

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
  .form-label{font-weight:600; font-size:.9rem;}
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
          <h5 class="mb-0">Edit Data - PSB</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStatusModal">+ Tambah Status</button>
        </div>

        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

        <div class="card card-custom shadow-sm">
          <div class="card-body">
            <form id="edit-form" method="POST" action="{{ route('detail-order-psb.update', $psb->id) }}">
              @csrf @method('PUT')

              <div class="row g-3">
                {{-- Identitas order --}}
                <div class="col-md-3">
                  <label class="form-label">Workorder</label>
                  <input type="text" name="workorder" class="form-control" value="{{ old('workorder', $psb->workorder) }}" placeholder="Workorder">
                </div>
                <div class="col-md-6">
                  <label class="form-label">SC Order No / Track ID / CSRM No</label>
                  <input type="text" name="sc_order_no" class="form-control" value="{{ old('sc_order_no', $psb->sc_order_no) }}" placeholder="SC Order No / Track ID / CSRM No">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Service No.</label>
                  <input type="text" name="service_no" class="form-control" value="{{ old('service_no', $psb->service_no) }}" placeholder="Service No.">
                </div>

                {{-- Kategori wilayah --}}
                <div class="col-md-3">
                  <label class="form-label">Sub District</label>
                  <select name="sub_district" class="form-select">
                    <option value="">Sub District — Semua</option>
                    @foreach(($subDistrictOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('sub_district', $psb->sub_district)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Service Area</label>
                  <select name="service_area" class="form-select">
                    <option value="">Service Area — Semua</option>
                    @foreach(($serviceAreaOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('service_area', $psb->service_area)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Branch</label>
                  <select name="branch" class="form-select">
                    <option value="">Branch — Semua</option>
                    @foreach(($branchOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('branch', $psb->branch)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">WOK</label>
                  <select name="wok" class="form-select">
                    <option value="">WOK — Semua</option>
                    @foreach(($wokOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('wok', $psb->wok)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- STO multi --}}
                @php $stoArr = $psb->sto ? explode(',', $psb->sto) : []; @endphp
                <div class="col-md-6">
                  <label class="form-label">STO (boleh pilih lebih dari 1)</label>
                  <div id="stoDD" class="dropdown">
                    <button class="btn btn-light w-100 text-start d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                      <span id="stoDropdownLabel">STO — Pilih</span><span class="ms-2">▾</span>
                    </button>
                    {{-- ⬇⬇ PENTING: tambahkan id="stoMenu" --}}
                    <div id="stoMenu" class="dropdown-menu sto-menu px-3 py-2">
                      <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <a href="#" id="stoSelectAll" class="text-decoration-none">Select all</a>
                        <a href="#" id="stoClear" class="text-decoration-none">Clear</a>
                      </div>
                      @foreach(($stoOpts ?? []) as $code)
                        <div class="form-check sto-item">
                          <input class="form-check-input sto-check" type="checkbox"
                                id="sto-{{ $code }}" name="sto[]"
                                value="{{ $code }}"
                                {{ in_array($code, (array)old('sto', $stoArr), true) ? 'checked':'' }}>
                          <label class="form-check-label" for="sto-{{ $code }}">{{ $code }}</label>
                        </div>
                      @endforeach
                    </div>
                  </div>
                </div>

                {{-- Produk & Transaksi --}}
                <div class="col-md-3">
                  <label class="form-label">Produk</label>
                  <select name="produk" class="form-select">
                    <option value="">Produk — Semua</option>
                    @foreach(($produkOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('produk', $psb->produk)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Transaksi</label>
                  <select name="transaksi" class="form-select">
                    <option value="">Transaksi — Semua</option>
                    @foreach(($transaksiOpts ?? []) as $v)
                      <option value="{{ $v }}" @selected(old('transaksi', $psb->transaksi)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>

                {{-- Customer / kontak / tim --}}
                <div class="col-md-3">
                  <label class="form-label">Status bima</label>
                  <input type="text" name="status_bima" class="form-control" value="{{ old('status_bima', $psb->status_bima) }}" placeholder="Status bima">
                </div>
                <div class="col-md-9">
                  <label class="form-label">Address</label>
                  <input type="text" name="address" class="form-control" value="{{ old('address', $psb->address) }}" placeholder="Address">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Customer Name</label>
                  <input type="text" name="customer_name" class="form-control" value="{{ old('customer_name', $psb->customer_name) }}" placeholder="Customer Name">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Contact Number</label>
                  <input type="text" name="contact_number" class="form-control" value="{{ old('contact_number', $psb->contact_number) }}" placeholder="Contact Number">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Team Name</label>
                  <select id="team_name" name="team_name" class="form-select">
                    <option value="">— pilih tim —</option>
                  </select>
                </div>

                {{-- ORDER STATUS (kategori) + SUB KENDALA (filtered) --}}
                <div class="col-md-4">
                  <label class="form-label">Order Status</label>
                  <select id="order_status" name="order_status" class="form-select">
                    <option value="">Order Status — Semua</option>
                    @foreach($statusOptions as $opt)
                      <option value="{{ $opt }}" @selected(old('order_status', $psb->order_status)===$opt)>{{ $opt }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-8">
                  <label class="form-label">Sub Kendala</label>
                  <select id="sub_kendala" name="sub_kendala" class="form-select">
                    <option value="">Sub Kendala — Semua</option>
                  </select>
                  <div class="form-text">Muncul & wajib hanya jika Order Status salah satu kategori <b>KENDALA</b>.</div>
                </div>

                {{-- Koordinat, Validasi, Validator, Keterangan, ID Valins --}}
                <div class="col-md-4">
                  <label class="form-label">Koordinat Survei</label>
                  <input type="text" name="koordinat_survei" class="form-control" value="{{ old('koordinat_survei', $psb->koordinat_survei) }}" placeholder="Koordinat Survei">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Validasi Eviden Kendala</label>
                  <select name="validasi_eviden_kendala" class="form-select">
                    <option value="">Validasi Eviden Kendala — Semua</option>
                    @foreach($validasiOptions as $v)
                      <option value="{{ $v }}" @selected(old('validasi_eviden_kendala', $psb->validasi_eviden_kendala)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nama Validator Kendala</label>
                  <input type="text" name="nama_validator_kendala" class="form-control" value="{{ old('nama_validator_kendala', $psb->nama_validator_kendala) }}" placeholder="Nama Validator Kendala">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Validasi Failwa / Invalid</label>
                  <select name="validasi_failwa_invalid" class="form-select">
                    <option value="">Validasi Failwa / Invalid — Semua</option>
                    @foreach($validasiOptions as $v)
                      <option value="{{ $v }}" @selected(old('validasi_failwa_invalid', $psb->validasi_failwa_invalid)===$v)>{{ $v }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Nama Validator Failwa</label>
                  <input type="text" name="nama_validator_failwa" class="form-control" value="{{ old('nama_validator_failwa', $psb->nama_validator_failwa) }}" placeholder="Nama Validator Failwa">
                </div>
                <div class="col-md-4">
                  <label class="form-label">ID Valins</label>
                  <input type="text" name="id_valins" class="form-control" value="{{ old('id_valins', $psb->id_valins) }}" placeholder="ID Valins">
                </div>

                {{-- Deskripsi & Keterangan Non Valid --}}
                <div class="col-md-12">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3" placeholder="Description">{{ old('description', $psb->description) }}</textarea>
                </div>
                <div class="col-md-12">
                  <label class="form-label">Keterangan Non Valid</label>
                  <textarea name="keterangan_non_valid" class="form-control" rows="3" placeholder="Keterangan Non Valid">{{ old('keterangan_non_valid', $psb->keterangan_non_valid) }}</textarea>
                </div>
              </div>

              <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update Data</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- MODAL: Tambah Status -->
      <div class="modal fade" id="addStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form class="modal-content" method="POST" action="{{ route('detail-order-psb.add-status', $psb->id) }}">
            @csrf
            <div class="modal-header">
              <h5 class="modal-title">Tambah Status</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Status</label>
                <select id="status_main_modal" name="status_main" class="form-select" required>
                  <option value="">— pilih status —</option>
                  @foreach($statusOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3 d-none" id="sub_kendala_modal_wrap">
                <label class="form-label">Sub Kendala</label>
                <select id="sub_kendala_modal" name="sub_kendala" class="form-select">
                  <option value="">— pilih sub kendala —</option>
                </select>
                <div class="form-text">Wajib dipilih jika status kategori <b>KENDALA</b>.</div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Tambahkan</button>
            </div>
          </form>
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

{{-- ===== Script: status + sub_kendala & STO label ===== --}}
<script>
(function(){
  const SUBS = @json($subKendalaOpts ?? []);
  const INIT_SUB = @json(old('sub_kendala', $psb->sub_kendala));
  const K_CATS = ['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'];

  /* STO label */
  const dd=document.getElementById('stoDD');
  if(dd){
    const label=dd.querySelector('#stoDropdownLabel');
    const checks=dd.querySelectorAll('.sto-check');
    function updateLabel(){
      const all=Array.from(checks).filter(c => c.closest('.sto-item') ? c.closest('.sto-item').style.display !== 'none' : true);
      const selected=all.filter(c=>c.checked).map(c=>c.value);
      if(selected.length===0) label.textContent='STO — Pilih';
      else if(selected.length===all.length && all.length>0) label.textContent='STO — All';
      else if(selected.length<=4) label.textContent='STO — '+selected.join(', ');
      else label.textContent='STO — '+selected.length+' selected';
    }
    dd.addEventListener('change', e=>{ if(e.target.classList.contains('sto-check')) updateLabel(); });
    dd.querySelector('#stoSelectAll')?.addEventListener('click', e=>{ e.preventDefault(); checks.forEach(c=>{ if(c.closest('.sto-item')?.style.display!=='none') c.checked=true; }); updateLabel(); });
    dd.querySelector('#stoClear')?.addEventListener('click', e=>{ e.preventDefault(); checks.forEach(c=>c.checked=false); updateLabel(); });
    updateLabel();
  }

  function isKendalaCategory(s){ return K_CATS.includes(s); }

  function fillSub(sel, category, selectedValue){
    sel.innerHTML=''; 
    sel.appendChild(new Option('Sub Kendala — Semua',''));
    if(!category){ sel.disabled=true; return; }
    const list = SUBS.filter(v => v.startsWith(category));
    list.forEach(v=>{
      const label=v.split('|').slice(1).join(' | ') || v;
      const opt=new Option(label, v);
      if(selectedValue && selectedValue===v) opt.selected=true;
      sel.appendChild(opt);
    });
    sel.disabled=false;
  }

  const osSel=document.getElementById('order_status');
  const skSel=document.getElementById('sub_kendala');
  function initForm(){
    const status=osSel.value||'';
    if(isKendalaCategory(status)){
      fillSub(skSel, status, INIT_SUB);
    }else{
      skSel.innerHTML=''; skSel.appendChild(new Option('Sub Kendala — Semua','')); skSel.disabled=true;
    }
  }
  osSel?.addEventListener('change', ()=>{
    const s=osSel.value||'';
    if(isKendalaCategory(s)) fillSub(skSel, s, null);
    else { skSel.innerHTML=''; skSel.appendChild(new Option('Sub Kendala — Semua','')); skSel.disabled=true; }
  });
  initForm();

  // Modal tambah status
  const statusMainModal=document.getElementById('status_main_modal');
  const wrapModal=document.getElementById('sub_kendala_modal_wrap');
  const subModal=document.getElementById('sub_kendala_modal');
  statusMainModal?.addEventListener('change', ()=>{
    const s=statusMainModal.value||'';
    const show=['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'].includes(s);
    wrapModal.classList.toggle('d-none', !show);
    if(show){ 
      subModal.innerHTML='<option value="">— pilih sub kendala —</option>';
      SUBS.filter(v=>v.startsWith(s)).forEach(v=>{
        subModal.appendChild(new Option(v.split('|').slice(1).join(' | '), v));
      });
    }else { subModal.innerHTML='<option value="">— pilih sub kendala —</option>'; }
  });
})();
</script>

{{-- ===== Script: isi dropdown Team Name berdasar STO (API + fallback 10 tim/sto) ===== --}}
<script>
(function(){
  function getCheckedStos(){
    return Array.from(document.querySelectorAll('#stoMenu .sto-check'))
      .filter(c => c.checked && (c.closest('.sto-item') ? c.closest('.sto-item').style.display !== 'none' : true))
      .map(c => c.value);
  }
  function generateTeamsFromSto(stos, max=10){
    const out = [];
    (stos||[]).forEach(code=>{
      for(let i=1;i<=max;i++){
        out.push(`${code}${String(i).padStart(2,'0')}`);
      }
    });
    return out;
  }
  async function fillTeamSelect_Edit(){
    const sel = document.getElementById('team_name');
    if(!sel) return;

    const current = @json(old('team_name', $psb->team_name) ?? '');
    sel.innerHTML = '<option value="">— pilih tim —</option>';
    sel.disabled = true;

    let stos = getCheckedStos();
    if (stos.length === 0) {
      const stoCsv = @json($psb->sto ?? '');
      if (stoCsv) stos = stoCsv.split(',').map(s=>s.trim()).filter(Boolean);
    }
    if (stos.length === 0){ sel.disabled = false; if(current) sel.value=current; return; }

    let names = [];
    try{
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
    }catch(e){}

    if (names.length === 0) names = generateTeamsFromSto(stos, 10);

    const seen = new Set();
    names.forEach(n=>{
      if(!n || seen.has(n)) return;
      sel.appendChild(new Option(n, n));
      seen.add(n);
    });

    sel.disabled = false;
    if (current) sel.value = current;
  }

  fillTeamSelect_Edit();
  document.getElementById('stoMenu')?.addEventListener('change', e=>{
    if(e.target.classList.contains('sto-check')) fillTeamSelect_Edit();
  });
  if (typeof window.applyFilters === 'function') {
    const _orig = window.applyFilters;
    window.applyFilters = function(){
      _orig();
      fillTeamSelect_Edit();
    };
  }
})();
</script>
@endsection
