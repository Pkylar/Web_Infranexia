@extends('layouts.app')
@section('title','Rekapan Foto')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  /* ===================== THEME & TOKENS ===================== */
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
    --sb-w: 300px;  /* lebar sidebar normal */
    --sb-mini: 72px;/* lebar sidebar mini  */
  }

  /* Gambar latar global (sesuai dashboard) */
  body{
    background:url('{{ asset('images/bg.jpg') }}') center/cover fixed no-repeat;
  }

  /* ===================== LAYOUT GRID ===================== */
  .layout{
    display:flex;
    gap:16px;       /* jarak antara sidebar dan konten */
  }
  .content{
    flex:1 1 auto;  /* kolom konten fleksibel */
    min-width:0;
  }

  /* ===================== SIDEBAR (konsisten dg dashboard) ===================== */
  .sidebar{
    position:relative;
    width:var(--sb-w);
    background:var(--glass);
    border-radius:16px;
    padding:16px;
    box-shadow:
      0 10px 26px rgba(2,8,23,.16),
      0 0 0 1px rgba(2,8,23,.06) inset;
    transition:
      width .24s ease,
      box-shadow .24s ease,
      background .24s ease;
    backdrop-filter: blur(8px);
  }
  .sidebar.mini{ width:var(--sb-mini); }

  .sb-section{ transition: opacity .18s ease; }
  .sidebar.mini .sb-section{
    opacity:0; pointer-events:none;
    position:absolute; inset:16px;
  }

  /* tombol bulat kecil untuk toggle mini/normal */
  .sb-toggle{
    position:absolute; top:10px; right:10px; z-index:2;
    border-radius:999px; padding:6px 10px; line-height:1;
    background:#fff; border:1px solid #e5e7eb;
    box-shadow:0 6px 18px rgba(0,0,0,.08);
  }

  /* avatar huruf inisial */
  .avatar{
    width:56px; height:56px; border-radius:50%;
    background:#6c757d; color:#fff; display:grid; place-items:center;
    font-weight:700; font-size:20px;
  }

  /* menu di sidebar */
  .menu-title{
    font-weight:700; font-size:12px; letter-spacing:.5px; color:#222;
    margin:12px 0 8px;
  }
  .menu-item{
    display:flex; align-items:center; gap:12px;
    padding:14px 16px; border-radius:16px; text-decoration:none; color:#111; position:relative;
    background: rgba(255,255,255,.5) !important;
    border:1px solid rgba(0,0,0,.08);
    backdrop-filter: blur(8px);
    box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset;
  }
  .menu-item::before{
    content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px;
    background: var(--psb); opacity:.9;
  }

  /* ===================== HEADER POLOS + LOGO ===================== */
  .app-header{
    display:flex; align-items:center; gap:12px;
    padding:12px 0;
    color:#111;
  }
  .app-header img.logo{ height:36px; }

  /* ===================== KARTU & GALERI FOTO ===================== */
  .card-like{
    background:rgba(255,255,255,.96);
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.14);
  }

  /* grid responsif untuk foto */
  .photo-grid{
    display:grid;
    grid-template-columns: repeat(auto-fill,minmax(180px,1fr));
    gap:12px;
  }
  .photo-card{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 6px 16px rgba(2,8,23,.08);
  }
  .photo-card img{
    width:100%;
    height:140px;
    object-fit:cover;
    cursor:zoom-in;      /* hint bahwa gambar bisa di-klik */
    background:#f3f3f3;  /* placeholder warna saat loading */
  }
  .photo-card .meta{
    padding:8px 10px;
    font-size:.9rem;
  }
  .photo-card .name{ font-weight:600; }

  /* responsive: auto-mini sidebar di perangkat sempit */
  @media (max-width: 991.98px){
    .sidebar{ width:var(--sb-mini); }
    .sidebar .sb-section{ opacity:0; pointer-events:none; }
  }

  /* ===================== Tombol Back ala FAB ===================== */
  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center; z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
  }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }

  /* ===================== IMAGE VIEWER (fullscreen + zoom + drag) ===================== */
  .img-viewer{position:fixed;inset:0;z-index:3000;display:none}
  .img-viewer.show{display:block}
  .iv-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.85)}
  .iv-img{
    position:absolute; top:50%; left:50%;
    max-width:90vw; max-height:85vh;
    transform:translate(-50%,-50%) scale(1);
    cursor:grab; user-select:none;
    box-shadow:0 12px 40px rgba(0,0,0,.5);
    border-radius:8px;
  }
  .iv-close{
    position:absolute; top:12px; right:12px;
    background:#fff; border:none;
    width:42px; height:42px; border-radius:999px;
    font-size:26px; line-height:1;
    display:grid; place-items:center;
    box-shadow:0 8px 20px rgba(0,0,0,.25);
  }
  .iv-ctrls{
    position:absolute; left:50%; bottom:16px; transform:translateX(-50%);
    display:flex; gap:8px;
  }
  .iv-ctrls button{
    background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:6px 10px;
    box-shadow:0 6px 16px rgba(0,0,0,.18);
  }
