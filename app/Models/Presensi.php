<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensis';

    protected $fillable = [
        // 'tim_id', // SUDAH TIDAK DIPAKAI
        'nik',
        'nama',
        'sto_now',
        'team_name',
        'checked_in_at',
        'checked_out_at', // kalau ada kolom ini di tabelmu; kalau tidak, boleh dihapus
    ];

    protected $casts = [
        'checked_in_at'  => 'datetime',
        'checked_out_at' => 'datetime',
    ];
}
