<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddDataController extends Controller
{
    public function create()
    {
        return view('add-data');
    }

    public function store(Request $request)
    {
        // Simulasi insert data (misalnya ke Google Sheet atau DB)
        // Untuk sekarang, cukup validasi input dan redirect

        $validated = $request->validate([
            'service_area' => 'required|string',
            'sto' => 'required|string',
            'status' => 'required|string|in:Open,Follow Up,Close,Kendala',
            'teknisi' => 'nullable|string',
        ]);

        // TODO: Simpan ke database atau Google Sheets di sini

        return redirect()->route('add-data')->with('success', 'Data berhasil disimpan (simulasi).');
    }
}
