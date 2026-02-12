@extends('layouts.app')

@section('title', 'Dashboard - Koleksi Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-home"></i>
                </span> Dashboard
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">
                        <span></span>Dashboard <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h4>
                <p class="card-description">
                    Anda berhasil login ke sistem Koleksi Buku
                </p>
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle"></i> Sistem siap digunakan. Silakan kelola data kategori dan buku Anda.
                </div>
                <div class="mt-4">
                    <a href="{{ url('/kategori') }}" class="btn btn-primary btn-lg me-2">
                        <i class="mdi mdi-folder"></i> Kelola Kategori
                    </a>
                    <a href="{{ url('/buku') }}" class="btn btn-info btn-lg">
                        <i class="mdi mdi-book-open-page-variant"></i> Kelola Buku
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
