<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google_Client;
use Google_Service_Sheets;

class GoogleSheetController extends Controller
{
    public function home(Request $request)
    {
        $client = new Google_Client();
        $client->setApplicationName('Infralexia App');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(storage_path('google/service-account.json'));
        $client->setAccessType('offline');

        $service = new Google_Service_Sheets($client);
        $spreadsheetId = '1VHyfmKM-ZXNrYiDS06MGJyyqq3ySVT-DKZVy6SVLm2Y';

        // Ambil data baris 1-45 kolom A-AW
        $range = 'DB WITEL!A1:AW45';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        // Header baris 2-4 (index 1–3)
        $header1 = $values[1] ?? [];
        $header2 = $values[2] ?? [];
        $header3 = $values[3] ?? [];

        // Ambil data isi dari baris 5 ke bawah (index 4+)
        $data = array_slice($values, 4);

        // Ambil parameter filter dari request
        $filterServiceArea = $request->input('service_area');
        $filterStatus = $request->input('status');
        $filterSto = $request->input('sto');

        // ====================
        // Ambil kolom dinamis
        // ====================
        $headerRow = $header3;

        $serviceAreaIndex = array_search('Service Area', $headerRow);
        $stoIndex = array_search('STO', $headerRow);

        // Fallback jika tidak ketemu
        $serviceAreaIndex = $serviceAreaIndex !== false ? $serviceAreaIndex : 0;
        $stoIndex = $stoIndex !== false ? $stoIndex : 1;

        // ====================
        // Filtering data
        // ====================
        $filteredData = collect($data)->filter(function ($row) use ($filterServiceArea, $filterSto, $serviceAreaIndex, $stoIndex) {
            $serviceArea = $row[$serviceAreaIndex] ?? null;
            $sto = $row[$stoIndex] ?? null;

            $matchArea = !$filterServiceArea || $filterServiceArea === $serviceArea;
            $matchSto = !$filterSto || $filterSto === $sto;

            return $matchArea && $matchSto;
        });

        // ====================
        // Ambil dropdown data
        // ====================
        $serviceAreas = collect($data)->pluck($serviceAreaIndex)->filter(fn ($x) =>
            $x !== null &&
            $x !== '' &&
            !str_contains($x, 'TOTAL') &&
            strlen($x) <= 50
        )->unique()->values();

        $stos = collect($data)->pluck($stoIndex)->filter(fn ($x) =>
            $x !== null &&
            $x !== '' &&
            !str_contains($x, 'TOTAL') &&
            !str_contains($x, '#') &&
            strlen($x) <= 20
        )->unique()->values();

        // Status dari baris ke-2 (index 1)
        $statuses = collect($header1)->filter(fn ($val) =>
            in_array(strtoupper($val), ['OPEN', 'FOLLOW UP', 'CLOSE', 'KENDALA'])
        )->unique()->values();

        // Return ke view
        return view('home', [
            'data' => $filteredData,
            'header1' => $header1,
            'header2' => $header2,
            'header3' => $header3,
            'serviceAreas' => $serviceAreas,
            'stos' => $stos,
            'statuses' => $statuses,
            'selectedStatus' => $filterStatus
        ]);
    }
}
