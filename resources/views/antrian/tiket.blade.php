@extends('layouts.app')

@section('title','Tiket Antrian')

@section('content')

<div class="card">
    <div class="card-body text-center">

        <h1>
            Nomor Antrian
        </h1>

        <h1 style="font-size:80px">

            {{ $antrian->nomor }}

        </h1>

        <h4>

            {{ $antrian->nama }}

        </h4>

    </div>
</div>

@endsection