<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    protected $table = 'tabel_nilai';

    protected $fillable = [
        'santri_id',
        'kriteria_id',
        'nilai'
    ];

    /**
     * Get the student that owns this score.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Get the criterion associated with this score.
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'kriteria_id');
    }
}
