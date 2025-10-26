@extends('layouts.app')

@section('content')
<div class="container" style="max-width:720px">
  <h3 class="mb-4">Keamanan Akun (Two-Factor Authentication)</h3>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if ($enabled)
    <div class="card mb-4">
      <div class="card-body">
        <h5 class="card-title">Status: Aktif </h5>
        <p class="text-muted mb-3">Two-Factor Authentication sedang aktif untuk akunmu.</p>

        <form method="POST" action="{{ route('2fa.disable') }}">
          @csrf
          <button class="btn btn-outline-danger"
                  onclick="return confirm('Nonaktifkan 2FA untuk akun ini?')">Nonaktifkan 2FA</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Recovery Codes</h5>
        <p class="text-muted">Simpan kode berikut di tempat aman. Bisa dipakai saat kehilangan HP.</p>
        @if (count($recoveryCodes))
          <ul class="list-group mb-3">
            @foreach ($recoveryCodes as $code)
              <li class="list-group-item">{{ $code }}</li>
            @endforeach
          </ul>
        @else
          <p class="text-muted">Belum ada recovery codes.</p>
        @endif

        <form method="POST" action="{{ route('2fa.recovery') }}">
          @csrf
          <button class="btn btn-outline-primary"
                  onclick="return confirm('Buat ulang recovery codes? Kode lama akan tidak berlaku.')">
            Generate Ulang Recovery Codes
          </button>
        </form>
      </div>
    </div>

  @else
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Aktifkan 2FA</h5>
        <ol>
          <li>Install <strong>Google Authenticator</strong> / <strong>Authy</strong> di HP.</li>
          <li>Scan QR code berikut atau masukkan secret secara manual.</li>
          <li>Masukkan 6-digit kode yang tampil untuk mengaktifkan.</li>
        </ol>

        @if ($inlineQr)
        <div class="mb-3">
            <img src="{{ $inlineQr }}" alt="QR 2FA" width="200" height="200">
        </div>
        <p class="text-muted">Atau masukkan secret/manual:</p>
        @endif

        @if ($secret)
        <p class="mb-3"><strong>Secret:</strong> <code>{{ $secret }}</code></p>
        @endif

        <form method="POST" action="{{ route('2fa.enable') }}" class="mt-3" style="max-width:420px">
          @csrf
          <div class="mb-3">
            <label class="form-label">Masukkan kode OTP</label>
            <input type="text" name="one_time_password" class="form-control" required>
            @error('one_time_password') <div class="text-danger mt-1">{{ $message }}</div> @enderror
          </div>
          <button class="btn btn-primary">Aktifkan 2FA</button>
        </form>
      </div>
    </div>
  @endif
</div>
@endsection
