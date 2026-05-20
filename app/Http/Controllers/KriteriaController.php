<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use Illuminate\Http\Request;

class KriteriaController extends Controller
{
    /**
     * Tampilkan seluruh kriteria pengukuran.
     */
    public function index()
    {
        $kriterias = Kriteria::orderBy('kode', 'asc')->get();
        return view('kriteria.index', compact('kriterias'));
    }
}
