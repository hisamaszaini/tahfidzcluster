<?php

namespace Database\Seeders;

use App\Models\Santri;
use App\Models\Nilai;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 25 data santri dengan sebaran nilai yang representatif
        $santris = [
            // 1-6: Campuran
            ['id' => 1, 'nama' => 'Ahmad Fauzi', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-05-12', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [85, 80, 88]],
            ['id' => 2, 'nama' => 'Budi Santoso', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2011-03-20', 'alamat' => 'Bantul, Yogyakarta', 'scores' => [62, 58, 60]],
            ['id' => 3, 'nama' => 'Citra Lestari', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2010-08-15', 'alamat' => 'Kota Yogyakarta', 'scores' => [90, 88, 92]],
            ['id' => 4, 'nama' => 'Dedi Wijaya', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2009-11-05', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [70, 72, 68]],
            ['id' => 5, 'nama' => 'Eka Putri', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2011-01-25', 'alamat' => 'Gunungkidul, Yogyakarta', 'scores' => [55, 52, 58]],
            ['id' => 6, 'nama' => 'Fahmi Idris', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-02-14', 'alamat' => 'Kulon Progo, Yogyakarta', 'scores' => [78, 80, 75]],
            
            // 7: TARGET CENTROID 1 (Cukup)
            ['id' => 7, 'nama' => 'Galih Saputra', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-07-07', 'alamat' => 'Bantul, Yogyakarta', 'scores' => [60, 65, 62]],
            
            // 8-13: Campuran
            ['id' => 8, 'nama' => 'Hani Nuraini', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2009-09-09', 'alamat' => 'Kota Yogyakarta', 'scores' => [92, 95, 90]],
            ['id' => 9, 'nama' => 'Indra Lesmana', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2011-04-18', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [50, 48, 52]],
            ['id' => 10, 'nama' => 'Joko Susilo', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-12-12', 'alamat' => 'Klaten, Jawa Tengah', 'scores' => [75, 78, 80]],
            ['id' => 11, 'nama' => 'Kartika Sari', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2010-06-30', 'alamat' => 'Magelang, Jawa Tengah', 'scores' => [88, 85, 87]],
            ['id' => 12, 'nama' => 'Lutfi Hakim', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2009-10-10', 'alamat' => 'Bantul, Yogyakarta', 'scores' => [65, 60, 63]],
            ['id' => 13, 'nama' => 'Mega Utami', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2011-07-22', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [58, 62, 59]],
            
            // 14: TARGET CENTROID 3 (Sangat Baik)
            ['id' => 14, 'nama' => 'Naufal Rizqi', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-01-14', 'alamat' => 'Kota Yogyakarta', 'scores' => [95, 92, 94]],
            
            // 15-23: Campuran
            ['id' => 15, 'nama' => 'Olivia Putri', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2010-03-03', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [72, 75, 70]],
            ['id' => 16, 'nama' => 'Panji Asmoro', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2009-05-17', 'alamat' => 'Bantul, Yogyakarta', 'scores' => [52, 55, 50]],
            ['id' => 17, 'nama' => 'Qori Aina', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2011-08-08', 'alamat' => 'Kulon Progo, Yogyakarta', 'scores' => [84, 82, 85]],
            ['id' => 18, 'nama' => 'Rian Hidayat', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-04-25', 'alamat' => 'Gunungkidul, Yogyakarta', 'scores' => [67, 63, 66]],
            ['id' => 19, 'nama' => 'Siti Aminah', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2009-12-25', 'alamat' => 'Klaten, Jawa Tengah', 'scores' => [91, 93, 89]],
            ['id' => 20, 'nama' => 'Taufiq Hidayat', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2011-02-28', 'alamat' => 'Kota Yogyakarta', 'scores' => [79, 81, 78]],
            ['id' => 21, 'nama' => 'Ulfa marifat', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2010-09-19', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [61, 59, 64]],
            ['id' => 22, 'nama' => 'Viko Ramadhan', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-10-22', 'alamat' => 'Bantul, Yogyakarta', 'scores' => [54, 50, 53]],
            ['id' => 23, 'nama' => 'Wulan Dari', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2009-07-11', 'alamat' => 'Magelang, Jawa Tengah', 'scores' => [87, 89, 86]],
            
            // 24: TARGET CENTROID 2 (Baik)
            ['id' => 24, 'nama' => 'Yusuf Habibi', 'jenis_kelamin' => 'L', 'tanggal_lahir' => '2010-05-24', 'alamat' => 'Solo, Jawa Tengah', 'scores' => [80, 78, 82]],
            
            // 25: Tambahan
            ['id' => 25, 'nama' => 'Zahra Aulia', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '2011-06-05', 'alamat' => 'Sleman, Yogyakarta', 'scores' => [76, 74, 78]]
        ];

        foreach ($santris as $data) {
            $scores = $data['scores'];
            unset($data['scores']);

            // Insert Santri
            $santri = Santri::updateOrCreate(['id' => $data['id']], $data);

            // Insert Nilai for C1, C2, C3
            Nilai::updateOrCreate(
                ['santri_id' => $santri->id, 'kriteria_id' => 1],
                ['nilai' => $scores[0]]
            );
            Nilai::updateOrCreate(
                ['santri_id' => $santri->id, 'kriteria_id' => 2],
                ['nilai' => $scores[1]]
            );
            Nilai::updateOrCreate(
                ['santri_id' => $santri->id, 'kriteria_id' => 3],
                ['nilai' => $scores[2]]
            );
        }
    }
}
