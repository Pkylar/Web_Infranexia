<?php

namespace App\Http\Controllers;

use App\Models\Teknisi;
use App\Models\TerritoryMapping;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeknisiController extends Controller
{
    /** Status yang dipakai di UI */
    private const STATUSES = ['AKTIF', 'NONAKTIF'];

    /** Ambil daftar STO dari master teknisi / mapping, dengan fallback statis */
    private function stoOptions(): array
    {
        $fromTeknisi = Teknisi::query()
            ->whereNotNull('base_sto')
            ->pluck('base_sto')
            ->filter(fn ($v) => trim((string)$v) !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
        if (!empty($fromTeknisi)) return $fromTeknisi;

        $fromMap = TerritoryMapping::query()
            ->whereNotNull('sto')
            ->pluck('sto')
            ->filter(fn ($v) => trim((string)$v) !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();
        if (!empty($fromMap)) return $fromMap;

        return [
            'PGC','SGB','LHT','LLG','PGA','SBU','TMO','MUB','TSS','PDP','TLK','KTU','SKY','MEM','TAM',
            'PBI','BET','BTA','BKS','BLT','MUD','MPA','SRO','PLJ','PBM','PDT','KAG','SPP','MUR','IDL',
            'TRA','TAB','OKI','BYU','JBS','KBO','MGR','MTK','PGP','SLT','TBI','TJN','TPL'
        ];
    }

    /** INDEX – daftar teknisi + filter + auto-search */
    public function index(Request $request)
    {
        // Kompatibel dengan name "sto" maupun "base_sto"
        $sto    = trim((string)($request->get('sto', $request->get('base_sto', ''))));
        $q      = trim((string)$request->get('q', ''));
        $status = trim((string)$request->get('status', ''));

        $rows = Teknisi::query()
            ->when($sto !== '',    fn($qq) => $qq->where('base_sto', $sto))
            ->when($status !== '', fn($qq) => $qq->where('status', $status))
            ->when($q !== '', function ($qq) use ($q) {
                $like = '%'.$q.'%';
                $qq->where(function ($sub) use ($like) {
                    $sub->where('nik', 'like', $like)
                        ->orWhere('nama',  'like', $like)
                        ->orWhere('mitra', 'like', $like);
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $stoOptions = $this->stoOptions();

        // NOTE: view kamu ada di folder "teknisi-master"
        return view('teknisi-master.index', [
            'rows'        => $rows,
            'stoOptions'  => $stoOptions,
            'stoOpts'     => $stoOptions, // kompatibel dgn blade lama
            'statuses'    => self::STATUSES,
        ]);
    }

    /** Endpoint saran untuk typeahead (JSON) */
    public function suggest(Request $request)
    {
        $q = trim((string)$request->get('q', ''));
        if ($q === '') return response()->json([]);

        $like = '%'.$q.'%';
        $items = Teknisi::query()
            ->select(['id','nik','nama','mitra','base_sto','status'])
            ->where(function ($qq) use ($like) {
                $qq->where('nik','like',$like)
                   ->orWhere('nama','like',$like)
                   ->orWhere('mitra','like',$like);
            })
            ->orderBy('nama')
            ->limit(20)
            ->get();

        // struktur ringan utk frontend
        return response()->json(
            $items->map(fn($t)=>[
                'id'   => $t->id,
                'nik'  => $t->nik,
                'nama' => $t->nama,
                'mitra'=> $t->mitra,
                'sto'  => $t->base_sto,
                'status'=>$t->status,
                'label'=> trim(($t->nik ? $t->nik.' — ' : '').$t->nama.($t->mitra ? " ({$t->mitra})" : '')),
            ])
        );
    }

    /* ===== CRUD tetap ===== */

    public function create()
    {
        $stoOptions = $this->stoOptions();

        return view('teknisi-master.create', [
            'stoOptions' => $stoOptions,   // baru
            'stoOpts'    => $stoOptions,   // kompatibel blade lama
            'statuses'   => self::STATUSES,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nik'      => ['required','string','max:30','unique:teknisis,nik'],
            'nama'     => ['required','string','max:120'],
            'mitra'    => ['nullable','string','max:120'],
            'base_sto' => ['nullable','string','max:10', Rule::in($this->stoOptions())],
            'status'   => ['required','string', Rule::in(self::STATUSES)],
        ]);

        Teknisi::create($request->only('nik','nama','mitra','base_sto','status'));
        return redirect()->route('registrasi-teknisi.index')->with('success','Teknisi berhasil ditambahkan.');
    }

    public function edit(Teknisi $teknisi)
    {
        return view('teknisi-master.edit', [
            'row'        => $teknisi,
            'stoOptions' => $this->stoOptions(),
            'statuses'   => self::STATUSES,
        ]);
    }

    public function update(Request $request, Teknisi $teknisi)
    {
        $request->validate([
            'nik'      => ['required','string','max:30', Rule::unique('teknisis','nik')->ignore($teknisi->id)],
            'nama'     => ['required','string','max:120'],
            'mitra'    => ['nullable','string','max:120'],
            'base_sto' => ['nullable','string','max:10', Rule::in($this->stoOptions())],
            'status'   => ['required','string', Rule::in(self::STATUSES)],
        ]);

        $teknisi->update($request->only('nik','nama','mitra','base_sto','status'));
        return redirect()->route('registrasi-teknisi.index')->with('success','Teknisi berhasil diperbarui.');
    }

    public function destroy(Teknisi $teknisi)
    {
        $teknisi->delete();
        return redirect()->route('registrasi-teknisi.index')->with('success','Teknisi dihapus.');
    }

    public function lookup(Request $request)
    {
        $nik = trim((string)$request->get('nik', ''));
        $row = null;

        if ($nik !== '') {
            $row = Teknisi::query()->where('nik', $nik)->first();
        }

        return view('teknisi-master.lookup', [
            'nik' => $nik,
            'row' => $row,
        ]);
    }

}
