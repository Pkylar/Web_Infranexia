<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}"/>

  <title>{{ config('app.name','Infranexia') }}</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>

  <style>
    /* ===== Tokens ===== */
    :root{
      --sb-w:300px; --sb-mini:72px;
      --glass: rgba(255,255,255,.84);
      --psb:#0ea5e9;
    }

    /* ===== Page ===== */
    html,body{height:100%}
    body{
      margin:0; color:#111;
      font-family: system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      background:url('{{ asset('images/bg.jpg') }}') center/cover no-repeat fixed;
    }
    @media (max-width: 767.98px){ body{ background-attachment:scroll!important; } }

    /* ===== Layout ===== */
    .layout{ display:flex; gap:16px; min-height:100vh; padding:24px; }
    .content{ flex:1 1 auto; min-width:0; }

    /* ===== Sidebar (global) ===== */
    #sidebar{
      position:relative; width:var(--sb-w); flex:0 0 var(--sb-w);
      padding:16px; border-radius:16px; background:var(--glass);
      box-shadow:0 10px 26px rgba(2,8,23,.16), 0 0 0 1px rgba(2,8,23,.06) inset;
      backdrop-filter: blur(10px);
      transition: width .25s ease, flex-basis .25s ease;
      z-index:20;
    }
    #sidebar.mini{ width:var(--sb-mini); flex-basis:var(--sb-mini); }
    #sidebar .sb-section{ transition:opacity .18s ease; }
    #sidebar.mini .sb-section{ opacity:0; pointer-events:none; position:absolute; inset:16px; }

    #sbToggle{
      position:absolute; top:10px; right:10px; z-index:30;
      border-radius:999px; padding:6px 10px; line-height:1;
      background:#fff; border:1px solid #e5e7eb; box-shadow:0 6px 18px rgba(0,0,0,.08);
    }

    .menu-title{ margin:12px 0 8px; font-weight:700; font-size:12px; letter-spacing:.5px; color:#222; }
    .menu-item{
      position:relative; display:flex; align-items:center; gap:12px;
      padding:14px 16px; border-radius:16px; text-decoration:none; color:#111;
      background:rgba(255,255,255,.60); border:1px solid rgba(0,0,0,.08);
      backdrop-filter: blur(8px);
      box-shadow:0 8px 22px rgba(2,8,23,.12), 0 0 0 1px rgba(255,255,255,.35) inset;
      transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
    }
    .menu-item::before{
      content:""; position:absolute; inset:0 auto 0 0; width:6px; border-radius:16px 0 0 16px; background:var(--psb);
    }
    .menu-item .mi-left{ width:36px; height:36px; display:grid; place-items:center; border-radius:10px; background:rgba(0,0,0,.05); border:1px solid rgba(0,0,0,.08); }
    .menu-item .mi-chevron{ margin-left:auto; color:#7a7a7a; transition:transform .15s ease, color .15s ease; }
    .menu-item:hover{ transform:translateY(-1px); box-shadow:0 14px 28px rgba(2,8,23,.16), 0 0 0 1px rgba(255,255,255,.45) inset; }

    .submenu{ display:none; padding-left:12px; }
    .submenu.show{ display:block; }
    .menu-subitem{
      display:flex; align-items:center; gap:10px; padding:12px 14px; border-radius:14px; text-decoration:none; color:#111;
      background:rgba(255,255,255,.72); border:1px solid rgba(14,165,233,.25);
      box-shadow:0 6px 18px rgba(2,8,23,.10), 0 0 0 1px rgba(255,255,255,.30) inset; margin-top:6px;
    }
    .menu-subitem::before{ content:""; position:absolute; inset:0 auto 0 0; width:4px; border-radius:14px 0 0 14px; background:#0ea5e9; }

    /* Mini mode: sembunyikan teks */
    #sidebar.mini .menu-title,
    #sidebar.mini .menu-item .mi-text,
    #sidebar.mini .user-name,
    #sidebar.mini .user-email,
    #sidebar.mini .logout-wrap{ display:none!important; }
    #sidebar.mini .menu-item{ justify-content:center; padding:12px 10px; }

    /* Tablet/HP: layout jadi kolom, sidebar tetap satu */
    @media (max-width: 991.98px){
      .layout{ flex-direction:column; padding:16px; }
      #sidebar{ width:100%; flex:auto; }
    }
  </style>

  @stack('styles')
</head>
<body>
  <div id="app">
    <main class="py-3">
      <div class="container-fluid">
        <div class="layout">
          @auth
            @include('partials.sidebar')
          @endauth

          <main class="content">
            @yield('content')
          </main>
        </div>
      </div>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')

  <!-- ===== Global behaviour: toggle sidebar + submenu ===== -->
  <script>
  (function(){
    const KEY_SIDEBAR='sidebar-mini', KEY_SUB='submenu-teknisi-open';
    const getSB=()=>document.getElementById('sidebar');

    function renderToggleIcon(){
      const sb=getSB(), btn=document.getElementById('sbToggle'); if(!sb||!btn) return;
      btn.innerHTML = sb.classList.contains('mini')
        ? '<i class="bi bi-chevron-right"></i>' : '<i class="bi bi-chevron-left"></i>';
    }
    function setMini(on){
      const sb=getSB(); if(!sb) return;
      sb.classList.toggle('mini', !!on);
      try{ localStorage.setItem(KEY_SIDEBAR, on?'1':'0'); }catch(e){}
      renderToggleIcon();
    }
    function bootSidebar(){
      setMini(localStorage.getItem(KEY_SIDEBAR)==='1');
      document.addEventListener('click',e=>{
        if(!e.target.closest('#sbToggle')) return;
        e.preventDefault();
        const sb=getSB(); if(!sb) return;
        setMini(!sb.classList.contains('mini'));
      });
    }
    function bootTeknisiSubmenu(){
      const toggle=document.querySelector('.js-tek-toggle');
      const sub=document.querySelector('.js-tek-sub');
      const caret=document.querySelector('.js-tek-caret');
      if(!toggle||!sub) return;
      const setOpen=(open)=>{
        sub.classList.toggle('show',open);
        if(caret){ caret.classList.toggle('bi-chevron-up',open); caret.classList.toggle('bi-chevron-down',!open); }
        try{ localStorage.setItem(KEY_SUB, open?'1':'0'); }catch(e){}
        toggle.setAttribute('aria-expanded', open?'true':'false');
      };
      setOpen(localStorage.getItem(KEY_SUB)==='1');
      toggle.addEventListener('click', ()=> setOpen(!sub.classList.contains('show')));
    }

    if(document.readyState!=='loading'){ bootSidebar(); bootTeknisiSubmenu(); }
    else{ document.addEventListener('DOMContentLoaded', ()=>{ bootSidebar(); bootTeknisiSubmenu(); }); }
  })();
  </script>
</body>
</html>
