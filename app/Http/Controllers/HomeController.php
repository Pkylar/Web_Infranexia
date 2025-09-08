<?php

namespace App\Http\Controllers;

use App\Models\DetailOrderPsb;
use App\Models\User;
use App\Models\RekapPhoto;

class HomeController extends Controller
{
    public function __construct()
    {
        // Semua role boleh akses /home selama sudah login
        $this->middleware('auth');
    }

    public function index()
    {
        // Angka-angka utama
        $totalUsers   = User::count();
        $totalOrders  = DetailOrderPsb::count();

        $openCount    = DetailOrderPsb::where('order_status', 'OPEN')->count();
        $surveiCount  = DetailOrderPsb::where('order_status', 'SURVEI')->count();
        $progresCount = DetailOrderPsb::where('order_status', 'PROGRES')->count();
        $acCount      = DetailOrderPsb::where('order_status', 'AC')->count();
        $closeCount   = DetailOrderPsb::where('order_status', 'CLOSE')->count();

        $kendalaPelanggan = DetailOrderPsb::where('order_status', 'like', 'KENDALA PELANGGAN%')->count();
        $kendalaTeknik    = DetailOrderPsb::where('order_status', 'like', 'KENDALA TEKNIK%')->count();
        $kendalaSistem    = DetailOrderPsb::where('order_status', 'like', 'KENDALA SISTEM%')->count();
        $kendalaLainnya   = DetailOrderPsb::where('order_status', 'like', 'KENDALA LAINNYA%')->count();

        $teamCount     = DetailOrderPsb::whereNotNull('team_name')->distinct('team_name')->count('team_name');
        $customerCount = DetailOrderPsb::whereNotNull('customer_name')->distinct('customer_name')->count('customer_name');

        // Aktivitas terbaru
        $recentOrders = DetailOrderPsb::orderByDesc('date_created')
            ->take(8)
            ->get(['date_created', 'customer_name', 'order_status', 'workorder']);

        // Rekapan foto terbaru (12 terakhir) — pakai created_at agar aman
        $latestPhotos = RekapPhoto::orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(12)
            ->get();

        return view('home', compact(
            'totalUsers','totalOrders',
            'openCount','surveiCount','progresCount','acCount','closeCount',
            'kendalaPelanggan','kendalaTeknik','kendalaSistem','kendalaLainnya',
            'teamCount','customerCount','recentOrders',
            'latestPhotos'
        ));
    }
}
