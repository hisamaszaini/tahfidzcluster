<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NilaiController extends Controller
{
    /**
     * Tampilkan skor kriteria santri dengan pencarian, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'nama');
        $sortDir = $request->input('sort_dir', 'asc');

        // Validasi kolom sort
        $allowedSorts = ['nama', 'C1', 'C2', 'C3'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nama';
        }
        $allowedDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedDirs)) {
            $sortDir = 'asc';
        }

        // Gunakan Left Join agar pengurutan per kriteria (C1, C2, C3) dapat dilakukan langsung secara database-level
        $query = Santri::select('tabel_santri.*')
            ->leftJoin('tabel_nilai as n1', function($join) {
                $join->on('tabel_santri.id', '=', 'n1.santri_id')->where('n1.kriteria_id', '=', 1);
            })
            ->leftJoin('tabel_nilai as n2', function($join) {
                $join->on('tabel_santri.id', '=', 'n2.santri_id')->where('n2.kriteria_id', '=', 2);
            })
            ->leftJoin('tabel_nilai as n3', function($join) {
                $join->on('tabel_santri.id', '=', 'n3.santri_id')->where('n3.kriteria_id', '=', 3);
            })
            ->with('nilai');

        if ($search) {
            $query->where('tabel_santri.nama', 'like', "%{$search}%");
        }

        // Eksekusi Sorting
        if ($sortBy === 'nama') {
            $query->orderBy('tabel_santri.nama', $sortDir);
        } elseif ($sortBy === 'C1') {
            $query->orderBy('n1.nilai', $sortDir);
        } elseif ($sortBy === 'C2') {
            $query->orderBy('n2.nilai', $sortDir);
        } elseif ($sortBy === 'C3') {
            $query->orderBy('n3.nilai', $sortDir);
        }

        $santris = $query->paginate(10)->withQueryString();

        // Cari santri yang belum diinputkan nilainya sama sekali
        $availableSantri = Santri::doesntHave('nilai')->orderBy('nama', 'asc')->get();

        return view('nilai.index', compact('santris', 'search', 'sortBy', 'sortDir', 'availableSantri'));
    }

    /**
     * Simpan nilai santri baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'santri_id' => 'required|exists:tabel_santri,id',
            'nilai_hafalan' => 'required|integer|min:0|max:100',
            'nilai_murojaah' => 'required|integer|min:0|max:100',
            'nilai_tahsin' => 'required|integer|min:0|max:100',
        ]);

        // Cek apakah santri ini sudah memiliki nilai
        $exists = Nilai::where('santri_id', $request->santri_id)->exists();
        if ($exists) {
            return redirect()->route('nilai.index')->with('error', 'Nilai untuk santri ini sudah terdaftar.');
        }

        DB::transaction(function () use ($request) {
            Nilai::create([
                'santri_id' => $request->santri_id,
                'kriteria_id' => 1,
                'nilai' => $request->nilai_hafalan
            ]);

            Nilai::create([
                'santri_id' => $request->santri_id,
                'kriteria_id' => 2,
                'nilai' => $request->nilai_murojaah
            ]);

            Nilai::create([
                'santri_id' => $request->santri_id,
                'kriteria_id' => 3,
                'nilai' => $request->nilai_tahsin
            ]);
        });

        return redirect()->route('nilai.index')->with('success', 'Skor nilai santri berhasil diinputkan!');
    }

    /**
     * Ubah nilai kriteria santri.
     */
    public function update(Request $request, $santriId)
    {
        $request->validate([
            'nilai_hafalan' => 'required|integer|min:0|max:100',
            'nilai_murojaah' => 'required|integer|min:0|max:100',
            'nilai_tahsin' => 'required|integer|min:0|max:100',
        ]);

        DB::transaction(function () use ($request, $santriId) {
            Nilai::updateOrCreate(
                ['santri_id' => $santriId, 'kriteria_id' => 1],
                ['nilai' => $request->nilai_hafalan]
            );

            Nilai::updateOrCreate(
                ['santri_id' => $santriId, 'kriteria_id' => 2],
                ['nilai' => $request->nilai_murojaah]
            );

            Nilai::updateOrCreate(
                ['santri_id' => $santriId, 'kriteria_id' => 3],
                ['nilai' => $request->nilai_tahsin]
            );
        });

        return redirect()->route('nilai.index')->with('success', 'Skor nilai santri berhasil diperbarui!');
    }

    /**
     * Hapus semua skor nilai santri tertentu.
     */
    public function destroy($santriId)
    {
        Nilai::where('santri_id', $santriId)->delete();
        return redirect()->route('nilai.index')->with('success', 'Seluruh skor nilai santri berhasil dihapus!');
    }

    /**
     * Hapus masal nilai terpilih (Bulk Delete).
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->route('nilai.index')->with('error', 'Tidak ada baris nilai yang dipilih.');
        }

        Nilai::whereIn('santri_id', $ids)->delete();

        return redirect()->route('nilai.index')->with('success', count($ids) . ' data nilai santri berhasil dihapus secara massal!');
    }
}
