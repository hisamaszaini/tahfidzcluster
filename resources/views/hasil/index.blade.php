@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-lg font-semibold tracking-tight text-slate-800">Laporan Hasil Akhir Clustering</h1>
            <p class="text-slate-400 text-xs font-medium mt-0.5">Ringkasan sebaran karakteristik kelompok santri tahfidz yang tersimpan di sistem.</p>
        </div>
        
        <div class="flex gap-2">
            <button onclick="printReport()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl font-semibold text-xs transition shadow-xs flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Laporan (PDF)</span>
            </button>
            <a href="{{ url('hasil/proses') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-semibold text-xs transition shadow-xs flex items-center space-x-2">
                <i class="fa-solid fa-bolt"></i>
                <span>Hitung Ulang K-Means</span>
            </a>
        </div>
    </div>

    @if($hasils->isEmpty())
        <!-- Empty state alert -->
        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4 max-w-xl mx-auto shadow-xs">
            <div class="text-4xl text-slate-300"><i class="fa-solid fa-chart-column"></i></div>
            <h2 class="text-sm font-semibold text-slate-800">Belum Ada Hasil Clustering</h2>
            <p class="text-slate-400 text-xs font-medium leading-relaxed">Sistem belum menyimpan hasil clustering santri. Silakan jalankan perhitungan algoritma K-Means terlebih dahulu melalui menu Proses K-Means.</p>
            <a href="{{ url('hasil/proses') }}" class="inline-block px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow-md transition">
                Jalankan K-Means Clustering Sekarang
            </a>
        </div>
    @else
        <!-- Cluster Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <!-- Cukup Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xxs font-semibold text-amber-600 uppercase tracking-wider">Kluster C1: Cukup</span>
                        <span class="block text-2xl font-semibold text-slate-800">{{ $clusterCounts['Cukup'] }} <span class="text-slate-400 text-xs font-normal">Santri</span></span>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-sm border border-amber-100/50">
                        <i class="fa-solid fa-arrow-trend-down"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xxs text-slate-400 font-medium">
                    <div class="flex justify-between">
                        <span>C1 (Hafalan) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Cukup']['C1'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C2 (Murojaah) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Cukup']['C2'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C3 (Tahsin) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Cukup']['C3'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Baik Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xxs font-semibold text-indigo-600 uppercase tracking-wider">Kluster C2: Baik</span>
                        <span class="block text-2xl font-semibold text-slate-800">{{ $clusterCounts['Baik'] }} <span class="text-slate-400 text-xs font-normal">Santri</span></span>
                    </div>
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-sm border border-indigo-100/50">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xxs text-slate-400 font-medium">
                    <div class="flex justify-between">
                        <span>C1 (Hafalan) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Baik']['C1'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C2 (Murojaah) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Baik']['C2'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C3 (Tahsin) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Baik']['C3'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Sangat Baik Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <span class="text-xxs font-semibold text-emerald-600 uppercase tracking-wider">Kluster C3: Sangat Baik</span>
                        <span class="block text-2xl font-semibold text-slate-800">{{ $clusterCounts['Sangat Baik'] }} <span class="text-slate-400 text-xs font-normal">Santri</span></span>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-sm border border-emerald-100/50">
                        <i class="fa-solid fa-award"></i>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xxs text-slate-400 font-medium">
                    <div class="flex justify-between">
                        <span>C1 (Hafalan) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Sangat Baik']['C1'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C2 (Murojaah) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Sangat Baik']['C2'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>C3 (Tahsin) Rata-Rata:</span>
                        <span class="font-semibold text-slate-700">{{ $clusterAverages['Sangat Baik']['C3'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visual Analytics Chart.js -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
            <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider mb-4">Grafik Karakteristik Skor Rata-Rata per Kluster</h3>
            <div class="relative w-full h-80">
                <canvas id="clusterChart" data-averages='@json($clusterAverages)'></canvas>
            </div>
        </div>

        <!-- Final Results Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
            <div class="p-5 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-800">Tabel Rincian Hasil Pengelompokan</h3>
                <p class="text-xxs text-slate-400 mt-0.5">Detail skor input, rata-rata skor kriteria untuk penentuan ranking, jarak Euclidean ke masing-masing kluster, dan hasil penugasan akhir santri.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <th class="px-6 py-4 text-center w-16">Rank</th>
                            <th class="px-6 py-4">Nama Santri</th>
                            <th class="px-6 py-4 text-center">C1</th>
                            <th class="px-6 py-4 text-center">C2</th>
                            <th class="px-6 py-4 text-center">C3</th>
                            <th class="px-6 py-4 text-center bg-slate-100/55 text-slate-700">Rata-rata Skor</th>
                            <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C1</th>
                            <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C2</th>
                            <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C3</th>
                            <th class="px-6 py-4 text-center">Kluster Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                        @foreach($hasils as $hasil)
                             @php
                                $hafalan = 0;
                                $murojaah = 0;
                                $tahsin = 0;
                                foreach($hasil->santri->nilai as $nilai) {
                                    if($nilai->kriteria_id == 1) $hafalan = $nilai->nilai;
                                    if($nilai->kriteria_id == 2) $murojaah = $nilai->nilai;
                                    if($nilai->kriteria_id == 3) $tahsin = $nilai->nilai;
                                }
                                $c1 = $hasil->jarak_c1;
                                $c2 = $hasil->jarak_c2;
                                $c3 = $hasil->jarak_c3;
                                $min = min($c1, $c2, $c3);
                            @endphp
                            <tr class="hover:bg-slate-50/40 transition-colors">
                                <!-- Ranking Badge -->
                                <td class="px-6 py-4 text-center">
                                    @if($loop->iteration == 1)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-800 text-xs font-bold" title="Peringkat 1">🥇</span>
                                    @elseif($loop->iteration == 2)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 text-slate-800 text-xs font-bold" title="Peringkat 2">🥈</span>
                                    @elseif($loop->iteration == 3)
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-600/10 text-amber-800 text-xs font-bold" title="Peringkat 3">🥉</span>
                                    @else
                                        <span class="text-slate-400 text-xxs font-bold">#{{ $loop->iteration }}</span>
                                    @endif
                                </td>

                                <!-- Nama -->
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $hasil->santri->nama }}
                                </td>

                                <!-- C1, C2, C3 -->
                                <td class="px-6 py-4 text-center text-slate-500">{{ $hafalan }}</td>
                                <td class="px-6 py-4 text-center text-slate-500">{{ $murojaah }}</td>
                                <td class="px-6 py-4 text-center text-slate-500">{{ $tahsin }}</td>

                                <!-- Rata-rata Skor -->
                                <td class="px-6 py-4 text-center bg-slate-50/50 text-slate-800 font-bold border-x border-slate-100/55">{{ $hasil->skor_rata }}</td>

                                <!-- Jarak Euclidean (Highlighting minimum value in green) -->
                                <td class="px-6 py-4 text-center bg-slate-50/20 {{ $c1 == $min ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c1, 4) }}
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/20 {{ $c2 == $min ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c2, 4) }}
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/20 {{ $c3 == $min ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                    {{ round($c3, 4) }}
                                </td>

                                <!-- Kluster -->
                                <td class="px-6 py-4 text-center">
                                    @if($hasil->kluster == 'Cukup')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xxs font-semibold bg-amber-50 text-amber-700 border border-amber-100/50">
                                            <i class="fa-solid fa-arrow-trend-down mr-1"></i> Cukup
                                        </span>
                                    @elseif($hasil->kluster == 'Baik')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xxs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100/50">
                                            <i class="fa-solid fa-chart-simple mr-1"></i> Baik
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xxs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
                                            <i class="fa-solid fa-award mr-1"></i> Sangat Baik
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Script Chart.js -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const ctx = document.getElementById('clusterChart').getContext('2d');
                
                // Data kriteria rata-rata per kluster dari canvas dataset
                const averagesRaw = JSON.parse(document.getElementById('clusterChart').getAttribute('data-averages'));
                
                const cukup = averagesRaw['Cukup'] ?? {C1: 0, C2: 0, C3: 0};
                const baik = averagesRaw['Baik'] ?? {C1: 0, C2: 0, C3: 0};
                const sangatBaik = averagesRaw['Sangat Baik'] ?? {C1: 0, C2: 0, C3: 0};

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['C1 (Hafalan)', 'C2 (Murojaah)', 'C3 (Tahsin)'],
                        datasets: [
                            {
                                label: 'Kluster Cukup (C1)',
                                data: [cukup.C1, cukup.C2, cukup.C3],
                                backgroundColor: 'rgba(217, 119, 6, 0.75)', // amber-600
                                borderColor: 'rgb(217, 119, 6)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            },
                            {
                                label: 'Kluster Baik (C2)',
                                data: [baik.C1, baik.C2, baik.C3],
                                backgroundColor: 'rgba(79, 70, 229, 0.75)', // indigo-600
                                borderColor: 'rgb(79, 70, 229)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            },
                            {
                                label: 'Kluster Sangat Baik (C3)',
                                data: [sangatBaik.C1, sangatBaik.C2, sangatBaik.C3],
                                backgroundColor: 'rgba(5, 150, 105, 0.75)', // emerald-600
                                borderColor: 'rgb(5, 150, 105)',
                                borderWidth: 1.5,
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        family: 'Instrument Sans',
                                        size: 11,
                                        weight: '500'
                                    },
                                    color: '#475569'
                                }
                            },
                            tooltip: {
                                titleFont: { family: 'Instrument Sans', size: 12, weight: '600' },
                                bodyFont: { family: 'Instrument Sans', size: 11 }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: { family: 'Instrument Sans', size: 11, weight: '500' },
                                    color: '#64748b'
                                },
                                grid: { display: false }
                            },
                            y: {
                                min: 0,
                                max: 100,
                                ticks: {
                                    font: { family: 'Instrument Sans', size: 11 },
                                    color: '#64748b'
                                },
                                grid: { color: '#f1f5f9' }
                            }
                        }
                    }
                });
            });
        </script>
        
        <!-- Hidden print iframe -->
        <iframe id="print-iframe" class="hidden"></iframe>
        
        <!-- Client-side printing script -->
        <script>
            function printReport() {
                const iframe = document.getElementById('print-iframe');
                iframe.src = "{{ route('hasil.cetak') }}";
            }
        </script>
    @endif
</div>
@endsection
