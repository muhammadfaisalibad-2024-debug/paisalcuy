@extends('layouts.app')

@section('content')

<div class="card">
<div class="card-body">

<h3>Tambah Toko</h3>

<form method="POST"
      action="{{ route('toko.store') }}">

@csrf

<input
class="form-control mb-2"
name="nama_toko"
placeholder="Nama Toko">

<input
class="form-control mb-2"
name="alamat"
placeholder="Alamat">

<input
id="latitude"
class="form-control mb-2"
name="latitude"
placeholder="Latitude">

<input
id="longitude"
class="form-control mb-2"
name="longitude"
placeholder="Longitude">

<input
id="accuracy"
class="form-control mb-2"
name="accuracy"
placeholder="Accuracy">

<div class="mb-3">
    <button
        type="button"
        class="btn btn-info"
        onclick="ambilLokasi()">

        Ambil Lokasi Toko

    </button>
</div>

<button
class="btn btn-success">

Simpan

</button>

</form>

</div>
</div>

@endsection

<script>

async function ambilLokasi(){

    navigator.geolocation.getCurrentPosition(

        function(pos){

            document
            .getElementById('latitude')
            .value =
            pos.coords.latitude;

            document
            .getElementById('longitude')
            .value =
            pos.coords.longitude;

            document
            .getElementById('accuracy')
            .value =
            pos.coords.accuracy;

        },

        function(error){

            alert(
                'Gagal mendapatkan lokasi'
            );

        },

        {
            enableHighAccuracy:true
        }

    );

}

</script>