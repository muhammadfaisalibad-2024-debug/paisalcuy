@extends('layouts.app')

@section('title','Kunjungan Toko')

@section('content')

<div class="card">
    <div class="card-body">

        <h3>Kunjungan Toko</h3>

        <div class="mb-3">

            <label class="form-label">
                Pilih Toko
            </label>

            <select
                id="toko"
                class="form-control">

                @foreach($tokos as $toko)

                    <option
                        value="{{ $toko->id }}"
                        data-lat="{{ $toko->latitude }}"
                        data-lng="{{ $toko->longitude }}"
                        data-acc="{{ $toko->accuracy }}">

                        {{ $toko->nama_toko }}

                    </option>

                @endforeach

            </select>

        </div>

        <hr>

        <h5>Data Toko</h5>

        <div id="infoToko">

            Latitude :
            <span id="tokoLat">-</span>

            <br>

            Longitude :
            <span id="tokoLng">-</span>

            <br>

            Accuracy :
            <span id="tokoAcc">-</span>

        </div>

        <hr>

        <h5>Scan QR Toko</h5>

        <video
            id="reader"
            style="width:100%;max-width:400px;border:1px solid #ccc;">
        </video>

        <br><br>

        <button
            class="btn btn-success"
            onclick="startScan()">

            Scan QR Toko

        </button>

        <hr>

        <button
            class="btn btn-primary"
            onclick="ambilLokasi()">

            Ambil Lokasi

        </button>

        <hr>

        <div>
            Latitude :
            <span id="lat">-</span>
        </div>

        <div>
            Longitude :
            <span id="lng">-</span>
        </div>

        <div>
            Accuracy :
            <span id="acc">-</span>
        </div>

        <hr>

        <h5>Hasil Perhitungan</h5>

        <div>
            Jarak :
            <span id="jarak">-</span>
            meter
        </div>

        <div>
            Threshold Efektif :
            <span id="threshold">-</span>
            meter
        </div>

        <div>
            Status :
            <span id="status">-</span>
        </div>

    </div>
</div>

<form id="formKunjungan">

    @csrf

    <input type="hidden" id="toko_id" name="toko_id">

    <input type="hidden" id="latitude_input" name="latitude">

    <input type="hidden" id="longitude_input" name="longitude">

    <input type="hidden" id="accuracy_input" name="accuracy">

    <input type="hidden" id="jarak_input" name="jarak">

    <input type="hidden" id="status_input" name="status">

</form>

<script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js"></script>

<script>

function getAccuratePosition(
    targetAccuracy = 50,
    maxWait = 20000
){
    return new Promise((resolve,reject)=>{

        let bestResult = null;
        const startTime = Date.now();

        const watchId =
        navigator.geolocation.watchPosition(

            function(position){

                const acc =
                position.coords.accuracy;

                if(
                    !bestResult ||
                    acc <
                    bestResult.coords.accuracy
                ){
                    bestResult = position;
                }

                if(acc <= targetAccuracy){

                    navigator.geolocation
                        .clearWatch(watchId);

                    resolve(bestResult);
                }

                if(
                    Date.now() - startTime
                    >= maxWait
                ){

                    navigator.geolocation
                        .clearWatch(watchId);

                    if(bestResult){
                        resolve(bestResult);
                    }else{
                        reject("Timeout");
                    }
                }

            },

            function(error){
                reject(error);
            },

            {
                enableHighAccuracy:true,
                maximumAge:0,
                timeout:maxWait
            }

        );
    });
}

function haversine(
    lat1,
    lon1,
    lat2,
    lon2
){

    const R = 6371000;

    const dLat =
        (lat2-lat1) *
        Math.PI/180;

    const dLon =
        (lon2-lon1) *
        Math.PI/180;

    const a =
        Math.sin(dLat/2) *
        Math.sin(dLat/2)
        +
        Math.cos(lat1*Math.PI/180)
        *
        Math.cos(lat2*Math.PI/180)
        *
        Math.sin(dLon/2)
        *
        Math.sin(dLon/2);

    const c =
        2 *
        Math.atan2(
            Math.sqrt(a),
            Math.sqrt(1-a)
        );

    return R * c;
}

