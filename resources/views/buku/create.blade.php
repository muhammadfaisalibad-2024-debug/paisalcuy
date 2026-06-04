@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book-plus"></i>
                </span> Tambah Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
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
                <h4 class="card-title">Form Tambah Buku</h4>
                <p class="card-description">
                    Silakan isi form di bawah ini untuk menambahkan buku baru
                </p>

                {{--
                    Form dengan id="formBuku" dan novalidate:
                    Validasi dikontrol manual via JavaScript (checkValidity / reportValidity),
                    bukan oleh browser secara otomatis saat form di-submit.
                --}}
                <form action="{{ route('buku.store') }}" method="POST" class="forms-sample"
                      id="formBuku" novalidate>
                    @csrf
                    
                    <div class="form-group">
                        <label for="kode">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('kode') is-invalid @enderror" 
                               id="kode" 
                               name="kode" 
                               value="{{ old('kode') }}"
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
                               value="{{ old('judul') }}"
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
                               value="{{ old('pengarang') }}"
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
                                <option value="{{ $item->idkategori }}" {{ old('idkategori') == $item->idkategori ? 'selected' : '' }}>
                                    {{ $item->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        @error('idkategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($kategori->isEmpty())
                            <small class="form-text text-warning">
                                <i class="mdi mdi-alert"></i> Belum ada kategori. 
                                <a href="{{ route('kategori.create') }}">Tambah kategori terlebih dahulu</a>
                            </small>
                        @endif
                    </div>

                </form>

                {{-- Tombol aksi DI LUAR <form> — type="button" agar tidak memicu submit bawaan browser --}}
                <div class="mt-4">
                    <button type="button" id="btnSubmit" class="btn btn-primary me-2"
                            {{ $kategori->isEmpty() ? 'disabled' : '' }}>
                        <i class="mdi mdi-content-save"></i> Simpan
                    </button>
                    <a href="{{ route('buku.index') }}" class="btn btn-light">
                        <i class="mdi mdi-arrow-left"></i> Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ============================================================
    // REFERENSI ELEMEN DOM
    // ============================================================
    const formBuku   = document.getElementById('formBuku');
    const btnSubmit  = document.getElementById('btnSubmit');

    // Simpan label teks asli tombol untuk dikembalikan setelah proses
    const labelAsli  = btnSubmit ? btnSubmit.innerHTML : '';

    // ============================================================
    // EVENT LISTENER: Tombol "Simpan" di-klik
    // ============================================================
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function () {

            // LANGKAH 1: Cek validitas semua field di dalam form
            // checkValidity() → true jika semua valid, false jika ada yang kosong/tidak sesuai
            if (!formBuku.checkValidity()) {
                // Tampilkan bubble pesan error bawaan browser pada field bermasalah
                formBuku.reportValidity();
                return; // Hentikan eksekusi
            }

            // LANGKAH 2: Tampilkan spinner + ubah teks tombol menjadi "Menyimpan..."
            btnSubmit.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2"
                      role="status" aria-hidden="true"></span>
                Menyimpan...
            `;

            // LANGKAH 3: Disable tombol agar tidak bisa diklik dua kali (double submit)
            btnSubmit.disabled = true;

            // LANGKAH 4: Submit form secara programatik ke server Laravel
            // (action="{{ route('buku.store') }}" method="POST")
            formBuku.submit();
        });
    }
</script>
@endpush
