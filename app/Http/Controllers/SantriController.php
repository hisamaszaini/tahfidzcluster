<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SantriController extends Controller
{
    /**
     * Tampilkan data santri dengan pencarian, sorting, dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Tentukan kolom dan arah sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortDir = $request->input('sort_dir', 'desc');

        // Validasi agar kolom sort aman dari SQL injection
        $allowedSortColumns = ['id', 'nama', 'tanggal_lahir', 'alamat'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }

        $allowedSortDirs = ['asc', 'desc'];
        if (!in_array($sortDir, $allowedSortDirs)) {
            $sortDir = 'desc';
        }

        $query = Santri::with(['nilai']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%");
            });
        }

        $santris = $query->orderBy($sortBy, $sortDir)->paginate(10)->withQueryString();

        return view('santri.index', compact('santris', 'search', 'sortBy', 'sortDir'));
    }

    /**
     * Simpan data santri dan nilai kriteria awal.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:500',
        ]);

        Santri::create([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil ditambahkan!');
    }

    /**
     * Edit data santri dan nilai kriteria.
     */
    public function update(Request $request, Santri $santri)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'alamat' => 'nullable|string|max:500',
        ]);

        $santri->update([
            'nama' => $request->nama,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('santri.index')->with('success', 'Data santri berhasil diubah!');
    }

    /**
     * Hapus satu data santri.
     */
    public function destroy(Santri $santri)
    {
        $santri->delete();
        return redirect()->route('santri.index')->with('success', 'Data santri berhasil dihapus!');
    }

    /**
     * Hapus banyak santri sekaligus (Bulk Delete).
     */
    public function destroyBulk(Request $request)
    {
        $ids = $request->input('ids');

        if (empty($ids)) {
            return redirect()->route('santri.index')->with('error', 'Tidak ada santri yang dipilih untuk dihapus.');
        }

        Santri::whereIn('id', $ids)->delete();

        return redirect()->route('santri.index')->with('success', count($ids) . ' data santri berhasil dihapus secara massal!');
    }

    /**
     * Proses Impor Data Santri & Nilai via Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new \App\Imports\SantriImport();
            Excel::import($import, $request->file('excel_file'));

            if ($import->importedCount > 0) {
                return redirect()->route('santri.index')->with('success', "Sukses mengimpor {$import->importedCount} data santri beserta nilai kriteria!");
            } else {
                return redirect()->route('santri.index')->with('error', 'Tidak ada data valid yang diimpor. Pastikan format kolom Excel Anda sudah benar.');
            }
        } catch (\Exception $e) {
            return redirect()->route('santri.index')->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage());
        }
    }
}
