{{-- Quick Edit Modal --}}
<div class="modal fade" id="qeModal" tabindex="-1" aria-labelledby="qeLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable">
    <form id="quickEditForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qeLabel">Quick Edit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="qe-id">

        <div class="mb-3">
          <label class="form-label">STO</label>
          <input type="text" class="form-control" id="qe-sto" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Team</label>
          <select class="form-select" id="qe-team">
            <option value="">— pilih tim —</option>
          </select>
          <div class="form-text">Status hanya bisa diubah bila tim sudah dipilih.</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Order Status</label>
          <select class="form-select" id="qe-status" disabled>
            <option value="">— pilih status —</option>
            <option value="OPEN">OPEN</option>
            <option value="SURVEI">SURVEI</option>
            <option value="REVOKE SC">REVOKE SC</option>
            <option value="PROGRES">PROGRES</option>
            <option value="AC">AC</option>
            <option value="CLOSE">CLOSE</option>
            <option value="KENDALA">KENDALA</option>
          </select>
        </div>

        <div class="mb-3 d-none" id="qe-subkendala-wrap">
          <label class="form-label">Sub Kendala</label>
          <select class="form-select" id="qe-subkendala">
            <option value="">— pilih sub kendala —</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" id="qe-desc" rows="3" placeholder="(optional)"></textarea>
        </div>

        <div class="alert alert-danger d-none" id="qe-error"></div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary" id="qe-save">Save</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
(function(){
  const SUBS_RAW = {!! json_encode($subKendalaOpts ?? []) !!}; // "KENDALA TEKNIK|ODP FULL"
  const apiTeamsUrl = @json(route('api.teams.by-sto'));         // pastikan route ini ada (GET ?sto=XXX -> [{"text":"Tim A"},...])
  const updateUrlTpl = @json(route('detail-order-psb.quick-update', ['order' => '__ID__']));
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const $ = (s, c=document)=>c.querySelector(s);

  let qeModal, qeForm, qeId, qeSto, qeTeam, qeStatus, qeSubWrap, qeSub, qeDesc, qeErr;

  function refs(){
    qeForm   = $('#quickEditForm');
    qeId     = $('#qe-id');
    qeSto    = $('#qe-sto');
    qeTeam   = $('#qe-team');
    qeStatus = $('#qe-status');
    qeSubWrap= $('#qe-subkendala-wrap');
    qeSub    = $('#qe-subkendala');
    qeDesc   = $('#qe-desc');
    qeErr    = $('#qe-error');
  }

  async function loadTeams(sto, selected=''){
    qeTeam.innerHTML = '<option value="">— pilih tim —</option>';
    qeTeam.disabled = true;
    try{
      const res = await fetch(`${apiTeamsUrl}?sto=${encodeURIComponent(sto)}`, {headers:{'Accept':'application/json'}});
      const js  = res.ok ? await res.json() : [];
      const items = Array.isArray(js) ? js : (js.data || js.items || []);
      for (const it of items){
        const name = it.text || it.name || it.team_name || it.nama_tim;
        if(name) qeTeam.add(new Option(name, name, false, name===selected));
      }
    }catch(e){}
    qeTeam.disabled = false;
  }

  function showSub(selected=''){
    qeSub.innerHTML = '<option value="">— pilih sub kendala —</option>';
    for (const full of SUBS_RAW){
      const label = full.split('|').slice(1).join(' | ');
      qeSub.add(new Option(label, full, false, full===selected));
    }
    qeSubWrap.classList.remove('d-none');
  }
  function hideSub(){
    qeSubWrap.classList.add('d-none');
    qeSub.innerHTML = '<option value="">— pilih sub kendala —</option>';
  }

  function openQuick(row){
    refs();
    qeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('qeModal'));

    const id   = row.dataset.id;
    const sto  = row.dataset.sto || '';
    const team = row.dataset.team || '';
    const stat = row.dataset.status || ''; // bisa "OPEN" atau "KENDALA TEKNIK|..."
    const desc = row.dataset.desc || '';

    qeErr.classList.add('d-none'); qeErr.textContent = '';
    qeId.value = id; qeSto.value = sto; qeDesc.value = desc;

    loadTeams(sto, team);
    qeStatus.disabled = !team;

    if (stat.startsWith('KENDALA')){
      qeStatus.value = 'KENDALA';
      showSub(stat);
    }else{
      qeStatus.value = stat || '';
      hideSub();
    }

    qeTeam.onchange   = ()=>{ qeStatus.disabled = !qeTeam.value; };
    qeStatus.onchange = ()=>{ (qeStatus.value === 'KENDALA') ? showSub('') : hideSub(); };
    qeForm.onsubmit   = submitQuick;

    qeModal.show();
  }

  async function submitQuick(e){
    e.preventDefault();
    qeErr.classList.add('d-none'); qeErr.textContent = '';

    const id   = $('#qe-id').value;
    const url  = updateUrlTpl.replace('__ID__', id);
    const team = $('#qe-team').value || '';
    const st   = $('#qe-status').value || '';
    const sub  = (st === 'KENDALA') ? ($('#qe-subkendala').value || '') : '';
    const desc = $('#qe-desc').value || '';

    const body = new URLSearchParams();
    body.append('_method','PATCH');
    if(team) body.append('team_name', team);
    if(st)   body.append('order_status', st);
    if(sub)  body.append('sub_kendala', sub);
    body.append('description', desc);

    try{
      const res = await fetch(url, {method:'POST', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}, body});
      const data = await res.json().catch(()=>({}));

      if(!res.ok || !data.ok){
        qeErr.textContent = data.message || 'Gagal menyimpan.';
        qeErr.classList.remove('d-none');
        return;
      }

      // update baris di tabel (kalau ada)
      const row = document.getElementById('row-'+id);
      if(row){
        row.querySelector('.td-team')?.replaceChildren(document.createTextNode(data.row.team_name || ''));
        row.querySelector('.td-status')?.replaceChildren(document.createTextNode(data.row.order_status || ''));
        row.querySelector('.td-subkendala')?.replaceChildren(document.createTextNode(data.row.sub_kendala || ''));
        row.querySelector('.td-description')?.replaceChildren(document.createTextNode(data.row.description || ''));
        if (data.row.work_log) row.querySelector('.td-worklog')?.replaceChildren(document.createTextNode(data.row.work_log));

        row.dataset.team = data.row.team_name || '';
        row.dataset.status = data.row.order_status || '';
        row.dataset.subkendala = data.row.sub_kendala || '';
        row.dataset.desc = data.row.description || '';
      }

      bootstrap.Modal.getInstance(document.getElementById('qeModal'))?.hide();
    }catch(err){
      qeErr.textContent = 'Gagal menyimpan (network).';
      qeErr.classList.remove('d-none');
    }
  }

  // delegasi: tombol .btn-quick-edit
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-quick-edit');
    if(!btn) return;
    const id = btn.getAttribute('data-id');
    const row = document.getElementById('row-'+id);
    if(row) openQuick(row);
  });
})();
</script>
@endpush
