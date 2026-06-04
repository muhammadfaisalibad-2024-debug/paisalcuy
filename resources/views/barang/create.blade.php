@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Barang Baru</h4>
                <p class="card-description text-muted">ID Barang akan dibuat otomatis (format BRG-YYMMDD-NNN).</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('barang.store') }}" method="POST">
                    @csrf

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Nama Barang <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_barang" value="{{ old('nama_barang') }}"
                                class="form-control @error('nama_barang') is-invalid @enderror"
                                placeholder="Nama barang" required maxlength="100">
                            @error('nama_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" name="harga" value="{{ old('harga') }}"
                                class="form-control @error('harga') is-invalid @enderror"
                                placeholder="Contoh: 15000" min="0" required>
                            @error('harga')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Satuan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="satuan" value="{{ old('satuan', 'pcs') }}"
                                class="form-control @error('satuan') is-invalid @enderror"
                                placeholder="pcs / kg / lusin / pak" required maxlength="30">
                            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Kategori</label>
                        <div class="col-sm-9">
                            <input type="text" name="kategori" value="{{ old('kategori') }}"
                                class="form-control @error('kategori') is-invalid @enderror"
                                placeholder="Pakaian / Makanan / Kerajinan / dll" maxlength="50">
                            @error('kategori')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Stok <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" name="stok" value="{{ old('stok', 0) }}"
                                class="form-control @error('stok') is-invalid @enderror"
                                min="0" required>
                            @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">Deskripsi</label>
                        <div class="col-sm-9">
                            <textarea name="deskripsi" rows="3"
                                class="form-control @error('deskripsi') is-invalid @enderror"
                                placeholder="Keterangan tambahan (opsional)">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="mdi mdi-content-save"></i> Simpan
                            </button>
                            <a href="{{ route('barang.index') }}" class="btn btn-secondary">
                                <i class="mdi mdi-arrow-left"></i> Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
