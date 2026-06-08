@extends('layouts.app')

@section('title', 'Scan QR Pesanan Vendor')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-success text-white me-2">
                    <i class="mdi mdi-qrcode-scan"></i>
                </span>
                Scan QR Pesanan Customer
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kantin.vendor.menu') }}">Vendor Menu</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Scan QR</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Scanner QR</h4>
                <p class="text-muted">Arahkan kamera ke QR code yang menampilkan ID pesanan customer. Scanner akan berbunyi beep lalu berhenti otomatis.</p>

                <video id="reader" style="width:100%;max-width:480px;"></video>

                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <button type="button" id="btnStartScan" class="btn btn-success">
                        <i class="mdi mdi-play"></i> Mulai Scan
                    </button>
                    <button type="button" id="btnStopScan" class="btn btn-outline-danger" disabled>
                        <i class="mdi mdi-stop"></i> Stop
                    </button>
                    <button type="button" id="btnResetScan" class="btn btn-outline-secondary" disabled>
                        <i class="mdi mdi-refresh"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Hasil Scan</h4>
                <div id="scanResultEmpty" class="text-muted">Belum ada hasil scan.</div>

                <div id="scanResultBox" class="d-none">
                    <div class="alert alert-success mb-3">
                        QR berhasil dibaca: <strong id="scannedCode"></strong>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Customer</div>
                        <div class="fw-bold" id="resultCustomer"></div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Status Pembayaran</div>
                        <span class="badge badge-pill badge-success" id="resultStatus"></span>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted">Total</div>
                        <div class="fw-bold" id="resultTotal"></div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle">
                            <thead class="thead-light">
                                <tr>
                                    <th>Menu</th>
                                    <th width="80">Qty</th>
                                    <th width="120">Harga</th>
                                    <th width="130">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="itemsBody"></tbody>
                        </table>
                    </div>

                    <div id="resultQrLinkWrap" class="mt-3 d-none">
                        <a id="resultQrLink" class="btn btn-sm btn-outline-dark" target="_blank" rel="noopener">Buka QR Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js"></script>
<script>
(function () {
    const readerElement = document.getElementById('reader');
    const btnStartScan = document.getElementById('btnStartScan');
    const btnStopScan = document.getElementById('btnStopScan');
    const btnResetScan = document.getElementById('btnResetScan');
    const scanResultEmpty = document.getElementById('scanResultEmpty');
    const scanResultBox = document.getElementById('scanResultBox');
    const scannedCode = document.getElementById('scannedCode');
    const resultCustomer = document.getElementById('resultCustomer');
    const resultStatus = document.getElementById('resultStatus');
    const resultTotal = document.getElementById('resultTotal');
    const itemsBody = document.getElementById('itemsBody');
    const resultQrLinkWrap = document.getElementById('resultQrLinkWrap');
    const resultQrLink = document.getElementById('resultQrLink');

    const apiBase = '{{ url('kantin/vendor/api/pesanan') }}';
    const codeReader = new ZXing.BrowserMultiFormatReader();
    let controls = null;
    let scanLocked = false;

    function rupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function beep() {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextClass) {
            return Promise.resolve();
        }

        const audioContext = new AudioContextClass();
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();
        oscillator.type = 'sine';
        oscillator.frequency.value = 880;
        gain.gain.value = 0.08;
        oscillator.connect(gain);
        gain.connect(audioContext.destination);
        oscillator.start();

        return new Promise((resolve) => {
            setTimeout(() => {
                oscillator.stop();
                audioContext.close();
                resolve();
            }, 140);
        });
    }

    function setIdleState() {
        btnStartScan.disabled = false;
        btnStopScan.disabled = true;
        btnResetScan.disabled = true;
    }

    function setScanningState() {
        btnStartScan.disabled = true;
        btnStopScan.disabled = false;
        btnResetScan.disabled = false;
    }

    function renderResult(data, kode) {
        scannedCode.textContent = kode;
        resultCustomer.textContent = data.nama_customer + ' (Pesanan #' + data.idpesanan + ')';
        resultStatus.textContent = data.status_bayar_label;
        resultStatus.className = 'badge badge-pill ' + (Number(data.status_bayar) === 1 ? 'badge-success' : 'badge-warning');
        resultTotal.textContent = rupiah(data.total);
        itemsBody.innerHTML = '';

        (data.items || []).forEach((item) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.nama_menu}</td>
                <td>${item.jumlah}</td>
                <td>${rupiah(item.harga)}</td>
                <td>${rupiah(item.subtotal)}</td>
            `;
            itemsBody.appendChild(tr);
        });

        if (data.qr_code_url) {
            resultQrLink.href = data.qr_code_url;
            resultQrLinkWrap.classList.remove('d-none');
        } else {
            resultQrLinkWrap.classList.add('d-none');
        }

        scanResultEmpty.classList.add('d-none');
        scanResultBox.classList.remove('d-none');
    }

    async function stopScan() {
        if (controls) {
            try {
                await controls.stop();
            } catch (error) {
                console.warn('Scanner stop error:', error);
            }
            controls = null;
        }
        scanLocked = false;
        setIdleState();
    }

    async function startScan() {
        scanLocked = false;
        scanResultEmpty.classList.remove('d-none');
        scanResultBox.classList.add('d-none');

        try {
            const devices = await codeReader.listVideoInputDevices();
            const deviceId = devices.length > 0 ? devices[0].deviceId : undefined;

            controls = await codeReader.decodeFromVideoDevice( deviceId, 'reader', async (result, err, controller) => {
                if (!result || scanLocked) {
                    return;
                }

                scanLocked = true;
                const kode = result.getText().trim();

                try {
                    await beep();
                    if (controller) {
                        await controller.stop();
                    }
                } catch (stopError) {
                    console.warn('Gagal menghentikan scanner:', stopError);
                }

                try {
                    const response = await fetch(apiBase + '/' + encodeURIComponent(kode), {
                        headers: { 'Accept': 'application/json' }
                    });
                    const json = await response.json();

                    if (!response.ok) {
                        throw new Error(json.message || 'Pesanan tidak ditemukan.');
                    }

                    renderResult(json.data, kode);
                } catch (fetchError) {
                    scanResultEmpty.classList.remove('d-none');
                    scanResultBox.classList.add('d-none');
                    scanResultEmpty.textContent = fetchError.message || 'Pesanan tidak ditemukan.';
                } finally {
                    await stopScan();
                }
            });

            setScanningState();
        } catch (error) {
            console.error(error);
            scanResultEmpty.textContent = 'Tidak dapat membuka kamera: ' + (error.message || 'Unknown error');
            setIdleState();
        }
    }

    btnStartScan.addEventListener('click', startScan);
    btnStopScan.addEventListener('click', stopScan);
    btnResetScan.addEventListener('click', async () => {
        await stopScan();
        scanResultEmpty.classList.remove('d-none');
        scanResultEmpty.textContent = 'Belum ada hasil scan.';
        scanResultBox.classList.add('d-none');
        scannedCode.textContent = '';
        resultCustomer.textContent = '';
        resultStatus.textContent = '';
        resultTotal.textContent = '';
        itemsBody.innerHTML = '';
        resultQrLinkWrap.classList.add('d-none');
    });

    setIdleState();
})();
</script>
@endpush
