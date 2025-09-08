{{-- resources/views/presensi/create.blade.php --}}
@extends('layouts.app')
@section('title','Presensi Teknisi')

@push('styles')
<style>
  .card-presensi{background:rgba(255,255,255,.95);border-radius:12px}
</style>
@endpush

@section('content')
<div class="container py-4">
  <h5 class="mb-3">Presensi Teknisi</h5>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

  <div class="card card-presensi shadow-sm">
    <div class="card-body">
      <form method="POST" action="{{ route('presensi.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">NIK Teknisi</label>
            <select name="nik" class="form-select" required>
              <option value="">— pilih teknisi —</option>
              @foreach($teknisis as $t)
                <option value="{{ $t->nik }}">{{ $t->nik }} — {{ $t->nama }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">STO Now</label>
            <select id="sto_now" name="sto_now" class="form-select" required>
              <option value="">— pilih STO —</option>
              @foreach($stoAll as $sto)
                <option value="{{ $sto }}">{{ $sto }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Nama Tim</label>
            <select id="tim_id" name="tim_id" class="form-select" disabled required>
              <option value="">— pilih tim —</option>
            </select>
            <div class="form-text">Maksimal 2 teknisi per tim.</div>
          </div>

          <div class="col-12">
            <label class="form-label">Catatan</label>
            <input type="text" name="catatan" class="form-control" placeholder="opsional">
          </div>
        </div>

        <div class="mt-3 d-flex justify-content-end">
          <button class="btn btn-primary" type="submit">Simpan Presensi</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
(function(){
  const stoSel = document.getElementById('sto_now');
  const timSel = document.getElementById('tim_id');

  stoSel?.addEventListener('change', async ()=>{
    timSel.innerHTML = '<option value="">— pilih tim —</option>';
    timSel.disabled = true;

    const sto = stoSel.value || '';
    if(!sto) return;

    try{
      const res = await fetch(`{{ route('api.teams.by-sto') }}?sto=${encodeURIComponent(sto)}`);
      const data = await res.json();
      data.forEach(it=>{
        const opt = new Option(it.text, it.id);
        if (it.penuh) { opt.disabled = true; opt.text += ' (penuh)'; }
        timSel.appendChild(opt);
      });
      timSel.disabled = false;
    }catch(e){ console.error(e); }
  });
})();
</script>
@endsection

