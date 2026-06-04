@extends('layouts.app')

@section('title', 'Vendor - Master Menu Kantin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-food"></i>
                </span> Vendor - Master Menu Kantin
            </h3>
        </div>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Vendor</h4>
                <form method="POST" action="{{ route('kantin.vendor.store-vendor') }}">
                    @csrf
                    <div class="form-group">
                        <label>Nama Vendor</label>
                        <input type="text" name="nama_vendor" class="form-control" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan Vendor</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Menu</h4>
                <form method="POST" action="{{ route('kantin.vendor.store-menu') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Vendor</label>
                            <select name="idvendor" class="form-control" required>
                                <option value="">-- Pilih Vendor --</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Nama Menu</label>
                            <input type="text" name="nama_menu" class="form-control" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Harga</label>
                            <input type="number" name="harga" class="form-control" min="1" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Path Gambar (opsional)</label>
                        <input type="text" name="path_gambar" class="form-control">
                    </div>
                    <button class="btn btn-success" type="submit">Simpan Menu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Menu</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Vendor</th>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th>Path Gambar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $menu)
                            <tr>
                                <td>{{ $menu->nama_vendor }}</td>
                                <td>{{ $menu->nama_menu }}</td>
                                <td>Rp {{ number_format($menu->harga, 0, ',', '.') }}</td>
                                <td>{{ $menu->path_gambar ?: '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data menu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
