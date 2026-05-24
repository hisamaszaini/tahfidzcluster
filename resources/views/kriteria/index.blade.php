@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200">
        <h1 class="text-lg font-semibold tracking-tight text-slate-800">Kriteria Pengukuran Akademik</h1>
        <p class="text-slate-400 text-xs font-medium mt-0.5">Daftar dimensi utama yang digunakan untuk menilai dan mengelompokkan karakteristik tahfidz santri.</p>
    </div>

    <!-- Info Note -->
    <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl text-xs font-medium flex items-start space-x-3">
        <span class="text-base">💡</span>
        <div>
            <span class="block font-semibold">Catatan Metodologi K-Means:</span>
            <span class="block text-slate-500 font-normal mt-0.5">Pengelompokan santri pada aplikasi ini dihitung menggunakan <strong>jarak Euclidean murni</strong> tanpa melibatkan bobot kriteria (<em>weight</em>) maupun tipe kriteria (<em>benefit/cost</em>). Seluruh kriteria dianggap memiliki derajat kontribusi yang setara secara geometris dalam ruang 3-dimensi.</span>
        </div>
    </div>

    <!-- Criteria Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                        <th class="px-6 py-4 text-center w-24">Kode</th>
                        <th class="px-6 py-4">Nama Dimensi Kriteria</th>
                        <th class="px-6 py-4">Deskripsi Karakteristik Pengukuran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-600 font-medium">
                    @foreach($kriterias as $kriteria)
                    <tr class="hover:bg-slate-50/40 transition-colors">
                        <!-- Kode -->
                        <td class="px-6 py-5 text-center">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-semibold tracking-wider">
                                {{ $kriteria->kode }}
                            </span>
                        </td>

                        <!-- Nama -->
                        <td class="px-6 py-5 font-semibold text-slate-800 text-sm">
                            {{ $kriteria->nama }}
                        </td>

                        <td class="px-6 py-5 text-slate-400 font-normal leading-relaxed">
                            @if($kriteria->kode == 'C1')
                            Tingkat kelancaran dan jumlah hafalan juz/lembaran baru yang disetorkan santri kepada Musyrif pembina setiap pekan.
                            @elseif($kriteria->kode == 'C2')
                            Tingkat konsistensi dan kekuatan pengulangan hafalan lama (<em>murojaah</em>) santri untuk menjaga kemutkinan hafalan.
                            @else
                            Ketepatan pelafalan huruf (<em>makhorijul huruf</em>), panjang-pendek (<em>mad</em>), dengung (<em>ghunnah</em>), serta keindahan tajwid.
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection