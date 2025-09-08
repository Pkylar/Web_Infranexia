<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TerritoryMapping;

class TerritoryMappingSeeder extends Seeder
{
    public function run(): void
    {
        TerritoryMapping::truncate(); // kosongin dulu biar tidak dobel

        $rows = [
            // ===== SUMSEL =====
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Palembang','service_area'=>'PALEMBANG ILIR','sto'=>'PGC'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Palembang','service_area'=>'PALEMBANG ULU','sto'=>'SGB'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'LHT'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'LLG'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Palembang','service_area'=>'PALEMBANG ULU','sto'=>'SBU'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'TMO'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Musibanyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'MUB'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'TSS'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'PDP'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'TLK'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'PALEMBANG ULU','sto'=>'KTU'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Musibanyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'SKY'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'BATURAJA','sto'=>'MEM'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'BATURAJA','sto'=>'TAM'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'PBI'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'BET'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oku','service_area'=>'BATURAJA','sto'=>'BTA'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Palembang','service_area'=>'PALEMBANG ILIR','sto'=>'BKS'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oku','service_area'=>'BATURAJA','sto'=>'BLT'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oku','service_area'=>'BATURAJA','sto'=>'MUD'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oku','service_area'=>'BATURAJA','sto'=>'MPA'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'MUSIBANYUASIN','sto'=>'SRO'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Palembang','service_area'=>'PALEMBANG ULU','sto'=>'PLJ'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'PRABUMULIH','sto'=>'PBM'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Banyuasin','service_area'=>'PRABUMULIH','sto'=>'PDT'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oki','service_area'=>'PRABUMULIH','sto'=>'KAG'],
            ['sub_district'=>'SUMSEL','branch'=>'Bengkulu','wok'=>'Musi Rawas','service_area'=>'LINGGAU','sto'=>'SPP'],
            ['sub_district'=>'SUMSEL','branch'=>'Jambi','wok'=>'Sarolangun','service_area'=>'LINGGAU','sto'=>'MUR'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'PRABUMULIH','sto'=>'IDL'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'PRABUMULIH','sto'=>'TRA'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Prabumulih','service_area'=>'PRABUMULIH','sto'=>'TAB'],
            ['sub_district'=>'SUMSEL','branch'=>'Palembang','wok'=>'Oki','service_area'=>'PRABUMULIH','sto'=>'OKI'],

            // ===== BABEL (branch/wok/service_area belum ada → tampilkan STO dulu) =====
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'BYU'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'JBS'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'KBO'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'MGR'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'MTK'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'PGP'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'SLT'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'TBI'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'TJN'],
            ['sub_district'=>'BABEL','branch'=>null,'wok'=>null,'service_area'=>null,'sto'=>'TPL'],
        ];

        // insert sekaligus
        TerritoryMapping::insert($rows);
    }
}
