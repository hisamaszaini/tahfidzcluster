<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Santri;
use App\Models\Kriteria;
use App\Models\Nilai;
use App\Models\Hasil;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan Halaman Dashboard Utama.
     */
    public function index()
    {
        // 1. Ambil jumlah data riil dari database
        $totalMusyrif = User::where('role', 'musyrif')->count();
        $totalSantri = Santri::count();
        $totalKriteria = Kriteria::count();
        
        // Menghitung berapa santri yang datanya sudah lengkap/memiliki skor terinput
        $totalSantriDinilai = Santri::has('nilai')->count();

        // 2. Ambil data santri terbaru untuk feed "Aktivitas Terbaru"
        $recentSantris = Santri::orderBy('id', 'desc')->take(5)->get();

        // 3. Status clustering saat ini
        $isClustered = Hasil::exists();
        $clusterCounts = [
            'Cukup' => Hasil::where('kluster', 'Cukup')->count(),
            'Baik' => Hasil::where('kluster', 'Baik')->count(),
            'Sangat Baik' => Hasil::where('kluster', 'Sangat Baik')->count(),
        ];

        return view('dashboard', compact(
            'totalMusyrif', 
            'totalSantri', 
            'totalKriteria', 
            'totalSantriDinilai',
            'recentSantris',
            'isClustered',
            'clusterCounts'
        ));
    }
}
