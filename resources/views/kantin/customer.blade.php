<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kantin Online - Customer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Midtrans Snap.js -->
    <script src="https://app.{{ config('services.midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --accent: #0f6d8c;
            --accent-2: #14a58f;
            --ink: #16324f;
        }
        body {
            background: radial-gradient(circle at top right, #dff3ff, transparent 38%),
                        radial-gradient(circle at bottom left, #d9f8ee, transparent 34%),
                        var(--bg);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 24px 0 40px;
        }
        .shell {
            max-width: 1100px;
            margin: 0 auto;
        }
        .hero {
            background: linear-gradient(120deg, var(--accent), var(--accent-2));
            color: #fff;
            border-radius: 18px;
            padding: 20px 22px;
            box-shadow: 0 16px 26px rgba(16, 72, 99, .22);
        }
        .card-soft {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(21, 54, 82, .08);
        }
        .table thead th {
            background: #183754;
            color: #fff;
            border-color: #244b70;
        }
    </style>
</head>
<body>
<div class="container shell">
    <div class="hero mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h2 class="mb-1">Mini Pemesanan Kantin</h2>
                <div>Customer dapat pesan tanpa login, lalu bayar Virtual Account atau QRIS.</div>
            </div>
            <a href="{{ url('/login') }}" class="btn btn-light btn-sm"><i class="bi bi-person"></i> Login Vendor</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card card-soft">
                <div class="card-body">
                    <h5 class="card-title mb-3">Form Pesanan</h5>

                    <div class="mb-3">
                        <label class="form-label">Nama Customer (opsional)</label>
                        <input type="text" id="namaCustomer" class="form-control" placeholder="Kosongkan untuk Guest otomatis">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Vendor</label>
                        <select id="selectVendor" class="form-select">
                            <option value="">-- Pilih Vendor --</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->idvendor }}">{{ $vendor->nama_vendor }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pilih Menu</label>
                        <select id="selectMenu" class="form-select" disabled>
                            <option value="">-- Pilih Menu --</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Menu</label>
                        <input type="text" id="namaMenu" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="text" id="hargaMenu" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jumlah</label>
                        <input type="number" id="jumlahMenu" class="form-control" min="1" value="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <input type="text" id="catatanMenu" class="form-control" placeholder="Opsional">
                    </div>

                    <button id="btnTambahkan" type="button" class="btn btn-primary w-100" disabled>
                        <i class="bi bi-plus-circle"></i> Tambahkan
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-soft">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="tableCart">
                            <thead>
                                <tr>
                                    <th width="40">No</th>
                                    <th>Menu</th>
                                    <th width="130">Harga</th>
                                    <th width="110">Jumlah</th>
                                    <th width="130">Subtotal</th>
                                    <th>Catatan</th>
                                    <th width="70">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="rowEmpty"><td colspan="7" class="text-center text-muted">Belum ada item pesanan.</td></tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <h5 class="m-0">Total: <span id="grandTotal">Rp 0</span></h5>
                        <button id="btnPesan" type="button" class="btn btn-success" disabled>
                            <i class="bi bi-bag-check"></i> Buat Pesanan
                        </button>
                    </div>

                    <div id="boxBayar" class="mt-4 d-none">
                        <div class="alert alert-info mb-3">
                            Pesanan <strong id="txtOrderId">-</strong> berhasil dibuat oleh <strong id="txtNamaCustomer">-</strong>.
                            Silakan pilih metode pembayaran.
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button id="btnBayarSnap" class="btn btn-primary"><i class="bi bi-credit-card"></i> Bayar dengan Midtrans</button>
                            <button id="btnCekStatus" class="btn btn-outline-success d-none"><i class="bi bi-arrow-repeat"></i> Cek Status Pembayaran</button>
                            <a id="btnLihatQr" class="btn btn-outline-dark d-none" target="_blank" rel="noopener"><i class="bi bi-qr-code"></i> Lihat QR Pesanan</a>
                            <button id="btnPesananBaru" class="btn btn-outline-secondary d-none" type="button"><i class="bi bi-plus-circle"></i> Pesanan Baru</button>
                        </div>
                        <div id="paymentInfo" class="alert alert-info mt-3 d-none mb-0">
                            <div id="paymentInfoText" class="small"></div>
                        </div>
                        <div id="paidQrBox" class="card border-0 shadow-sm mt-3 d-none">
                            <div class="card-body text-center">
                                <h6 class="mb-2">QR Pesanan</h6>
                                <img id="paidQrImage" alt="QR Pesanan" class="img-fluid mb-3" style="max-width: 240px;">
                                <div class="small text-muted mb-3">Simpan atau buka ulang QR melalui link di bawah ini.</div>
                                <a id="paidQrLink" class="btn btn-sm btn-dark" target="_blank" rel="noopener">Buka Halaman QR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const selectVendor = document.getElementById('selectVendor');
    const selectMenu = document.getElementById('selectMenu');
    const namaMenu = document.getElementById('namaMenu');
    const hargaMenu = document.getElementById('hargaMenu');
    const jumlahMenu = document.getElementById('jumlahMenu');
    const catatanMenu = document.getElementById('catatanMenu');
    const btnTambahkan = document.getElementById('btnTambahkan');
    const btnPesan = document.getElementById('btnPesan');
    const grandTotal = document.getElementById('grandTotal');
    const rowEmpty = document.getElementById('rowEmpty');
    const tableBody = document.querySelector('#tableCart tbody');
    const namaCustomerInput = document.getElementById('namaCustomer');
    const boxBayar = document.getElementById('boxBayar');
    const txtOrderId = document.getElementById('txtOrderId');
    const txtNamaCustomer = document.getElementById('txtNamaCustomer');
    const btnBayarSnap = document.getElementById('btnBayarSnap');
    const btnCekStatus = document.getElementById('btnCekStatus');
    const btnLihatQr = document.getElementById('btnLihatQr');
    const btnPesananBaru = document.getElementById('btnPesananBaru');
    const paymentInfo = document.getElementById('paymentInfo');
    const paymentInfoText = document.getElementById('paymentInfoText');
    const paidQrBox = document.getElementById('paidQrBox');
    const paidQrImage = document.getElementById('paidQrImage');
    const paidQrLink = document.getElementById('paidQrLink');

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let menus = [];
    let selectedMenu = null;
    let cart = [];
    let activeOrderId = null;

    function rupiah(n) {
        return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
    }

    function totalCart() {
        return cart.reduce((acc, item) => acc + item.subtotal, 0);
    }

    function setButtonState() {
        const qty = parseInt(jumlahMenu.value, 10) || 0;
        btnTambahkan.disabled = !(selectedMenu && qty > 0 && !activeOrderId);
        btnPesan.disabled = cart.length === 0 || !!activeOrderId;
    }

    function request(method, url, data) {
        return fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: method === 'GET' ? null : JSON.stringify(data || {}),
        }).then(async (response) => {
            const json = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw json;
            }
            return json;
        });
    }

    function getErr(err, fallback) {
        if (err?.responseJSON?.message) return err.responseJSON.message;
        if (err?.response?.data?.message) return err.response.data.message;
        const errsA = err?.responseJSON?.errors;
        const errsB = err?.response?.data?.errors;
        if (errsA) return Object.values(errsA)[0][0];
        if (errsB) return Object.values(errsB)[0][0];
        return fallback;
    }

    async function loadMenusByVendor(idvendor) {
        if (!idvendor) {
            selectMenu.innerHTML = '<option value="">-- Pilih Menu --</option>';
            selectMenu.disabled = true;
            menus = [];
            selectedMenu = null;
            namaMenu.value = '';
            hargaMenu.value = '';
            jumlahMenu.value = 1;
            setButtonState();
            return;
        }

        try {
            const url = '/kantin/api/vendors/' + idvendor + '/menus';
            const result = await request('GET', url);
            menus = result.data || [];
            selectMenu.disabled = false;
            selectMenu.innerHTML = '<option value="">-- Pilih Menu --</option>';
            menus.forEach(menu => {
                const opt = document.createElement('option');
                opt.value = menu.idmenu;
                opt.textContent = menu.nama_menu + ' - ' + rupiah(menu.harga);
                selectMenu.appendChild(opt);
            });
            selectedMenu = null;
            namaMenu.value = '';
            hargaMenu.value = '';
            jumlahMenu.value = 1;
            setButtonState();
        } catch (err) {
            Swal.fire('Error', getErr(err, 'Gagal memuat menu vendor.'), 'error');
        }
    }

    function renderCart() {
        if (cart.length === 0) {
            tableBody.innerHTML = '';
            tableBody.appendChild(rowEmpty);
            grandTotal.textContent = rupiah(0);
            setButtonState();
            return;
        }

        tableBody.innerHTML = '';

        cart.forEach((item, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.idmenu = item.idmenu;
            tr.innerHTML = `
                <td>${idx + 1}</td>
                <td>${item.nama_menu}</td>
                <td>${rupiah(item.harga)}</td>
                <td><input type="number" class="form-control form-control-sm qty-row" min="1" value="${item.jumlah}" ${activeOrderId ? 'disabled' : ''}></td>
                <td class="subtotal-cell">${rupiah(item.subtotal)}</td>
                <td><input type="text" class="form-control form-control-sm note-row" value="${item.catatan || ''}" ${activeOrderId ? 'disabled' : ''}></td>
                <td><button class="btn btn-sm btn-danger btn-del" ${activeOrderId ? 'disabled' : ''}><i class="bi bi-trash"></i></button></td>
            `;
            tableBody.appendChild(tr);
        });

        grandTotal.textContent = rupiah(totalCart());
        setButtonState();
    }

    function resetAfterPaid() {
        activeOrderId = null;
        cart = [];
        renderCart();
        boxBayar.classList.add('d-none');
        btnCekStatus.classList.add('d-none');
        btnLihatQr.classList.add('d-none');
        btnPesananBaru.classList.add('d-none');
        paymentInfo.classList.add('d-none');
        paymentInfoText.textContent = '';
        paidQrBox.classList.add('d-none');
        paidQrImage.removeAttribute('src');
        paidQrLink.removeAttribute('href');
        selectVendor.value = '';
        selectMenu.innerHTML = '<option value="">-- Pilih Menu --</option>';
        selectMenu.disabled = true;
        selectedMenu = null;
        namaMenu.value = '';
        hargaMenu.value = '';
        jumlahMenu.value = 1;
        catatanMenu.value = '';
        setButtonState();
    }

    function renderPaymentInfo(data) {
        const txStatus = data?.transaction_status || 'pending';

        const message = 'Snap token berhasil dibuat. Status saat ini: ' + txStatus + '. Silakan selesaikan pembayaran melalui popup Snap.';

        paymentInfoText.textContent = message;
        paymentInfo.classList.remove('d-none');
        btnCekStatus.classList.remove('d-none');
    }

    function showQrState(data) {
        const qrPageUrl = '/kantin/pesanan/' + data.idpesanan + '/qrcode';
        btnLihatQr.href = qrPageUrl;
        btnLihatQr.classList.remove('d-none');
        btnPesananBaru.classList.remove('d-none');
        btnBayarSnap.classList.add('d-none');
        btnCekStatus.classList.add('d-none');

        if (data.qr_code_url) {
            paidQrImage.src = data.qr_code_url;
            paidQrLink.href = qrPageUrl;
            paidQrBox.classList.remove('d-none');
        }
    }

    async function buatPesanan() {
        if (cart.length === 0) return;

        const payload = {
            nama_customer: namaCustomerInput.value.trim(),
            items: cart.map(i => ({ idmenu: i.idmenu, jumlah: i.jumlah, catatan: i.catatan || '' }))
        };

        try {
            const result = await request('POST', '/kantin/api/pesanan', payload);
            activeOrderId = result.data.idpesanan;
            txtOrderId.textContent = '#' + result.data.idpesanan;
            txtNamaCustomer.textContent = result.data.nama_customer;
            boxBayar.classList.remove('d-none');
            btnLihatQr.classList.add('d-none');
            btnPesananBaru.classList.add('d-none');
            paidQrBox.classList.add('d-none');
            renderCart();
            Swal.fire('Berhasil', result.message, 'success');
        } catch (err) {
            Swal.fire('Error', getErr(err, 'Gagal membuat pesanan.'), 'error');
        }
    }

    async function bayarPesanan() {
        if (!activeOrderId) return;

        try {
            Swal.fire({
                title: 'Mempersiapkan Pembayaran',
                html: 'Menghubungi server Midtrans...',
                allowOutsideClick: false,
                didOpen: async () => {
                    Swal.showLoading();
                    try {
                        const result = await request('POST', '/kantin/snap/token/' + activeOrderId);
                        if (!result.success) {
                            Swal.close();
                            Swal.fire('Error', result.message, 'error');
                            return;
                        }

                        Swal.close();
                        if (typeof snap === 'undefined') {
                            Swal.fire('Error', 'Snap.js belum termuat. Cek script Midtrans.', 'error');
                            return;
                        }

                        snap.pay(result.data.snap_token, {
                            onSuccess: (paymentResult) => {
                                handlePaymentSuccess(paymentResult);
                            },
                            onPending: (paymentResult) => {
                                handlePaymentPending(paymentResult);
                            },
                            onError: (paymentResult) => {
                                handlePaymentError(paymentResult);
                            },
                            onClose: () => {
                                console.log('Snap popup ditutup oleh user');
                            }
                        });
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.close();
                        Swal.fire('Error', error?.message || error?.status_message || 'Gagal memproses pembayaran', 'error');
                    }
                }
            });

        } catch (error) {
            console.error('Pembayaran error:', error);
            Swal.fire('Error', error.message || 'Terjadi kesalahan', 'error');
        }
    }

    function handlePaymentSuccess(result) {
        console.log('Payment success:', result);
        Swal.fire({
            icon: 'success',
            title: 'Pembayaran Berhasil Diproses',
            text: 'Kami sedang sinkronisasi status pembayaran ke Midtrans...',
            timer: 1800,
            showConfirmButton: false,
        }).then(async () => {
            await cekStatusBayar();
        });
    }

    function handlePaymentPending(result) {
        console.log('Payment pending:', result);
        Swal.fire({
            icon: 'info',
            title: 'Pembayaran Sedang Diproses',
            text: 'Silahkan tunggu konfirmasi dari bank, lalu cek status lagi.',
            timer: 1800,
            showConfirmButton: false,
        }).then(async () => {
            await cekStatusBayar();
        });
    }

    function handlePaymentError(result) {
        console.log('Payment error:', result);
        Swal.fire({
            icon: 'error',
            title: 'Pembayaran Gagal',
            text: 'Terjadi kesalahan saat memproses pembayaran. Silahkan coba lagi.',
            timer: 3000
        });
    }

    async function cekStatusBayar() {
        if (!activeOrderId) return;

        try {
            const result = await request('GET', '/kantin/snap/status/' + activeOrderId);

            if (!result.success) {
                Swal.fire('Error', result.message, 'error');
                return;
            }

            if (Number(result.data.status_bayar) === 1) {
                await Swal.fire('Lunas', result.message, 'success');
                showQrState(result.data);
                renderPaymentInfo(result.data);
                return;
            }

            renderPaymentInfo(result.data);
            Swal.fire('Belum Lunas', result.message, 'warning');
        } catch (err) {
            Swal.fire('Error', getErr(err, 'Gagal mengecek status pembayaran.'), 'error');
        }
    }

    selectVendor.addEventListener('change', () => {
        loadMenusByVendor(selectVendor.value);
    });

    selectMenu.addEventListener('change', () => {
        selectedMenu = menus.find(m => String(m.idmenu) === String(selectMenu.value)) || null;
        if (selectedMenu) {
            namaMenu.value = selectedMenu.nama_menu;
            hargaMenu.value = rupiah(selectedMenu.harga);
            jumlahMenu.value = 1;
        } else {
            namaMenu.value = '';
            hargaMenu.value = '';
            jumlahMenu.value = 1;
        }
        setButtonState();
    });

    jumlahMenu.addEventListener('input', () => {
        if ((parseInt(jumlahMenu.value, 10) || 0) < 1) jumlahMenu.value = 1;
        setButtonState();
    });

    btnTambahkan.addEventListener('click', () => {
        if (!selectedMenu || activeOrderId) return;
        const jumlah = parseInt(jumlahMenu.value, 10) || 0;
        if (jumlah <= 0) return;

        const idx = cart.findIndex(i => i.idmenu === selectedMenu.idmenu);
        if (idx >= 0) {
            cart[idx].jumlah += jumlah;
            cart[idx].subtotal = cart[idx].harga * cart[idx].jumlah;
            cart[idx].catatan = catatanMenu.value.trim() || cart[idx].catatan;
        } else {
            cart.push({
                idmenu: selectedMenu.idmenu,
                nama_menu: selectedMenu.nama_menu,
                harga: Number(selectedMenu.harga),
                jumlah,
                subtotal: Number(selectedMenu.harga) * jumlah,
                catatan: catatanMenu.value.trim()
            });
        }

        catatanMenu.value = '';
        renderCart();
    });

    btnPesananBaru.addEventListener('click', () => {
        resetAfterPaid();
        btnBayarSnap.classList.remove('d-none');
        Swal.fire('Siap', 'Silakan buat pesanan baru.', 'info');
    });

    tableBody.addEventListener('input', (event) => {
        if (activeOrderId) return;

        const tr = event.target.closest('tr');
        if (!tr || !tr.dataset.idmenu) return;
        const idx = cart.findIndex(i => String(i.idmenu) === tr.dataset.idmenu);
        if (idx < 0) return;

        if (event.target.classList.contains('qty-row')) {
            let qty = parseInt(event.target.value, 10) || 0;
            if (qty < 1) {
                qty = 1;
                event.target.value = 1;
            }
            cart[idx].jumlah = qty;
            cart[idx].subtotal = qty * cart[idx].harga;
            tr.querySelector('.subtotal-cell').textContent = rupiah(cart[idx].subtotal);
            grandTotal.textContent = rupiah(totalCart());
        }

        if (event.target.classList.contains('note-row')) {
            cart[idx].catatan = event.target.value;
        }
    });

    tableBody.addEventListener('click', (event) => {
        if (activeOrderId) return;

        const btn = event.target.closest('.btn-del');
        if (!btn) return;

        const tr = btn.closest('tr');
        cart = cart.filter(i => String(i.idmenu) !== tr.dataset.idmenu);
        renderCart();
    });

    btnPesan.addEventListener('click', buatPesanan);
    btnBayarSnap.addEventListener('click', bayarPesanan);
    btnCekStatus.addEventListener('click', cekStatusBayar);

    renderCart();
})();
</script>
</body>
</html>
