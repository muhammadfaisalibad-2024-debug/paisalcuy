@extends('layouts.app')

{{-- Tugas 4: Demo Select Biasa & Select2 — data kategori buku --}}
@section('title', 'Demo Select & Select2 — Kategori Buku')

@push('styles')
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
          rel="stylesheet">
    {{-- Select2 Bootstrap 5 Theme —  menyesuaikan tampilan Select2 dengan Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
          rel="stylesheet">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-format-list-bulleted"></i>
                </span> Demo Select &amp; Select2 — Kategori Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kategori.index') }}">Kategori</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Demo Select</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- DUA CARD BERDAMPINGAN: Card 1 = Select Biasa | Card 2 = Select2   --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<div class="row">

    {{-- ═══════════════════════════════════════ --}}
    {{-- CARD 1 — Select Biasa (Vanilla JavaScript) --}}
    {{-- ═══════════════════════════════════════ --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-format-list-checkbox me-1"></i> Select Biasa
                </h4>
                <p class="card-description">
                    Menggunakan elemen <code>&lt;select&gt;</code> HTML standar + Vanilla JavaScript
                </p>
                <hr>

                {{-- Input tambah kategori buku --}}
                <div class="form-group mb-3">
                    <label for="inputKategori1" class="form-label fw-semibold">
                        Tambah Kategori Baru
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="inputKategori1"
                               placeholder="Contoh: Biografi">
                        {{-- Tombol Tambah — type="button" agar tidak submit form --}}
                        <button type="button" id="btnTambah1" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Tambah
                        </button>
                    </div>
                </div>

                {{-- Dropdown pilih kategori --}}
                <div class="form-group mb-3">
                    <label for="selectKategori1" class="form-label fw-semibold">
                        Pilih Kategori
                    </label>
                    <select class="form-select" id="selectKategori1"
                            onchange="tampilKategoriTerpilih1()">
                        <option value="">-- Pilih Kategori --</option>
                        {{-- Opsi default awal sesuai domain koleksi buku --}}
                        <option value="Fiksi">Fiksi</option>
                        <option value="Non-Fiksi">Non-Fiksi</option>
                        <option value="Sains">Sains</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Teknologi">Teknologi</option>
                    </select>
                </div>

                {{-- Area tampilan kategori yang dipilih --}}
                <div class="alert alert-info py-2 mb-2" id="hasilKategori1">
                    <i class="mdi mdi-book-outline me-1"></i>
                    <strong>Kategori Terpilih:</strong>
                    <span id="teksKategori1" class="fst-italic text-muted">—</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnReset1">
                    <i class="mdi mdi-refresh"></i> Hapus Data Card 1
                </button>

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════ --}}
    {{-- CARD 2 — Select2 (jQuery + Select2 Library) --}}
    {{-- ═══════════════════════════════════════════════════ --}}
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">
                    <i class="mdi mdi-magnify me-1"></i> Select 2
                </h4>
                <p class="card-description">
                    Menggunakan library <code>Select2</code> + jQuery — mendukung pencarian
                </p>
                <hr>

                {{-- Input tambah kategori buku --}}
                <div class="form-group mb-3">
                    <label for="inputKategori2" class="form-label fw-semibold">
                        Tambah Kategori Baru
                    </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="inputKategori2"
                               placeholder="Contoh: Biografi">
                        {{-- Tombol Tambah — type="button" --}}
                        <button type="button" id="btnTambah2" class="btn btn-primary">
                            <i class="mdi mdi-plus"></i> Tambah
                        </button>
                    </div>
                </div>

                {{-- Dropdown Select2 pilih kategori --}}
                <div class="form-group mb-3">
                    <label for="selectKategori2" class="form-label fw-semibold">
                        Pilih Kategori
                    </label>
                    <select class="form-select" id="selectKategori2">
                        <option value="">-- Pilih Kategori --</option>
                        {{-- Opsi default awal --}}
                        <option value="Fiksi">Fiksi</option>
                        <option value="Non-Fiksi">Non-Fiksi</option>
                        <option value="Sains">Sains</option>
                        <option value="Sejarah">Sejarah</option>
                        <option value="Teknologi">Teknologi</option>
                    </select>
                </div>

                {{-- Area tampilan kategori yang dipilih --}}
                <div class="alert alert-success py-2 mb-2" id="hasilKategori2">
                    <i class="mdi mdi-book-outline me-1"></i>
                    <strong>Kategori Terpilih:</strong>
                    <span id="teksKategori2" class="fst-italic text-muted">—</span>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" id="btnReset2">
                    <i class="mdi mdi-refresh"></i> Hapus Data Card 2
                </button>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
{{-- jQuery 3.x (wajib ada sebelum Select2) --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // ============================================================
    // KUNCI LOCALSTORAGE
    // ============================================================
    const KEY_OPSI1     = 'select_demo_opsi1';      // Opsi tambahan Card 1
    const KEY_PILIHAN1  = 'select_demo_pilihan1';   // Nilai terpilih Card 1
    const KEY_OPSI2     = 'select_demo_opsi2';      // Opsi tambahan Card 2
    const KEY_PILIHAN2  = 'select_demo_pilihan2';   // Nilai terpilih Card 2

    // Daftar opsi bawaan — tidak perlu disimpan ke storage (sudah ada di HTML)
    const OPSI_DEFAULT = ['Fiksi', 'Non-Fiksi', 'Sains', 'Sejarah', 'Teknologi'];

    // ============================================================
    // FUNGSI UTILITAS STORAGE
    // ============================================================

    /** Ambil array opsi tambahan dari localStorage */
    function getOpsiTambahan(key) {
        const raw = localStorage.getItem(key);
        return raw ? JSON.parse(raw) : [];
    }

    /** Simpan array opsi tambahan ke localStorage */
    function simpanOpsi(key, arr) {
        localStorage.setItem(key, JSON.stringify(arr));
    }

    // ============================================================
    // CARD 1 — Vanilla JavaScript
    // ============================================================

    const select1 = document.getElementById('selectKategori1');
    const teks1   = document.getElementById('teksKategori1');

    /**
     * Muat opsi tambahan & nilai terpilih Card 1 dari localStorage.
     * Dipanggil saat halaman pertama dibuka / di-refresh.
     */
    function muatCard1() {
        const opsiTambahan = getOpsiTambahan(KEY_OPSI1);
        opsiTambahan.forEach(function (nilai) {
            const opt = document.createElement('option');
            opt.value = nilai;
            opt.textContent = nilai;
            select1.appendChild(opt);
        });

        // Pulihkan nilai terpilih
        const pilihan = localStorage.getItem(KEY_PILIHAN1);
        if (pilihan) {
            select1.value = pilihan;
            tampilKategoriTerpilih1();
        }
    }

    /**
     * Tampilkan kategori terpilih di area hasil & simpan ke localStorage.
     * Dipanggil via onchange="..." pada <select>.
     */
    function tampilKategoriTerpilih1() {
        if (select1.value) {
            teks1.textContent = select1.value;
            teks1.classList.remove('fst-italic', 'text-muted');
            teks1.classList.add('fw-bold');
        } else {
            teks1.textContent = '—';
            teks1.classList.add('fst-italic', 'text-muted');
            teks1.classList.remove('fw-bold');
        }
        // Simpan pilihan aktif ke localStorage
        localStorage.setItem(KEY_PILIHAN1, select1.value);
    }

    /** Tambah opsi baru ke Card 1 */
    document.getElementById('btnTambah1').addEventListener('click', function () {
        const input = document.getElementById('inputKategori1');
        const nilai = input.value.trim();

        if (!nilai) { input.focus(); return; }

        // Cek duplikasi
        for (let opt of select1.options) {
            if (opt.value.toLowerCase() === nilai.toLowerCase()) {
                alert('Kategori "' + nilai + '" sudah ada dalam daftar.');
                input.select();
                return;
            }
        }

        // Append opsi baru ke <select>
        const opt = document.createElement('option');
        opt.value = nilai;
        opt.textContent = nilai;
        select1.appendChild(opt);
        select1.value = nilai;
        input.value = '';

        // Simpan opsi tambahan (hanya yang bukan default) ke localStorage
        const opsiTambahan = getOpsiTambahan(KEY_OPSI1);
        if (!opsiTambahan.includes(nilai)) {
            opsiTambahan.push(nilai);
            simpanOpsi(KEY_OPSI1, opsiTambahan);
        }

        tampilKategoriTerpilih1();
    });

    // Muat Card 1 saat halaman dibuka
    muatCard1();

    /** Hapus data Card 1 dari localStorage lalu reload */
    document.getElementById('btnReset1').addEventListener('click', function () {
        if (!confirm('Hapus semua data Card 1 (opsi tambahan & pilihan)? Halaman akan di-refresh.')) return;
        localStorage.removeItem(KEY_OPSI1);
        localStorage.removeItem(KEY_PILIHAN1);
        location.reload();
    });

    // ============================================================
    // CARD 2 — jQuery + Select2
    // ============================================================

    $(document).ready(function () {

        /**
         * Muat opsi tambahan Card 2 ke <select> SEBELUM inisialisasi Select2,
         * agar semua opsi sudah tersedia saat Select2 di-render.
         */
        function muatCard2() {
            const opsiTambahan = getOpsiTambahan(KEY_OPSI2);
            opsiTambahan.forEach(function (nilai) {
                $('#selectKategori2').append(new Option(nilai, nilai));
            });

            // Inisialisasi Select2
            $('#selectKategori2').select2({
                theme:       'bootstrap-5',
                placeholder: '-- Pilih Kategori --',
                allowClear:  true
            });

            // Pulihkan nilai terpilih
            const pilihan = localStorage.getItem(KEY_PILIHAN2);
            if (pilihan) {
                $('#selectKategori2').val(pilihan).trigger('change');
            }
        }

        muatCard2();

        /**
         * Event change Select2 — tampilkan kategori terpilih & simpan ke localStorage.
         */
        $('#selectKategori2').on('change', function () {
            const nilai = $(this).val();
            const teks  = $('#teksKategori2');

            if (nilai) {
                teks.text(nilai).removeClass('fst-italic text-muted').addClass('fw-bold');
            } else {
                teks.text('—').addClass('fst-italic text-muted').removeClass('fw-bold');
            }

            // Simpan pilihan aktif ke localStorage
            localStorage.setItem(KEY_PILIHAN2, nilai || '');
        });

        /** Tambah opsi baru ke Card 2 (Select2) */
        $('#btnTambah2').on('click', function () {
            const input  = $('#inputKategori2');
            const select = $('#selectKategori2');
            const nilai  = input.val().trim();

            if (!nilai) { input.focus(); return; }

            // Cek duplikasi
            let sudahAda = false;
            select.find('option').each(function () {
                if ($(this).val().toLowerCase() === nilai.toLowerCase()) {
                    sudahAda = true;
                    return false;
                }
            });

            if (sudahAda) {
                alert('Kategori "' + nilai + '" sudah ada dalam daftar.');
                input.select();
                return;
            }

            // Append opsi baru
            select.append(new Option(nilai, nilai, true, true));

            // Simpan opsi tambahan ke localStorage
            const opsiTambahan = getOpsiTambahan(KEY_OPSI2);
            if (!opsiTambahan.includes(nilai)) {
                opsiTambahan.push(nilai);
                simpanOpsi(KEY_OPSI2, opsiTambahan);
            }

            // Refresh Select2 dan trigger change untuk update "Kategori Terpilih"
            select.trigger('change.select2');
            select.trigger('change');

            input.val('');
        });

        /** Hapus data Card 2 dari localStorage lalu reload */
        $('#btnReset2').on('click', function () {
            if (!confirm('Hapus semua data Card 2 (opsi tambahan & pilihan)? Halaman akan di-refresh.')) return;
            localStorage.removeItem(KEY_OPSI2);
            localStorage.removeItem(KEY_PILIHAN2);
            location.reload();
        });

    }); // end $(document).ready
</script>
@endpush