</style>
@endpush

@section('content')
  <!-- Tombol back ke Dashboard -->
  <a href="{{ url('/home') }}" class="btn-back-fixed" aria-label="Back">
    <span class="chev">&lsaquo;</span>
  </a>

  @php
    $role = auth()->user()->role ?? '';
    $canUpload = in_array($role,['Super Admin','Team Leader']);
  @endphp

  <div class="container-fluid py-4">
    <div class="layout">

      {{-- ===== Sidebar (pakai partial yang sama) ===== --}}
      @include('partials.sidebar')

      {{-- ===== Konten Utama ===== --}}
      <main class="content">

        {{-- Header judul + tombol Upload --}}
        <div class="app-header mb-2">
          <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
          <h5 class="mb-0">Rekapan Foto</h5>

          <div class="ms-auto d-flex gap-2">
            @if($canUpload)
              <a href="{{ route('rekap-foto.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-cloud-arrow-up me-1"></i> Upload Foto
              </a>
            @endif
          </div>
        </div>

        {{-- Flash message sukses --}}
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter STO + Search --}}
        <div class="card-like mb-3">
          <form class="row g-2" method="GET" action="{{ route('rekap-foto.index') }}">
            <div class="col-md-3">
              <label class="form-label">STO</label>
              <select name="sto" class="form-select">
                <option value="">— semua STO —</option>
                @foreach(($stoOpts ?? []) as $s)
                  <option value="{{ $s }}" @selected(request('sto')===$s)>{{ $s }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Cari (NIK/Nama/Keterangan)</label>
              <input type="text"
                     name="q"
                     class="form-control"
                     placeholder="Ketik kata kunci…"
                     value="{{ request('q') }}">
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
              <button class="btn btn-primary w-100">
                <i class="bi bi-filter me-1"></i> Filter
              </button>
              <a href="{{ route('rekap-foto.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
          </form>
        </div>

        {{-- Galeri foto --}}
        <div class="card-like">
          @if($rows->count()===0)
            <div class="text-center text-muted py-4">Belum ada foto.</div>
          @else
            <div class="photo-grid">

              @foreach($rows as $p)
                @php
                  /**
                   * ==== NORMALISASI PATH GAMBAR ====
                   * - Saat store: $path -> "public/rekap-foto/xxxx.jpg"
                   * - URL publik yang benar: "/storage/rekap-foto/xxxx.jpg"
                   * - handle data lama: "rekap-foto/xxxx.jpg" atau "storage/rekap-foto/xxxx.jpg"
                   */
                  $raw = $p->photo_path;
                  if ($raw) {
                      // buang prefix "public/" jika ada
                      $rel = \Illuminate\Support\Str::startsWith($raw, 'public/')
                             ? substr($raw, 7)
                             : $raw;

                      // jika sudah diawali "storage/", pakai apa adanya; kalau belum, prefiks "storage/"
                      $rel = \Illuminate\Support\Str::startsWith($rel, 'storage/')
                             ? $rel
                             : 'storage/'.$rel;

                      // hasil akhir jadi /storage/rekap-foto/xxx
                      $src = asset($rel);
                  } else {
                      $src = asset('images/no-photo.png');
                  }
                @endphp

                <div class="photo-card">
                  <img
                    class="js-viewable"
                    src="{{ $src }}"
                    data-full="{{ $src }}"
                    alt="foto"
                    loading="lazy">
                  <div class="meta">
                    <div class="name">{{ $p->teknisi_nama ?? '—' }}</div>
                    <div class="small text-muted">
                      {{ $p->teknisi_nik ?? '—' }} • STO: {{ $p->sto ?? '—' }}
                    </div>
                    <div class="small text-muted">
                      {{ optional($p->created_at)->format('d-m-Y H:i') }}
                    </div>

                    @if(!empty($p->note))
                      <div class="small">{{ $p->note }}</div>
                    @endif

                    @if($canUpload)
                      <form class="mt-2" method="POST" action="{{ route('rekap-foto.destroy',$p) }}"
                            onsubmit="return confirm('Hapus foto ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100">
                          <i class="bi bi-trash me-1"></i> Hapus
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-3 d-flex justify-content-end">
              {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
          @endif
        </div>
      </main>
    </div>
  </div>

  @push('scripts')
  <script>
    /* ===================== SIDEBAR TOGGLE (ingat state) ===================== */
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

    /* ===================== IMAGE VIEWER (fullscreen + zoom + drag) ===================== */
    (function(){
      // buat elemen viewer sekali (di-append ke <body>)
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

      // state transform
      const img = viewer.querySelector('#ivImg');
      let scale=1, tx=0, ty=0, dragging=false, sx=0, sy=0;

      // fungsi apply transform
      const apply = ()=> img.style.transform =
        `translate(calc(-50% + ${tx}px), calc(-50% + ${ty}px)) scale(${scale})`;

      // open/close
      const close = ()=>{ viewer.classList.remove('show'); document.body.style.overflow=''; };
      const open  = (src)=>{
        img.src=src;
        // reset state setiap buka
        scale=1; tx=0; ty=0; apply();
        viewer.classList.add('show');
        document.body.style.overflow='hidden';
      };

      // klik gambar kecil -> open viewer
      document.addEventListener('click', e=>{
        const t = e.target.closest('.js-viewable'); if(!t) return;
        e.preventDefault();
        open(t.dataset.full || t.src);
      });

      // close via backdrop, tombol, atau ESC
      viewer.querySelector('.iv-backdrop').addEventListener('click', close);
      viewer.querySelector('#ivClose').addEventListener('click', close);
      window.addEventListener('keydown', e=>{ if(e.key==='Escape') close(); });

      // controls zoom
      viewer.querySelector('#ivIn').addEventListener('click', ()=>{ scale=Math.min(5, scale+0.2); apply(); });
      viewer.querySelector('#ivOut').addEventListener('click', ()=>{ scale=Math.max(0.2, scale-0.2); apply(); });
      viewer.querySelector('#ivReset').addEventListener('click', ()=>{ scale=1; tx=0; ty=0; apply(); });

      // zoom pakai scroll
      viewer.addEventListener('wheel', e=>{
        if(!viewer.classList.contains('show')) return;
        e.preventDefault();
        scale = Math.min(5, Math.max(0.2, scale + (e.deltaY<0 ? 0.1 : -0.1)));
        apply();
      }, {passive:false});

      // drag gambar
      img.addEventListener('mousedown', e=>{
        dragging=true; img.style.cursor='grabbing';
        sx=e.clientX - tx; sy=e.clientY - ty;
      });
      window.addEventListener('mousemove', e=>{
        if(!dragging) return;
        tx = e.clientX - sx;
        ty = e.clientY - sy;
        apply();
      });
      window.addEventListener('mouseup', ()=>{
        dragging=false; img.style.cursor='grab';
      });
    })();
  </script>
  @endpush
@endsection
