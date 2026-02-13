@extends('layouts.app')

@section('title', 'Daftar Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book-open-page-variant"></i>
                </span> Koleksi Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Buku</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="card-title mb-0">Daftar Buku</h4>
                    <a href="{{ route('buku.create') }}" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus"></i> Tambah Buku
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                                <th>Kategori</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buku as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><span class="badge badge-dark">{{ $item->kode }}</span></td>
                                    <td><strong>{{ $item->judul }}</strong></td>
                                    <td>{{ $item->pengarang }}</td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $item->kategori->nama_kategori ?? '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('buku.edit', $item->idbuku) }}" 
                                           class="btn btn-warning btn-sm">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('buku.destroy', $item->idbuku) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="py-4">
                                            <i class="mdi mdi-book-open-page-variant mdi-48px text-muted"></i>
                                            <p class="text-muted mt-2">Belum ada data buku</p>
                                        </div>
                                    </td>
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
