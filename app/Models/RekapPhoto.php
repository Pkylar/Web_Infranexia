<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapPhoto extends Model
{
    protected $fillable = [
        'photo_path',
        'sto',
        'teknisi_nik',
        'teknisi_nama',
        'note',
        'uploaded_by',   // <-- tambahkan ini
    ];

    public function uploader()
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
