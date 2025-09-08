<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimTeknisi extends Model
{
    protected $table = 'tim_teknisis';

    // izinkan kolom ini di-mass assign
    protected $fillable = [
        'sto_code',
        'nama_tim',
        'nik_teknisi1',
        'nik_teknisi2',
        'status',
    ];
    // Atau alternatif longgar:
    // protected $guarded = [];
}
