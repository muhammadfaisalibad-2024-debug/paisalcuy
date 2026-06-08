<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
class TokoController extends Controller
{
    public function index()
    {
        $tokos = Toko::all();

        return view('toko.index', compact('tokos'));
    }

    public function create()
    {
        return view('toko.create');
    }

    public function store(Request $request)
    {
        Toko::create([
            'barcode' => uniqid('TOKO'),
            'nama_toko' => $request->nama_toko,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'accuracy' => $request->accuracy
        ]);

        return redirect()->route('toko.index');
    }


    public function qr($id)
    {
        $toko = Toko::findOrFail($id);

        $result = new \Endroid\QrCode\Builder\Builder(
            writer: new \Endroid\QrCode\Writer\PngWriter()
        );

        $qr = $result->build(
            data: (string)$toko->id
        );

        return response(
            $qr->getString(),
            200,
            [
                'Content-Type' => 'image/png'
            ]
        );
    }
}

