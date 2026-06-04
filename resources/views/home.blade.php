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

{{-- PDF Section --}}
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="bg-gradient-warning p-2 rounded me-3">
                        <i class="mdi mdi-certificate text-white" style="font-size:1.5rem"></i>
                    </span>
                    <div>
                        <h4 class="card-title mb-0">Sertifikat Keanggotaan</h4>
                        <p class="text-muted small mb-0">Landscape A4 &bull; Data koleksi buku terbaru</p>
                    </div>
                </div>
                <p class="card-description">
                    Sertifikat keanggotaan perpustakaan digital yang memuat statistik koleksi
                    dan 5 buku terbaru.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('pdf.sertifikat') }}" target="_blank" class="btn btn-outline-warning">
                        <i class="mdi mdi-eye"></i> Preview
                    </a>
                    <a href="{{ route('pdf.sertifikat.download') }}" class="btn btn-warning text-white">
                        <i class="mdi mdi-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <span class="bg-gradient-success p-2 rounded me-3">
                        <i class="mdi mdi-email-open text-white" style="font-size:1.5rem"></i>
                    </span>
                    <div>
                        <h4 class="card-title mb-0">Undangan Peluncuran Buku</h4>
                        <p class="text-muted small mb-0">Portrait A4 &bull; Daftar koleksi &amp; kategori</p>
                    </div>
                </div>
                <p class="card-description">
                    Surat undangan resmi acara peluncuran koleksi buku digital, memuat
                    daftar buku terbaru dan ringkasan per kategori.
                </p>
                <div class="d-flex gap-2 mt-3">
                    <a href="{{ route('pdf.undangan') }}" target="_blank" class="btn btn-outline-success">
                        <i class="mdi mdi-eye"></i> Preview
                    </a>
                    <a href="{{ route('pdf.undangan.download') }}" class="btn btn-success">
                        <i class="mdi mdi-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
