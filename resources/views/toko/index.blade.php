@extends('layouts.app')

@section('title','Data Toko')

@section('content')

<div class="card">
    <div class="card-body">

        <h3>Data Toko</h3>

        <a href="{{ route('toko.create') }}"
           class="btn btn-primary mb-3">

            Tambah Toko

        </a>

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Barcode</th>
                    <th>Nama Toko</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Accuracy</th>
                    <th width="120">Aksi</th>
                </tr>
            </thead>

            <tbody>

            @forelse($tokos as $toko)

                <tr>

                    <td>{{ $toko->id }}</td>

                    <td>{{ $toko->barcode }}</td>

                    <td>{{ $toko->nama_toko }}</td>

                    <td>{{ $toko->latitude }}</td>

                    <td>{{ $toko->longitude }}</td>

                    <td>{{ $toko->accuracy }}</td>

                    <td>

                        <a href="{{ route('toko.qr',$toko->id) }}"
                           target="_blank"
                           class="btn btn-success btn-sm">

                            QR

                        </a>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="7" class="text-center">
                        Belum ada data toko
                    </td>
                </tr>

            @endforelse

            </tbody>

        </table>

    </div>
</div>

@endsection