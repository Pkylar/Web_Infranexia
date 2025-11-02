@extends('layouts.app')
@section('title','Form Presensi (Check-in)')

@push('styles')
<style>
  body{
    background:url('{{ asset('images/bg.jpg') }}') no-repeat center/cover fixed;
  }
  .card-glass{
    background:rgba(255,255,255,.94);
    border:1px solid #e5e7eb;
    border-radius:14px;
    box-shadow:0 10px 26px rgba(2,8,23,.18)
  }
  .form-label.required::after{
    content:" *"; color:#ef4444; font-weight:700
  }

  /* ===== Typeahead NIK/Nama ===== */
  #nikNamaList{
    z-index:1050;
    max-height:240px;
    overflow:auto;
    display:none;
    top:100%; left:0;
  }
  #nikNamaBox .list-group-item{ cursor:pointer }
  #nikNamaClear{ font-size:.85rem }

  /* ===== Header polos (tanpa whitecard) ===== */
  .app-header{
    display:flex; align-items:center; gap:12px;
    padding:0 0 10px 0; color:#111;
  }
  .app-header .logo{height:36px}
  .app-header .title{margin:0;font-weight:700;line-height:1.2}
  .app-header .sub{display:block;opacity:.75;font-size:.85rem}

  /* ===== Ratakan lebar header & form ===== */
  .form-shell{ max-width:1200px; margin:0 auto; }

</style>
@endpush

