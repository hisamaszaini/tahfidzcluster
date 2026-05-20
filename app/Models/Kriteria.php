<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    protected $table = 'tabel_kriteria';

    protected $fillable = [
        'kode',
        'nama'
    ];

    /**
     * Get all scores for this criterion.
     */
    public function nilai()
    {
        return $this->hasMany(Nilai::class, 'kriteria_id');
    }
}
