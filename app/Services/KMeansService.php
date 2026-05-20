<?php

namespace App\Services;

use App\Models\Santri;
use App\Models\Hasil;
use App\Models\Nilai;
use Illuminate\Support\Facades\DB;

class KMeansService
{
    /**
     * Jalankan proses clustering K-Means.
     *
     * @param int $maxIterations Batas maksimal iterasi.
     * @return array Log lengkap iterasi untuk kebutuhan visualisasi.
     */
    public function runClustering(int $maxIterations = 100): array
    {
        // 1. Ambil data kriteria dan semua santri dengan nilainya
        $santris = Santri::with('nilai')->get();
        
        if ($santris->isEmpty()) {
            throw new \Exception("Data santri masih kosong. Silakan isi data terlebih dahulu.");
        }

        // Siapkan dataset dalam bentuk koordinat: [santri_id => [c1, c2, c3]]
        $dataset = [];
        $santriInfo = []; // Menyimpan nama dan detail untuk log

        foreach ($santris as $santri) {
            $scores = [
                'C1' => 0, // Hafalan
                'C2' => 0, // Murojaah
                'C3' => 0  // Tahsin
            ];

            foreach ($santri->nilai as $nilai) {
                if ($nilai->kriteria_id == 1) $scores['C1'] = (double)$nilai->nilai;
                if ($nilai->kriteria_id == 2) $scores['C2'] = (double)$nilai->nilai;
                if ($nilai->kriteria_id == 3) $scores['C3'] = (double)$nilai->nilai;
            }

            $dataset[$santri->id] = $scores;
            $santriInfo[$santri->id] = [
                'nama' => $santri->nama,
                'jenis_kelamin' => $santri->jenis_kelamin,
            ];
        }

        // 2. Inisialisasi Centroid Awal secara Objektif & Dinamis (Metode Persentil)
        // Urutkan dataset berdasarkan skor rata-rata kriteria (C1 + C2 + C3) / 3
        $sortedDataset = collect($dataset)->map(function ($scores, $id) use ($santriInfo) {
            $average = ($scores['C1'] + $scores['C2'] + $scores['C3']) / 3;
            return [
                'id' => $id,
                'scores' => $scores,
                'average' => $average,
                'nama' => $santriInfo[$id]['nama']
            ];
        })->sortBy('average')->values()->all();

        $countDataset = count($sortedDataset);
        
        // Tentukan indeks persentil 15%, 50% (median), dan 85%
        $idxCukup = max(0, min($countDataset - 1, (int)floor($countDataset * 0.15)));
        $idxBaik = max(0, min($countDataset - 1, (int)floor($countDataset * 0.50)));
        $idxSangatBaik = max(0, min($countDataset - 1, (int)floor($countDataset * 0.85)));

        $santriCukup = $sortedDataset[$idxCukup];
        $santriBaik = $sortedDataset[$idxBaik];
        $santriSangatBaik = $sortedDataset[$idxSangatBaik];

        $centroids = [
            'Cukup' => $santriCukup['scores'],
            'Baik' => $santriBaik['scores'],
            'Sangat Baik' => $santriSangatBaik['scores']
        ];

        $centroidAwalInfo = [
            'Cukup' => [
                'nama' => $santriCukup['nama'],
                'scores' => $santriCukup['scores']
            ],
            'Baik' => [
                'nama' => $santriBaik['nama'],
                'scores' => $santriBaik['scores']
            ],
            'Sangat Baik' => [
                'nama' => $santriSangatBaik['nama'],
                'scores' => $santriSangatBaik['scores']
            ]
        ];

        $history = []; // Untuk mencatat pergeseran centroid dan jarak per iterasi
        $converged = false;
        $iteration = 0;

        while (!$converged && $iteration < $maxIterations) {
            $iteration++;
            $assignments = [
                'Cukup' => [],
                'Baik' => [],
                'Sangat Baik' => []
            ];

            $iterationLog = [
                'centroids' => $centroids,
                'details' => []
            ];

            // A. Hitung jarak Euclidean untuk tiap santri ke masing-masing centroid
            foreach ($dataset as $santriId => $scores) {
                // Jarak ke C1 (Cukup)
                $distC1 = $this->calculateEuclideanDistance($scores, $centroids['Cukup']);
                // Jarak ke C2 (Baik)
                $distC2 = $this->calculateEuclideanDistance($scores, $centroids['Baik']);
                // Jarak ke C3 (Sangat Baik)
                $distC3 = $this->calculateEuclideanDistance($scores, $centroids['Sangat Baik']);

                // Tentukan cluster terdekat (jarak terkecil)
                $minDist = min($distC1, $distC2, $distC3);
                $cluster = '';

                if ($minDist === $distC1) {
                    $cluster = 'Cukup';
                } elseif ($minDist === $distC2) {
                    $cluster = 'Baik';
                } else {
                    $cluster = 'Sangat Baik';
                }

                $assignments[$cluster][] = $santriId;

                // Log rincian untuk iterasi saat ini
                $iterationLog['details'][$santriId] = [
                    'nama' => $santriInfo[$santriId]['nama'],
                    'scores' => $scores,
                    'jarak_c1' => round($distC1, 4),
                    'jarak_c2' => round($distC2, 4),
                    'jarak_c3' => round($distC3, 4),
                    'kluster_sementara' => $cluster
                ];
            }

            // B. Hitung ulang koordinat centroid baru (rata-rata nilai anggota cluster)
            $newCentroids = [];
            foreach ($assignments as $clusterName => $members) {
                if (count($members) > 0) {
                    $sumC1 = 0;
                    $sumC2 = 0;
                    $sumC3 = 0;

                    foreach ($members as $memberId) {
                        $sumC1 += $dataset[$memberId]['C1'];
                        $sumC2 += $dataset[$memberId]['C2'];
                        $sumC3 += $dataset[$memberId]['C3'];
                    }

                    $count = count($members);
                    $newCentroids[$clusterName] = [
                        'C1' => round($sumC1 / $count, 4),
                        'C2' => round($sumC2 / $count, 4),
                        'C3' => round($sumC3 / $count, 4)
                    ];
                } else {
                    // Jika cluster kosong, centroid tidak bergeser
                    $newCentroids[$clusterName] = $centroids[$clusterName];
                }
            }

            // C. Cek Konvergensi (apakah centroid bergeser?)
            $converged = $this->checkCentroidsConvergence($centroids, $newCentroids);
            
            // Catat log iterasi ini ke history
            $iterationLog['assignments_count'] = [
                'Cukup' => count($assignments['Cukup']),
                'Baik' => count($assignments['Baik']),
                'Sangat Baik' => count($assignments['Sangat Baik'])
            ];
            $history[] = $iterationLog;

            // Update centroid untuk iterasi selanjutnya
            $centroids = $newCentroids;
        }

        // 3. Simpan Hasil Akhir ke Database (tabel_hasil)
        DB::transaction(function () use ($dataset, $history) {
            // Kosongkan tabel hasil lama dengan delete (aman untuk transaksi MySQL)
            Hasil::query()->delete();

            // Ambil rincian iterasi terakhir
            $finalIteration = end($history);
            
            foreach ($dataset as $santriId => $scores) {
                $detail = $finalIteration['details'][$santriId];
                
                Hasil::create([
                    'santri_id' => $santriId,
                    'kluster' => $detail['kluster_sementara'],
                    'jarak_c1' => $detail['jarak_c1'],
                    'jarak_c2' => $detail['jarak_c2'],
                    'jarak_c3' => $detail['jarak_c3'],
                ]);
            }
        });

        return [
            'total_iterasi' => $iteration,
            'log_iterasi' => $history,
            'centroid_akhir' => $centroids,
            'centroid_awal' => $centroidAwalInfo,
        ];
    }