async function startScan(){

    try{

        const codeReader =
            new ZXing.BrowserMultiFormatReader();

        await codeReader.decodeFromVideoDevice(
            null,
            'reader',
            (result,err)=>{

                if(result){

                    let tokoId =
                        result.getText();

                    document
                        .getElementById('toko')
                        .value = tokoId;

                    tampilToko();

                    codeReader.reset();

                    alert(
                        'QR berhasil dibaca'
                    );
                }

            }
        );

    }catch(error){

        console.log(error);

        alert(
            'Kamera tidak bisa dibuka'
        );

    }

}

async function ambilLokasi(){

    try{

        document.getElementById('acc').innerHTML =
            'Mencari lokasi terbaik...';

        const pos =
            await getAccuratePosition(50);

        document.getElementById('lat').innerHTML =
            pos.coords.latitude;

        document.getElementById('lng').innerHTML =
            pos.coords.longitude;

        document.getElementById('acc').innerHTML =
            pos.coords.accuracy;

        let tokoLat =
        parseFloat(
            document.getElementById('tokoLat')
            .innerText
        );

        let tokoLng =
        parseFloat(
            document.getElementById('tokoLng')
            .innerText
        );

        let tokoAcc =
        parseFloat(
            document.getElementById('tokoAcc')
            .innerText
        );

        let salesLat =
        pos.coords.latitude;

        let salesLng =
        pos.coords.longitude;

        let salesAcc =
        pos.coords.accuracy;

        let jarak =
        haversine(
            tokoLat,
            tokoLng,
            salesLat,
            salesLng
        );

        let threshold =
        300 +
        tokoAcc +
        salesAcc;

        let hasilStatus =
        jarak <= threshold
        ? 'DITERIMA'
        : 'DITOLAK';

        document.getElementById('jarak')
            .innerHTML =
            jarak.toFixed(2);

        document.getElementById('threshold')
            .innerHTML =
            threshold.toFixed(2);

        document.getElementById('status')
            .innerHTML =
            hasilStatus;

        document.getElementById('toko_id').value =
            document.getElementById('toko').value;

        document.getElementById('latitude_input').value =
            salesLat;

        document.getElementById('longitude_input').value =
            salesLng;

        document.getElementById('accuracy_input').value =
            salesAcc;

        document.getElementById('jarak_input').value =
            jarak;

        document.getElementById('status_input').value =
            hasilStatus;

        fetch(
            "{{ route('kunjungan.store') }}",
            {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':
                    document.querySelector(
                        'input[name="_token"]'
                    ).value
                },
                body:
                new FormData(
                    document.getElementById(
                        'formKunjungan'
                    )
                )
            }
        );

    }catch(e){

        alert(
            'Gagal mendapatkan lokasi'
        );

    }

}

function tampilToko(){

    let opt =
        document.getElementById('toko')
        .selectedOptions[0];

    document.getElementById('tokoLat')
        .innerHTML =
        opt.dataset.lat;

    document.getElementById('tokoLng')
        .innerHTML =
        opt.dataset.lng;

    document.getElementById('tokoAcc')
        .innerHTML =
        opt.dataset.acc;
}

document
.getElementById('toko')
.addEventListener(
    'change',
    tampilToko
);

tampilToko();

</script>

<hr>

<h4>Riwayat Kunjungan</h4>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>Toko</th>
            <th>Jarak</th>
            <th>Status</th>
            <th>Tanggal</th>
        </tr>
    </thead>

    <tbody>

    @foreach($kunjungans as $k)

        <tr>

            <td>
                {{ $k->toko->nama_toko ?? '-' }}
            </td>

            <td>
                {{ number_format($k->jarak,2) }}
                meter
            </td>

            <td>

                @if($k->status == 'DITERIMA')

                    <span class="badge bg-success">
                        DITERIMA
                    </span>

                @else

                    <span class="badge bg-danger">
                        DITOLAK
                    </span>

                @endif

            </td>

            <td>
                {{ $k->created_at }}
            </td>

        </tr>

    @endforeach

    </tbody>

</table>

@endsection