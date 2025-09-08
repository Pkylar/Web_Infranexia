<?php

namespace App\Http\Controllers;

use App\Models\RekapPhoto;
use App\Models\Teknisi;
use App\Models\TerritoryMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RekapPhotoController extends Controller
{
    private function stoOptions(): array
    {
        $fromTeknisi = Teknisi::query()
            ->whereNotNull('base_sto')
            ->pluck('base_sto')
            ->filter(fn ($v) => trim((string) $v) !== '')
            ->unique()->sort()->values()->all();
        if (!empty($fromTeknisi)) return $fromTeknisi;

        $fromMap = TerritoryMapping::query()
            ->whereNotNull('sto')
            ->pluck('sto')
            ->filter(fn ($v) => trim((string) $v) !== '')
            ->unique()->sort()->values()->all();
        if (!empty($fromMap)) return $fromMap;

        return [
            'PGC','SGB','LHT','LLG','PGA','SBU','TMO','MUB','TSS','PDP','TLK','KTU','SKY','MEM','TAM',
            'PBI','BET','BTA','BKS','BLT','MUD','MPA','SRO','PLJ','PBM','PDT','KAG','SPP','MUR','IDL',
            'TRA','TAB','OKI','BYU','JBS','KBO','MGR','MTK','PGP','SLT','TBI','TJN','TPL',
        ];
    }

    public function index(Request $request)
    {
        $sto = trim((string) $request->get('sto', ''));
        $q   = trim((string) $request->get('q', ''));

        $rows = RekapPhoto::query()
            ->when($sto !== '', fn ($qq) => $qq->where('sto', $sto))
            ->when($q   !== '', function ($qq) use ($q) {
                $like = '%' . $q . '%';
                $qq->where(function ($sub) use ($like) {
                    $sub->where('teknisi_nik', 'like', $like)
                        ->orWhere('teknisi_nama', 'like', $like)
                        ->orWhere('note', 'like', $like);
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(24)
            ->withQueryString();

        return view('rekap-foto.index', [
            'rows'    => $rows,
            'stoOpts' => $this->stoOptions(),
        ]);
    }

    public function create()
    {
        return view('rekap-foto.create', [
            'stoOpts' => $this->stoOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo'       => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:15360', // 15 MB
            'sto'         => 'nullable|string|max:100',
            'teknisi_nik' => 'nullable|string|max:50',
            'note'        => 'nullable|string|max:255',
        ],[
            'photo.max' => 'Ukuran foto maksimal 15 MB.',
        ]);

        // Jika PHP menolak upload (post_max_size terlalu kecil), $_FILES akan kosong
        if ((int)($request->server('CONTENT_LENGTH') ?? 0) > 0 && empty($_FILES)) {
            return back()->with('error', 'Upload gagal di level PHP. Kecilkan file atau naikkan limit server.');
        }

        // SIMPAN DI DISK "public" -> path RELATIF: rekap-foto/xxxx.jpg
        $path = $request->file('photo')->store('rekap-foto', 'public');

        $payload = [
            'photo_path'   => $path,                               // contoh: rekap-foto/abc.jpg
            'sto'          => $request->sto,
            'teknisi_nik'  => $request->teknisi_nik,
            'teknisi_nama' => auth()->user()->name ?? '—',         // auto isi user login
            'note'         => $request->note,
        ];

        // Isi uploaded_by kalau kolomnya ada
        if (Schema::hasColumn('rekap_photos', 'uploaded_by')) {
            $payload['uploaded_by'] = auth()->id();
        }

        RekapPhoto::create($payload);

        return redirect()->route('rekap-foto.index')->with('success', 'Foto berhasil di-upload.');
    }

    public function destroy(RekapPhoto $photo)
    {
        // Hapus file dari disk public (pakai path relatif yang benar)
        if ($photo->photo_path && Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }
        $photo->delete();

        return back()->with('success', 'Foto dihapus.');
    }
}
