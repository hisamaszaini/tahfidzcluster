<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            $query->where(function($q) use ($search) {
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
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:500',
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
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string|max:500',
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
     * Proses Impor Data Santri & Nilai via CSV (Upload File ATAU Tempel Teks).
     */
    public function import(Request $request)
    {
        $csvContent = '';

        // Opsi 1: Pembacaan dari unggah file
        if ($request->hasFile('csv_file')) {
            $request->validate([
                'csv_file' => 'required|file|mimes:csv,txt|max:2048'
            ]);
            $csvContent = file_get_contents($request->file('csv_file')->getRealPath());
        } 
        // Opsi 2: Pembacaan dari tempel teks textarea
        elseif ($request->filled('csv_text')) {
            $csvContent = $request->csv_text;
        } 
        // Jika dua-duanya kosong
        else {
            return redirect()->route('santri.index')->with('error', 'Silakan pilih file CSV atau tempel teks CSV terlebih dahulu.');
        }

        // Parse content menjadi baris
        $lines = explode("\n", str_replace("\r", "", $csvContent));
        $importedCount = 0;

        DB::transaction(function () use ($lines, &$importedCount) {
            foreach ($lines as $index => $line) {
                if (trim($line) === '') continue;

                $data = str_getcsv($line, ',');

                // Lewatkan baris pertama jika itu baris header kolom
                if ($index === 0 && (strtolower($data[0]) === 'nama' || strtolower($data[0]) === 'name')) {
                    continue;
                }

                // Harus memiliki minimal 7 kolom (nama, jk, tgl_lahir, alamat, hafalan, murojaah, tahsin)
                if (count($data) < 7) continue;

                $nama = trim($data[0]);
                $jk = strtoupper(trim($data[1]));
                $tglLahir = trim($data[2]);
                $alamat = trim($data[3]);
                $hafalan = (int)trim($data[4]);
                $murojaah = (int)trim($data[5]);
                $tahsin = (int)trim($data[6]);

                // Validasi data dasar
                if (empty($nama) || !in_array($jk, ['L', 'P']) || empty($tglLahir)) {
                    continue;
                }

                // Buat Santri
                $santri = Santri::create([
                    'nama' => $nama,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $tglLahir,
                    'alamat' => $alamat ?: 'Tidak ada alamat',
                ]);

                // Buat Skor C1, C2, C3
                Nilai::create([
                    'santri_id' => $santri->id,
                    'kriteria_id' => 1,
                    'nilai' => min(100, max(0, $hafalan))
                ]);

                Nilai::create([
                    'santri_id' => $santri->id,
                    'kriteria_id' => 2,
                    'nilai' => min(100, max(0, $murojaah))
                ]);

                Nilai::create([
                    'santri_id' => $santri->id,
                    'kriteria_id' => 3,
                    'nilai' => min(100, max(0, $tahsin))
                ]);

                $importedCount++;
            }
        });

        if ($importedCount > 0) {
            return redirect()->route('santri.index')->with('success', "Sukses mengimpor {$importedCount} data santri beserta nilai kriteria!");
        } else {
            return redirect()->route('santri.index')->with('error', 'Gagal mengimpor data. Pastikan format kolom CSV Anda sudah benar.');
        }
    }
}
