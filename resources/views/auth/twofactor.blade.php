@extends('layouts.app')

@section('content')
<div class="container" style="max-width:480px">
  <h3 class="mb-3">Verifikasi Two-Factor Authentication</h3>

  <form method="POST" action="{{ route('2fa.check') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Kode OTP (Google Authenticator)</label>
      <input type="text" name="one_time_password" class="form-control" required autofocus>
      @error('one_time_password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>
    <button class="btn btn-primary w-100">Verifikasi</button>
  </form>

  <div class="mt-3">
    <a href="{{ route('login') }}">&larr; Kembali ke login</a>
  </div>
</div>
@endsection
