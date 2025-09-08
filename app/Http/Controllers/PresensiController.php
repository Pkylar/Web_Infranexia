<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\TimTeknisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    /**
     * Tampilkan form check-in dengan daftar STO.
     */
    public function create()
    {
        // Ambil daftar STO dari tabel tim_teknisis (distinct sto_code)
        $stoOptions = TimTeknisi::query()
            ->select('sto_code')
            ->distinct()
            ->orderBy('sto_code')
            ->pluck('sto_code')
            ->filter()
            ->values()
            ->all();

        // Fallback kalau tabel tim_teknisis belum ada datanya
        if (empty($stoOptions)) {
            $stoOptions = [
                'PGC','SGB','LHT','LLG','PGA','SBU','TMO','MUB','TSS','PDP','TLK','KTU','SKY','MEM','TAM',
                'PBI','BET','BTA','BKS','BLT','MUD','MPA','SRO','PLJ','PBM','PDT','KAG','SPP','MUR','IDL',
                'TRA','TAB','OKI','BYU','JBS','KBO','MGR','MTK','PGP','SLT','TBI','TJN','TPL',
            ];
        }

        return view('presensi.checkin', compact('stoOptions'));
    }

    /**
     * Simpan check-in teknisi.
     *
     * Catatan: Tabel `presensis` TIDAK lagi memiliki kolom tim_id.
     * Jadi kita hanya simpan: nik, nama, sto_now, team_name, checked_in_at.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nik'       => ['required', 'string', 'max:50'],
            'nama'      => ['required', 'string', 'max:191'],
            'sto'       => ['required', 'string', 'max:50'],
            'team_name' => ['required', 'string', 'max:50'],
        ]);

        try {
            $message = DB::transaction(function () use ($data) {
                // Lock tim agar perhitungan slot aman
                $team = TimTeknisi::where('sto_code', $data['sto'])
                    ->where('nama_tim', $data['team_name'])
                    ->lockForUpdate()
                    ->first();

                if (!$team) {
                    throw new \RuntimeException('Tim tidak ditemukan untuk STO tersebut.');
                }

                // Pengisian slot tim (maks 2 orang)
                $nik = $data['nik'];

                // Jika nik sudah terdaftar di salah satu slot, anggap idempotent
                if ($team->nik_teknisi1 === $nik || $team->nik_teknisi2 === $nik) {
                    // do nothing, lanjut simpan presensi
                } else {
                    if (empty($team->nik_teknisi1)) {
                        $team->nik_teknisi1 = $nik;
                        $team->save();
                    } elseif (empty($team->nik_teknisi2)) {
                        $team->nik_teknisi2 = $nik;
                        $team->save();
                    } else {
                        throw new \RuntimeException('FULL'); // tim sudah 2 orang
                    }
                }

                // Insert presensi TANPA tim_id (karena kolomnya sudah di-drop)
                Presensi::create([
                    'nik'           => $data['nik'],
                    'nama'          => $data['nama'],
                    'sto_now'       => $data['sto'],
                    'team_name'     => $data['team_name'],
                    'checked_in_at' => now(),
                ]);

                return "Teknisi {$data['nama']} ({$data['nik']}) berhasil presensi di tim {$data['team_name']} (STO {$data['sto']}).";
            });

            return redirect()->route('presensi.checkin')->with('success', $message);
        } catch (\RuntimeException $e) {
            $msg = $e->getMessage() === 'FULL'
                ? 'Tim ini sudah penuh (maks 2 teknisi).'
                : $e->getMessage();

            return redirect()->route('presensi.checkin')->with('error', $msg)->withInput();
        } catch (\Throwable $e) {
            return redirect()->route('presensi.checkin')->with('error', 'Gagal presensi: '.$e->getMessage())->withInput();
        }
    }
}
