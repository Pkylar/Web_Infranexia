<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Infralexia') }}</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons (WAJIB untuk ikon chevron bi-*) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- FontAwesome (opsional) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

  <style>
    body { background-color:#f8fafc; color:#333; }
    .rounded-circle { width:60px; height:60px; font-size:1.5rem; }

    /* Pastikan overlay viewer tdk menghalangi klik saat hidden */
    .img-viewer{ pointer-events:none; }
    .img-viewer.show{ pointer-events:auto; }

    /* Pastikan tombol toggle selalu klik-able di atas konten lain */
    #sidebar .sb-toggle{ z-index: 9999; position: absolute; top:10px; right:10px; }
  </style>

  @stack('styles')
</head>
<body>
  <div id="app">
    <main class="py-4">
      @yield('content')
    </main>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Script halaman (image viewer, dsb) --}}
  @stack('scripts')

  {{-- ===== GLOBAL: toggle sidebar + submenu teknisi (stabil, tahan-banting) ===== --}}
  <script>
  (function(){
    const KEY_SIDEBAR = 'sidebar-mini';
    const KEY_SUB     = 'submenu-teknisi-open';

    // helper
    const getSidebar = () => document.getElementById('sidebar');

    // render ikon kiri/kanan
    function renderToggleIcon(){
      const sb  = getSidebar(); if(!sb) return;
      const on  = sb.classList.contains('mini');
      const btn = document.getElementById('sbToggle');
      if(btn){
        btn.innerHTML = on ? '<i class="bi bi-chevron-right"></i>'
                           : '<i class="bi bi-chevron-left"></i>';
      }
    }

    // set mini class + simpan state
    function setMini(on){
      const sb = getSidebar(); if(!sb) return;
      sb.classList.toggle('mini', !!on);
      try{ localStorage.setItem(KEY_SIDEBAR, on ? '1' : '0'); }catch(e){}
      renderToggleIcon();
    }

    // inisialisasi toggle sidebar
    function bootSidebar(){
      // apply state saat load
      setMini(localStorage.getItem(KEY_SIDEBAR) === '1');

      // event delegation — klik tombol #sbToggle
      document.addEventListener('click', function(e){
        const t = e.target.closest('#sbToggle');
        if(!t) return;
        e.preventDefault();
        const sb = getSidebar(); if(!sb) return;
        setMini(!sb.classList.contains('mini'));
      });
    }

    // inisialisasi submenu Teknisi
    function bootTeknisiSubmenu(){
      const toggleBtn = document.querySelector('.js-tek-toggle');
      const sub       = document.querySelector('.js-tek-sub');
      const caret     = document.querySelector('.js-tek-caret');
      if(!toggleBtn || !sub || !caret) return;

      function setOpen(open){
        sub.classList.toggle('show', open);
        caret.classList.toggle('bi-chevron-up', open);
        caret.classList.toggle('bi-chevron-down', !open);
        try{ localStorage.setItem(KEY_SUB, open ? '1' : '0'); }catch(e){}
        toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
      }

      setOpen(localStorage.getItem(KEY_SUB) === '1');
      toggleBtn.addEventListener('click', ()=> setOpen(!sub.classList.contains('show')));
    }

    if(document.readyState !== 'loading'){
      bootSidebar(); bootTeknisiSubmenu();
    }else{
      document.addEventListener('DOMContentLoaded', ()=>{ bootSidebar(); bootTeknisiSubmenu(); });
    }
  })();
  </script>
</body>
</html>
