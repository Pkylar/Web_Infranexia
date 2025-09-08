<?php

namespace App\Http\Controllers;

use App\Models\TimTeknisi;
use App\Models\TerritoryMapping;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TimTeknisiController extends Controller
{
    /** Ambil daftar STO dari mapping; fallback ke daftar statis jika kosong */
    private function stoOptions(): array
    {
        $list = TerritoryMapping::query()
            ->whereNotNull('sto')
            ->pluck('sto')
            ->filter(fn($v) => trim((string)$v) !== '')
            ->unique()
            ->sort()
            ->values()
            ->all();

        if (!empty($list)) return $list;

        // fallback default
        return [
            'PGC','SGB','LHT','LLG','PGA','SBU','TMO','MUB','TSS','PDP','TLK','KTU','SKY','MEM','TAM',
            'PBI','BET','BTA','BKS','BLT','MUD','MPA','SRO','PLJ','PBM','PDT','KAG','SPP','MUR','IDL',
            'TRA','TAB','OKI','BYU','JBS','KBO','MGR','MTK','PGP','SLT','TBI','TJN','TPL'
        ];
    }

public function index(Request $request)
{
    $q = TimTeknisi::query();

    // Filter STO
    if ($request->filled('sto')) {
        $q->where('sto_code', $request->string('sto'));
    }

    // Filter nama tim dari dropdown (opsional)
    if ($request->filled('team_name')) {
        $q->where('nama_tim', $request->string('team_name'));
    }

    // (opsional) pencarian bebas kalau kamu masih pakai input text "q"
    if ($request->filled('q')) {
        $kw = trim((string)$request->q);
        $q->where('nama_tim', 'like', '%'.$kw.'%');
    }

    $rows = $q->orderBy('sto_code')->orderBy('nama_tim')->paginate(20)->withQueryString();

    // --- penting: isi dropdown tim server-side bila STO dipilih ---
    $teamOptions = [];
    if ($request->filled('sto')) {
        $teamOptions = TimTeknisi::where('sto_code', $request->string('sto'))
            ->orderBy('nama_tim')
            ->pluck('nama_tim')
            ->unique()
            ->values()
            ->all();
    }

    return view('teknisi.index', [
        'rows'        => $rows,
        'stoOpts'     => $this->stoOptions(),
        'teamOptions' => $teamOptions,   // <-- dikirim ke view
    ]);
}



    public function create()
    {
        return view('teknisi.create', [
            'stoOpts' => $this->stoOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'sto_code'     => ['required', 'string', 'max:16', Rule::in($this->stoOptions())],
            'nama_tim'     => [
                'required','string','max:50',
                Rule::unique('tim_teknisis','nama_tim')->where(fn($q) => $q->where('sto_code', $request->sto_code)),
            ],
            'nik_teknisi1' => ['nullable','string','max:20'],
            'nik_teknisi2' => ['nullable','string','max:20','different:nik_teknisi1'],
        ]);

        $t = new TimTeknisi();
        $t->sto_code     = $request->sto_code;
        $t->nama_tim     = $request->nama_tim;
        $t->nik_teknisi1 = $request->nik_teknisi1 ?: null;
        $t->nik_teknisi2 = $request->nik_teknisi2 ?: null;
        $t->save();

        return redirect()->route('teknisi.index')->with('success','Tim berhasil dibuat.');
    }

    // ====== Pakai implicit model binding: parameter $teknisi ======
    public function edit(TimTeknisi $teknisi)
    {
        return view('teknisi.edit', [
            'row'     => $teknisi,
            'stoOpts' => $this->stoOptions(),
        ]);
    }

    public function update(Request $request, TimTeknisi $teknisi)
    {
        $request->validate([
            'sto_code'     => ['required', 'string', 'max:16', Rule::in($this->stoOptions())],
            'nama_tim'     => [
                'required','string','max:50',
                Rule::unique('tim_teknisis','nama_tim')
                    ->ignore($teknisi->id)
                    ->where(fn($q) => $q->where('sto_code', $request->sto_code)),
            ],
            'nik_teknisi1' => ['nullable','string','max:20'],
            'nik_teknisi2' => ['nullable','string','max:20','different:nik_teknisi1'],
        ]);

        $teknisi->sto_code     = $request->sto_code;
        $teknisi->nama_tim     = $request->nama_tim;
        $teknisi->nik_teknisi1 = $request->nik_teknisi1 ?: null;
        $teknisi->nik_teknisi2 = $request->nik_teknisi2 ?: null;
        $teknisi->save();

        return redirect()->route('teknisi.index')->with('success','Tim berhasil diperbarui.');
    }

    public function destroy(TimTeknisi $teknisi)
    {
        $teknisi->delete();
        return redirect()->route('teknisi.index')->with('success','Tim berhasil dihapus.');
    }
}
