<?php

namespace App\Http\Controllers;

use App\Models\DetailOrderPsb;
use App\Models\TerritoryMapping;
use App\Models\TimTeknisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

// ==== Tambahan untuk dukung Excel → CSV ====
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv as CsvWriter;

class DetailOrderPSBController extends Controller
{
    /* =================== MASTER OPTIONS =================== */

    // 7 opsi utama
    private array $orderStatusMain = [
        'OPEN','SURVEI','REVOKE SC','PROGRES',
        'KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA',
        'AC','CLOSE'
    ];

    // Sub Kendala (muncul hanya jika order_status=KENDALA)
    private array $subKendalaOptions = [
        'KENDALA PELANGGAN|PENDING|PENDING > 1 HARI SALPEN OK',
        'KENDALA PELANGGAN|PENDING|PENDING > 1 HARI SALPEN NOK',
        'KENDALA PELANGGAN|PENDING|PENDING 1 HARI SALPEN OK',
        'KENDALA PELANGGAN|PENDING|PENDING 1 HARI SALPEN NOK',
        'KENDALA PELANGGAN|BATAL',
        'KENDALA PELANGGAN|ALAMAT TIDAK DITEMUKAN',
        'KENDALA PELANGGAN|INDIKASI CABUT PASANG',
        'KENDALA PELANGGAN|DOUBLE INPUT',
        'KENDALA PELANGGAN|GANTI PAKET',
        'KENDALA PELANGGAN|RUMAH KOSONG',
        'KENDALA PELANGGAN|KENDALA DEPOSIT',
        'KENDALA PELANGGAN|CP RNA',
        'KENDALA PELANGGAN|KENDALA PERANGKAT',
        'KENDALA PELANGGAN|PELANGGAN MASIH RAGU',
        'KENDALA TEKNIK|ODP JAUH',
        'KENDALA TEKNIK|ODP FULL',
        'KENDALA TEKNIK|KENDALA JALUR/RUTE TARIKAN',
        'KENDALA TEKNIK|CROSS JALAN',
        'KENDALA TEKNIK|ODP LOSS',
        'KENDALA TEKNIK|ODP RETI',
        'KENDALA TEKNIK|SALAH TAGGING',
        'KENDALA TEKNIK|KENDALA MATERIAL/NTE|ONT',
        'KENDALA TEKNIK|KENDALA MATERIAL/NTE|STB',
        'KENDALA TEKNIK|KENDALA MATERIAL/NTE|ONT&STB',
        'KENDALA TEKNIK|KENDALA MATERIAL/NTE|DC',
        'KENDALA TEKNIK|TIANG|ADA SPACE',
        'KENDALA TEKNIK|TIANG|TIDAK ADA SPACE TANAM',
        'KENDALA TEKNIK|LIMITASI ONU',
        'KENDALA TEKNIK|KENDALA BERULANG',
        'KENDALA TEKNIK|ODP NODE-B',
        'KENDALA TEKNIK|ODP BELUM GO LIVE',
        'KENDALA TEKNIK|ODP GENDONG',
        'KENDALA TEKNIK|ODP RUSAK',
        'KENDALA TEKNIK|KENDALA IKG/IKR',
        'KENDALA SISTEM|FALLOUT ACTIVATION',
        'KENDALA SISTEM|FALLOUT UIM',
        'KENDALA SISTEM|PELURUSAN SDI',
        'KENDALA SISTEM|REVOKE BELUM PI',
        'KENDALA SISTEM|IT TOOLS GANGGUAN',
        'KENDALA LAINNYA|HUJAN',
        'KENDALA LAINNYA|MATI LISTRIK',
        'KENDALA LAINNYA|PERLU INPUT ULANG',
    ];

    private array $validasiOptions = ['VALID','NON VALID','BELUM DI VALIDASI'];

    // master fallback (dipakai jika mapping belum lengkap)
    private array $subDistrictOptions = ['SUMSEL','BABEL'];
    private array $branchOptions      = ['Palembang','Bengkulu','Jambi'];
    private array $wokOptions         = ['Palembang','Musi Rawas','Musibanyuasin','Prabumulih','Banyuasin','Oku','Oki','Sarolangun'];
    private array $serviceAreaOptions = ['PALEMBANG ILIR','PALEMBANG ULU','LINGGAU','MUSIBANYUASIN','BATURAJA','PRABUMULIH'];
    private array $stoOptions         = [
        'PGC','SGB','LHT','LLG','PGA','SBU','TMO','MUB','TSS','PDP','TLK','KTU','SKY','MEM','TAM',
        'PBI','BET','BTA','BKS','BLT','MUD','MPA','SRO','PLJ','PBM','PDT','KAG','SPP','MUR','IDL',
        'TRA','TAB','OKI','BYU','JBS','KBO','MGR','MTK','PGP','SLT','TBI','TJN','TPL'
    ];
    private array $produkOptions     = ['INDIHOME','INDIBIZ','NNI','TRANSPORT','VULA','BITSTREAM','ASTINET'];
    private array $transaksiOptions  = ['CREATE','MODIFY','MIGRATE','DISCONNECT','PDA'];

    /* =================== Kolom Tabel (header UI & export) =================== */
    private array $columns = [
        'Date Created','Workorder','SC Order No/Track ID/CSRM No','Service No.','Description',
        'Status bima','Address','Customer Name','Contact Number','Team Name',
        'Order Status','Sub Kendala',
        'Work Log','Koordinat Survei','Validasi Eviden Kendala','Nama Validator Kendala',
        'Validasi Failwa / Invalid Survey','Nama Validator Failwa','Keterangan Non Valid',
        'Sub District','Service Area','Branch','WOK','STO','Produk','Transaksi',
        'ID Valins',
    ];

    // Kolom yang boleh diimport
    private array $importableFields = [
        'workorder','sc_order_no','service_no','description','status_bima','address',
        'customer_name','contact_number','team_name','order_status','sub_kendala','koordinat_survei',
        'validasi_eviden_kendala','nama_validator_kendala','validasi_failwa_invalid',
        'nama_validator_failwa','keterangan_non_valid',
        // NEW:
        'sub_district','service_area','branch','wok','sto','produk','transaksi',
        'id_valins','date_created',
    ];

