@extends('layouts.app')

{{-- Tugas 2A + 3A: CRUD DOM-only (data tidak ke database) + Modal Update/Delete --}}
@section('title', 'Daftar Buku (Demo CRUD)')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book-open-page-variant"></i>
                </span> Koleksi Buku — Demo CRUD
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

    {{-- ===== CARD FORM INPUT (kiri) ===== --}}
    <div class="col-md-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tambah Buku</h4>
                <p class="card-description">Isi form untuk menambah data buku (DOM only)</p>

                {{--
                    Form dengan novalidate: validasi dikontrol manual via JS.
                    Data tidak dikirim ke server, hanya ditambahkan ke DOM tabel.
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

    {{-- ===== CARD TABEL DATA (kanan) ===== --}}
    <div class="col-md-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Daftar Buku</h4>
                <p class="card-description">
                    Klik baris untuk <strong>edit</strong> atau <strong>hapus</strong> data
                </p>

                <div class="table-responsive">
                    {{-- table-hover memberi efek warna saat hover --}}
                    <table class="table table-hover" id="tabelBuku">
                        <thead class="table-dark">
                            <tr>
                                <th width="45">No</th>
                                <th width="110">ID Buku</th>
                                <th width="110">Kode</th>
                                <th>Judul</th>
                                <th>Pengarang</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBuku">
                            {{-- Baris kosong awal — akan disembunyikan saat ada data --}}
                            <tr id="emptyRow">
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="mdi mdi-book-open-page-variant mdi-36px d-block mb-1"></i>
                                    Belum ada data buku
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>

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
             
                <form id="formModal" novalidate>

                    {{-- ID Buku — hanya tampil, tidak bisa diedit --}}
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
                {{-- Tombol Batal — menutup modal tanpa perubahan --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="mdi mdi-close"></i> Batal
                </button>
                {{-- Tombol Hapus — menghapus baris dari tabel --}}
                <button type="button" id="btnHapus" class="btn btn-danger">
                    <i class="mdi mdi-delete"></i> Hapus
                </button>
                {{-- Tombol Ubah — menyimpan perubahan ke baris terpilih --}}
                <button type="button" id="btnUbah" class="btn btn-primary">
                    <i class="mdi mdi-content-save"></i> Ubah
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
   
    let dataBuku    = [];
    let nomorUrut   = 1;      
    let selectedRow = null;   

    const STORAGE_KEY = 'koleksi_buku_demo_crud';

    
    const formBuku       = document.getElementById('formBuku');
    const btnTambah      = document.getElementById('btnTambah');
    const inputKode      = document.getElementById('inputKode');
    const inputJudul     = document.getElementById('inputJudul');
    const inputPengarang = document.getElementById('inputPengarang');
    const tbodyBuku      = document.getElementById('tbodyBuku');
    const emptyRow       = document.getElementById('emptyRow');

    
    const formModal      = document.getElementById('formModal');
    const modalEl        = document.getElementById('modalEditBuku');
    const modalId        = document.getElementById('modalId');
    const modalKode      = document.getElementById('modalKode');
    const modalJudul     = document.getElementById('modalJudul');
    const modalPengarang = document.getElementById('modalPengarang');
    const btnUbah        = document.getElementById('btnUbah');
    const btnHapus       = document.getElementById('btnHapus');

    
    const bsModal = new bootstrap.Modal(modalEl);

    
    /**
     * Membuat ID Buku otomatis berformat BK-XXX berdasarkan nomor urut.
     * Contoh: 1 → "BK-001", 12 → "BK-012"
     */
    function generateId(nomor) {
        return 'BK-' + String(nomor).padStart(3, '0');
    }

 

    /**
     * Menyimpan array dataBuku ke localStorage dalam format JSON.
     * Dipanggil setiap kali data berubah (tambah / ubah / hapus).
     */
    function simpanKeStorage() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(dataBuku));
    }

    /**
     * Memuat data dari localStorage dan me-render ulang seluruh tabel.
     * Dipanggil satu kali saat halaman pertama kali dimuat (DOMContentLoaded).
     */
    function muatDariStorage() {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) return; 

        dataBuku = JSON.parse(raw);

        
        if (dataBuku.length > 0) {
            nomorUrut = Math.max(...dataBuku.map(function (d) { return d.nomor; })) + 1;
        }

        
        dataBuku.forEach(function (item) {
            renderBaris(item.nomor, item.kode, item.judul, item.pengarang);
        });
    }

    
    
    

    /**
     * Me-render satu baris ke dalam <tbody> tabel.
     * Dipisah dari tambahBaris() agar bisa dipanggil ulang saat muat dari storage.
     * @param {number} nomor
     * @param {string} kode
     * @param {string} judul
     * @param {string} pengarang
     */
    function renderBaris(nomor, kode, judul, pengarang) {
        
        emptyRow.style.display = 'none';

        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.setAttribute('data-nomor', nomor); 

        tr.innerHTML = `
            <td>${nomor}</td>
            <td><span class="badge badge-dark">${generateId(nomor)}</span></td>
            <td>${kode}</td>
            <td><strong>${judul}</strong></td>
            <td>${pengarang}</td>
        `;

        
        tr.addEventListener('click', function () {
            bukaModalEdit(this);
        });

        tbodyBuku.appendChild(tr);
    }

    
    
    
    btnTambah.addEventListener('click', function () {

        
        if (!formBuku.checkValidity()) {
            formBuku.reportValidity();
            return;
        }

        const kode      = inputKode.value.trim();
        const judul     = inputJudul.value.trim();
        const pengarang = inputPengarang.value.trim();
        const nomor     = nomorUrut;

        
        btnTambah.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2"
                  role="status" aria-hidden="true"></span>
            Menyimpan...
        `;
        btnTambah.disabled = true;

        
        setTimeout(function () {

            
            dataBuku.push({ nomor: nomor, kode: kode, judul: judul, pengarang: pengarang });
            simpanKeStorage();

            
            renderBaris(nomor, kode, judul, pengarang);

            nomorUrut++;
            formBuku.reset();

            btnTambah.innerHTML = '<i class="mdi mdi-plus"></i> Tambah Buku';
            btnTambah.disabled  = false;

        }, 1500);
    });

    
    
    

    /**
     * Membaca data dari sel baris yang diklik, lalu mengisi form modal.
     * @param {HTMLTableRowElement} row - Baris tabel yang diklik
     */
    function bukaModalEdit(row) {
        selectedRow = row;

        
        const cells = row.querySelectorAll('td');
        modalId.value        = cells[1].textContent.trim();
        modalKode.value      = cells[2].textContent.trim();
        modalJudul.value     = cells[3].textContent.trim();
        modalPengarang.value = cells[4].textContent.trim();

        bsModal.show();
    }

    
    
    
    btnHapus.addEventListener('click', function () {

        const konfirmasi = confirm('Yakin ingin menghapus buku "' + modalJudul.value + '"?');

        if (konfirmasi) {
            
            const nomorHapus = parseInt(selectedRow.getAttribute('data-nomor'));

            
            selectedRow.remove();
            selectedRow = null;

            
            dataBuku = dataBuku.filter(function (d) { return d.nomor !== nomorHapus; });
            simpanKeStorage();

            bsModal.hide();

            
            if (tbodyBuku.querySelectorAll('tr:not(#emptyRow)').length === 0) {
                emptyRow.style.display = '';
            }
        }
    });

    
    
    
    btnUbah.addEventListener('click', function () {

        
        if (!formModal.checkValidity()) {
            formModal.reportValidity();
            return;
        }

        const nomorEdit  = parseInt(selectedRow.getAttribute('data-nomor'));
        const kodeBaru   = modalKode.value.trim();
        const judulBaru  = modalJudul.value.trim();
        const pengBaru   = modalPengarang.value.trim();

        
        const cells = selectedRow.querySelectorAll('td');
        cells[2].textContent = kodeBaru;
        cells[3].innerHTML   = `<strong>${judulBaru}</strong>`;
        cells[4].textContent = pengBaru;

        
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

    
    
    
    
    document.addEventListener('DOMContentLoaded', function () {
        muatDariStorage();
    });
</script>
@endpush
