@extends('layouts.app')
@section('title','Rekapan Foto')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  :root{
    --psb:#0ea5e9;
    --psb-soft:#e6f6fe;
  }

  /* background global */
  body{
    background:url('{{ asset('images/bg.jpg') }}') center/cover fixed no-repeat;
  }

  /* konten di area main */
  .app-header{
    display:flex; align-items:center; gap:12px;
    padding:12px 0;
    color:#111;
  }
  .app-header img.logo{ height:36px; }

  .card-like{
    background:rgba(255,255,255,.96);
    border:1px solid #e5e7eb;
    border-radius:16px;
    padding:16px;
    box-shadow:0 10px 26px rgba(2,8,23,.14);
  }

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
    cursor:zoom-in;
    background:#f3f3f3;
  }
  .photo-card .meta{
    padding:8px 10px;
    font-size:.9rem;
  }
  .photo-card .name{ font-weight:600; }

  /* FAB back */
  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center; z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
  }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }

  /* paksa strip sidebar tetap biru */
  .sidebar .menu-item::before{
    background:#0ea5e9 !important;
  }

  /* viewer fullscreen */
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
  {{-- Tombol back ke dashboard --}}
  <a href="{{ url('/home') }}" class="btn-back-fixed" aria-label="Back">
    <span class="chev">&lsaquo;</span>
  </a>

  @php
    $role = auth()->user()->role ?? '';
    $canUpload = in_array($role,['Super Admin','Team Leader']);
  @endphp

  <div class="container-fluid py-4">
    {{-- Header judul + tombol upload --}}
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

    {{-- Flash message --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter --}}
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

    {{-- Galeri --}}
    <div class="card-like">
      @if($rows->count()===0)
        <div class="text-center text-muted py-4">Belum ada foto.</div>
      @else
        <div class="photo-grid">
          @foreach($rows as $p)
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
              <img class="js-viewable"
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

        <div class="mt-3 d-flex justify-content-end">
          {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>

  @push('scripts')
  <script>
    // IMAGE VIEWER fullscreen + zoom + drag
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
      const open  = (src)=>{
        img.src=src;
        scale=1; tx=0; ty=0; apply();
        viewer.classList.add('show');
        document.body.style.overflow='hidden';
      };

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
