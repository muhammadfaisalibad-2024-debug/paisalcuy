<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Antrian;
class AntrianController extends Controller
{
    public function guest()
    {
        return view('antrian.guest');
    }

    public function daftar(Request $request)
    {
        $nomor =
            Antrian::max('nomor') + 1;

        $antrian =
            Antrian::create([
                'nama' => $request->nama,
                'nomor' => $nomor
            ]);

        return view(
            'antrian.tiket',
            compact('antrian')
        );
    }
    public function admin()
    {
        $antrians =
            Antrian::all();

        return view(
            'antrian.admin',
            compact('antrians')
        );
    }
    public function panggil()
    {
    $antrian =
        Antrian::where(
            'status',
            'menunggu'
        )
        ->orderBy('nomor')
        ->first();

    if($antrian){

        $antrian->update([
            'status' => 'dipanggil'
        ]);

        cache()->put(
            'antrian_aktif',
            [
                'nomor' => $antrian->nomor,
                'nama' => $antrian->nama
            ]
        );
    }

    return back();
    }public function stream()
    {
        return response()->stream(

            function(){

                set_time_limit(0);

                while(true){

                    $data =
                        cache()->get(
                            'antrian_aktif',
                            []
                        );

                    echo
                        "data: "
                        .
                        json_encode($data)
                        .
                        "\n\n";

                    ob_flush();
                    flush();

                    if(connection_aborted()){
                        break;
                    }

                    sleep(1);
                }

            },

            200,

            [
                'Content-Type'
                    =>
                    'text/event-stream',

                'Cache-Control'
                    =>
                    'no-cache',

                'X-Accel-Buffering'
                    =>
                    'no'
            ]

        );
    }
    public function papan()
    {
        return view(
            'antrian.papan'
        );
    }
}
