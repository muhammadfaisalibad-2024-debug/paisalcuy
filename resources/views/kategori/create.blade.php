@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-folder-plus"></i>
                </span> Tambah Kategori
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Tambah Kategori</h4>
                <p class="card-description">
                    Silakan isi form di bawah ini untuk menambahkan kategori baru
                </p>

                <form action="{{ route('kategori.store') }}" method="POST" class="forms-sample">
                    @csrf
                    
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('nama_kategori') is-invalid @enderror" 
                               id="nama_kategori" 
                               name="nama_kategori" 
                               value="{{ old('nama_kategori') }}"
                               placeholder="Masukkan nama kategori"
                               required>
                        @error('nama_kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Contoh: Novel, Biografi, Komik, dll.
                        </small>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="mdi mdi-content-save"></i> Simpan
                        </button>
                        <a href="{{ route('kategori.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
