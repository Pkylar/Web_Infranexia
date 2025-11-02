@extends('layouts.app')
@section('title','Registrasi Teknisi')

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
  .logout-wrap .btn{ min-width:140px; }
  .menu-title{ font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; margin:12px 0 8px; }
  .menu-item{ display:flex; align-items:center; gap:12px; padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background:rgba(255,255,255,.5)!important; border:1px solid rgba(0,0,0,.08); backdrop-filter:blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset; }
  .menu-item::before{ content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px; background:var(--psb); opacity:.9; }
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{height:36px}
  .card-like{ background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px; box-shadow:0 10px 26px rgba(2,8,23,.14); }
  .foto-thumb{ width:40px; height:40px; border-radius:50%; object-fit:cover; border:1px solid #e5e7eb; }

  /* Typeahead list */
  #qList{ z-index:1050; max-height:240px; overflow:auto; display:none; top:100%; left:0; }
  #qBox .list-group-item{ cursor:pointer }

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
<div class="container-fluid py-4">
  <div class="layout">
    <!-- @include('partials.sidebar') -->

    <main class="content">
      <div class="app-header mb-2">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
        <div>
          <h5 class="title mb-0">Registrasi Teknisi</h5>
          <small class="text-muted">Kelola daftar master teknisi.</small>
        </div>
        <div class="ms-auto">
          <a href="{{ route('registrasi-teknisi.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Tambah Teknisi
          </a>
        </div>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

      {{-- FILTER --}}
      <div class="card-like mb-3">
        <form id="filterForm" class="row g-2 align-items-end" method="GET" action="{{ route('registrasi-teknisi.index') }}">
          <div class="col-md-3">
            <label class="form-label">STO</label>
            <select name="base_sto" class="form-select">
              <option value="">— semua STO —</option>
              @foreach(($stoOpts ?? $stoOptions ?? []) as $s)
                <option value="{{ $s }}" @selected(request('base_sto')===$s || request('sto')===$s)>{{ $s }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-5">
            <label class="form-label">Cari (NIK/Nama/Mitra)</label>

            {{-- INPUT + dropdown saran --}}
            <div class="position-relative" id="qBox">
              <input id="qInput" type="text" name="q" class="form-control"
                     value="{{ request('q') }}" placeholder="Ketik kata kunci…"
                     autocomplete="off" aria-autocomplete="list" aria-expanded="false">
              <div id="qList" class="list-group position-absolute w-100 shadow-sm bg-white border"></div>
            </div>
          </div>

          <div class="col-md-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="">— semua —</option>
              @foreach(($statuses ?? ['AKTIF','NONAKTIF']) as $st)
                <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i> Filter</button>
            <a href="{{ route('registrasi-teknisi.index') }}" class="btn btn-outline-secondary">Reset</a>
          </div>
        </form>
      </div>

      {{-- TABLE --}}
      <div class="card-like">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:48px"></th>
                <th style="width:140px">NIK</th>
                <th>Nama</th>
                <th>Mitra</th>
                <th style="width:110px">Base STO</th>
                <th style="width:110px">Status</th>
                <th style="width:150px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $r)
                <tr>
                  <td class="text-center">
                    @if(!empty($r->foto_path))
                      <img class="foto-thumb" src="{{ Storage::url($r->foto_path) }}" alt="foto">
                    @else
                      <span class="badge bg-secondary">No Foto</span>
                    @endif
                  </td>
                  <td class="fw-semibold">{{ $r->nik }}</td>
                  <td>{{ $r->nama }}</td>
                  <td>{{ $r->mitra }}</td>
                  <td>{{ $r->base_sto }}</td>
                  <td>
                    <span class="badge {{ $r->status==='AKTIF' ? 'bg-success' : 'bg-secondary' }}">{{ $r->status }}</span>
                  </td>
                  <td>
                    <a href="{{ route('registrasi-teknisi.edit',$r) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form method="POST" action="{{ route('registrasi-teknisi.destroy',$r) }}" class="d-inline"
                          onsubmit="return confirm('Hapus teknisi {{ $r->nama }} ({{ $r->nik }})?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex justify-content-end">
          {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </main>
  </div>
</div>

@push('scripts')
<script>
  // ========== Typeahead untuk input q ==========
  (function(){
    const form  = document.getElementById('filterForm');
    const input = document.getElementById('qInput');
    const list  = document.getElementById('qList');
    const box   = document.getElementById('qBox');
    const api   = @json(route('registrasi-teknisi.suggest'));

    const debounce = (fn, ms=200) => {
      let t; return (...args)=>{ clearTimeout(t); t=setTimeout(()=>fn(...args), ms); };
    };

    function hideList(){ list.style.display='none'; input.setAttribute('aria-expanded','false'); }
    function showList(){ list.style.display='block'; input.setAttribute('aria-expanded','true'); }

    function render(items){
      list.innerHTML='';
      if(!items.length){ hideList(); return; }
      items.forEach(it=>{
        const btn=document.createElement('button');
        btn.type='button';
        btn.className='list-group-item list-group-item-action d-flex justify-content-between align-items-center';
        btn.innerHTML = `<span>${it.label}</span>
                         <small class="text-muted">${it.sto ?? ''} ${it.status ? ' • '+it.status : ''}</small>`;
        btn.addEventListener('click', ()=>{
          input.value = it.label;
          hideList();
          form.requestSubmit();   // auto filter setelah pilih
        });
        list.appendChild(btn);
      });
      showList();
    }

    const fetchSuggest = debounce(async (q)=>{
      q = (q||'').trim();
      if(!q){ hideList(); return; }
      try{
        const res = await fetch(api+'?q='+encodeURIComponent(q), { headers:{'Accept':'application/json'} });
        const data = res.ok ? await res.json() : [];
        render(Array.isArray(data) ? data : []);
      }catch{ hideList(); }
    }, 220);

    input.addEventListener('input', e=> fetchSuggest(e.target.value));
    input.addEventListener('keydown', e=>{
      if(e.key==='Enter'){
        const first=list.querySelector('.list-group-item');
        if(first){ e.preventDefault(); first.click(); }
      }else if(e.key==='Escape'){ hideList(); }
    });
    document.addEventListener('click', e=>{ if(!box.contains(e.target)) hideList(); });

    // Auto-submit saat user berhenti mengetik (debounce)
    const autoSubmit = debounce(()=> form.requestSubmit(), 600);
    input.addEventListener('input', autoSubmit);
  })();

  // toggle sidebar (konsisten)
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