    /* =================== LIST =================== */
    public function index(Request $request)
    {
        $q = DetailOrderPsb::query();

        // nilai territory filter
        $sd = trim((string)$request->input('sub_district', ''));
        $sa = trim((string)$request->input('service_area', ''));
        $br = trim((string)$request->input('branch', ''));
        $wk = trim((string)$request->input('wok', ''));

        // date range
        if ($request->filled('date_from')) {
            $q->where('date_created', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }
        if ($request->filled('date_to')) {
            $q->where('date_created', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        // contains filters (team_name DIHAPUS dari daftar contains)
        foreach ([
            'workorder','sc_order_no','service_no','description','status_bima','address',
            'customer_name','contact_number','work_log','koordinat_survei',
            'nama_validator_kendala','nama_validator_failwa','keterangan_non_valid','id_valins',
        ] as $field) {
            if ($request->filled($field)) {
                $q->where($field, 'like', '%'.$request->input($field).'%');
            }
        }

        // Team Name: exact match dari dropdown
        if ($request->filled('team_name')) {
            $q->where('team_name', $request->team_name);
        }

        // ==== Order Status + Sub Kendala (kompatibel data lama) ====
        if ($request->filled('order_status')) {
            $os = $request->input('order_status');
            if ($os === 'KENDALA') {
                if ($request->filled('sub_kendala')) {
                    $sk = $request->input('sub_kendala');
                    $q->where(function($qq) use ($sk) {
                        $qq->where('sub_kendala', $sk)
                           ->orWhere('order_status', $sk); // data lama
                    });
                } else {
                    $q->where(function($qq) {
                        $qq->where('order_status', 'KENDALA')
                           ->orWhere('order_status', 'like', 'KENDALA%') // data lama
                           ->orWhereNotNull('sub_kendala');
                    });
                }
            } elseif (is_string($os) && str_starts_with($os, 'KENDALA ')) {
                $sk = $os;
                $q->where(function($qq) use ($sk) {
                    $qq->where('sub_kendala', $sk)->orWhere('order_status', $sk);
                });
            } else {
                $q->where('order_status', $os);
            }
        }

        // ==== Territory rule: Service Area vs Branch/WOK saling mengunci ====
        $mode = 'neutral';
        if ($sa !== '') {
            $mode = 'by_sa';
            $br = $wk = '';
        } elseif ($br !== '' || $wk !== '') {
            $mode = 'by_bw';
            $sa = '';
        }

        if ($mode === 'by_sa') {
            $q->where('service_area', $sa);
        } else {
            if ($br !== '') $q->where('branch', $br);
            if ($wk !== '') $q->where('wok', $wk);
        }

        // eq filters lain
        foreach (['validasi_eviden_kendala','validasi_failwa_invalid','produk','transaksi','sub_district'] as $eq) {
            if ($request->filled($eq)) $q->where($eq, $request->input($eq));
        }

        // STO multi (checkbox)
        if ($request->filled('sto')) {
            $chosens = array_filter((array) $request->input('sto'));
            if ($chosens) {
                $q->where(function($qq) use ($chosens) {
                    foreach ($chosens as $code) {
                        $qq->orWhere('sto', 'like', '%'.$code.'%');
                    }
                });
            }
        }

        $allowedPerPage = [10, 50, 100, 500, 1000];
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, $allowedPerPage, true)) $perPage = 10;

        $rows = $q->latest('date_created')->paginate($perPage)->withQueryString();

        /* ====== opsi dinamis dari mapping (SELURUHNYA dari DB) ====== */

        $clean = function ($items) {
            return collect($items)->filter(function ($v) {
                if ($v === null) return false;
                $v = trim((string)$v);
                if ($v === '') return false;
                $u = strtoupper($v);
                if (str_contains($u, '#VALUE!')) return false;
                if (str_contains($u, 'TOTAL')) return false;
                return true;
            })->unique()->sort()->values()->all();
        };

        $base = TerritoryMapping::query()
            ->when($sd !== '', fn($qq) => $qq->where('sub_district', $sd));

        // Sub-District: penuh (distinct)
        $subDistrictOpts = $clean(
            (clone $base)->pluck('sub_district')->all()
        );

        // Service Area
        $saQ = (clone $base);
        if ($mode === 'by_bw') {
            if ($br !== '') $saQ->where('branch', $br);
            if ($wk !== '') $saQ->where('wok', $wk);
        }
        $serviceAreaOpts = $clean($saQ->pluck('service_area')->all());

        // Branch
        $brQ = (clone $base);
        if ($mode === 'by_sa' && $sa !== '') {
            $brQ->where('service_area', $sa);
        }
        $branchOpts = $clean($brQ->pluck('branch')->all());

        // WOK
        $wkQ = (clone $base);
        if ($mode === 'by_sa' && $sa !== '') {
            $wkQ->where('service_area', $sa);
        }
        if ($mode === 'by_bw' && $br !== '') {
            $wkQ->where('branch', $br);
        }
        $wokOpts = $clean($wkQ->pluck('wok')->all());

        // STO ikut filter aktif
        $stoQ = (clone $base);
        if ($mode === 'by_sa' && $sa !== '') {
            $stoQ->where('service_area', $sa);
        }
        if ($mode === 'by_bw') {
            if ($br !== '') $stoQ->where('branch', $br);
            if ($wk !== '') $stoQ->where('wok', $wk);
        }
        $stoList = $clean($stoQ->orderBy('sto')->pluck('sto')->all());
        $stoOpts = $stoList ?: $this->stoOptions;

        /* ====== kirim full mapping ke view utk JS (territory flow) ====== */
        $territories = TerritoryMapping::select('sub_district', 'branch', 'wok', 'service_area', 'sto')
            ->orderBy('sub_district')->orderBy('branch')->orderBy('wok')->orderBy('service_area')->get()
            ->map(fn ($r) => [
                'sub_district' => $r->sub_district,
                'branch'       => $r->branch,
                'wok'          => $r->wok,
                'service_area' => $r->service_area,
                'sto'          => $r->sto,
            ])->all();

        // master lain (static)
        $columns         = $this->columns;
        $statusOptions   = $this->orderStatusMain;   // 7 opsi
        $subKendalaOpts  = $this->subKendalaOptions; // dropdown conditional
        $validasiOptions = $this->validasiOptions;

        $produkOpts      = $this->produkOptions;
        $transaksiOpts   = $this->transaksiOptions;

        // flags untuk Blade (disable/enable)
        $disableSA = ($mode === 'by_bw');
        $disableBW = ($mode === 'by_sa');

        /* === kalau AJAX (live search / pagination tanpa reload) kirim partial tabel === */
        if ($request->ajax()) {
            return view('detail-order-psb.partials.table', compact(
                'columns', 'rows', 'perPage', 'allowedPerPage'
            ));
        }

        return view('detail-order-psb.index', compact(
            'columns','rows','statusOptions','subKendalaOpts','validasiOptions','perPage','allowedPerPage',
            'subDistrictOpts','branchOpts','wokOpts','serviceAreaOpts','stoOpts','produkOpts','transaksiOpts',
            'territories',
            'disableSA','disableBW'
        ));
    }

    /* =================== CREATE / STORE =================== */
    public function create()
    {
        // ===== mapping lengkap untuk flow di form create (sama seperti di index) =====
        $territories = TerritoryMapping::select('sub_district', 'branch', 'wok', 'service_area', 'sto')
            ->orderBy('sub_district')->orderBy('branch')->orderBy('wok')->orderBy('service_area')->get()
            ->map(fn ($r) => [
                'sub_district' => $r->sub_district,
                'branch'       => $r->branch,
                'wok'          => $r->wok,
                'service_area' => $r->service_area,
                'sto'          => $r->sto,
            ])->all();

        return view('detail-order-psb.create', [
            'statusOptions'   => $this->orderStatusMain,
            'subKendalaOpts'  => $this->subKendalaOptions,
            'validasiOptions' => $this->validasiOptions,

            // master default (untuk placeholder awal)
            'subDistrictOpts' => $this->subDistrictOptions,
            'branchOpts'      => $this->branchOptions,
            'wokOpts'         => $this->wokOptions,
            'serviceAreaOpts' => $this->serviceAreaOptions,
            'stoOpts'         => $this->stoOptions,

            'produkOpts'      => $this->produkOptions,
            'transaksiOpts'   => $this->transaksiOptions,

            // === tambahan untuk flow dinamis ===
            'territories'     => $territories,
        ]);
    }

    
public function quickUpdate(Request $req, $order)
{
    $psb = \App\Models\DetailOrderPsb::findOrFail($order);

    $kendalaCats = ['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'];

    $data = $req->validate([
        'team_name'    => ['nullable','string','max:191'],
        'order_status' => ['nullable','string'],
        'sub_kendala'  => ['nullable','string','max:255'],
        'description'  => ['nullable','string','max:1000'],
    ]);

    if (($data['order_status'] ?? null) && empty($psb->team_name) && empty($data['team_name'])) {
        return response()->json([
            'ok' => false,
            'message' => 'Pilih tim terlebih dahulu sebelum mengubah status.',
        ], 422);
    }

    if (!empty($data['team_name'])) {
        $teamOk = \App\Models\TimTeknisi::where('sto_code', $psb->sto)
            ->where('nama_tim', $data['team_name'])
            ->exists();
        if (!$teamOk) {
            return response()->json([
                'ok' => false,
                'message' => "Tim {$data['team_name']} tidak ditemukan pada STO {$psb->sto}.",
            ], 422);
        }
        $psb->team_name = $data['team_name'];
    }

    $labelToLog = $data['order_status'] ?? null;
    if ($labelToLog !== null && in_array($labelToLog, $kendalaCats, true)) {
        $sk = trim((string)($data['sub_kendala'] ?? ''));
        if ($sk === '') {
            return response()->json([
                'ok' => false,
                'message' => 'Sub Kendala wajib dipilih untuk status Kendala.',
            ], 422);
        }
        $labelToLog = $sk;
        $psb->sub_kendala = $sk;
    } elseif ($labelToLog !== null) {
        $psb->sub_kendala = null;
    }

    if (array_key_exists('order_status', $data)) {
        if (empty($psb->team_name)) {
            return response()->json([
                'ok'=>false,
                'message'=>'Pilih tim terlebih dahulu sebelum mengubah status.',
            ], 422);
        }
        if ($labelToLog !== null && $labelToLog !== '') {
            $log = (string)($psb->work_log ?? '');
            if ($labelToLog !== $psb->order_status) {
                $log .= ($log ? "\n" : '') . now()->format('d-m-Y H:i') . ' - ' . $labelToLog;
            }
            $psb->work_log = $log;
            $psb->order_status = $labelToLog;
        }
    }

    if (array_key_exists('description', $data)) {
        $psb->description = $data['description'];
    }

    $psb->save();

    return response()->json([
        'ok'  => true,
        'row' => [
            'id'           => $psb->id,
            'team_name'    => $psb->team_name,
            'order_status' => $psb->order_status,
            'sub_kendala'  => $psb->sub_kendala,
            'description'  => $psb->description,
            'work_log'     => $psb->work_log,
        ],
    ]);
}


    public function store(Request $request)
    {
        // STO checkbox → CSV string
        $stoCodes = array_values(array_unique(array_filter((array) $request->input('sto'))));
        $request->merge(['sto' => $stoCodes ? implode(',', $stoCodes) : null]);

        // Normalisasi: jika order_status diisi "KENDALA …" (data lama), pindahkan ke sub_kendala
        $rawStatus = trim((string) $request->input('order_status'));
        if ($rawStatus && !in_array($rawStatus, $this->orderStatusMain, true) && str_starts_with($rawStatus, 'KENDALA')) {
            $request->merge(['order_status' => 'KENDALA', 'sub_kendala' => $rawStatus]);
        }

        $data = $request->validate($this->rulesForStore());

        // Jika bukan KENDALA, kosongkan sub_kendala
        if (($data['order_status'] ?? null) !== 'KENDALA') {
            $data['sub_kendala'] = null;
        }

        // Duplikat by WORKORDER saja
        $existing = $this->findExistingByKey($data);
        if ($existing && !$request->boolean('confirm_replace') && !$request->filled('replace_id')) {
            return back()
                ->with('dup_exists', [
                    'id'          => $existing->id,
                    'workorder'   => $existing->workorder,
                    'sc_order_no' => $existing->sc_order_no,
                    'service_no'  => $existing->service_no,
                    'status'      => $existing->order_status,
                    'date'        => optional($existing->date_created)->format('d-m-Y H:i'),
                ])
                ->withInput();
        }

        if ($request->filled('replace_id')) {
            $existing = DetailOrderPsb::find($request->input('replace_id'));
        }

        if ($existing && ($request->boolean('confirm_replace') || $request->filled('replace_id'))) {
            $log = $existing->work_log ?: '';
            if (($data['order_status'] ?? null) && $data['order_status'] !== $existing->order_status) {
                $log .= ($log ? "\n" : '') . now()->format('d-m-Y H:i') . ' - ' . $data['order_status'];
            }
            $existing->fill($data);
            $existing->work_log = $log ?: ($existing->work_log ?? '');
            $existing->date_created = $existing->date_created ?: now();
            $existing->save();

            return redirect()->route('detail-order-psb.index')
                ->with('success', 'Data duplikat berhasil diganti (replace).');
        }

        // === create baru (TANPA order_status dari form → default OPEN)
        $data['date_created'] = now();

        // default status jika tidak diisi di form
        $statusAwal = $data['order_status'] ?? 'OPEN';
        $data['order_status'] = $statusAwal; // simpan juga ke kolom order_status

        // work log awal
        $data['work_log'] = now()->format('d-m-Y H:i') . ' - ' . $statusAwal;

        DetailOrderPsb::create($data);

        return redirect()->route('detail-order-psb.index')
            ->with('success', 'Data berhasil ditambahkan.');

    }

    /* =================== EDIT / UPDATE / DELETE =================== */
    public function edit(DetailOrderPsb $psb)
    {
        return view('detail-order-psb.edit', [
            'psb'             => $psb,
            'statusOptions'   => $this->orderStatusMain,
            'subKendalaOpts'  => $this->subKendalaOptions,
            'validasiOptions' => $this->validasiOptions,
            'subDistrictOpts' => $this->subDistrictOptions,
            'branchOpts'      => $this->branchOptions,
            'wokOpts'         => $this->wokOptions,
            'serviceAreaOpts' => $this->serviceAreaOptions,
            'stoOpts'         => $this->stoOptions,
            'produkOpts'      => $this->produkOptions,
            'transaksiOpts'   => $this->transaksiOptions,
        ]);
    }

    public function update(Request $request, DetailOrderPsb $psb)
    {
        // STO checkbox → CSV
        $stoCodes = array_values(array_unique(array_filter((array) $request->input('sto'))));
        $request->merge(['sto' => $stoCodes ? implode(',', $stoCodes) : null]);

        // Normalisasi status lama (jika masih ada entri "KENDALA …" versi lama)
        $rawStatus = trim((string) $request->input('order_status'));
        if ($rawStatus && !in_array($rawStatus, $this->orderStatusMain, true) && str_starts_with($rawStatus, 'KENDALA')) {
            $request->merge(['order_status' => 'KENDALA', 'sub_kendala' => $rawStatus]);
        }

        $data = $request->validate($this->rulesForUpdate());

        // === Tentukan label untuk log & untuk MENGGANTI order_status ===
        $kendalaCats = ['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'];
        $labelToLog  = $data['order_status'] ?? null;

        if (($data['order_status'] ?? null) !== null &&
            in_array($data['order_status'], $kendalaCats, true) &&
            !empty($data['sub_kendala'])) {
            // kalau kategori Kendala dan sub_kendala ada → pakai yang lengkap
            $labelToLog = $data['sub_kendala'];               // contoh: "KENDALA SISTEM|FALLOUT UIM"
        }

        // Work Log (tambah baris hanya bila order_status benar2 berubah)
        $log = (string)($psb->work_log ?? '');
        if ($labelToLog !== null && $labelToLog !== $psb->order_status) {
            $log .= ($log ? "\n" : '') . now()->format('d-m-Y H:i') . ' - ' . $labelToLog;
        }

        // === REPLACE summary: kolom order_status di-RESET ke label baru saja ===
        if ($labelToLog !== null) {
            $psb->order_status = $labelToLog;
        }

        // Simpan sub_kendala hanya jika Kendala; selain itu kosongkan
        if (in_array(($data['order_status'] ?? ''), $kendalaCats, true) && !empty($data['sub_kendala'])) {
            $psb->sub_kendala = $data['sub_kendala'];
        } else {
            $psb->sub_kendala = null;
        }

        // Field lain tetap diisi dari $data (kecuali order_status karena kita sudah set di atas)
        unset($data['order_status'], $data['sub_kendala']);
        $psb->fill($data);
        $psb->work_log = $log;
        $psb->save();

        return redirect()->route('detail-order-psb.index')->with('success', 'Data berhasil diperbarui.');
    }


    public function destroy(DetailOrderPsb $psb)
    {
        $psb->delete();
        return redirect()->route('detail-order-psb.index')
            ->with('success', 'Data berhasil dihapus.');
    }

    public function addStatus(Request $request, DetailOrderPsb $psb)
    {
        $kendalaCats = ['KENDALA PELANGGAN','KENDALA TEKNIK','KENDALA SISTEM','KENDALA LAINNYA'];

        $data = $request->validate([
            'status_main' => ['required', Rule::in(array_merge(['OPEN','SURVEI','REVOKE SC','PROGRES','AC','CLOSE'], $kendalaCats))],
            'sub_kendala' => ['nullable','string'],
            'note'        => ['nullable','string','max:255'],
        ]);

        // Tentukan label yang masuk ke Work Log & ringkasan
        $label = in_array($data['status_main'], $kendalaCats, true)
            ? (string)($data['sub_kendala'] ?? '')
            : $data['status_main'];

        if (in_array($data['status_main'], $kendalaCats, true) && $label === '') {
            return back()->with('error', 'Sub Kendala wajib diisi untuk status Kendala.');
        }

        // Work Log (selalu pakai label lengkap)
        $note  = trim((string)($data['note'] ?? ''));
        $entry = now()->format('d-m-Y H:i') . ' - ' . $label . ($note ? ' — ' . $note : '');
        $psb->work_log = ($psb->work_log ? $psb->work_log . "\n" : '') . $entry;

        // Ringkasan di kolom order_status: DITAMBAH (gabung)
        $psb->order_status = $this->joinStatusSummary($psb->order_status, $label);

        // Simpan sub_kendala terakhir kalau memang Kendala (optional, buat filter lama tetap jalan)
        $psb->sub_kendala = in_array($data['status_main'], $kendalaCats, true) ? $label : null;

        $psb->save();

        return back()->with('success', 'Status berhasil ditambahkan.');
    }



    /* =================== IMPORT (single page confirm, memory-friendly) =================== */
    public function importForm()
    {
        return view('detail-order-psb.import', [
            'statusOptions'   => $this->orderStatusMain,
            'validasiOptions' => $this->validasiOptions,
            'importable'      => $this->importableFields,
            'produkOpts'      => $this->produkOptions, // dropdown override produk (opsional)
        ]);
    }

    public function importStore(Request $request)
    {
        // ==== ambil pilihan override produk (opsional) ====
        $overrideProduk = trim((string)$request->input('override_produk', ''));
        if ($overrideProduk !== '' && !in_array($overrideProduk, $this->produkOptions, true)) {
            return back()->with('error', 'Pilihan produk tidak valid.');
        }

        // === Aksi Konfirmasi ===
        if ($request->filled('confirm_action')) {
            $cache = Session::pull('psb_import_cache');
            if (!$cache || empty($cache['path']) || !Storage::exists($cache['path'])) {
                return redirect()->route('detail-order-psb.import.form')
                    ->with('error', 'Sesi konfirmasi habis atau file sementara tidak ditemukan. Silakan upload ulang.');
            }

            if ($request->input('confirm_action') === 'cancel') {
                Storage::delete($cache['path']);
                return redirect()->route('detail-order-psb.import.form')
                    ->with('info', 'Import dibatalkan.');
            }

            // === Replace: proses file dari storage baris-per-baris
            $fullPath  = Storage::path($cache['path']);
            $delimiter = $cache['delimiter'] ?? ',';
            $map       = $cache['map'] ?? [];
            $overrideProduk = $cache['override_produk'] ?? ''; // pakai pilihan saat upload

            $fh = fopen($fullPath, 'r');
            if (!$fh) {
                Storage::delete($cache['path']);
                return redirect()->route('detail-order-psb.import.form')
                    ->with('error', 'Gagal membuka file sementara.');
            }

            // skip header
            fgetcsv($fh, 0, $delimiter);

            $inserted = 0; $replaced = 0; $skipped = 0; $errors = []; $rowNumber = 1;

            DB::beginTransaction();
            try {
                while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
                    $rowNumber++;
                    if ($this->rowIsEmpty($row)) continue;

                    // payload by map
                    $payload = [];
                    foreach ($row as $i => $val) {
                        $key = $map[$i] ?? null;
                        if (!$key) continue;
                        if (!in_array($key, $this->importableFields, true)) continue;
                        $payload[$key] = trim($val);
                    }

                    // Normalisasi date & status lama
                    $dateCreated = $this->parseDateFlexible($payload['date_created'] ?? null);
                    unset($payload['date_created']);

                    $rawStatus = $payload['order_status'] ?? null;
                    if ($rawStatus && !in_array($rawStatus, $this->orderStatusMain, true) && str_starts_with($rawStatus, 'KENDALA')) {
                        $payload['sub_kendala'] = $rawStatus;
                        $payload['order_status'] = 'KENDALA';
                    }

                    $validator = Validator::make($payload, [
                        'workorder'                 => ['required','string','max:191'], // WAJIB
                        'sc_order_no'               => ['nullable','string','max:191'],
                        'service_no'                => ['nullable','string','max:191'],
                        'description'               => ['nullable','string'],
                        'status_bima'               => ['nullable','string','max:191'],
                        'address'                   => ['nullable','string'],
                        'customer_name'             => ['nullable','string','max:191'],
                        'contact_number'            => ['nullable','string','max:191'],
                        'team_name'                 => ['nullable','string','max:191'],
                        'order_status'              => ['nullable', Rule::in($this->orderStatusMain)],
                        'sub_kendala'               => ['nullable', Rule::in($this->subKendalaOptions)],
                        'koordinat_survei'          => ['nullable','string','max:191'],
                        'validasi_eviden_kendala'   => ['nullable', Rule::in($this->validasiOptions)],
                        'nama_validator_kendala'    => ['nullable','string','max:191'],
                        'validasi_failwa_invalid'   => ['nullable', Rule::in($this->validasiOptions)],
                        'nama_validator_failwa'     => ['nullable','string','max:191'],
                        'keterangan_non_valid'      => ['nullable','string'],
                        'id_valins'                 => ['nullable','string','max:191'],

                        // NEW fields
                        'sub_district'              => ['nullable', Rule::in($this->subDistrictOptions)],
                        'service_area'              => ['nullable', Rule::in($this->serviceAreaOptions)],
                        'branch'                    => ['nullable', Rule::in($this->branchOptions)],
                        'wok'                       => ['nullable', Rule::in($this->wokOptions)],
                        'sto'                       => ['nullable','string','max:512'],
                        'produk'                    => ['nullable', Rule::in($this->produkOptions)],
                        'transaksi'                 => ['nullable', Rule::in($this->transaksiOptions)],
                    ]);

                    if ($validator->fails()) {
                        $skipped++;
                        $errors[] = "Baris {$rowNumber}: ".implode('; ', $validator->errors()->all());
                        continue;
                    }

                    $data = $validator->validated();

                    // Override produk jika dipilih
                    if ($overrideProduk !== '') {
                        $data['produk'] = $overrideProduk;
                    }

                    // normalisasi STO dari CSV/pipe/spasi
                    if (array_key_exists('sto', $data)) {
                        $data['sto'] = $this->normalizeStoString($data['sto']);
                    }

                    $date   = $dateCreated ?? now();
                    $status = $data['order_status'] ?? 'OPEN';

                    // Duplikat by WORKORDER
                    $existing = $this->findExistingByKey($data);

                    if ($existing) {
                        $log = $existing->work_log ?: '';
                        if (($data['order_status'] ?? null) && $data['order_status'] !== $existing->order_status) {
                            $log .= ($log ? "\n" : '') . now()->format('d-m-Y H:i') . ' - ' . $data['order_status'];
                        }
                        $existing->fill($data);
                        if ($dateCreated instanceof Carbon) $existing->date_created = $dateCreated;
                        $existing->work_log = $log ?: ($existing->work_log ?? '');
                        $existing->save();
                        $replaced++;
                    } else {
                        $data['date_created'] = $date;
                        $data['work_log']     = $date->format('d-m-Y H:i') . ' - ' . $status;
                        DetailOrderPsb::create($data);
                        $inserted++;
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                fclose($fh);
                Storage::delete($cache['path']);
                return redirect()->route('detail-order-psb.import.form')
                    ->with('error', 'Import gagal: '.$e->getMessage());
            }

            fclose($fh);
            Storage::delete($cache['path']);

            return redirect()->route('detail-order-psb.import.form')
                ->with('success', "Import selesai: {$inserted} baru, {$replaced} diganti, {$skipped} gagal.")
                ->with('import_errors', $errors);
        }

        // === Upload awal: simpan ke storage + (baru) dukung Excel ===
        $request->validate([
            'file' => ['required','file','mimes:csv,txt,xlsx,xls,ods','max:20480'],
        ]);

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());

        // simpan mentah dulu sesuai ekstensi aslinya
        $token   = 'psb_' . Str::uuid()->toString();
        $rawPath = $file->storeAs('imports', $token . '.' . $ext); // ex: imports/psb_xxx.xlsx
        $rawFull = Storage::path($rawPath);

        $delimiter = ',';
        if (in_array($ext, ['xlsx','xls','ods'], true)) {
            // konversi Excel → CSV
            try {
                $conv      = $this->convertExcelToCsv($rawFull);   // ['path'=>..., 'delimiter'=>',']
                $path      = $conv['path'];
                $delimiter = $conv['delimiter'];                   // ','
                Storage::delete($rawPath);
            } catch (\Throwable $e) {
                Storage::delete($rawPath);
                return back()->with('error', 'Gagal membaca file Excel: '.$e->getMessage());
            }
        } else {
            // CSV/TXT → deteksi delimiter, lalu pastikan pakai ekstensi .csv
            $delimiter = $this->detectDelimiter($rawFull);
            $csvPath   = 'imports/'.$token.'.csv';
            Storage::move($rawPath, $csvPath); // rename agar konsisten
            $path = $csvPath;
        }

        $fullPath = Storage::path($path);
        $fh = fopen($fullPath, 'r');
        if (!$fh) {
            Storage::delete($path);
            return back()->with('error','Gagal membuka file yang diupload.');
        }

        $header = fgetcsv($fh, 0, $delimiter);
        if (!$header) {
            fclose($fh); Storage::delete($path);
            return back()->with('error','Header tidak ditemukan.');
        }

        $map = [];
        foreach ($header as $i => $h) $map[$i] = $this->canonicalHeader($h);

        // Wajib: workorder + order_status (order_status bisa kosong per baris, tapi header harus ada)
        if (!in_array('workorder', $map, true)) {
            fclose($fh); Storage::delete($path);
            return back()->with('error','Kolom "workorder" wajib ada di header.');
        }

        // hitung total & duplikat (by workorder saja)
        $total = 0; $dup = 0;
        while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
            if ($this->rowIsEmpty($row)) continue;
            $total++;

            $m = [];
            foreach ($row as $i => $val) {
                $key = $map[$i] ?? null;
                if ($key === 'workorder') {
                    $m['workorder'] = trim($val);
                }
            }
            if ($this->findExistingByKey($m)) $dup++;
        }
        fclose($fh);

        // Tidak ada dup → langsung insert batch streaming dari file
        if ($dup === 0) {
            $fh = fopen($fullPath, 'r');
            if (!$fh) {
                Storage::delete($path);
                return back()->with('error','Gagal membuka file yang diupload (insert).');
            }
            // skip header
            fgetcsv($fh, 0, $delimiter);

            $inserted = 0; $errs = []; $rowNumber = 1;
            DB::beginTransaction();
            try {
                while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
                    $rowNumber++;
                    if ($this->rowIsEmpty($row)) continue;

                    $payload = [];
                    foreach ($row as $i => $val) {
                        $key = $map[$i] ?? null;
                        if (!$key) continue;
                        if (!in_array($key, $this->importableFields, true)) continue;
                        $payload[$key] = trim($val);
                    }

                    // Normalisasi date & status lama
                    $dateCreated = $this->parseDateFlexible($payload['date_created'] ?? null);
                    unset($payload['date_created']);

                    $rawStatus = $payload['order_status'] ?? null;
                    if ($rawStatus && !in_array($rawStatus, $this->orderStatusMain, true) && str_starts_with($rawStatus, 'KENDALA')) {
                        $payload['sub_kendala'] = $rawStatus;
                        $payload['order_status'] = 'KENDALA';
                    }

                    $validator = Validator::make($payload, [
                        'workorder'                 => ['required','string','max:191'], // WAJIB
                        'sc_order_no'               => ['nullable','string','max:191'],
                        'service_no'                => ['nullable','string','max:191'],
                        'description'               => ['nullable','string'],
                        'status_bima'               => ['nullable','string','max:191'],
                        'address'                   => ['nullable','string'],
                        'customer_name'             => ['nullable','string','max:191'],
                        'contact_number'            => ['nullable','string','max:191'],
                        'team_name'                 => ['nullable','string','max:191'],
                        'order_status'              => ['nullable', Rule::in($this->orderStatusMain)],
                        'sub_kendala'               => ['nullable', Rule::in($this->subKendalaOptions)],
                        'koordinat_survei'          => ['nullable','string','max:191'],
                        'validasi_eviden_kendala'   => ['nullable', Rule::in($this->validasiOptions)],
                        'nama_validator_kendala'    => ['nullable','string','max:191'],
                        'validasi_failwa_invalid'   => ['nullable', Rule::in($this->validasiOptions)],
                        'nama_validator_failwa'     => ['nullable','string','max:191'],
                        'keterangan_non_valid'      => ['nullable','string'],
                        'id_valins'                 => ['nullable','string','max:191'],
                        'sub_district'              => ['nullable', Rule::in($this->subDistrictOptions)],
                        'service_area'              => ['nullable', Rule::in($this->serviceAreaOptions)],
                        'branch'                    => ['nullable', Rule::in($this->branchOptions)],
                        'wok'                       => ['nullable', Rule::in($this->wokOptions)],
                        'sto'                       => ['nullable','string','max:512'],
                        'produk'                    => ['nullable', Rule::in($this->produkOptions)],
                        'transaksi'                 => ['nullable', Rule::in($this->transaksiOptions)],
                    ]);

                    if ($validator->fails()) {
                        $errs[] = "Baris {$rowNumber}: ".implode('; ', $validator->errors()->all());
                        continue;
                    }

                    $data = $validator->validated();

                    // Override produk jika dipilih
                    if ($overrideProduk !== '') {
                        $data['produk'] = $overrideProduk;
                    }

                    if (array_key_exists('sto',$data)) $data['sto'] = $this->normalizeStoString($data['sto']);

                    $date   = $dateCreated ?? now();
                    $status = $data['order_status'] ?? 'OPEN';
                    $data['date_created'] = $date;
                    $data['work_log']     = $date->format('d-m-Y H:i') . ' - ' . $status;

                    DetailOrderPsb::create($data);
                    $inserted++;
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                fclose($fh);
                Storage::delete($path);
                return redirect()->route('detail-order-psb.import.form')
                    ->with('error', 'Insert gagal: '.$e->getMessage());
            }

            fclose($fh);
            Storage::delete($path);

            return redirect()->route('detail-order-psb.import.form')
                ->with('success', "Import selesai: {$inserted} baris masuk.")
                ->with('import_errors', $errs);
        }

        // Ada duplikat → simpan cache untuk confirm (ikutkan override_produk)
        Session::put('psb_import_cache', [
            'token'           => $token,
            'path'            => $path,
            'delimiter'       => $delimiter,
            'map'             => $map,
            'total'           => $total,
            'dup'             => $dup,
            'override_produk' => $overrideProduk, // <— disimpan utk proses replace
        ]);

        return view('detail-order-psb.import', [
            'statusOptions'   => $this->orderStatusMain,
            'validasiOptions' => $this->validasiOptions,
            'importable'      => $this->importableFields,
            'produkOpts'      => $this->produkOptions,
            'confirm'         => true,
            'confirmStats'    => ['total' => $total, 'dup' => $dup],
        ]);
    }

