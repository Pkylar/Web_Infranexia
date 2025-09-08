<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teknisi extends Model
{
    protected $table = 'teknisis';

    // Cocokkan dengan kolom di migration kamu
    protected $fillable = ['nik','nama','mitra','base_sto','foto_path','status'];

    // Migration kamu punya timestamps() → biarkan true (default)
    // Hapus properti $timestamps = false;

    // Backward-compat: jika dulu kamu pakai $teknisi->foto, ini akan tetap jalan
    protected $appends = ['foto'];
    public function getFotoAttribute(): ?string
    {
        return $this->foto_path;
    }
}
