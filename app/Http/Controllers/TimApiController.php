<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimTeknisi;

class TimApiController extends Controller
{
    public function teamsBySto(Request $req)
    {
        // dukung ?sto=PGC,LLG atau ?sto[]=PGC&sto[]=LLG
        $v = $req->input('sto', []);
        $stos = is_array($v) ? $v
               : (is_string($v) ? array_filter(array_map('trim', explode(',', $v))) : []);

        $q = TimTeknisi::query();
        if ($stos) $q->whereIn('sto_code', $stos);

        $rows = $q->orderBy('sto_code')
                  ->orderBy('nama_tim')
                  ->get(['sto_code','nama_tim','nik_teknisi1','nik_teknisi2']);

        // format ringan untuk frontend
        $data = $rows->map(fn($t) => [
            'id'    => $t->nama_tim,
            'text'  => $t->nama_tim,
            'sto'   => $t->sto_code,
            'penuh' => !empty($t->nik_teknisi1) && !empty($t->nik_teknisi2),
        ]);

        return response()->json($data);
    }
}
