<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    protected $table = 'tabel_santri';

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'alamat'
    ];

    /**
     * Get all scores for the student.
     */
    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'santri_id');
    }

    /**
     * Get the clustering result for the student.
     */
    public function hasil()
    {
        return $this->hasOne(Hasil::class, 'santri_id');
    }
}