    /**
     * Dapatkan data centroid awal secara dinamis.
     */
    public function getInitialCentroids(): array
    {
        $santris = Santri::with('nilai')->get();
        if ($santris->isEmpty()) {
            return [];
        }

        $dataset = [];
        $santriInfo = [];

        foreach ($santris as $santri) {
            $scores = [
                'C1' => 0,
                'C2' => 0,
                'C3' => 0
            ];

            foreach ($santri->nilai as $nilai) {
                if ($nilai->kriteria_id == 1) $scores['C1'] = (double)$nilai->nilai;
                if ($nilai->kriteria_id == 2) $scores['C2'] = (double)$nilai->nilai;
                if ($nilai->kriteria_id == 3) $scores['C3'] = (double)$nilai->nilai;
            }

            $dataset[$santri->id] = $scores;
            $santriInfo[$santri->id] = [
                'nama' => $santri->nama,
            ];
        }

        $sortedDataset = collect($dataset)->map(function ($scores, $id) use ($santriInfo) {
            $average = ($scores['C1'] + $scores['C2'] + $scores['C3']) / 3;
            return [
                'id' => $id,
                'scores' => $scores,
                'average' => $average,
                'nama' => $santriInfo[$id]['nama']
            ];
        })->sortBy('average')->values()->all();

        $countDataset = count($sortedDataset);
        if ($countDataset === 0) {
            return [];
        }

        $idxCukup = max(0, min($countDataset - 1, (int)floor($countDataset * 0.15)));
        $idxBaik = max(0, min($countDataset - 1, (int)floor($countDataset * 0.50)));
        $idxSangatBaik = max(0, min($countDataset - 1, (int)floor($countDataset * 0.85)));

        $santriCukup = $sortedDataset[$idxCukup];
        $santriBaik = $sortedDataset[$idxBaik];
        $santriSangatBaik = $sortedDataset[$idxSangatBaik];

        return [
            'Cukup' => [
                'nama' => $santriCukup['nama'],
                'scores' => $santriCukup['scores']
            ],
            'Baik' => [
                'nama' => $santriBaik['nama'],
                'scores' => $santriBaik['scores']
            ],
            'Sangat Baik' => [
                'nama' => $santriSangatBaik['nama'],
                'scores' => $santriSangatBaik['scores']
            ]
        ];
    }

    /**
     * Hitung Jarak Euclidean 3-Dimensi.
     */
    private function calculateEuclideanDistance(array $point1, array $point2): float
    {
        $sum = pow($point1['C1'] - $point2['C1'], 2) +
               pow($point1['C2'] - $point2['C2'], 2) +
               pow($point1['C3'] - $point2['C3'], 2);
               
        return sqrt($sum);
    }

    /**
     * Cek apakah koordinat centroid sama persis antara sebelum dan sesudah update.
     */
    private function checkCentroidsConvergence(array $oldCentroids, array $newCentroids): bool
    {
        foreach ($oldCentroids as $cluster => $coords) {
            if ($coords['C1'] !== $newCentroids[$cluster]['C1'] ||
                $coords['C2'] !== $newCentroids[$cluster]['C2'] ||
                $coords['C3'] !== $newCentroids[$cluster]['C3']) {
                return false; // Ada pergeseran
            }
        }
        return true; // Konvergen
    }
}
