{{-- Per-page & pagination (atas) --}}
<div class="listbar d-flex flex-wrap align-items-center justify-content-between mb-2">
  <form method="GET" action="{{ route('detail-order-psb.index') }}" class="d-flex align-items-center gap-2">
    @foreach(request()->except(['per_page','page']) as $k => $v)
      @if(is_array($v))
        @foreach($v as $vv)<input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">@endforeach
      @else
        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
      @endif
    @endforeach
    <span class="me-1">Show</span>
    <select name="per_page" class="form-select" style="width:120px">
      @foreach($allowedPerPage as $opt)
        <option value="{{ $opt }}" {{ (int)($perPage ?? 10) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
      @endforeach
    </select>
    <span>rows</span>
  </form>
  <div class="ms-auto">
    {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
</div>

{{-- TABEL --}}
<div class="card shadow-sm">
  <div class="scroll-x">
    <table class="table table-bordered psb-table mb-2">
      <thead>
        <tr>
          @foreach($columns as $col)<th>{{ $col }}</th>@endforeach
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $r)
          <tr>
            <td>{{ optional($r->date_created)->format('Y-m-d H:i') }}</td>
            <td>{{ $r->workorder }}</td>
            <td>{{ $r->sc_order_no }}</td>
            <td>{{ $r->service_no }}</td>
            <td>{{ $r->description }}</td>
            <td>{{ $r->status_bima }}</td>
            <td>{{ $r->address }}</td>
            <td>{{ $r->customer_name }}</td>
            <td>{{ $r->contact_number }}</td>
            <td>{{ $r->team_name }}</td>
            <td>{{ $r->order_status }}</td>
            <td>{{ $r->sub_kendala }}</td>
            <td class="worklog-cell">{{ $r->work_log }}</td>
            <td>{{ $r->koordinat_survei }}</td>
            <td>{{ $r->validasi_eviden_kendala }}</td>
            <td>{{ $r->nama_validator_kendala }}</td>
            <td>{{ $r->validasi_failwa_invalid }}</td>
            <td>{{ $r->nama_validator_failwa }}</td>
            <td>{{ $r->keterangan_non_valid }}</td>
            <td>{{ $r->sub_district }}</td>
            <td>{{ $r->service_area }}</td>
            <td>{{ $r->branch }}</td>
            <td>{{ $r->wok }}</td>
            <td>{{ $r->sto }}</td>
            <td>{{ $r->produk }}</td>
            <td>{{ $r->transaksi }}</td>
            <td>{{ $r->id_valins }}</td>
            <td>
              <div class="d-flex flex-column gap-1">
                <a href="{{ route('detail-order-psb.edit',$r->id) }}" class="btn btn-xxs btn-edit w-100">Edit</a>
                <form action="{{ route('detail-order-psb.destroy',$r->id) }}" method="POST" onsubmit="return confirm('Hapus baris ini?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-xxs btn-del w-100">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="{{ count($columns)+1 }}" class="text-center text-muted">Belum ada data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="d-flex justify-content-end">
    {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
</div>
