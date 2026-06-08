@extends('layouts.app')

@section('title','Ambil Antrian')

@section('content')

<div class="card">
    <div class="card-body">

        <h3>Ambil Nomor Antrian</h3>

        <form method="POST"
              action="{{ route('antrian.daftar') }}">

            @csrf

            <div class="mb-3">

                <label>Nama</label>

                <input
                    type="text"
                    name="nama"
                    class="form-control"
                    required>

            </div>

            <button
                type="submit"
                class="btn btn-primary">

                Ambil Nomor

            </button>

        </form>

    </div>
</div>

@endsection