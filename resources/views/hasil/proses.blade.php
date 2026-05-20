@extends('layouts.app')

@section('content')
<script>
    window.MathJax = {
        tex: {
            inlineMath: [['$', '$'], ['\\(', '\\)']],
            displayMath: [['$$', '$$'], ['\\[', '\\]']],
            processEscapes: true
        }
    };
</script>
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js" defer></script>

<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200">
        <h1 class="text-lg font-semibold tracking-tight text-slate-800">Proses Perhitungan K-Means</h1>
        <p class="text-slate-400 text-xs font-medium mt-0.5">Halaman kalkulasi algoritma pengelompokan secara transparan, akurat, dan step-by-step.</p>
    </div>

    @if(!$run)
        <div class="mx-auto bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-xs">
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-8 text-white text-center space-y-3 relative">
                <span class="px-2.5 py-1 bg-white/20 rounded-full text-xxs font-semibold tracking-wider uppercase">Fase Perhitungan</span>
                <h2 class="text-xl font-semibold tracking-tight">Mulai Pengelompokan Karakteristik Santri</h2>
                <p class="text-emerald-100 text-xs font-medium max-w-lg mx-auto">Sistem akan menarik data koordinat 3-dimensi (C1 Hafalan, C2 Murojaah, C3 Tahsin) dari seluruh santri yang aktif dinilai, dan menghitung pengelompokannya secara otomatis.</p>
                
                <form action="{{ route('hasil.proses') }}" method="POST" class="pt-4">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-white hover:bg-emerald-50 text-emerald-700 rounded-xl font-semibold text-xs transition shadow-md duration-200">
                        <i class="fa-solid fa-bolt mr-1.5"></i> Jalankan Kalkulasi K-Means
                    </button>
                </form>
            </div>

            <!-- Parameters Overview -->
            <div class="p-6 sm:p-8 space-y-6 text-slate-600 text-xxs font-medium leading-relaxed">
                <h3 class="text-xs font-semibold text-slate-800 border-b border-slate-100 pb-3">Konfigurasi Pengelompokan (Fixed Parameters)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Param 1 -->
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1">
                        <span class="block font-semibold text-slate-700">Jumlah Kluster ($K = 3$)</span>
                        <p class="text-slate-400 text-xxxs">Ditetapkan sebanyak <strong>$K = 3$</strong> kelompok (Cukup, Baik, dan Sangat Baik) berdasarkan target klasifikasi kompetensi akademik pondok.</p>
                    </div>

                    <!-- Param 2 -->
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1.5">
                        <span class="block font-semibold text-slate-700">Inisialisasi Centroid</span>
                        <p class="text-slate-400 text-xxxs leading-relaxed">
                            Ditentukan secara <strong>Dinamis & Objektif</strong> berbasis letak persentil ($P$) dari skor rata-rata santri $\bar{x} = \frac{C_1+C_2+C_3}{3}$:
                        </p>
                        <div class="text-[10px] text-slate-500 font-medium space-y-1 bg-white p-2 rounded-lg border border-slate-100">
                            @if(!empty($centroidAwal))
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-amber-700">C1 (Cukup - $P_{15}$):</span>
                                    <span class="text-slate-600 truncate max-w-[120px]" title="{{ $centroidAwal['Cukup']['nama'] }}">{{ $centroidAwal['Cukup']['nama'] }}</span>
                                </div>
                                <div class="text-[9px] text-slate-400 pl-4 border-l border-amber-200">
                                    C1: {{ $centroidAwal['Cukup']['scores']['C1'] }} | C2: {{ $centroidAwal['Cukup']['scores']['C2'] }} | C3: {{ $centroidAwal['Cukup']['scores']['C3'] }}
                                </div>
                                
                                <div class="flex justify-between items-center mt-1 pt-1 border-t border-slate-50">
                                    <span class="font-bold text-indigo-700">C2 (Baik - $P_{50}$):</span>
                                    <span class="text-slate-600 truncate max-w-[120px]" title="{{ $centroidAwal['Baik']['nama'] }}">{{ $centroidAwal['Baik']['nama'] }}</span>
                                </div>
                                <div class="text-[9px] text-slate-400 pl-4 border-l border-indigo-200">
                                    C1: {{ $centroidAwal['Baik']['scores']['C1'] }} | C2: {{ $centroidAwal['Baik']['scores']['C2'] }} | C3: {{ $centroidAwal['Baik']['scores']['C3'] }}
                                </div>
                                
                                <div class="flex justify-between items-center mt-1 pt-1 border-t border-slate-50">
                                    <span class="font-bold text-emerald-700">C3 (Sangat Baik - $P_{85}$):</span>
                                    <span class="text-slate-600 truncate max-w-[120px]" title="{{ $centroidAwal['Sangat Baik']['nama'] }}">{{ $centroidAwal['Sangat Baik']['nama'] }}</span>
                                </div>
                                <div class="text-[9px] text-slate-400 pl-4 border-l border-emerald-200">
                                    C1: {{ $centroidAwal['Sangat Baik']['scores']['C1'] }} | C2: {{ $centroidAwal['Sangat Baik']['scores']['C2'] }} | C3: {{ $centroidAwal['Sangat Baik']['scores']['C3'] }}
                                </div>
                            @else
                                <span class="text-slate-400 italic">Data santri belum tersedia.</span>
                            @endif
                        </div>
                    </div>

                    <!-- Param 3 -->
                    <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl space-y-1 md:col-span-2">
                        <span class="block font-semibold text-slate-700">Kriteria Pembobot & Rumus Jarak</span>
                        <p class="text-slate-400 text-xxxs leading-relaxed">
                            Menggunakan <strong>jarak Euclidean murni</strong> tanpa pembobotan kriteria (<em>weight</em>) dengan bobot $w_j = 1$ untuk semua kriteria. Jarak geometris 3-Dimensi dihitung dengan formula:
                            <span class="block text-center font-mono font-semibold text-slate-700 bg-white py-1 px-2 rounded-lg border border-slate-100 mt-1">
                                $$d(p, q) = \sqrt{(p_{C1} - q_{C1})^2 + (p_{C2} - q_{C2})^2 + (p_{C3} - q_{C3})^2}$$
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- FINAL POST STATE: DISPLAY STEP-BY-STEP CALCULATION -->
        <div x-data="{ activeTab: 1 }" class="space-y-6">
            
            <!-- Success Convergence Info Banner -->
            <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-xs font-medium border border-emerald-100/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 shadow-xxs">
                <div class="flex items-start space-x-3">
                    <span class="text-emerald-600 text-base"><i class="fa-solid fa-circle-check"></i></span>
                    <div>
                        <span class="block font-semibold">Algoritma K-Means Konvergen Sempurna!</span>
                        <span class="block text-slate-500 font-normal mt-0.5">Sistem berhasil mencapai kestabilan (pusat centroid tidak lagi bergeser) pada <strong>Iterasi ke-{{ $totalIterasi }}</strong>. Pengelompokan santri dihentikan dan disimpan secara permanen.</span>
                    </div>
                </div>
                <a href="{{ route('hasil.index') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xxs font-semibold shadow-xs flex-shrink-0 transition">
                    Lihat Laporan Akhir <i class="fa-solid fa-chart-line ml-1.5"></i>
                </a>
            </div>

            <!-- Initial Centroid Iteration 0 Card -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 space-y-4 shadow-xxs">
                <div class="flex items-center space-x-2.5 border-b border-slate-100 pb-3">
                    <span class="text-emerald-600 text-sm"><i class="fa-solid fa-flag-checkered"></i></span>
                    <h3 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Hasil Inisialisasi Centroid Awal (Iterasi 0)</h3>
                </div>
                <p class="text-xxs text-slate-400">Berikut adalah data santri riil yang terpilih secara dinamis berdasarkan persentil rata-rata skor awal untuk menjadi acuan awal perhitungan jarak Euclidean.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Cukup Initial -->
                    <div class="p-4 bg-amber-50/50 border border-amber-100/50 rounded-2xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xxs text-amber-800 font-bold uppercase">C1: Cukup ($P_{15}$)</span>
                            <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-xxxs font-semibold">Bawah</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-800">{{ $centroidAwal['Cukup']['nama'] }}</span>
                        <div class="pt-2 border-t border-amber-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                            <div>
                                <span class="block text-slate-400 text-xxxs">C1 (Hafalan)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Cukup']['scores']['C1'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C2 (Mur)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Cukup']['scores']['C2'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C3 (Tahsin)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Cukup']['scores']['C3'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Baik Initial -->
                    <div class="p-4 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xxs text-indigo-800 font-bold uppercase">C2: Baik ($P_{50}$)</span>
                            <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xxxs font-semibold">Tengah</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-800">{{ $centroidAwal['Baik']['nama'] }}</span>
                        <div class="pt-2 border-t border-indigo-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                            <div>
                                <span class="block text-slate-400 text-xxxs">C1 (Hafalan)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Baik']['scores']['C1'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C2 (Mur)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Baik']['scores']['C2'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C3 (Tahsin)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Baik']['scores']['C3'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sangat Baik Initial -->
                    <div class="p-4 bg-emerald-50/50 border border-emerald-100/50 rounded-2xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xxs text-emerald-800 font-bold uppercase">C3: Sangat Baik ($P_{85}$)</span>
                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-xxxs font-semibold">Atas</span>
                        </div>
                        <span class="block text-xs font-semibold text-slate-800">{{ $centroidAwal['Sangat Baik']['nama'] }}</span>
                        <div class="pt-2 border-t border-emerald-100/30 grid grid-cols-3 gap-1 text-center text-xxs font-medium text-slate-500">
                            <div>
                                <span class="block text-slate-400 text-xxxs">C1 (Hafalan)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Sangat Baik']['scores']['C1'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C2 (Mur)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Sangat Baik']['scores']['C2'] }}</span>
                            </div>
                            <div>
                                <span class="block text-slate-400 text-xxxs">C3 (Tahsin)</span>
                                <span class="font-bold text-slate-700">{{ $centroidAwal['Sangat Baik']['scores']['C3'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Iterations Tab Buttons -->
            <div class="bg-white p-3 rounded-2xl border border-slate-200 flex flex-wrap gap-1.5 shadow-xxs">
                @for($i = 1; $i <= $totalIterasi; $i++)
                    <button @click="activeTab = {{ $i }}" 
                            :class="activeTab === {{ $i }} ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800'"
                            class="px-4 py-2 rounded-xl text-xxs font-semibold transition duration-150">
                        Iterasi {{ $i }} {{ $i == $totalIterasi ? '(Stabil)' : '' }}
                    </button>
                @endfor
            </div>

            <!-- Iterations Container (AlpineJS Switch) -->
            @foreach($logIterasi as $index => $log)
                @php
                    $iterationNum = $index + 1;
                @endphp
                <div x-show="activeTab === {{ $iterationNum }}" style="display: none;" class="space-y-6" x-transition>
                    
                    <!-- Grid: Centroids & Counts in this iteration -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Centroids in this iteration -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 md:col-span-2 space-y-4 shadow-xxs">
                            <h3 class="text-xs font-semibold text-slate-800 border-b border-slate-100 pb-3 uppercase tracking-wider">Koordinat Centroid Iterasi {{ $iterationNum }}</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <!-- Cukup Centroid -->
                                <div class="p-3 bg-amber-50/50 border border-amber-100/50 rounded-xl space-y-1">
                                    <span class="block text-xxs text-amber-800 font-semibold">C1 (Cukup)</span>
                                    <span class="block text-xs font-semibold text-slate-700">Hafalan: {{ $log['centroids']['Cukup']['C1'] }}</span>
                                    <span class="block text-xxs text-slate-400 font-medium">Mur: {{ $log['centroids']['Cukup']['C2'] }} | Tah: {{ $log['centroids']['Cukup']['C3'] }}</span>
                                </div>
                                <!-- Baik Centroid -->
                                <div class="p-3 bg-indigo-50/50 border border-indigo-100/50 rounded-xl space-y-1">
                                    <span class="block text-xxs text-indigo-800 font-semibold">C2 (Baik)</span>
                                    <span class="block text-xs font-semibold text-slate-700">Hafalan: {{ $log['centroids']['Baik']['C1'] }}</span>
                                    <span class="block text-xxs text-slate-400 font-medium">Mur: {{ $log['centroids']['Baik']['C2'] }} | Tah: {{ $log['centroids']['Baik']['C3'] }}</span>
                                </div>
                                <!-- Sangat Baik Centroid -->
                                <div class="p-3 bg-emerald-50/50 border border-emerald-100/50 rounded-xl space-y-1">
                                    <span class="block text-xxs text-emerald-800 font-semibold">C3 (Sangat Baik)</span>
                                    <span class="block text-xs font-semibold text-slate-700">Hafalan: {{ $log['centroids']['Sangat Baik']['C1'] }}</span>
                                    <span class="block text-xxs text-slate-400 font-medium">Mur: {{ $log['centroids']['Sangat Baik']['C2'] }} | Tah: {{ $log['centroids']['Sangat Baik']['C3'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Count in this iteration -->
                        <div class="bg-white p-5 rounded-2xl border border-slate-200 flex flex-col justify-between shadow-xxs">
                            <h3 class="text-xs font-semibold text-slate-800 border-b border-slate-100 pb-3 uppercase tracking-wider">Sebaran Anggota</h3>
                            <div class="space-y-2 pt-2 text-xxs text-slate-500 font-medium">
                                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                    <span>Kluster Cukup (C1):</span>
                                    <span class="font-bold text-amber-700 px-2 py-0.5 bg-amber-50 rounded-lg">{{ $log['assignments_count']['Cukup'] }} Santri</span>
                                </div>
                                <div class="flex justify-between items-center py-1 border-b border-slate-50">
                                    <span>Kluster Baik (C2):</span>
                                    <span class="font-bold text-indigo-700 px-2 py-0.5 bg-indigo-50 rounded-lg">{{ $log['assignments_count']['Baik'] }} Santri</span>
                                </div>
                                <div class="flex justify-between items-center py-1">
                                    <span>Kluster Sangat Baik (C3):</span>
                                    <span class="font-bold text-emerald-700 px-2 py-0.5 bg-emerald-50 rounded-lg">{{ $log['assignments_count']['Sangat Baik'] }} Santri</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Distance Calculation Table Card in this iteration -->
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                        <div class="p-5 border-b border-slate-100">
                            <h4 class="text-xs font-semibold text-slate-800 uppercase tracking-wider">Lembar Hitung Jarak Euclidean Iterasi {{ $iterationNum }}</h4>
                            <p class="text-xxs text-slate-400 mt-0.5">Tabel visualisasi perhitungan jarak setiap koordinat santri ke pusat ketiga centroid.</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                                        <th class="px-6 py-4">Nama Santri</th>
                                        <th class="px-6 py-4 text-center">C1</th>
                                        <th class="px-6 py-4 text-center">C2</th>
                                        <th class="px-6 py-4 text-center">C3</th>
                                        <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C1</th>
                                        <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C2</th>
                                        <th class="px-6 py-4 text-center bg-slate-100/50 text-slate-700">Jarak Ke C3</th>
                                        <th class="px-6 py-4 text-center">Taraf Sementara</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                                    @foreach($log['details'] as $santriId => $detail)
                                        @php
                                            $c1 = $detail['jarak_c1'];
                                            $c2 = $detail['jarak_c2'];
                                            $c3 = $detail['jarak_c3'];
                                            $minDist = min($c1, $c2, $c3);
                                        @endphp
                                        <tr class="hover:bg-slate-50/40 transition-colors">
                                            <!-- Nama -->
                                            <td class="px-6 py-3.5 font-semibold text-slate-800">
                                                {{ $detail['nama'] }}
                                            </td>

                                            <!-- Scores -->
                                            <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ $detail['scores']['C1'] }}</td>
                                            <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ $detail['scores']['C2'] }}</td>
                                            <td class="px-6 py-3.5 text-center text-slate-400 font-normal">{{ $detail['scores']['C3'] }}</td>

                                            <!-- Euclidean distances with smallest highlighted in emerald green -->
                                            <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c1 == $minDist ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                                {{ round($c1, 4) }}
                                            </td>
                                            <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c2 == $minDist ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                                {{ round($c2, 4) }}
                                            </td>
                                            <td class="px-6 py-3.5 text-center bg-slate-50/20 {{ $c3 == $minDist ? 'bg-emerald-50 text-emerald-700 border-x border-emerald-100/50 font-bold' : 'text-slate-400 font-normal' }}">
                                                {{ round($c3, 4) }}
                                            </td>

                                            <!-- Assigned Kluster -->
                                            <td class="px-6 py-3.5 text-center">
                                                @if($detail['kluster_sementara'] == 'Cukup')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xxs font-semibold bg-amber-50 text-amber-700 border border-amber-100/50">
                                                        <i class="fa-solid fa-arrow-trend-down mr-1"></i> Cukup
                                                    </span>
                                                @elseif($detail['kluster_sementara'] == 'Baik')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xxs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100/50">
                                                        <i class="fa-solid fa-chart-simple mr-1"></i> Baik
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xxs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100/50">
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

                </div>
            @endforeach

        </div>
    @endif
</div>
@endsection
