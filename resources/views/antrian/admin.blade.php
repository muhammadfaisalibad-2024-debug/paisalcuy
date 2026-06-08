@extends('layouts.app')

@section('title','Admin Antrian')

@section('content')

<div class="card">
    <div class="card-body">

        <h3>Manajemen Antrian</h3>

        <form
            method="POST"
            action="{{ route('antrian.panggil') }}">

            @csrf

            <button
                class="btn btn-success">

                Panggil Berikutnya

            </button>

        </form>

        <hr>

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>No</th>

                    <th>Nama</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                @foreach($antrians as $a)

                <tr>

                    <td>{{ $a->nomor }}</td>

                    <td>{{ $a->nama }}</td>

                    <td>{{ $a->status }}</td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>
</div>

@endsection