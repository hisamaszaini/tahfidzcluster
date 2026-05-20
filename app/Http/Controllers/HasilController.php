<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Santri;
use App\Services\KMeansService;
use Illuminate\Http\Request;

class HasilController extends Controller
{
    protected $kMeansService;

    public function __construct(KMeansService $kMeansService)
    {
        $this->kMeansService = $kMeansService;
    }

    /**
     * Tampilkan Dashboard Hasil Clustering Akhir.
     */
    public function index()
    {
        // Ambil hasil akhir beserta data santri dan nilainya
        $hasils = Hasil::with(['santri.nilai.kriteria'])->get();
        $totalSantri = Santri::count();

        if ($hasils->isEmpty()) {
            return view('hasil.index', [
                'hasils' => collect(),
                'totalSantri' => $totalSantri,
                'clusterCounts' => ['Cukup' => 0, 'Baik' => 0, 'Sangat Baik' => 0],
                'clusterAverages' => []
            ]);
        }

        // Hitung jumlah santri di masing-masing cluster
        $clusterCounts = [
            'Cukup' => $hasils->where('kluster', 'Cukup')->count(),
            'Baik' => $hasils->where('kluster', 'Baik')->count(),
            'Sangat Baik' => $hasils->where('kluster', 'Sangat Baik')->count(),
        ];

        // Hitung rata-rata nilai kriteria untuk masing-masing cluster (Profil Cluster/Centroid Akhir)
        $clusterAverages = [
            'Cukup' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
            'Baik' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
            'Sangat Baik' => ['C1' => 0, 'C2' => 0, 'C3' => 0, 'count' => 0],
        ];

        foreach ($hasils as $hasil) {
            $kluster = $hasil->kluster;
            $clusterAverages[$kluster]['count']++;

            foreach ($hasil->santri->nilai as $nilai) {
                if ($nilai->kriteria_id == 1) $clusterAverages[$kluster]['C1'] += $nilai->nilai;
                if ($nilai->kriteria_id == 2) $clusterAverages[$kluster]['C2'] += $nilai->nilai;
                if ($nilai->kriteria_id == 3) $clusterAverages[$kluster]['C3'] += $nilai->nilai;
            }
        }

        foreach ($clusterAverages as $kluster => $data) {
            if ($data['count'] > 0) {
                $clusterAverages[$kluster]['C1'] = round($data['C1'] / $data['count'], 2);
                $clusterAverages[$kluster]['C2'] = round($data['C2'] / $data['count'], 2);
                $clusterAverages[$kluster]['C3'] = round($data['C3'] / $data['count'], 2);
            }
        }

        // Tambahkan pemrosesan skor rata-rata kombinasi untuk meranking santri
        $hasils = $hasils->map(function($hasil) {
            $hafalan = 0;
            $murojaah = 0;
            $tahsin = 0;
            foreach ($hasil->santri->nilai as $nilai) {
                if ($nilai->kriteria_id == 1) $hafalan = $nilai->nilai;
                if ($nilai->kriteria_id == 2) $murojaah = $nilai->nilai;
                if ($nilai->kriteria_id == 3) $tahsin = $nilai->nilai;
            }
            $hasil->skor_rata = round(($hafalan + $murojaah + $tahsin) / 3, 2);
            return $hasil;
        })->sortByDesc('skor_rata')->values();

        return view('hasil.index', compact('hasils', 'totalSantri', 'clusterCounts', 'clusterAverages'));
    }

    /**
     * Tampilkan halaman formulir awal untuk memicu K-Means.
     */
    public function prosesForm()
    {
        $run = false;
        $totalSantri = Santri::count();
        $isClustered = Hasil::exists();
        $centroidAwal = $this->kMeansService->getInitialCentroids();

        return view('hasil.proses', compact('run', 'totalSantri', 'isClustered', 'centroidAwal'));
    }

    /**
     * Jalankan proses K-Means (POST) dan tampilkan log iterasi matematis lengkap.
     */
    public function proses(Request $request)
    {
        try {
            // Jalankan core service K-Means
            $output = $this->kMeansService->runClustering();
            
            $run = true;
            $totalIterasi = $output['total_iterasi'];
            $logIterasi = $output['log_iterasi'];
            $centroidAkhir = $output['centroid_akhir'];
            $centroidAwal = $output['centroid_awal'];
            $totalSantri = Santri::count();

            return view('hasil.proses', compact('run', 'totalIterasi', 'logIterasi', 'centroidAkhir', 'centroidAwal', 'totalSantri'))
                ->with('success', 'Algoritma K-Means berhasil dijalankan dan dikalkulasikan!');
        } catch (\Exception $e) {
            return redirect()->route('hasil.proses-form')->with('error', $e->getMessage());
        }
    }
}
