@extends('layouts.app')
@section('title','Daftar Tim Teknisi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{ --brand-red:#b41111; --glass:rgba(255,255,255,.84); --psb:#0ea5e9; --psb-soft:#e6f6fe; --sb-w:300px; --sb-mini:72px; }
  body{ background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed; }

  /* Layout */
  .layout{ display:flex; gap:16px; }
  .content{ flex:1 1 auto; min-width:0; }

  /* Sidebar */
  .sidebar{
    position:relative; width:var(--sb-w);
    background:var(--glass); border-radius:16px; padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.16), 0 0 0 1px rgba(2,8,23,.06) inset;
    transition: width .24s ease; backdrop-filter: blur(8px);
  }
  .sidebar.mini{ width:var(--sb-mini); }
  .sb-section{ transition: opacity .18s ease; }
  .sidebar.mini .sb-section{ opacity:0; pointer-events:none; position:absolute; inset:16px; }
  .sb-toggle{ position:absolute; top:10px; right:10px; z-index:2; border-radius:999px; padding:6px 10px; }

  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff; display:grid; place-items:center; font-weight:700; font-size:20px; }

  /* Logout center */
  .logout-wrap{ display:flex; justify-content:center; text-align:center; }
  .logout-wrap form{ display:inline-block; width:auto; }
  .logout-wrap .btn{ min-width:160px; }

  .menu-title{ font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; margin:12px 0 8px; }
  .menu-item{
    display:flex; align-items:center; gap:12px;
    padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background: rgba(255,255,255,.5) !important; border:1px solid rgba(0,0,0,.08); backdrop-filter: blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset;
  }
  .menu-item::before{
    content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px;
    background: var(--psb); opacity:.9;
  }
  .menu-item .mi-left{
    width:36px; height:36px; display:grid; place-items:center; border-radius:10px;
    background: rgba(14,165,233,.10); border:1px solid rgba(14,165,233,.25);
  }
  .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; }
  .menu-item[data-menu="psb"]{ background: var(--psb-soft) !important; border:1px solid rgba(14,165,233,.35); }

  .sidebar.mini .menu-item{ justify-content:center; padding:12px 10px; }
  .sidebar.mini .menu-title,.sidebar.mini .user-name,.sidebar.mini .user-email,.sidebar.mini .logout-wrap{ display:none!important; }

  /* Header */
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{height:36px}
  .app-header .title{margin:0;font-weight:700;line-height:1.2}

  /* Card */
  .card-like{ background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:16px; padding:16px; box-shadow:0 10px 26px rgba(2,8,23,.14); }

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
  $role = auth()->user()->role ?? '';
  $showTeknisi = in_array($role, ['Super Admin','HD TA','Team Leader']);
@endphp

<div class="container-fluid py-4">
  <div class="layout">

    <!-- {{-- ===== SIDEBAR ===== --}}
    @include('partials.sidebar') -->


    {{-- ===== KONTEN ===== --}}
    <main class="content">
      <div class="app-header mb-2">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
        <div>
          <h5 class="title mb-0">Daftar Tim Teknisi</h5>
          <small class="text-muted">Kelola tim & kapasitas teknisi.</small>
        </div>
        <div class="ms-auto">
          <a href="{{ route('teknisi.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Tim
          </a>
        </div>
      </div>

      {{-- FILTER --}}
      <div class="card-like mb-3">
        <form class="row g-2 align-items-end" method="GET" action="{{ route('teknisi.index') }}">
          <div class="col-md-3">
            <label class="form-label">STO</label>
            <select name="sto" id="sto" class="form-select">
              <option value="">— semua STO —</option>
              @foreach(($stoOpts ?? []) as $opt)
                <option value="{{ $opt }}" @selected(request('sto')===$opt)>{{ $opt }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-5">
            <label class="form-label">Nama Tim</label>
            <select name="team_name" id="team_name" class="form-select" {{ request('sto') ? '' : 'disabled' }}>
              <option value="">{{ request('sto') ? '— semua tim —' : '— pilih STO dulu —' }}</option>
              @if(!empty($teamOptions))
                @foreach($teamOptions as $t)
                  <option value="{{ $t }}" @selected(request('team_name')===$t)>{{ $t }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
            <a href="{{ route('teknisi.index') }}" class="btn btn-outline-secondary">Reset</a>
          </div>
        </form>
      </div>

      {{-- TABLE --}}
      <div class="card-like">
        <div class="table-responsive">
          <table class="table table-bordered align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width:100px">STO</th>
                <th>Nama Tim</th>
                <th style="width:160px">NIK Teknisi 1</th>
                <th style="width:160px">NIK Teknisi 2</th>
                <th style="width:140px">Kapasitas</th>
                <th style="width:140px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $r)
                <tr>
                  <td>{{ $r->sto_code }}</td>
                  <td class="fw-semibold">{{ $r->nama_tim }}</td>
                  <td>{{ $r->nik_teknisi1 }}</td>
                  <td>{{ $r->nik_teknisi2 }}</td>
                  <td>
                    @php $penuh = !empty($r->nik_teknisi1) && !empty($r->nik_teknisi2); @endphp
                    <span class="badge {{ $penuh ? 'bg-danger' : 'bg-success' }}">{{ $penuh ? 'Penuh' : 'Tersedia' }}</span>
                  </td>
                  <td>
                    <a href="{{ route('teknisi.edit',$r->id) }}" class="btn btn-sm btn-primary">Edit</a>
                    <form action="{{ route('teknisi.destroy',$r->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Hapus tim {{ $r->nama_tim }}?')">
                      @csrf @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted">Belum ada data tim.</td></tr>
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
  (function(){
    const stoSel  = document.getElementById('sto');
    const teamSel = document.getElementById('team_name');
    const apiUrl  = @json(route('api.teams.by-sto'));

    async function loadTeamsBySto(sto){
      teamSel.innerHTML = '';
      if(!sto){
        teamSel.disabled = true;
        teamSel.add(new Option('— pilih STO dulu —', ''));
        return;
      }
      try{
        const res  = await fetch(apiUrl + '?sto=' + encodeURIComponent(sto), { headers:{'Accept':'application/json'} });
        const data = await res.json(); // [{id,text,sto,penuh},...]
        const names = [...new Set(data.map(it => it.text))].sort();
        teamSel.disabled = false;
        teamSel.add(new Option('— semua tim —', ''));
        names.forEach(n => teamSel.add(new Option(n, n)));

        // restore selected (if any)
        const selected = @json(request('team_name'));
        if(selected) teamSel.value = selected;
      }catch(e){
        teamSel.disabled = true;
        teamSel.add(new Option('— gagal memuat —', ''));
      }
    }

    stoSel?.addEventListener('change', e => loadTeamsBySto(e.target.value));

    // jika reload dengan STO terpilih tapi server tidak kirim $teamOptions
    @if(request()->filled('sto') && empty($teamOptions))
      loadTeamsBySto(@json(request('sto')));
    @endif
  })();

  // toggle sidebar
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
