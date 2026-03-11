@extends('layouts.app')

{{-- Tugas 2B + 3B: CRUD DOM-only dengan DataTables + Modal Update/Delete --}}
@section('title', 'Daftar Buku (DataTables)')

@push('styles')
    {{-- DataTables Bootstrap 5 CSS --}}
    <link rel="stylesheet"
          href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-table-search"></i>
                </span> Koleksi Buku — DataTables
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('buku.index') }}">Buku</a></li>
                    <li class="breadcrumb-item active" aria-current="page">DataTables</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════ --}}
{{-- BARIS UTAMA: Form Input (kiri) + Tabel DataTables (kanan) --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="row">

    {{-- ===== CARD FORM INPUT (kiri) ===== --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Buku</h4>
                <p class="card-description">Isi form untuk menambah data buku (DOM only)</p>

                {{--
                    Form dengan novalidate: validasi dikontrol manual via JS.
                    Data tidak dikirim ke server, hanya ke DataTables melalui API.
                --}}
                <form id="formBuku" novalidate>

                    {{-- Input Kode Buku --}}
                    <div class="form-group">
                        <label for="inputKode">Kode Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputKode"
                               placeholder="Contoh: BK-001" required>
                    </div>

                    {{-- Input Judul Buku --}}
                    <div class="form-group">
                        <label for="inputJudul">Judul Buku <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputJudul"
                               placeholder="Masukkan judul buku" required>
                    </div>

                    {{-- Input Pengarang --}}
                    <div class="form-group">
                        <label for="inputPengarang">Pengarang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inputPengarang"
                               placeholder="Nama pengarang" required>
                    </div>

                </form>

                {{-- Tombol Tambah berada DI LUAR <form>, type="button" --}}
                <button type="button" id="btnTambah" class="btn btn-primary btn-block w-100 mt-3">
                    <i class="mdi mdi-plus"></i> Tambah Buku
                </button>
            </div>
        </div>
    </div>

    {{-- ===== CARD TABEL DATATABLES (kanan) ===== --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Buku</h4>
                <p class="card-description">
                    Klik baris untuk <strong>edit</strong> atau <strong>hapus</strong> data
                </p>

                <div class="table-responsive">
                    {{--
                        Tabel diinisialisasi oleh DataTables.
                        thead WAJIB ada agar DataTables mengenali kolom.
                    --}}
                    <table class="table table-hover" id="tabelBuku" style="width:100%">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>ID Buku</th>
                                <th>Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables akan mengisi tbody ini secara dinamis --}}
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- ════════════════════════════════════════════════════════════════════ --}}
{{-- TUGAS 3B: MODAL EDIT / HAPUS (ditampilkan saat row DataTables diklik) --}}
{{-- ════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="modalEditBuku" tabindex="-1" aria-labelledby="judulModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="judulModal">
                    <i class="mdi mdi-pencil"></i> Edit Data Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{--
                    Form modal dengan novalidate agar validasi dikontrol JS.
                    ID Buku bersifat readonly (tidak bisa diubah user).
                --}}
                <form id="formModal" novalidate>

                    {{-- ID Buku — readonly --}}
                    <div class="form-group mb-3">
                        <label for="modalId" class="form-label">ID Buku</label>
                        <input type="text" class="form-control bg-light" id="modalId" readonly>
                    </div>

                    {{-- Kode Buku --}}
                    <div class="form-group mb-3">
                        <label for="modalKode" class="form-label">
                            Kode Buku <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalKode" required>
                    </div>

                    {{-- Judul Buku --}}
                    <div class="form-group mb-3">
                        <label for="modalJudul" class="form-label">
                            Judul Buku <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalJudul" required>
                    </div>

                    {{-- Pengarang --}}
                    <div class="form-group mb-3">
                        <label for="modalPengarang" class="form-label">
                            Pengarang <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="modalPengarang" required>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                {{-- Tombol Batal --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> Batal
                </button>
                {{-- Tombol Hapus --}}
                <button type="button" id="btnHapus" class="btn btn-danger">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
                {{-- Tombol Ubah --}}
                <button type="button" id="btnUbah" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Ubah
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- jQuery 3.x (wajib ada sebelum DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
{{-- DataTables Bootstrap 5 Integration --}}
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // ============================================================
    // VARIABEL GLOBAL
    // ============================================================
    let dataBuku    = [];     // Array penyimpanan data — di-persist ke localStorage
    let nomorUrut   = 1;      // Counter nomor urut & generate ID buku
    let selectedRow = null;   // Referensi baris DataTables yang sedang dipilih
    let dataTable   = null;   // Instance DataTables

    // Kunci localStorage — unik per halaman agar tidak bentrok
    const STORAGE_KEY = 'koleksi_buku_datatables';

    // --- Referensi elemen DOM (form input) ---
    const formBuku       = document.getElementById('formBuku');
    const btnTambah      = document.getElementById('btnTambah');
    const inputKode      = document.getElementById('inputKode');
    const inputJudul     = document.getElementById('inputJudul');
    const inputPengarang = document.getElementById('inputPengarang');

    // --- Referensi elemen modal ---
    const formModal      = document.getElementById('formModal');
    const modalEl        = document.getElementById('modalEditBuku');
    const modalId        = document.getElementById('modalId');
    const modalKode      = document.getElementById('modalKode');
    const modalJudul     = document.getElementById('modalJudul');
    const modalPengarang = document.getElementById('modalPengarang');
    const btnUbah        = document.getElementById('btnUbah');
    const btnHapus       = document.getElementById('btnHapus');

    // Instance Bootstrap Modal
    const bsModal = new bootstrap.Modal(modalEl);

    // ============================================================
    // FUNGSI UTILITAS
    // ============================================================

    /**
     * Membuat ID Buku otomatis berformat BK-XXX.
     * Contoh: 1 → "BK-001", 12 → "BK-012"
     */
    function generateId(nomor) {
        return 'BK-' + String(nomor).padStart(3, '0');
    }

    // ============================================================
    // FUNGSI LOCALSTORAGE
    // ============================================================

    /**
     * Simpan array dataBuku ke localStorage (format JSON).
     * Dipanggil setiap kali data berubah: tambah / ubah / hapus.
     */
    function simpanKeStorage() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataBuku));
    }

    /**
     * Muat data dari localStorage dan masukkan ke DataTables via API.
     * Dipanggil sekali saat DataTables selesai diinisialisasi.
     */
    function muatDariStorage() {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return;

        dataBuku = JSON.parse(raw);

        if (dataBuku.length > 0) {
            // Tentukan nomorUrut berikutnya dari nomor tertinggi yang tersimpan
            nomorUrut = Math.max.apply(null, dataBuku.map(function (d) { return d.nomor; })) + 1;

            // Masukkan semua data ke DataTables sekaligus, lalu draw sekali
            dataBuku.forEach(function (item) {
                dataTable.row.add([
                    item.nomor,
                    generateId(item.nomor),
                    item.kode,
                    item.judul,
                    item.pengarang
                ]);
            });
            dataTable.draw();
        }
    }

    // ============================================================
    // INISIALISASI DATATABLES + MUAT DATA
    // ============================================================
    $(document).ready(function () {

        /**
         * Inisialisasi DataTables dengan bahasa Indonesia.
         */
        dataTable = $('#tabelBuku').DataTable({
            language: {
                search:       'Cari:',
                lengthMenu:   'Tampilkan _MENU_ data',
                info:         'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
                infoEmpty:    'Tidak ada data yang ditampilkan',
                infoFiltered: '(difilter dari _MAX_ total data)',
                zeroRecords:  'Data tidak ditemukan',
                emptyTable:   'Belum ada data buku',
                paginate: {
                    first:    'Pertama',
                    last:     'Terakhir',
                    next:     'Berikutnya',
                    previous: 'Sebelumnya'
                }
            },
            columnDefs: [
                { orderable: false, targets: 0 }
            ]
        });

        // Muat data dari localStorage setelah DataTables siap
        muatDariStorage();

        // ============================================================
        // EVENT KLIK BARIS — Event Delegation (wajib untuk DataTables)
        // ============================================================
        /**
         * Gunakan event delegation agar event tetap aktif setelah
         * DataTables me-render ulang DOM (paginasi / sorting).
         */
        $('#tabelBuku tbody').on('click', 'tr', function () {
            selectedRow = this;

            // --- DATATABLES API: Ambil array data baris yang diklik ---
            var rowData = dataTable.row(this).data();
            if (!rowData) return;

            // rowData: [No, ID Buku, Kode, Judul, Pengarang]
            modalId.value        = rowData[1];
            modalKode.value      = rowData[2];
            modalJudul.value     = rowData[3];
            modalPengarang.value = rowData[4];

            bsModal.show();
        });
    });

    // ============================================================
    // EVENT LISTENER: Klik tombol "Tambah Buku"
    // ============================================================
    btnTambah.addEventListener('click', function () {

        if (!formBuku.checkValidity()) {
            formBuku.reportValidity();
            return;
        }

        const kode      = inputKode.value.trim();
        const judul     = inputJudul.value.trim();
        const pengarang = inputPengarang.value.trim();
        const nomor     = nomorUrut;

        // Ubah tombol menjadi spinner
        btnTambah.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  role="status" aria-hidden="true"></span>
            Menyimpan...
        `;
        btnTambah.disabled = true;

        // Simulasi proses penyimpanan (1.5 detik)
        setTimeout(function () {

            // Simpan ke array dan localStorage
            dataBuku.push({ nomor: nomor, kode: kode, judul: judul, pengarang: pengarang });
            simpanKeStorage();

            // --- DATATABLES API: Tambah baris baru ke tabel ---
            dataTable.row.add([
                nomor,
                generateId(nomor),
                kode,
                judul,
                pengarang
            ]).draw();

            nomorUrut++;
            formBuku.reset();

            btnTambah.innerHTML = '<i class="mdi mdi-plus"></i> Tambah Buku';
            btnTambah.disabled  = false;

        }, 1500);
    });

    // ============================================================
    // EVENT LISTENER: Klik tombol "Hapus" di modal
    // ============================================================
    btnHapus.addEventListener('click', function () {

        const konfirmasi = confirm('Yakin ingin menghapus buku "' + modalJudul.value + '"?');

        if (konfirmasi) {
            // Cari nomor baris dari data DataTables untuk sinkronisasi localStorage
            const rowData    = dataTable.row(selectedRow).data();
            const nomorHapus = rowData[0]; // Kolom 0 = nomor urut

            // --- DATATABLES API: Hapus baris dari DataTables ---
            dataTable.row(selectedRow).remove().draw();

            // Hapus dari array dan simpan ulang ke localStorage
            dataBuku = dataBuku.filter(function (d) { return d.nomor !== nomorHapus; });
            simpanKeStorage();

            selectedRow = null;
            bsModal.hide();
        }
    });

    // ============================================================
    // EVENT LISTENER: Klik tombol "Ubah" di modal
    // ============================================================
    btnUbah.addEventListener('click', function () {

        if (!formModal.checkValidity()) {
            formModal.reportValidity();
            return;
        }

        const rowData    = dataTable.row(selectedRow).data();
        const nomorEdit  = rowData[0]; // Kolom 0 = nomor urut
        const kodeBaru   = modalKode.value.trim();
        const judulBaru  = modalJudul.value.trim();
        const pengBaru   = modalPengarang.value.trim();

        // --- DATATABLES API: Update data baris ---
        dataTable.row(selectedRow).data([
            nomorEdit,
            modalId.value,  // ID Buku tetap (readonly)
            kodeBaru,
            judulBaru,
            pengBaru
        ]).draw();

        // Sinkronisasi perubahan ke array dan localStorage
        const idx = dataBuku.findIndex(function (d) { return d.nomor === nomorEdit; });
        if (idx !== -1) {
            dataBuku[idx].kode      = kodeBaru;
            dataBuku[idx].judul     = judulBaru;
            dataBuku[idx].pengarang = pengBaru;
            simpanKeStorage();
        }

        selectedRow = null;
        bsModal.hide();
    });
</script>
@endpush