{{-- ADDED: Sidebar styles (copy dari Home) --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  :root{
    --brand-red:#b41111;
    --glass: rgba(255,255,255,.84);
    --psb:#0ea5e9; --psb-soft:#e6f6fe;
    --sb-w: 300px; --sb-mini: 72px;
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

  /* ===== Back FAB (biru) ===== */
  .btn-back-fixed{
    position: fixed; top: 10px; left: 14px;
    width: 56px; height: 56px; border-radius: 50%;
    background:#fff; border:4px solid #03A9F4;
    display:flex; align-items:center; justify-content:center;
    z-index:2000; text-decoration:none;
    box-shadow:0 4px 12px rgba(0,0,0,.15);
    transition:transform .15s ease, box-shadow .15s ease;
  }
  .btn-back-fixed:hover{ transform: translateY(-1px); box-shadow: 0 8px 18px rgba(0,0,0,.22); }
  .btn-back-fixed .chev{ color:#03A9F4; font-size:28px; font-weight:700; line-height:1; }
</style>
@endpush
{{-- /ADDED --}}

@section('content')
<div class="container-fluid py-4">
  <div class="layout">

    <!-- @include('partials.sidebar') -->


    <main class="content">
      {{-- Back ke HOME --}}
      <a href="{{ url('/home') }}" class="btn-back-fixed" aria-label="Back"><span class="chev">&lsaquo;</span></a>

      {{-- ===== Header & Form dalam 1 wrapper agar SEJAJAR ===== --}}
      <div class="form-shell">
        {{-- HEADER polos (segaris dengan tepi form) --}}
        <div class="app-header">
          <img src="{{ asset('images/logo.png') }}" class="logo" alt="logo">
          <div>
            <h5 class="title mb-0">Presensi</h5>
            <small class="sub">Form Presensi (Check-in).</small>
          </div>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div>   @endif

        <div class="card card-glass p-4">
          <h5 class="mb-3">Form Presensi (Check-in)</h5>

          <form method="POST" action="{{ route('presensi.store') }}" id="checkinForm">
            @csrf
            <div class="row g-3">

              {{-- ================= NIK / NAMA (typeahead) ================= --}}
              <div class="col-md-6">
                <label class="form-label required">NIK / Nama</label>
                <div class="position-relative" id="nikNamaBox">
                  <input id="nikNamaSearch" class="form-control" type="text"
                         placeholder="Ketik NIK atau Nama..." autocomplete="off"
                         aria-autocomplete="list" aria-expanded="false">

                  {{-- yang dikirim ke server --}}
                  <input type="hidden" name="nik"  id="nikHidden"  value="{{ old('nik') }}">
                  <input type="hidden" name="nama" id="namaHidden" value="{{ old('nama') }}">

                  <button type="button" id="nikNamaClear"
                          class="btn btn-sm btn-link position-absolute top-0 end-0 mt-1 me-1 d-none">Clear</button>

                  <div id="nikNamaList" class="list-group position-absolute w-100 shadow-sm bg-white border"></div>

                  <div class="form-text">Ketik lalu pilih dari daftar (bisa cari NIK atau Nama).</div>
                </div>
              </div>
              {{-- ========================================================== --}}

              <div class="col-md-3">
                <label class="form-label required">STO</label>
                <select name="sto" id="sto" class="form-select" required>
                  <option value="">— pilih STO —</option>
                  @foreach($stoOptions as $s)
                    <option value="{{ $s }}" @selected(old('sto')===$s)>{{ $s }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label required">Nama Tim</label>
                <select name="team_name" id="team_name"
                        class="form-select"
                        data-url="{{ route('api.teams.by-sto') }}"
                        required disabled>
                  <option value="">— pilih tim —</option>
                </select>
                <div class="form-text">Hanya menampilkan tim yang belum penuh pada STO terpilih.</div>
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary">Check-in</button>
              </div>
            </div>
          </form>
        </div>
      </div> {{-- /form-shell --}}
    </main>
  </div>
</div>
@endsection

{{-- =================== DATA NIK–NAMA =================== --}}
<script>
/**
 * RAW = daftar teknisi dalam format: NIK<TAB>Nama
 * (tempel semua baris persis seperti ini)
 */
const RAW = `
16021169	SEPTIANTO
16870403	MUHAMMAD FADLI
16901344	DEDI ARIYANTO
16911069	M. ISMAIL
16952282	AGUSTONI
16981699	MUHAMMAD FAUZAN
16990460	ABDUL AZIZ TRIWIDODO
16991897	ERLANGGA AGUSRIANTO
15896196	ADI PUTRA
16962768	ARIYANTO
16001406	BENI SAPUTRA
16860593	DADANG USMAN
15896208	DODI FAJAR YANTO
16921359	IVAN FACHREZA
16981808	M RIDHO
16981702	R SURYO HARTONO
16011149	SEPTIAN YOGANTARA
16001277	ZAZALI NANDA
16022183	MUHAMMAD REYHAN
16030812	RUSDI HARYANTO
16994939	YOSEP GUMELAR
16031336	IVAN NAZERO
16060090	BILLY APRIANSYAH
16031337	PEBRI REZEKI SAPUTRA
16031092	MUHAMMAD RIDHO
16971349	RICKY TRI SAPUTRA
16921394	YEBI ADITYA PRAMANA
16910004	RIAN WAHYUDI
15990079	PARLIN HIDAYATULLAH
16910425	HENDRA
16880692	SUPRIADI
16002427	DIKI WAHYUDI
16963890	TIMBUL JAYA
15896219	HAFID ABDUROHMAN MUNDIR
16020708	GABRIEL CANDRIKA SHELVIAN
16910714	ASEP HARTONO
16973747	JERRY RAMADHAN
16942398	S. IMAN MUSTAKIM
16050143	MUHAMMAD RIYANTO
16040409	NUGROHO GEMILANG
16013554	M RENALDY W
16004626	TRI RENALDY
16050410	M.FIRDAUS
16031090	FERDI AMBAS
16040708	ABDUL HAKIM
16040509	KELVIN
16040562	DIMAS PRASETYO
16930321	EDO ADI SAPUTRO
16994994	ENGGA PRATAMA
16013432	MUHAMMAD BARLIN ISPAHEGOZSYAH
16953638	NOVAN TRI ADI SAPUTRA
16941204	MUHAMMAD ANDRE
16031089	RENALDI PRADA AGUNG
19830007	RATNO YULIANTO
19740021	PUJI WIWOKO
21000021	ARIEF BUDIMAN
21980044	FAJRI AGUSMAN. AS
19960079	EKO CHARLIS SIGITO
21000063	RIKI DWI ANUGRAH
20010081	MUHAMMAD ALIF JAMILUDIN
20921053	MUSTAKIM
20971235	ABU SANI
20010086	ANDRE SAPUTRA
19980248	IQBAL ARAFAH
19950088	ELVIS SAPUTRA
19930073	KGS. APANDI JAUHARI
20000077	WINARYUSUF
18890019	WAGI PRIYANTO
18840009	NOPIYANTO
23000020	YOLLAN PRATAMA SAPUTRA
19900031	ALI
19800024	SIIL SAHFITRI ALI
16973252	SATRIA GUNAWAN
16993098	FADLILAHI SAPUTRA
16004744	ERLANGGA
16995027	RAHMAT MUZAKI
16050464	ROBBY KURNIAWAN
16050463	AGIL MARDIANSYAH
16040741	FABBIO OKAN CORNELLIUS
16060117	AHMAD SENDI SAPUTRA
16060118	DEVIN PRAYOGA
16953637	SULTON PRIA P
16960465	LUKMAN
16850097	ISEN EFFENDI
16960494	MOHAMED ANDRI AL FATHAN
16041069	PUTRA SALAM SINDAPATI
16960166	RISWANTO
16900692	FRENGKY ALYAN S
16870144	RAHMATTULLOH
16994796	ANDI MARZUKI
16901465	ANGGERA
16790079	RONI ERSAN
16700164	MUHAMMAD
16830678	DODI DAMAIDI
16740222	JHON EMLIN
16730254	FERRY JUMARDY
21970041	RIZKI RAMADHAN
21960052	RIDO ILAHI
20990227	RANDY ARMADON
16020315	YOPI NOPRIANSYAH
16050437	DWI IQBAL PRADANA
16050435	FIKRI NOVALDO
15902750	ANDESA
16780276	JUMLI
16952396	RIZKY APRIZA PRASETYA PUTRA
16041083	TICO RAMADHANI
16921395	HERILANI
20910759	DESTHI GIANTRI
18970131	PUTRA NOVA KELANA
19890098	ROSMA WARDA
20970910	YOGI PAMUNGKAS
20950663	M. DWI NUGROHO
20980889	FAHMI IDRIS
20960014	HABIE DWI MUDAARTA
19950014	MUTIARA AMALIA
18970494	IRFANSYAH
20920865	JIMMY PUTRA, S. ST
19890127	AFRIZAL HARYONO
19840014	OCTAVIANSYAH AL RIDWAN
19800026	PAWARTO
19970087	MUHAMMAD CHARLES EFANDI
955070	FRASTIAN ERWANDY
18950137	MUHAMMAD DIKY SETIAWAN
20930790	MUHAMMAD NUR
19950236	ARFA MULYADI
20980929	REFZQI SAFEI
18930095	ADITYA RAMANDA
935503	MOCH IQBAL TAMARA
18880024	YOGA PRANATHA
955389	HELMI INDRAWAN YUS
18940241	RIZKA DWI SYAFITRI
18930321	DENDY FARIZI LUBIS
20980796	EVINDRA FAJAR RAMADHAN
18930481	IMAM SATRIA
18940113	R. HANGGA KRISNADI
20910756	SUNNY
20800030	SUPO GUNAWAN
18860002	DINO FERNANDO
18900218	M. RIZKY HARRITAMA
18910312	ELI MURDIANSYAH
18950915	SYARIFUDIN
20931315	FADHLI PRATAMA
19920181	DEKA ADDARI
19920089	HENDRI SUGARA
21990027	JAYA SATRIA
20990223	M. EKI LESMANA
20950677	ROMI ADMAISYAH
21990085	FIKRI TANJUNG
18860076	NOPEN
19970286	MUHAMMAT MUAMAR
18830012	WARSITO TEGUH WIBOWO
18890051	HANIFAH RIADI
18770008	WIYONO
18990346	RENDY SETIAWAN
18810003	DEDY
20970874	MGS. USMAN ISMAIL
20990072	ALFIAN SYAH FITRI
20910755	SYARIF HIDAYATULLAH
20970904	RAHMAD TRI WAHYUDI
18960258	M IMAM NURYANTO
18950611	HARRY GUNAWAN
20980802	ZAINAL ABIDIN NASUTION
18940382	RANGGA ADESTIA PRATAMA
20840071	TARISNA HADISIMA
19720005	HERLAN
18990344	IMAM RIZKY PRIMAYANI
18860016	ALVI NIZAR
18970263	DIANSYAH PRATAMA
21970042	ALAN ANGGARA
18890018	UJI ANDERTA SAPUTRA
18880010	ABDUL CHALID
18930230	OCHINTILASAN DEWI
19870062	JHON ALKAHFI
21030014	ANUGRA PERDANA
19970091	SINDU FAJRI
19930089	TRY SUTRISNO
20950827	AGA ADITYA PARTAMA
19770015	HARIS WAHYUDI
1982003	SUSILO WAHYONO
18960415	ACHMAD SURKATI
20020052	FARENZA FATHAM MUBIN
19970344	JUNAIDI
17950322	PRASETYO BR
20921051	NOVIAN HERMANSYAH
21990110	M. YURI MARWANSYAH
18960412	M. RONY PRATAMA
18920333	JOKO SUPRIANTO
21000058	ALDI SURYA PERDANA
19910029	M.AIDIL FITRA
20980797	GUNA SULAIMAN
20960812	RENO ABI MAYU
18980140	RONAL GANDA PARJUANGAN SINURAT
19800037	HENDRIK
22010194	DWI SANTOSO
18960256	IWAN HARTANTO
19970145	MUHAMMAD FANY WIJAYA
20800022	ROMANI
20951294	CHAIDIR WIJAYA
19820038	DWI FANDRIYANI E
19820046	R.M ALI
20951001	M. HARDIANSYAH
20010113	TRY RIFQI PEBIYANTO
20880145	ANDRIANT PRADIPTA
19870071	MERYANTO
18780004	MEDIA FITRA
18850001	AKHMAD SYAMSUDIN A. MD
20830041	DEDE IRAWAN
18880020	MUHAMMAD VRISTIAN KURNIANSAH
20970898	MUHAMMAD RICAD ARPANI
18750003	MUSDARYANTO
18960332	RELI AFRIADI
18850003	BENNY BERRYAWAN
19860069	GUNAWI
19800025	HENDRIK SAPUTRA
24010001	TANDRE HARIANTO SAPUTRA
24950001	RIO KURNIAWAN
24750001	SLAMET HARYADI
18780005	SYARIPUDIN
20951288	M.MUSLIM
19980025	MUHAMMAD AMY
18890205	FAUZI SAPUTRA
18970257	AZMIN RAIS
18900063	AHMAD ALI DEKAR
20010080	DWI SAPUTRA
19900178	NOVA KUSUMA PAHLAWAN
19740001	AGUS DEDI IRAWAN
20940865	KMS. MUKSIN
16001571	IBNU
19710002	EDI HADI YANTO
16681541	ROBBISROHLI
16691542	PANDU GUSTI WARDANA
20941077	FREDI AGUSTINUS
19920090	WARDI ARIZAL
18930228	ACHMAD SHOLIHAN
16972578	HOLBI SAPUTRA
20000079	YOS RIZAL
18930176	FERRY HARYONO
12345678	WIJO YULIUS SUSENO
16042117	TAUHID RHAMA DANIL
16720240	SARWONO
16850759	SAPRON ADI YANSAH
16830683	ERIK WITANTO
16993864	MUHAMMAD ANWAR PRAMU RAYA
16990012	ARIES SAPUTRA WICAKSANA
16710176	JOKO SUSILO
16760306	WAHIDIN
16004887	ARIEF BUDIMAN
16000165	M.AGUS IRYANSYAH
16890784	WAHYU IMAN SANTOSO
24920023	RACHMATULLAH
24980037	JUNIARDI DWI SAPUTRA
24980038	OKTAVIANI TRI PUTRI
24890016	YAYU DWI SRI AYU
24950040	ARUMING TIAS PUDYASTUTI
24980039	YOGI ANTO
24900020	INDO MAHARANI
18940093	IMAM SUBARKAH
1671162105800001	BUDIMAN KURNIAWAN
1671030210750006	IRWANSYAH
`.trim();

const MASTER_TEKNISI = RAW.split(/\r?\n/).map(l=>{
  const [nik, ...rest] = l.split(/\t+/);
  return { nik: (nik||'').trim(), nama: (rest.join(' ')||'').trim() };
}).filter(x=>x.nik && x.nama);
</script>
{{-- =================== /DATA NIK–NAMA =================== --}}

<script>
document.addEventListener('DOMContentLoaded', async () => {
  /* ====== Dropdown Tim by STO (hanya yang belum penuh) ====== */
  const stoSel  = document.getElementById('sto');
  const teamSel = document.getElementById('team_name');
  const apiUrl  = teamSel.dataset.url;

  async function loadTeams(preselect=null){
    const sto = stoSel.value;
    teamSel.innerHTML = '<option value="">— pilih tim —</option>';
    teamSel.disabled = true;
    if(!sto) return;

    try{
      const qs = new URLSearchParams();
      qs.set('sto', sto);
      qs.append('sto[]', sto);
      const res = await fetch(`${apiUrl}?${qs.toString()}`, { headers:{'Accept':'application/json'} });
      const raw = res.ok ? await res.json() : [];
      const list = Array.isArray(raw) ? raw : (raw.data || raw.items || []);
      let added = 0;

      list.forEach(it => {
        const name = it.text || it.name || it.team_name || it.nama_tim;
        const full = it.penuh === true;
        if (name && !full){ teamSel.add(new Option(name, name)); added++; }
      });

      if (!added) teamSel.add(new Option('— semua tim sudah penuh —',''));
    } catch {
      for (let i=1;i<=10;i++){
        const label = sto + String(i).padStart(2,'0');
        teamSel.add(new Option(label, label));
      }
    } finally {
      teamSel.disabled = false;
      if (preselect) teamSel.value = preselect;
    }
  }

  stoSel.addEventListener('change', () => loadTeams());

  @if(session('success'))
    alert(@json(session('success')));
    const f = document.getElementById('checkinForm');
    f.reset();
    teamSel.innerHTML = '<option value="">— pilih tim —</option>';
    teamSel.disabled = true;
    document.getElementById('nikHidden').value = '';
    document.getElementById('namaHidden').value = '';
    document.getElementById('nikNamaSearch').value = '';
    document.getElementById('nikNamaClear').classList.add('d-none');
  @endif

  const oldSto  = @json(old('sto'));
  const oldTeam = @json(old('team_name'));
  if (oldSto) {
    stoSel.value = oldSto;
    await loadTeams(oldTeam);
  }

  /* ====== Typeahead NIK/Nama ====== */
  const box   = document.getElementById('nikNamaBox');
  const input = document.getElementById('nikNamaSearch');
  const list  = document.getElementById('nikNamaList');
  const clear = document.getElementById('nikNamaClear');
  const hNik  = document.getElementById('nikHidden');
  const hNama = document.getElementById('namaHidden');

  const norm = s => (s||'').toLowerCase().normalize('NFKD');
  const search = (q,limit=20)=>{
    q = norm(q);
    if(!q) return [];
    return MASTER_TEKNISI
      .filter(p => norm(p.nik).includes(q) || norm(p.nama).includes(q))
      .slice(0,limit);
  };

  function render(items){
    list.innerHTML='';
    if(!items.length){
      list.style.display='none';
      input.setAttribute('aria-expanded','false');
      return;
    }
    items.forEach(p=>{
      const btn = document.createElement('button');
      btn.type='button';
      btn.className='list-group-item list-group-item-action d-flex justify-content-between align-items-center';
      btn.innerHTML = `<span><strong>${p.nik}</strong> — ${p.nama}</span>`;
      btn.addEventListener('click', ()=>choose(p));
      list.appendChild(btn);
    });
    list.style.display='block';
    input.setAttribute('aria-expanded','true');
  }

  function choose(p){
    input.value = `${p.nik} — ${p.nama}`;
    hNik.value = p.nik;
    hNama.value = p.nama;
    list.style.display='none';
    input.setAttribute('aria-expanded','false');
    clear.classList.remove('d-none');
  }

  function resetChoice(){
    input.value=''; hNik.value=''; hNama.value='';
    list.style.display='none'; input.setAttribute('aria-expanded','false');
    clear.classList.add('d-none');
    input.focus();
  }

  input.addEventListener('input', e=>{
    const q = e.target.value.trim();
    if (hNik.value && !q.startsWith(hNik.value)){
      hNik.value=''; hNama.value=''; clear.classList.add('d-none');
    }
    if(!q){
      list.style.display='none';
      input.setAttribute('aria-expanded','false');
      return;
    }
    render(search(q));
  });

  input.addEventListener('keydown', e=>{
    if(e.key === 'Enter'){
      const first = list.querySelector('.list-group-item');
      if(first){ e.preventDefault(); first.click(); }
    } else if (e.key === 'Escape'){
      list.style.display='none'; input.setAttribute('aria-expanded','false');
    }
  });

  document.addEventListener('click', e=>{
    if(!box.contains(e.target)){
      list.style.display='none';
      input.setAttribute('aria-expanded','false');
    }
  });

  clear.addEventListener('click', resetChoice);

  document.getElementById('checkinForm')?.addEventListener('submit', e=>{
    if(!hNik.value || !hNama.value){
      e.preventDefault();
      alert('Silakan pilih NIK/Nama dari daftar terlebih dahulu.');
    }
  });

  const oldNik=@json(old('nik'));
  const oldNama=@json(old('nama'));
  if(oldNik && oldNama){
    input.value=`${oldNik} — ${oldNama}`;
    hNik.value=oldNik;
    hNama.value=oldNama;
    clear.classList.remove('d-none');
  }
});
</script>

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