    /* =================== AJAX: CEK DUPLIKAT ADD DATA (BY WORKORDER SAJA) =================== */
    public function dupCheck(Request $request)
    {
        $wo = trim((string) $request->input('workorder', ''));

        $existing = $this->findExistingByKey([
            'workorder'   => $wo ?: null,
        ]);

        if ($existing) {
            return response()->json([
                'duplicate' => true,
                'id'        => $existing->id,
                'summary'   => [
                    'workorder'   => $existing->workorder,
                    'sc_order_no' => $existing->sc_order_no,
                    'service_no'  => $existing->service_no,
                    'status'      => $existing->order_status,
                    'date'        => optional($existing->date_created)->format('d-m-Y H:i'),
                ],
            ]);
        }

        return response()->json(['duplicate' => false]);
    }

    /* =================== TEMPLATE CSV =================== */
    public function downloadTemplate(): StreamedResponse
    {
        $columns = [
            'workorder','sc_order_no','service_no','description','status_bima','address',
            'customer_name','contact_number','team_name',
            'order_status','sub_kendala',
            'koordinat_survei',
            'validasi_eviden_kendala','nama_validator_kendala','validasi_failwa_invalid',
            'nama_validator_failwa','keterangan_non_valid',
            'sub_district','service_area','branch','wok','sto','produk','transaksi',
            'id_valins','date_created',
        ];

        return response()->streamDownload(function () use ($columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            fputcsv($out, [
                'WO123','DGP123456','1524123456','Pemasangan IndiHome paket X',
                'OK','Jl. Mawar No.1, Palembang','Budi Santoso','08123456789',
                'Tim A','KENDALA','KENDALA TEKNIK|ODP FULL',
                '-2.991,104.76','BELUM DI VALIDASI','','BELUM DI VALIDASI','',
                'SUMSEL','PALEMBANG ILIR','Palembang','Palembang','PGC,LLG','INDIHOME','CREATE',
                'VAL123', now()->format('d-m-Y H:i'),
            ]);
            fclose($out);
        }, 'template_detail_order_psb.csv', ['Content-Type' => 'text/csv']);
    }

