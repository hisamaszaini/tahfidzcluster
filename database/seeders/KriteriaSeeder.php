<?php

namespace Database\Seeders;

use App\Models\Kriteria;
use Illuminate\Database\Seeder;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kriterias = [
            [
                'id' => 1,
                'kode' => 'C1',
                'nama' => 'Hafalan'
            ],
            [
                'id' => 2,
                'kode' => 'C2',
                'nama' => 'Murojaah'
            ],
            [
                'id' => 3,
                'kode' => 'C3',
                'nama' => 'Tahsin'
            ]
        ];

        foreach ($kriterias as $kriteria) {
            Kriteria::updateOrCreate(['id' => $kriteria['id']], $kriteria);
        }
    }
}
