@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Tambah Data Baru</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('add-data.store') }}">
        @csrf

        <div class="mb-3">
            <label for="service_area" class="form-label">Service Area</label>
            <input type="text" class="form-control" id="service_area" name="service_area" required>
        </div>

        <div class="mb-3">
            <label for="sto" class="form-label">STO</label>
            <input type="text" class="form-control" id="sto" name="sto" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="">-- Pilih Status --</option>
                <option>Open</option>
                <option>Follow Up</option>
                <option>Close</option>
                <option>Kendala</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="teknisi" class="form-label">Teknisi</label>
            <input type="text" class="form-control" id="teknisi" name="teknisi">
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('home') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
