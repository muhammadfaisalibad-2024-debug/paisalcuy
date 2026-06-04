@extends('layouts.app')

@section('title', 'Kasir POS - Ajax dan Axios')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-cash-register"></i>
                </span>
                Kasir POS (Versi Ajax dan Axios)
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kasir POS</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Input Barang</h4>

                <div class="mb-3">
                    <a href="{{ route('barang.scanner') }}" id="openScannerBtn" class="btn btn-outline-info btn-sm">
                        <i class="mdi mdi-barcode-scan"></i> Buka Scanner Barcode
                    </a>
                </div>

                <div class="form-group mb-3">
                    <label for="modeRequest" class="font-weight-bold">Mode Request</label>
                    <select id="modeRequest" class="form-control">
                        <option value="ajax">jQuery Ajax</option>
                        <option value="axios">Axios</option>
                    </select>
                    <small class="text-muted">Silakan pilih mode untuk pembuktian versi Ajax/Axios.</small>
                </div>

                <div class="form-group mb-3">
                    <label for="kodeBarang" class="font-weight-bold">Kode Barang</label>
                    <input type="text" id="kodeBarang" class="form-control" placeholder="Masukkan kode barang lalu tekan Enter">
                </div>

                <div class="form-group mb-3">
                    <label for="namaBarang" class="font-weight-bold">Nama Barang</label>
                    <input type="text" id="namaBarang" class="form-control" readonly>
                </div>

                <div class="form-group mb-3">
                    <label for="hargaBarang" class="font-weight-bold">Harga Barang</label>
                    <input type="text" id="hargaBarang" class="form-control" readonly>
                </div>

                <div class="form-group mb-4">
                    <label for="jumlahBarang" class="font-weight-bold">Jumlah</label>
                    <input type="number" id="jumlahBarang" class="form-control" value="1" min="1">
                </div>

                <button type="button" id="btnTambahkan" class="btn btn-primary btn-block" disabled>
                    <i class="mdi mdi-plus-circle-outline"></i> Tambahkan
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Tabel Penjualan</h4>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableKeranjang">
                        <thead class="thead-dark">
                            <tr>
                                <th width="40">No</th>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th width="145">Harga</th>
                                <th width="110">Jumlah</th>
                                <th width="160">Subtotal</th>
                                <th width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="rowKosong">
                                <td colspan="7" class="text-center text-muted">Belum ada item.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap">
                    <h4 class="mb-2">Total: <span id="grandTotal">Rp 0</span></h4>
                    <button type="button" id="btnBayar" class="btn btn-success" disabled>
                        <i class="mdi mdi-cash-check"></i> Bayar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const modeRequest = document.getElementById('modeRequest');
    const kodeBarang = document.getElementById('kodeBarang');
    const namaBarang = document.getElementById('namaBarang');
    const hargaBarang = document.getElementById('hargaBarang');
    const jumlahBarang = document.getElementById('jumlahBarang');
    const btnTambahkan = document.getElementById('btnTambahkan');
    const btnBayar = document.getElementById('btnBayar');
    const tableBody = document.querySelector('#tableKeranjang tbody');
    const rowKosong = document.getElementById('rowKosong');
    const grandTotal = document.getElementById('grandTotal');

    const lookupBaseUrl = '{{ url('barang/api') }}';
    const saveUrl = '{{ route('barang.transaksi.store') }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let barangAktif = null;
    let keranjang = [];

    function rupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function getQtyInputValue() {
        const qty = parseInt(jumlahBarang.value, 10);
        return Number.isNaN(qty) ? 0 : qty;
    }

    function setButtonState() {
        btnTambahkan.disabled = !(barangAktif && getQtyInputValue() > 0);
        btnBayar.disabled = keranjang.length === 0;
    }

    function clearInputBarang() {
        barangAktif = null;
        kodeBarang.value = '';
        namaBarang.value = '';
        hargaBarang.value = '';
        jumlahBarang.value = 1;
        setButtonState();
        kodeBarang.focus();
    }

    function totalKeranjang() {
        return keranjang.reduce(function (acc, item) {
            return acc + item.subtotal;
        }, 0);
    }

    function renderTable() {
        if (keranjang.length === 0) {
            tableBody.innerHTML = '';
            tableBody.appendChild(rowKosong);
            grandTotal.textContent = rupiah(0);
            setButtonState();
            return;
        }

        tableBody.innerHTML = '';

        keranjang.forEach(function (item, index) {
            const row = document.createElement('tr');
            row.setAttribute('data-id', item.id_barang);
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.id_barang}</td>
                <td>${item.nama_barang}</td>
                <td>${rupiah(item.harga)}</td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-row" min="1" value="${item.jumlah}">
                </td>
                <td class="subtotal-cell">${rupiah(item.subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm btn-hapus" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </td>
            `;
            tableBody.appendChild(row);
        });

        grandTotal.textContent = rupiah(totalKeranjang());
        setButtonState();
    }

    function requestLookup(kode) {
        const endpoint = lookupBaseUrl + '/' + encodeURIComponent(kode);

        if (modeRequest.value === 'ajax') {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: endpoint,
                    method: 'GET',
                    dataType: 'json',
                    success: resolve,
                    error: reject
                });
            });
        }

        return axios.get(endpoint).then(function (response) {
            return response.data;
        });
    }

    function requestSave(payload) {
        if (modeRequest.value === 'ajax') {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: saveUrl,
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    success: resolve,
                    error: reject
                });
            });
        }

        return axios.post(saveUrl, payload, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        }).then(function (response) {
            return response.data;
        });
    }

    function extractErrorMessage(error, fallback) {
        if (error && error.responseJSON) {
            if (error.responseJSON.message) {
                return error.responseJSON.message;
            }
            if (error.responseJSON.errors) {
                const first = Object.values(error.responseJSON.errors)[0];
                if (Array.isArray(first) && first.length > 0) {
                    return first[0];
                }
            }
        }

        if (error && error.response && error.response.data) {
            const data = error.response.data;
            if (data.message) {
                return data.message;
            }
            if (data.errors) {
                const first = Object.values(data.errors)[0];
                if (Array.isArray(first) && first.length > 0) {
                    return first[0];
                }
            }
        }

        return fallback;
    }

    async function cariBarang() {
        const kode = kodeBarang.value.trim();

        if (!kode) {
            Swal.fire('Perhatian', 'Kode barang tidak boleh kosong.', 'warning');
            return;
        }

        btnTambahkan.disabled = true;

        try {
            const result = await requestLookup(kode);
            const data = result.data;

            barangAktif = {
                id_barang: data.id_barang,
                nama_barang: data.nama_barang,
                harga: Number(data.harga)
            };

            kodeBarang.value = data.id_barang;
            namaBarang.value = data.nama_barang;
            hargaBarang.value = rupiah(data.harga);
            jumlahBarang.value = 1;
            setButtonState();
            jumlahBarang.focus();
            jumlahBarang.select();
        } catch (error) {
            barangAktif = null;
            namaBarang.value = '';
            hargaBarang.value = '';
            jumlahBarang.value = 1;
            setButtonState();

            Swal.fire('Gagal', extractErrorMessage(error, 'Barang tidak ditemukan.'), 'error');
        }
    }

    function addToCart() {
        if (!barangAktif) {
            return;
        }

        const qty = getQtyInputValue();
        if (qty <= 0) {
            setButtonState();
            return;
        }

        const existingIndex = keranjang.findIndex(function (item) {
            return item.id_barang === barangAktif.id_barang;
        });

        if (existingIndex >= 0) {
            keranjang[existingIndex].jumlah += qty;
            keranjang[existingIndex].subtotal = keranjang[existingIndex].jumlah * keranjang[existingIndex].harga;
        } else {
            keranjang.push({
                id_barang: barangAktif.id_barang,
                nama_barang: barangAktif.nama_barang,
                harga: barangAktif.harga,
                jumlah: qty,
                subtotal: barangAktif.harga * qty
            });
        }

        renderTable();
        clearInputBarang();
    }

    async function bayar() {
        if (keranjang.length === 0) {
            return;
        }

        const payload = {
            items: keranjang.map(function (item) {
                return {
                    id_barang: item.id_barang,
                    jumlah: item.jumlah
                };
            })
        };

        btnBayar.disabled = true;
        btnBayar.innerHTML = '<span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>Menyimpan...';

        try {
            const result = await requestSave(payload);

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: result.message + ' ID Penjualan: ' + result.data.id_penjualan
            });

            keranjang = [];
            renderTable();
            clearInputBarang();
        } catch (error) {
            Swal.fire('Gagal', extractErrorMessage(error, 'Pembayaran gagal disimpan.'), 'error');
            setButtonState();
        } finally {
            btnBayar.innerHTML = '<i class="mdi mdi-cash-check"></i> Bayar';
            setButtonState();
        }
    }

    kodeBarang.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            cariBarang();
        }
    });

    jumlahBarang.addEventListener('input', function () {
        const qty = getQtyInputValue();
        if (qty < 1) {
            jumlahBarang.value = 1;
        }
        setButtonState();
    });

    // Listen for scanner popup messages
    window.addEventListener('message', async function (e) {
        if (!e.data || e.data.type !== 'barcodeScanned') return;
        try {
            const kode = (e.data.kode || '').toString();
            if (!kode) return;
            kodeBarang.value = kode;
            // perform lookup then auto add
            await cariBarang();
            // small delay to ensure barangAktif populated
            setTimeout(function () { addToCart(); }, 80);
        } catch (err) {
            console.warn('Failed to handle barcodeScanned message', err);
        }
    });

    // Also listen for storage events (in case scanner used localStorage to notify)
    window.addEventListener('storage', function (ev) {
        try {
            if (!ev.key || ev.key !== 'kb_last_scanned') return;
            if (!ev.newValue) return;
            const payload = JSON.parse(ev.newValue);
            const kode = (payload && payload.kode) ? String(payload.kode) : null;
            if (!kode) return;
            kodeBarang.value = kode;
            // lookup and add
            (async function(){
                try { await cariBarang(); setTimeout(function(){ addToCart(); }, 80); } catch(e){ console.warn('storage handler failed', e); }
            })();
        } catch (e) { console.debug('storage event handler error', e); }
    });

    // Open scanner in popup window
    const openScannerBtn = document.getElementById('openScannerBtn');
    if (openScannerBtn) {
        openScannerBtn.addEventListener('click', function (ev) {
            ev.preventDefault();
            const url = this.href;
            window.open(url, 'scannerWindow', 'width=900,height=700,menubar=no,toolbar=no');
        });
    }

    btnTambahkan.addEventListener('click', addToCart);
    btnBayar.addEventListener('click', bayar);

    tableBody.addEventListener('input', function (event) {
        if (!event.target.classList.contains('qty-row')) {
            return;
        }

        const row = event.target.closest('tr');
        const id = row.getAttribute('data-id');
        const idx = keranjang.findIndex(function (item) {
            return item.id_barang === id;
        });

        if (idx < 0) {
            return;
        }

        let newQty = parseInt(event.target.value, 10) || 0;
        if (newQty <= 0) {
            newQty = 1;
            event.target.value = 1;
        }

        keranjang[idx].jumlah = newQty;
        keranjang[idx].subtotal = keranjang[idx].harga * newQty;

        row.querySelector('.subtotal-cell').textContent = rupiah(keranjang[idx].subtotal);
        grandTotal.textContent = rupiah(totalKeranjang());
        setButtonState();
    });

    tableBody.addEventListener('click', function (event) {
        const btnHapus = event.target.closest('.btn-hapus');
        if (!btnHapus) {
            return;
        }

        const row = btnHapus.closest('tr');
        const id = row.getAttribute('data-id');

        keranjang = keranjang.filter(function (item) {
            return item.id_barang !== id;
        });

        renderTable();
    });

    renderTable();
    clearInputBarang();
})();
</script>
@endpush
