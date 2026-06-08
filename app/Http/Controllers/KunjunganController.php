<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Kunjungan;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function index()
{
    $tokos = Toko::all();

    $kunjungans = Kunjungan::with('toko')
                    ->latest()
                    ->get();

    return view(
        'kunjungan.index',
        compact(
            'tokos',
            'kunjungans'
        )
    );
    }

    public function store(Request $request)
    {
        Kunjungan::create([
            'toko_id'   => $request->toko_id,
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy'  => $request->accuracy,
            'jarak'     => $request->jarak,
            'status'    => $request->status,
        ]);

        return response()->json([
            'success' => true
        ]);
    }
}