@extends('layouts.app')

@section('content')
<!-- MathJax Configuration and Script for Beautiful Mathematical Equations -->
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

<div class="space-y-8">
    <!-- Greeting Banner -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 p-6 sm:p-8 rounded-3xl shadow-sm text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <span class="px-3 py-1 bg-white/20 rounded-full text-xxs font-semibold tracking-wider uppercase">Sistem SPK Aktif</span>
            <h1 class="text-2xl font-semibold tracking-tight mt-2">Assalamu'alaikum, {{ Auth::user()->name ?? 'Ustadz' }}!</h1>
            <p class="text-emerald-100 text-xs font-medium max-w-xl">Selamat datang di panel administrasi akademik TahfidzCluster. Gunakan menu sebelah kiri untuk mengelola data santri, nilai, dan melakukan analisis kluster.</p>
        </div>
        <div class="flex-shrink-0 flex gap-2">
            <a href="{{ route('santri.index') }}" class="px-4 py-2.5 bg-white text-emerald-700 hover:bg-emerald-50 rounded-xl font-semibold text-xs transition shadow-xs flex items-center space-x-1.5">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Kelola Santri</span>
            </a>
            <a href="{{ url('hasil/proses') }}" class="px-4 py-2.5 bg-emerald-500 text-white hover:bg-emerald-400 rounded-xl font-semibold text-xs border border-emerald-400/20 transition shadow-xs flex items-center space-x-1.5">
                <i class="fa-solid fa-bolt"></i>
                <span>Jalankan K-Means</span>
            </a>
        </div>
    </div>

    <!-- Stats Widget Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Musyrif -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xxs font-semibold text-slate-400 uppercase tracking-wider">Total Musyrif</span>
                <span class="block text-2xl font-semibold text-slate-800">{{ $totalMusyrif }}</span>
                <span class="block text-xxs text-slate-400 font-medium">Pembina Halaqah</span>
            </div>
            <div class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center text-sm border border-slate-100">
                <i class="fa-solid fa-user-tie"></i>
            </div>
        </div>

        <!-- Card 2: Santri -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xxs font-semibold text-slate-400 uppercase tracking-wider">Total Santri</span>
                <span class="block text-2xl font-semibold text-slate-800">{{ $totalSantri }}</span>
                <span class="block text-xxs text-slate-400 font-medium">Aktif Terdaftar</span>
            </div>
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-sm border border-emerald-100/50">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
        </div>

        <!-- Card 3: Kriteria -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xxs font-semibold text-slate-400 uppercase tracking-wider">Kriteria Pengukuran</span>
                <span class="block text-2xl font-semibold text-slate-800">{{ $totalKriteria }}</span>
                <span class="block text-xxs text-slate-400 font-medium">Hafalan, Murojaah, Tahsin</span>
            </div>
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-sm border border-indigo-100/50">
                <i class="fa-solid fa-sliders"></i>
            </div>
        </div>

        <!-- Card 4: Progress Kelengkapan Nilai -->
        <div class="bg-white p-5 rounded-2xl shadow-xs border border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <span class="text-xxs font-semibold text-slate-400 uppercase tracking-wider">Nilai Terinput</span>
                <span class="block text-2xl font-semibold text-slate-800">{{ $totalSantriDinilai }} <span class="text-slate-400 text-xs font-normal">/ {{ $totalSantri }}</span></span>
                @php
                    $pct = $totalSantri > 0 ? round(($totalSantriDinilai / $totalSantri) * 100) : 0;
                @endphp
                <span class="block text-xxs text-emerald-600 font-medium">{{ $pct }}% Santri Selesai Dinilai</span>
            </div>
            <div class="w-11 h-11 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-sm border border-amber-100/50">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Cols: Clustering Status & Quick Intro -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Clustering Status Card -->
            <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-100">
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-sm font-semibold text-slate-800">Status & Pembagian Kluster Santri</h3>
                    <p class="text-xxs text-slate-400 mt-0.5">Hasil pemetaan kluster santri aktif berdasarkan kalkulasi K-Means terakhir.</p>
                </div>

                @if(!$isClustered)
                    <!-- Unclustered alert -->
                    <div class="p-6 bg-slate-50 border border-slate-200 rounded-2xl text-center space-y-3">
                        <div class="text-xl text-slate-400"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <h4 class="text-xs font-semibold text-slate-700">Analisis Kluster Belum Dijalankan</h4>
                        <p class="text-xxs text-slate-400 max-w-sm mx-auto">Database mendeteksi belum ada penugasan kluster santri yang disimpan. Silakan picu kalkulasi matematika untuk melahirkan data kluster.</p>
                        <a href="{{ url('hasil/proses') }}" class="inline-block px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xxs font-semibold rounded-lg shadow-sm transition">
                            Jalankan Clustering Sekarang
                        </a>
                    </div>
                @else
                    <!-- Display Cluster Share -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <!-- Cukup -->
                            <div class="p-3 bg-amber-50/50 border border-amber-100/50 rounded-xl">
                                <span class="block text-xxs text-amber-800 font-semibold uppercase flex items-center justify-center gap-1.5"><i class="fa-solid fa-arrow-trend-down"></i> Cukup (C1)</span>
                                <span class="block text-lg font-semibold text-slate-800 mt-1">{{ $clusterCounts['Cukup'] }}</span>
                                <span class="block text-xxs text-slate-400 font-medium">Santri</span>
                            </div>
                            <!-- Baik -->
                            <div class="p-3 bg-indigo-50/50 border border-indigo-100/50 rounded-xl">
                                <span class="block text-xxs text-indigo-800 font-semibold uppercase flex items-center justify-center gap-1.5"><i class="fa-solid fa-chart-simple"></i> Baik (C2)</span>
                                <span class="block text-lg font-semibold text-slate-800 mt-1">{{ $clusterCounts['Baik'] }}</span>
                                <span class="block text-xxs text-slate-400 font-medium">Santri</span>
                            </div>
                            <!-- Sangat Baik -->
                            <div class="p-3 bg-emerald-50/50 border border-emerald-100/50 rounded-xl">
                                <span class="block text-xxs text-emerald-800 font-semibold uppercase flex items-center justify-center gap-1.5"><i class="fa-solid fa-star"></i> Sangat Baik (C3)</span>
                                <span class="block text-lg font-semibold text-slate-800 mt-1">{{ $clusterCounts['Sangat Baik'] }}</span>
                                <span class="block text-xxs text-slate-400 font-medium">Santri</span>
                            </div>
                        </div>

                        <!-- Info note -->
                        <div class="p-3.5 bg-emerald-50 text-emerald-800 rounded-xl text-xxs font-medium flex items-start space-x-2.5">
                            <span class="mt-0.5"><i class="fa-solid fa-lightbulb"></i></span>
                            <span>Sistem menggunakan <strong class="font-semibold text-emerald-950">Inisialisasi Centroid Dinamis & Objektif</strong> berbasis letak persentil ($P_{15}$ Cukup, $P_{50}$ Baik, dan $P_{85}$ Sangat Baik) dari skor rata-rata santri untuk memastikan pengelompokan yang transparan, adil, dan siap menampung data skala besar (200+ santri).</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Methodology Quick Card -->
            <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-100 space-y-3">
                <h3 class="text-sm font-semibold text-slate-800 border-b border-slate-100 pb-3">Metodologi SPK K-Means</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-slate-600 text-xxs font-medium leading-relaxed">
                    <div class="space-y-2">
                        <span class="block font-semibold text-emerald-700">1. Jarak Euclidean Murni</span>
                        <p>K-Means memetakan jarak geometri antara tiap santri ke pusat centroid menggunakan formula <strong class="font-semibold text-slate-700">jarak Euclidean murni</strong> tanpa pembobotan kriteria (<em>weight</em> $w_j = 1$ untuk semua kriteria) berdasarkan skor Hafalan, Murojaah, dan Tahsin:</p>
                        <div class="overflow-x-auto max-w-full py-2 text-center font-semibold text-slate-700 bg-slate-50 rounded-xl border border-slate-100/50 mt-1">
                            $$d(p, q) = \sqrt{\sum_{j=1}^{3} (p_j - q_j)^2}$$
                        </div>
                    </div>
                    <div class="space-y-2">
                        <span class="block font-semibold text-emerald-700">2. Inisialisasi Dinamis & Objektif</span>
                        <p>Penentuan pusat awal ($K = 3$) dilakukan secara otomatis menggunakan metode sebaran persentil populasi dari skor rata-rata santri $\bar{x} = \frac{C_1+C_2+C_3}{3}$:</p>
                        <ul class="space-y-1.5 text-slate-500 pl-2 mt-2">
                            <li class="flex items-start gap-2">
                                <span class="text-amber-600 font-bold">•</span>
                                <span><strong class="font-semibold text-slate-700">C1 (Cukup):</strong> Dipetakan pada persentil 15% ($P_{15}$) dari sebaran skor kelompok bawah.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-indigo-600 font-bold">•</span>
                                <span><strong class="font-semibold text-slate-700">C2 (Baik):</strong> Dipetakan pada persentil 50% / Median ($P_{50}$) dari sebaran skor kelompok tengah.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-emerald-600 font-bold">•</span>
                                <span><strong class="font-semibold text-slate-700">C3 (Sangat Baik):</strong> Dipetakan pada persentil 85% ($P_{85}$) dari sebaran skor kelompok atas.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 1 Col: Recent Activities / Additions -->
        <div class="bg-white p-6 rounded-2xl shadow-xs border border-slate-100 flex flex-col justify-between">
            <div>
                <div class="border-b border-slate-100 pb-4 mb-4">
                    <h3 class="text-sm font-semibold text-slate-800 font-semibold">Santri Baru Ditambahkan</h3>
                    <p class="text-xxs text-slate-400 mt-0.5">Daftar santri terbaru yang baru saja didaftarkan ke sistem.</p>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentSantris as $santri)
                        <div class="py-3 flex items-center justify-between text-slate-700">
                            <div>
                                <span class="block text-xs font-semibold text-slate-800">{{ $santri->nama }}</span>
                                <span class="block text-xxs text-slate-400 font-medium">Alamat: {{ Str::limit($santri->alamat, 20) }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-lg text-xxs font-semibold {{ $santri->jenis_kelamin == 'L' ? 'bg-sky-50 text-sky-600' : 'bg-pink-50 text-pink-600' }}">
                                {{ $santri->jenis_kelamin }}
                            </span>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-400 text-xxs">
                            Belum ada data santri terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('santri.index') }}" class="w-full mt-4 py-2 border border-slate-200 hover:bg-slate-50 rounded-xl text-xxs font-semibold text-slate-600 text-center flex items-center justify-center gap-1.5 transition">
                <span>Lihat Seluruh Santri</span>
                <i class="fa-solid fa-graduation-cap"></i>
            </a>
        </div>

    </div>
</div>
@endsection
