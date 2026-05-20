<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    protected $table = 'tabel_hasil';
    protected $fillable = [
        'santri_id',
        'kluster',
        'jarak_c1',
        'jarak_c2',
        'jarak_c3',
    ];
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'santri_id', 'id');
    }
}
