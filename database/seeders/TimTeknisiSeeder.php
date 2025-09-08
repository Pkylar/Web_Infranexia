<?php

// database/seeders/TimTeknisiSeeder.php
namespace Database\Seeders;

use App\Models\TimTeknisi;
use Illuminate\Database\Seeder;

class TimTeknisiSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil list STO dari config sendiri atau langsung array:
        $stos = ['PLJ','PGC','XYZ']; // ganti sesuai punyamu, atau tarik dari $stoAll

        foreach ($stos as $sto) {
            for ($i=1; $i<=10; $i++) {
                $nama = $sto . str_pad($i, 2, '0', STR_PAD_LEFT);
                TimTeknisi::firstOrCreate(
                    ['sto_code'=>$sto, 'nama_tim'=>$nama],
                    ['status'=>'AKTIF']
                );
            }
        }
    }
}

