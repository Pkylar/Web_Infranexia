@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  /* ========= TOKEN ========= */
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
    --sb-w: 300px;
    --sb-mini: 72px;
  }

  body{ background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed; }

  /* ========= Layout ========= */
  .layout{ display:flex; gap:16px; }
  .content{ flex:1 1 auto; min-width:0; }

  /* ========= Sidebar ========= */
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

  .sb-toggle{
    position:absolute; top:10px; right:10px; z-index:2;
    border-radius:999px; padding:6px 10px; line-height:1;
    background:#fff; border:1px solid #e5e7eb;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
  }

  .avatar{ width:56px; height:56px; border-radius:50%; background:#6c757d; color:#fff; display:grid; place-items:center; font-weight:700; font-size:20px; }
  .logout-wrap{ display:flex; justify-content:center; } .logout-wrap .btn{ min-width:140px; }

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
  .menu-item .mi-left{ width:36px; height:36px; display:grid; place-items:center; border-radius:10px; background: rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.08); }
  .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; transition: transform .15s ease, color .15s ease; }
  .menu-item:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(2,8,23,.16), 0 0 0 1px rgba(255,255,255,.45) inset; }
  .menu-item:hover .mi-chevron{ transform: translateX(3px); color:#333; }

  /* variasi untuk menu PSB */
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

  /* ========= Header & CTA ========= */
  .app-header{ display:flex; align-items:center; gap:12px; padding:12px 0; color:#111; }
  .app-header .logo{height:36px}
  .app-header .title{margin:0;font-weight:700;line-height:1.2}
  .app-header .sub{display:block;opacity:.75;font-size:.85rem}

  .cta-chip{
    display:inline-flex; align-items:center; gap:8px;
    padding:6px 12px; border-radius:999px; font-weight:600;
    background:#fff3cd; border:1px solid #ffe69c; color:#664d03;
    text-decoration:none;
    box-shadow:0 6px 14px rgba(0,0,0,.08);
  }
  .cta-chip:hover{ background:#ffe69c; color:#5c4b05; text-decoration:none; }

  /* ========= Widget metric ========= */
  .metric{ border-radius:14px; padding:16px; color:#fff; }
  .metric .big{ font-size:28px; font-weight:700; line-height:1; }
  .metric .label{ opacity:.9; font-weight:500; }
  .metric .icon{ font-size:28px; opacity:.95; }
  .metric.blue{background:#0d6efd;} .metric.green{background:#20c997;}
  .metric.indigo{background:#6610f2;} .metric.cyan{background:#0dcaf0; color:#103;}
  .metric.orange{background:#fd7e14;} .metric.red{background:#dc3545;}
  .metric.gray{background:#6c757d;} .metric.yellow{background:#ffc107; color:#432;}

  .table-activity td{ padding:8px 10px; vertical-align:middle; }
  .badge-status{ font-size:.8rem; }

  /* ========= Galeri kecil di dashboard ========= */
  .photo-grid{
    display:grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap:12px;
  }
  .photo-card{
    background:rgba(255,255,255,.96); border:1px solid #e5e7eb; border-radius:12px; padding:8px;
    box-shadow:0 6px 16px rgba(2,8,23,.10);
  }
  .photo-card img{
    width:100%; height:120px; object-fit:cover; border-radius:8px;
    border:1px solid #e5e7eb; cursor: zoom-in; background:#f3f3f3;
  }
  .photo-card .meta{ margin-top:6px; font-size:.85rem; }
  .photo-card .name{ font-weight:600; line-height:1.2; }

  @media (max-width: 991.98px){
    .sidebar{ width:var(--sb-mini); }
    .sidebar .sb-section{ opacity:0; pointer-events:none; }
  }

  /* ========= Image Viewer (reusable dengan yang di rekap) ========= */
  .img-viewer{position:fixed;inset:0;z-index:3000;display:none}
  .img-viewer.show{display:block}
  .iv-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.85)}
  .iv-img{position:absolute;top:50%;left:50%;max-width:90vw;max-height:85vh;transform:translate(-50%,-50%) scale(1);cursor:grab;user-select:none;box-shadow:0 12px 40px rgba(0,0,0,.5);border-radius:8px}
  .iv-close{position:absolute;top:12px;right:12px;background:#fff;border:none;width:42px;height:42px;border-radius:999px;font-size:26px;line-height:1;display:grid;place-items:center;box-shadow:0 8px 20px rgba(0,0,0,.25)}
  .iv-ctrls{position:absolute;left:50%;bottom:16px;transform:translateX(-50%);display:flex;gap:8px}
  .iv-ctrls button{background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:6px 10px;box-shadow:0 6px 16px rgba(0,0,0,.18)}
</style>
@endpush

@section('content')
@php
  $role = auth()->user()->role ?? '';
  $canUploadRekap = in_array($role, ['Super Admin','Team Leader']);
@endphp

<div class="container-fluid py-4">
  <div class="layout">
    {{-- ===== SIDEBAR ===== --}}
    @include('partials.sidebar')

    {{-- ===== KONTEN ===== --}}
    <main class="content">

      {{-- Header + CTA rekap foto --}}
      <div class="app-header mb-3">
        <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
        <div>
          <h5 class="title mb-0">Dashboard PSB</h5>
          <small class="sub">Ringkasan order, status, dan aktivitas terbaru.</small>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
          <a href="{{ route('rekap-foto.index') }}" class="cta-chip" title="Lihat Rekap Foto">
            <i class="bi bi-camera"></i> Rekap Foto
          </a>
          @if($canUploadRekap)
            <a href="{{ route('rekap-foto.create') }}" class="btn btn-primary btn-sm" title="Upload foto">
              <i class="bi bi-upload me-1"></i> Upload
            </a>
          @endif
        </div>
      </div>

      {{-- Metrik --}}
      <div class="row g-3">
        <div class="col-md-3 col-sm-6">
          <div class="metric blue d-flex justify-content-between align-items-center">
            <div><div class="big">{{ $totalUsers }}</div><div class="label">User</div></div>
            <i class="bi bi-people-fill icon"></i>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="metric indigo d-flex justify-content-between align-items-center">
            <div><div class="big">{{ $teamCount }}</div><div class="label">Team</div></div>
            <i class="bi bi-diagram-3 icon"></i>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="metric green d-flex justify-content-between align-items-center">
            <div><div class="big">{{ $customerCount }}</div><div class="label">Pelanggan</div></div>
            <i class="bi bi-person-badge icon"></i>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="metric red d-flex justify-content-between align-items-center">
            <div><div class="big">{{ $totalOrders }}</div><div class="label">Order PSB</div></div>
            <i class="bi bi-card-checklist icon"></i>
          </div>
        </div>
      </div>

      {{-- Ringkasan status --}}
      <div class="row g-3 mt-1">
        <div class="col-md-2 col-6"><div class="metric yellow text-dark"><div class="big">{{ $openCount }}</div><div class="label">OPEN</div></div></div>
        <div class="col-md-2 col-6"><div class="metric cyan"><div class="big">{{ $surveiCount }}</div><div class="label">SURVEI</div></div></div>
        <div class="col-md-2 col-6"><div class="metric blue"><div class="big">{{ $progresCount }}</div><div class="label">PROGRES</div></div></div>
        <div class="col-md-2 col-6"><div class="metric green"><div class="big">{{ $acCount }}</div><div class="label">AC</div></div></div>
        <div class="col-md-2 col-6"><div class="metric gray"><div class="big">{{ $closeCount }}</div><div class="label">CLOSE</div></div></div>
        <div class="col-md-2 col-6"><div class="metric orange"><div class="big">{{ $kendalaPelanggan + $kendalaTeknik + $kendalaSistem + $kendalaLainnya }}</div><div class="label">Total Kendala</div></div></div>
      </div>

      {{-- Aktivitas terbaru --}}
      <div class="mt-3 p-0">
        <div class="app-header mb-2 d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Aktivitas Terbaru</h6>
          <a href="{{ route('detail-order-psb.index') }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Detail
          </a>
        </div>
        <div class="table-responsive">
          <table class="table table-borderless table-activity mb-0">
            <tbody>
              @forelse($recentOrders as $r)
                <tr>
                  <td class="text-muted" style="width:180px;">
                    {{ optional($r->date_created)->format('d-m-Y H:i') }}
                  </td>
                  <td class="text-muted" style="width:120px;">
                    <span class="badge bg-light text-dark">WO: {{ $r->workorder }}</span>
                  </td>
                  <td>{{ $r->customer_name }}</td>
                  <td class="text-end" style="width:160px;">
                    <span class="badge badge-status bg-secondary">{{ $r->order_status }}</span>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-muted">Belum ada aktivitas.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Rekapan Foto Terbaru (preview) --}}
      @if(!empty($latestPhotos) && count($latestPhotos))
        <div class="mt-4">
          <div class="app-header mb-2">
            <h6 class="mb-0">Rekapan Foto Terbaru</h6>
          </div>
          <div class="photo-grid">
            @foreach($latestPhotos as $p)
              @php
                $raw = $p->photo_path;
                if ($raw) {
                    $rel = \Illuminate\Support\Str::startsWith($raw, 'public/')
                           ? substr($raw, 7)
                           : $raw;
                    $rel = \Illuminate\Support\Str::startsWith($rel, 'storage/')
                           ? $rel
                           : 'storage/'.$rel;
                    $src = asset($rel);
                } else {
                    $src = asset('images/no-photo.png');
                }
              @endphp

              <div class="photo-card">
                <img class="js-viewable" src="{{ $src }}" data-full="{{ $src }}" alt="foto" loading="lazy">
                <div class="meta">
                  <div class="name">{{ $p->teknisi_nama ?? '—' }}</div>
                  <div class="small">{{ optional($p->created_at)->format('d-m-Y H:i') }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endif

    </main>
  </div>
</div>

<script>
  /* ===== Sidebar toggle (ingat state) ===== */
  document.addEventListener('DOMContentLoaded', () => {
    const sb   = document.getElementById('sidebar');
    const btn  = document.getElementById('sbToggle');
    const KEY  = 'sidebar-mini';
    if(!sb || !btn) return;

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

  /* ===== Image Viewer (fullscreen + zoom + drag) ===== */
  (function(){
    const viewer = document.createElement('div');
    viewer.className = 'img-viewer';
    viewer.innerHTML = `
      <div class="iv-backdrop"></div>
      <img class="iv-img" id="ivImg" alt="">
      <button class="iv-close" id="ivClose" aria-label="Close">&times;</button>
      <div class="iv-ctrls">
        <button id="ivIn" type="button">Zoom +</button>
        <button id="ivOut" type="button">Zoom −</button>
        <button id="ivReset" type="button">Reset</button>
      </div>`;
    document.body.appendChild(viewer);

    const img = viewer.querySelector('#ivImg');
    let scale=1, tx=0, ty=0, dragging=false, sx=0, sy=0;
    const apply = ()=> img.style.transform =
      `translate(calc(-50% + ${tx}px), calc(-50% + ${ty}px)) scale(${scale})`;
    const close = ()=>{ viewer.classList.remove('show'); document.body.style.overflow=''; };
    const open  = (src)=>{ img.src=src; scale=1; tx=0; ty=0; apply(); viewer.classList.add('show'); document.body.style.overflow='hidden'; };

    document.addEventListener('click', e=>{
      const t = e.target.closest('.js-viewable'); if(!t) return;
      e.preventDefault();
      open(t.dataset.full || t.src);
    });

    viewer.querySelector('.iv-backdrop').addEventListener('click', close);
    viewer.querySelector('#ivClose').addEventListener('click', close);
    window.addEventListener('keydown', e=>{ if(e.key==='Escape') close(); });

    viewer.querySelector('#ivIn').addEventListener('click', ()=>{ scale=Math.min(5, scale+0.2); apply(); });
    viewer.querySelector('#ivOut').addEventListener('click', ()=>{ scale=Math.max(0.2, scale-0.2); apply(); });
    viewer.querySelector('#ivReset').addEventListener('click', ()=>{ scale=1; tx=0; ty=0; apply(); });

    viewer.addEventListener('wheel', e=>{
      if(!viewer.classList.contains('show')) return;
      e.preventDefault();
      scale = Math.min(5, Math.max(0.2, scale + (e.deltaY<0 ? 0.1 : -0.1)));
      apply();
    }, {passive:false});

    img.addEventListener('mousedown', e=>{
      dragging=true; img.style.cursor='grabbing';
      sx=e.clientX - tx; sy=e.clientY - ty;
    });
    window.addEventListener('mousemove', e=>{
      if(!dragging) return;
      tx = e.clientX - sx; ty = e.clientY - sy; apply();
    });
    window.addEventListener('mouseup', ()=>{
      dragging=false; img.style.cursor='grab';
    });
  })();
</script>
@endsection