    /* =================== EXPORT CSV (ikut kolom baru) =================== */
    public function exportCsv(Request $request): StreamedResponse
    {
        $q = DetailOrderPsb::query();

        if ($request->filled('date_from')) {
            $from = Carbon::parse($request->input('date_from'))->startOfDay();
            $q->where('date_created', '>=', $from);
        }
        if ($request->filled('date_to')) {
            $to = Carbon::parse($request->input('date_to'))->endOfDay();
            $q->where('date_created', '<=', $to);
        }

        // contains filters (team_name DIHAPUS dari daftar contains)
        foreach ([
            'workorder','sc_order_no','service_no','description','status_bima','address',
            'customer_name','contact_number','work_log','koordinat_survei',
            'nama_validator_kendala','nama_validator_failwa','keterangan_non_valid','id_valins',
        ] as $field) {
            if ($request->filled($field)) $q->where($field, 'like', '%'.$request->input($field).'%');
        }

        // Team Name: exact match dari dropdown (optional tapi disarankan)
        if ($request->filled('team_name')) {
            $q->where('team_name', $request->team_name);
        }

        // ==== Order Status + Sub Kendala (kompatibel data lama) ====
        if ($request->filled('order_status')) {
            $os = $request->input('order_status');
            if ($os === 'KENDALA') {
                if ($request->filled('sub_kendala')) {
                    $sk = $request->input('sub_kendala');
                    $q->where(function($qq) use ($sk) {
                        $qq->where('sub_kendala', $sk)
                           ->orWhere('order_status', $sk);
                    });
                } else {
                    $q->where(function($qq) {
                        $qq->where('order_status', 'KENDALA')
                           ->orWhere('order_status', 'like', 'KENDALA%')
                           ->orWhereNotNull('sub_kendala');
                    });
                }
            } else {
                $q->where('order_status', $os);
            }
        }

        foreach (['validasi_eviden_kendala','validasi_failwa_invalid','sub_district','service_area','branch','wok','produk','transaksi'] as $eq) {
            if ($request->filled($eq)) $q->where($eq, $request->input($eq));
        }
        if ($request->filled('sto')) {
            $chosens = array_filter((array) $request->input('sto'));
            if ($chosens) {
                $q->where(function($qq) use ($chosens) {
                    foreach ($chosens as $code) $qq->orWhere('sto','like','%'.$code.'%');
                });
            }
        }

        $headersText = $this->columns;
        $fields = [
            'date_created','workorder','sc_order_no','service_no','description','status_bima',
            'address','customer_name','contact_number','team_name',
            'order_status','sub_kendala',
            'work_log','koordinat_survei','validasi_eviden_kendala','nama_validator_kendala',
            'validasi_failwa_invalid','nama_validator_failwa','keterangan_non_valid',
            'sub_district','service_area','branch','wok','sto','produk','transaksi',
            'id_valins',
        ];

        $filename = 'detail_order_psb_'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($q, $fields, $headersText) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM
            fputcsv($out, $headersText);
            foreach ($q->orderByDesc('date_created')->cursor() as $r) {
                $row = [];
                foreach ($fields as $f) {
                    $row[] = $f === 'date_created'
                        ? optional($r->date_created)->format('Y-m-d H:i')
                        : $r->{$f};
                }
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /* =================== RULES & HELPERS =================== */

    private function rulesForStore(): array
    {
        return [
            'workorder'               => ['required','string','max:191'], // WAJIB
            'sc_order_no'             => ['nullable','string','max:191'],
            'service_no'              => ['nullable','string','max:191'],
            'description'             => ['nullable','string'],
            'status_bima'             => ['nullable','string','max:191'],
            'address'                 => ['nullable','string'],
            'customer_name'           => ['nullable','string','max:191'],
            'contact_number'          => ['nullable','string','max:191'],
            'team_name'               => ['nullable','string','max:191'],

            // status utama + sub_kendala (wajib jika KENDALA)
            'order_status'            => ['nullable', Rule::in($this->orderStatusMain)],
            'sub_kendala'             => ['nullable', Rule::in($this->subKendalaOptions), 'required_if:order_status,KENDALA'],

            'koordinat_survei'        => ['nullable','string','max:191'],
            'validasi_eviden_kendala' => ['nullable', Rule::in($this->validasiOptions)],
            'nama_validator_kendala'  => ['nullable','string','max:191'],
            'validasi_failwa_invalid' => ['nullable', Rule::in($this->validasiOptions)],
            'nama_validator_failwa'   => ['nullable','string','max:191'],
            'keterangan_non_valid'    => ['nullable','string'],

            // NEW:
            'sub_district'            => ['nullable', Rule::in($this->subDistrictOptions)],
            'service_area'            => ['nullable', Rule::in($this->serviceAreaOptions)],
            'branch'                  => ['nullable', Rule::in($this->branchOptions)],
            'wok'                     => ['nullable', Rule::in($this->wokOptions)],
            'sto'                     => ['nullable','string','max:512'], // CSV dari checkbox
            'produk'                  => ['nullable', Rule::in($this->produkOptions)],
            'transaksi'               => ['nullable', Rule::in($this->transaksiOptions)],
            'id_valins'               => ['nullable','string','max:191'],

            'date_created'            => ['prohibited'],
            'work_log'                => ['prohibited'],
        ];
    }

    private function rulesForUpdate(): array
    {
        return [
            'team_name'               => ['nullable','string','max:191'],

            'order_status'            => ['nullable', Rule::in($this->orderStatusMain)],
            'sub_kendala'             => ['nullable', Rule::in($this->subKendalaOptions), 'required_if:order_status,KENDALA'],

            'koordinat_survei'        => ['nullable','string','max:191'],
            'validasi_eviden_kendala' => ['nullable', Rule::in($this->validasiOptions)],
            'nama_validator_kendala'  => ['nullable','string','max:191'],
            'validasi_failwa_invalid' => ['nullable', Rule::in($this->validasiOptions)],
            'nama_validator_failwa'   => ['nullable','string','max:191'],
            'keterangan_non_valid'    => ['nullable','string'],
            'id_valins'               => ['nullable','string','max:191'],

            // NEW editable:
            'sub_district'            => ['nullable', Rule::in($this->subDistrictOptions)],
            'service_area'            => ['nullable', Rule::in($this->serviceAreaOptions)],
            'branch'                  => ['nullable', Rule::in($this->branchOptions)],
            'wok'                     => ['nullable', Rule::in($this->wokOptions)],
            'sto'                     => ['nullable','string','max:512'],
            'produk'                  => ['nullable', Rule::in($this->produkOptions)],
            'transaksi'               => ['nullable', Rule::in($this->transaksiOptions)],

            'date_created'            => ['prohibited'],
            'work_log'                => ['prohibited'],
        ];
    }

    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string)$cell) !== '') return false;
        }
        return true;
    }

    private function detectDelimiter(string $path): string
    {
        $line = '';
        if ($fh = fopen($path, 'r')) {
            $line = fgets($fh, 4096) ?: '';
            fclose($fh);
        }
        $counts = [','=>substr_count($line, ','), ';'=>substr_count($line, ';'), "\t"=>substr_count($line,"\t"), '|'=>substr_count($line,'|')];
        arsort($counts);
        $best = array_key_first($counts);
        return $counts[$best] > 0 ? $best : ',';
    }

    private function canonicalHeader(string $h): string
    {
        $h = ltrim($h, "\xEF\xBB\xBF");
        $h = strtolower(trim($h));
        $h = preg_replace('/[^a-z0-9]+/', '_', $h);
        $h = trim($h, '_');

        $aliases = [
            'date_created' => ['date_created','date','created_at','date_create'],
            'workorder'    => ['workorder','work_order','work_order_number','work_order_no','workordernumber'],
            'sc_order_no'  => ['sc_order_no','sc_order_no_track_id_csrm_no','sc_order_number','sc_order'],
            'service_no'   => ['service_no','service_no_','service_number'],
            'description'  => ['description','deskripsi'],
            'status_bima'  => ['status_bima','statusbima'],
            'address'      => ['address','alamat'],
            'customer_name'=> ['customer_name','customer','nama_pelanggan'],
            'contact_number'=>['contact_number','contact','telephone','phone','no_hp','contact_telephone_numb','contact_telephone_number'],
            'team_name'    => ['team_name','team','nama_team','nama_tim','tim'],
            'order_status' => ['order_status','status','orderstatus','status_order'],
            'sub_kendala'  => ['sub_kendala','subkendala','sub_kendala_status','rincian_kendala'],
            'koordinat_survei' => ['koordinat_survei','koordinat','survey_coordinate','koordinat_survey'],
            'validasi_eviden_kendala' => ['validasi_eviden_kendala','validasi_eviden','validasi_evidence_kendala'],
            'nama_validator_kendala'  => ['nama_validator_kendala','validator_kendala'],
            'validasi_failwa_invalid' => ['validasi_failwa_invalid','validasi_failwa_invalid_survey','validasi_failwa_invalid_survei','validasi_failwa__invalid_survey'],
            'nama_validator_failwa'   => ['nama_validator_failwa','validator_failwa'],
            'keterangan_non_valid'    => ['keterangan_non_valid','keterangan_nonvalid','keterangan_not_valid','ket_non_valid'],
            'id_valins'               => ['id_valins','id_valin'],

            // NEW aliases
            'sub_district' => ['sub_district','subdistrict','sub_distrik','witel'],
            'service_area' => ['service_area','servicearea','area_layanan'],
            'branch'       => ['branch','cabang'],
            'wok'          => ['wok','witel_op_kab'],
            'sto'          => ['sto','pos_sto','kode_sto', 'workzone'],
            'produk'       => ['produk','product','product_name'],
            'transaksi'    => ['transaksi','transaction'],
        ];

        foreach ($aliases as $canon => $list) {
            if (in_array($h, $list, true)) return $canon;
        }
        return $h;
    }

    private function parseDateFlexible(?string $value)
    {
        if (!$value) return null;

        $value   = trim($value);
        $formats = ['d-m-Y H:i','Y-m-d H:i','d/m/Y H:i','Y/m/d H:i','d-m-Y','Y-m-d','d/m/Y','Y/m/d'];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $value);
                if ($dt) {
                    if (strlen($value) <= 10) $dt->setTime(0, 0);
                    return $dt;
                }
            } catch (\Throwable $e) { /* lanjut */ }
        }

        try { return Carbon::parse($value); }
        catch (\Throwable $e) { return null; }
    }

    /** Cari record existing by WORKORDER saja */
    private function findExistingByKey(array $row): ?DetailOrderPsb
    {
        $wo = $row['workorder'] ?? null;
        if (!$wo) return null;

        return DetailOrderPsb::where('workorder', $wo)->first();
    }

    /* ==== helpers untuk import ==== */
    private function normalizeStoString(?string $s): ?string
    {
        if (!$s) return null;
        $parts = preg_split('/[,\|\s]+/', $s);
        $parts = array_values(array_unique(array_filter($parts, fn($v) => $v !== '')));
        // keep only known codes
        $parts = array_values(array_filter($parts, fn($v) => in_array($v, $this->stoOptions, true)));
        return $parts ? implode(',', $parts) : null;
    }

    private function joinStatusSummary(?string $current, string $label): string
    {
        $tokens = preg_split('/\s*&\s*/', trim((string)$current)) ?: [];
        $tokens = array_values(array_filter(array_map('trim', $tokens)));
        if (!in_array($label, $tokens, true)) {
            $tokens[] = $label;
        }
        return implode(' & ', $tokens);
    }

    /** Konversi Excel (xlsx/xls/ods) ke CSV UTF-8, hanya sheet pertama. */
    private function convertExcelToCsv(string $sourceFullPath): array
    {
        $spreadsheet = IOFactory::load($sourceFullPath);
        $writer = new CsvWriter($spreadsheet);
        $writer->setSheetIndex(0);
        $writer->setDelimiter(',');
        $writer->setEnclosure('"');
        $writer->setLineEnding("\n");
        $writer->setUseBOM(false);

        $token   = 'psb_' . Str::uuid()->toString();
        $relPath = 'imports/' . $token . '.csv';
        $fullOut = Storage::path($relPath);

        @mkdir(dirname($fullOut), 0777, true);
        $writer->save($fullOut);

        return ['path' => $relPath, 'delimiter' => ','];
    }
}
