<?php

namespace App\Imports;

use App\Models\Santri;
use App\Models\Nilai;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToCollection, WithHeadingRow
{
    public $importedCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (!isset($row['nama'])) {
                continue;
            }

            $nama = trim($row['nama']);
            $hafalan = isset($row['hafalan']) ? (int)trim($row['hafalan']) : 0;
            $murojaah = isset($row['murojaah']) ? (int)trim($row['murojaah']) : 0;
            $tahsin = isset($row['tahsin']) ? (int)trim($row['tahsin']) : 0;

            if (empty($nama)) {
                continue;
            }

            // Buat Santri
            $santri = Santri::create([
                'nama' => $nama,
                'jenis_kelamin' => null,
                'tanggal_lahir' => null,
                'alamat' => null,
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

            $this->importedCount++;
        }
    }

    public function headingRow(): int
    {
        return 3;
    }
}
