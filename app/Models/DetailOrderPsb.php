<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrderPsb extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'date_created','workorder','sc_order_no','service_no','description','status_bima',
        'address','customer_name','contact_number','team_name',
        'order_status','sub_kendala','work_log','koordinat_survei',
        'validasi_eviden_kendala','nama_validator_kendala',
        'validasi_failwa_invalid','nama_validator_failwa','keterangan_non_valid',
        'sub_district','service_area','branch','wok','sto','produk','transaksi',
        'id_valins',
    ];

    protected $casts = [
        'date_created' => 'datetime',
    ];
}
