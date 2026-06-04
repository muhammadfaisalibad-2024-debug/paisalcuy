@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-pencil"></i>
                </span> Edit Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Form Edit Buku</h4>
                <p class="card-description">
                    Silakan ubah data buku di bawah ini
                </p>

                <form action="{{ route('buku.update', $buku->idbuku) }}" method="POST" class="forms-sample">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group">
                        <label for="kode">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('kode') is-invalid @enderror" 
                               id="kode" 
                               name="kode" 
                               value="{{ old('kode', $buku->kode) }}"
                               placeholder="Contoh: BK001"
                               required>
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="judul">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('judul') is-invalid @enderror" 
                               id="judul" 
                               name="judul" 
                               value="{{ old('judul', $buku->judul) }}"
                               placeholder="Masukkan judul buku"
                               required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pengarang">Pengarang <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('pengarang') is-invalid @enderror" 
                               id="pengarang" 
                               name="pengarang" 
                               value="{{ old('pengarang', $buku->pengarang) }}"
                               placeholder="Masukkan nama pengarang"
                               required>
                        @error('pengarang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="idkategori">Kategori <span class="text-danger">*</span></label>
                        <select class="form-control @error('idkategori') is-invalid @enderror" 
                                id="idkategori" 
                                name="idkategori"
                                required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategori as $item)
                                <option value="{{ $item->idkategori }}" 
                                    {{ old('idkategori', $buku->idkategori) == $item->idkategori ? 'selected' : '' }}>
                                    {{ $item->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('idkategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="mdi mdi-content-save"></i> Update
                        </button>
                        <a href="{{ route('buku.index') }}" class="btn btn-light">
                            <i class="mdi mdi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
