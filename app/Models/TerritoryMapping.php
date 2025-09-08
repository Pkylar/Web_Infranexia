<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerritoryMapping extends Model
{
    protected $fillable = [
        'sub_district',
        'branch',
        'wok',
        'service_area',
        'sto',
    ];
}
