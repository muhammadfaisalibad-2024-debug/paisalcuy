@extends('layouts.app')

@section('title', 'Scanner Barcode Barang')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-info text-white me-2">
                    <i class="mdi mdi-barcode-scan"></i>
                </span>
                Scanner Barcode Barang
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('barang.kasir') }}">Kasir POS</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Scanner Barcode</li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Scan Barcode Label</h4>
                <p class="text-muted">Arahkan kamera ke barcode pada label barang. Setelah berhasil dibaca, scanner akan berbunyi beep lalu berhenti otomatis.</p>

                <div class="mb-2 d-flex gap-2 align-items-center">
                    <select id="cameraSelect" class="form-select" style="max-width:320px;"></select>
                    <button id="btnRefreshCams" type="button" class="btn btn-sm btn-outline-secondary">Refresh</button>
                </div>
                <video id="reader" style="width:100%; max-width: 480px; background:#000" playsinline muted></video>

                <div class="mt-3 d-flex gap-2 flex-wrap">
                    <button type="button" id="btnStartScan" class="btn btn-primary">
                        <i class="mdi mdi-play"></i> Mulai Scan
                    </button>
                    <button type="button" id="btnStopScan" class="btn btn-outline-danger" disabled>
                        <i class="mdi mdi-stop"></i> Stop
                    </button>
                    <button type="button" id="btnResetScan" class="btn btn-outline-secondary" disabled>
                        <i class="mdi mdi-refresh"></i> Reset
                    </button>
                    <div class="d-flex align-items-center">
                        <input type="file" id="uploadImage" accept="image/*" class="form-control ms-2" style="width:220px;" />
                        <button type="button" id="btnDecodeImage" class="btn btn-sm btn-outline-primary ms-2">Decode Gambar</button>
                        <button type="button" id="btnCaptureDecode" class="btn btn-sm btn-outline-success ms-2">Capture & Decode</button>
                    </div>
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
                        Barcode berhasil dibaca: <strong id="scannedCode"></strong>
                    </div>
                    <table class="table table-bordered">
                        <tr>
                            <th width="180">ID Barang</th>
                            <td id="resultId"></td>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <td id="resultNama"></td>
                        </tr>
                        <tr>
                            <th>Harga Barang</th>
                            <td id="resultHarga"></td>
                        </tr>
                        <tr>
                            <th>Stok</th>
                            <td id="resultStok"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Prefer local copy to avoid Tracking Protection blocking third-party storage access -->
<script src="/assets/vendors/zxing/index.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('[scanner] DOM ready');
    (function () {
    const readerElement = document.getElementById('reader');
    const btnStartScan = document.getElementById('btnStartScan');
    const btnStopScan = document.getElementById('btnStopScan');
    const btnResetScan = document.getElementById('btnResetScan');
    const scanResultEmpty = document.getElementById('scanResultEmpty');
    const scanResultBox = document.getElementById('scanResultBox');
    const scannedCode = document.getElementById('scannedCode');
    const resultId = document.getElementById('resultId');
    const resultNama = document.getElementById('resultNama');
    const resultHarga = document.getElementById('resultHarga');
    const resultStok = document.getElementById('resultStok');

    const apiBase = '{{ url('barang/api') }}';
    // ZXing hints: be more aggressive and allow multiple common formats (QR + linear)
    const hints = new Map();
    hints.set(ZXing.DecodeHintType.TRY_HARDER, true);
    hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [
        ZXing.BarcodeFormat.QR_CODE,
        ZXing.BarcodeFormat.CODE_128,
        ZXing.BarcodeFormat.CODE_39,
        ZXing.BarcodeFormat.EAN_13
    ]);
    const codeReader = new ZXing.BrowserMultiFormatReader(hints);
    let controls = null;
    let scanLocked = false;
    const cameraSelect = document.getElementById('cameraSelect');
    const btnRefreshCams = document.getElementById('btnRefreshCams');
    let livePreprocessInterval = null;
    let autoCaptureInterval = null;
    let liveCanvas = null;
    let liveCtx = null;
    const LIVE_INTERVAL_MS = 300; // sample every 300ms
    const AUTO_CAPTURE_MS = 100; // auto-capture interval when scanning

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

    function showResult(data, kode) {
        scanResultEmpty.classList.add('d-none');
        scanResultBox.classList.remove('d-none');
        scannedCode.textContent = kode;
        resultId.textContent = data.id_barang;
        resultNama.textContent = data.nama_barang;
        resultHarga.textContent = rupiah(data.harga);
        resultStok.textContent = data.stok;
        // notify opener (POS) and persist to storage so POS can pick it up
        try {
            const payload = { kode: String(kode), data: data };
            try { localStorage.setItem('kb_last_scanned', JSON.stringify(payload)); } catch(_){}
            if (window.opener && !window.opener.closed) {
                try { window.opener.postMessage({ type: 'barcodeScanned', kode: payload.kode, data: payload.data }, '*'); } catch(_){}
                // close popup shortly after notifying
                setTimeout(()=>{ try{ window.close(); } catch(_){ } }, 250);
            }
        } catch (e) { console.debug('showResult notify failed', e); }
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
        try { codeReader.reset(); } catch (_) {}
        // stop live preprocess
        if (livePreprocessInterval) {
            clearInterval(livePreprocessInterval);
            livePreprocessInterval = null;
        }
        if (autoCaptureInterval) {
            clearInterval(autoCaptureInterval);
            autoCaptureInterval = null;
        }
        // stop any video element stream
        try {
            if (readerElement && readerElement.srcObject) {
                const st = readerElement.srcObject;
                if (st.getTracks) st.getTracks().forEach(t=>{ try{ t.stop(); }catch(_){}});
                try { readerElement.srcObject = null; } catch(_){}
            }
        } catch (e) { console.debug('stopScan: failed to clear srcObject', e); }
    }

    async function startScan() {
        scanLocked = false;
        scanResultEmpty.classList.remove('d-none');
        scanResultBox.classList.add('d-none');

        try {
            let devices = await codeReader.listVideoInputDevices();
            // If devices not listed (labels empty) request a getUserMedia to prompt permission
            if (!devices || devices.length === 0 || devices.every(d=>!d.label)) {
                    try {
                    console.log('[scanner] requesting getUserMedia to prompt permission');
                    const tmpStream = await navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } });
                    // immediately stop tracks
                    tmpStream.getTracks().forEach(t=>t.stop());
                    devices = await codeReader.listVideoInputDevices();
                } catch (permErr) {
                    console.warn('[scanner] getUserMedia permission request failed', permErr);
                    throw permErr;
                }
            }
            if (!devices || devices.length === 0) {
                throw new Error('Tidak ditemukan perangkat kamera. Pastikan perangkat memiliki kamera dan browser mengizinkan akses.');
            }
            // populate camera dropdown
            cameraSelect.innerHTML = '';
            devices.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.deviceId;
                opt.textContent = d.label || d.deviceId;
                cameraSelect.appendChild(opt);
            });
            // Try each available device until one successfully starts
            let started = false;
            let lastErr = null;
            // prefer selected camera if any
            const preferredId = cameraSelect.value || (devices[0] && devices[0].deviceId);
            const tryList = devices.slice();
            // move preferred to front
            if (preferredId) {
                const idx = tryList.findIndex(x => x.deviceId === preferredId);
                if (idx > 0) tryList.unshift(tryList.splice(idx,1)[0]);
            }
            for (const dev of tryList) {
                let streamForDev = null;
                try {
                    console.log('[scanner] trying device:', dev.label || dev.deviceId);
                    // try to obtain a stream explicitly first (helps when browser requires early permission)
                    try {
                        streamForDev = await navigator.mediaDevices.getUserMedia({ video: { deviceId: { exact: dev.deviceId }, width: { ideal: 1280 }, height: { ideal: 720 } } });
                        // stop any previous stream attached
                        try { if (readerElement && readerElement.srcObject && readerElement.srcObject.getTracks) readerElement.srcObject.getTracks().forEach(t=>t.stop()); } catch(_) {}
                        readerElement.srcObject = streamForDev;
                        readerElement.autoplay = true; readerElement.playsInline = true; readerElement.muted = true;
                        try { if (typeof readerElement.play === 'function' && readerElement.paused) await readerElement.play(); } catch(playErr) { console.debug('video.play() during explicit stream failed', playErr); }
                        // small delay to let the camera start and provide stable frames
                        await new Promise(r => setTimeout(r, 150));
                        // use decodeFromVideoElement to avoid library opening its own MediaStream
                        try {
                            controls = await codeReader.decodeFromVideoElement(readerElement, async (result, err, controller) => {
                                // same callback body will be used (see below)
                                // if no result yet, silently return
                                if (!result) return;
                                if (scanLocked) return;

                                let kode = null;
                                try {
                                    if (typeof result.getText === 'function') kode = result.getText();
                                    else if (result && result.text) kode = result.text;
                                    else if (typeof result === 'string') kode = result;
                                    else kode = String(result);
                                } catch (ex) { console.warn('[scanner] failed to read text from result', ex, result); return; }
                                if (!kode) return;
                                kode = kode.trim();
                                scanLocked = true;
                                try { await beep(); } catch(_){}
                                try { if (controller && typeof controller.stop === 'function') { try { controller.stop(); } catch(e){} } } catch(_){}
                                try { await stopScan(); } catch(_){}
                                try {
                                    const response = await fetch(apiBase + '/' + encodeURIComponent(kode), { headers: { 'Accept': 'application/json' } });
                                    const json = await response.json();
                                    if (!response.ok) throw new Error(json.message || 'Barang tidak ditemukan.');
                                    showResult(json.data, kode);
                                } catch (fetchError) { console.warn('[scanner] fetch item failed', fetchError); scanResultEmpty.classList.remove('d-none'); scanResultEmpty.textContent = fetchError.message || 'Barang tidak ditemukan.'; }
                            });
                            console.log('[scanner] started on device (element):', dev.label || dev.deviceId);
                            started = true;
                            break;
                        } catch (decElErr) {
                            console.debug('[scanner] decodeFromVideoElement failed', decElErr && decElErr.message);
                            // fall through to attempting library-managed start below
                        }
                    } catch (gme) {
                        // explicit getUserMedia may fail on some browsers; we'll fall back to library API
                        console.debug('[scanner] explicit getUserMedia failed, will try library start', gme && gme.message);
                    }

                    // if explicit stream wasn't used or decodeFromVideoElement failed, let library manage device
                    controls = await codeReader.decodeFromVideoDevice(dev.deviceId, readerElement, async (result, err, controller) => {
                        console.debug('[scanner callback] result, err:', result, err);
                        // if no result yet, silently return
                        if (!result) {
                            return;
                        }
                        if (scanLocked) {
                            console.debug('[scanner] scan locked, ignoring result');
                            return;
                        }

                        // obtain text safely from different result shapes
                        let kode = null;
                        try {
                            if (typeof result.getText === 'function') {
                                kode = result.getText();
                            } else if (result && result.text) {
                                kode = result.text;
                            } else if (typeof result === 'string') {
                                kode = result;
                            } else {
                                kode = String(result);
                            }
                        } catch (ex) {
                            console.warn('[scanner] failed to read text from result', ex, result);
                            return;
                        }

                        if (!kode) {
                            console.warn('[scanner] empty kode extracted', result);
                            return;
                        }

                        kode = kode.trim();
                        scanLocked = true;

                        try {
                            await beep();
                        } catch (beepErr) {
                            console.warn('Beep failed', beepErr);
                        }

                        // try stop controller and reader safely
                        try {
                            if (controller && typeof controller.stop === 'function') {
                                try { controller.stop(); } catch(e) { console.warn('controller.stop() failed', e); }
                            }
                            try { await stopScan(); } catch(e) { console.warn('stopScan() failed', e); }
                        } catch (stopError) {
                            console.warn('Error while stopping scanner:', stopError);
                        }

                        // fetch item data
                        try {
                            const response = await fetch(apiBase + '/' + encodeURIComponent(kode), {
                                headers: { 'Accept': 'application/json' }
                            });
                            const json = await response.json();
                            if (!response.ok) {
                                throw new Error(json.message || 'Barang tidak ditemukan.');
                            }
                            showResult(json.data, kode);
                        } catch (fetchError) {
                            console.warn('[scanner] fetch item failed', fetchError);
                            scanResultEmpty.classList.remove('d-none');
                            scanResultBox.classList.add('d-none');
                            scanResultEmpty.textContent = fetchError.message || 'Barang tidak ditemukan.';
                        }
                    });
                    try { if (typeof readerElement.play === 'function' && readerElement.paused) await readerElement.play(); } catch(playErr){ console.debug('video.play() failed', playErr); }
                    console.log('[scanner] started on device (library):', dev.label || dev.deviceId);
                    started = true;
                    break;
                } catch (e) {
                    console.warn('[scanner] failed to start on device with library', dev.label || dev.deviceId, e && e.message);
                    // if we obtained an explicit stream, use manual decode fallback (draw frames to canvas and try decode)
                    if (streamForDev) {
                        console.log('[scanner] falling back to manual decode using explicit stream for device', dev.label || dev.deviceId);
                        controls = {
                            stop: async () => { try { streamForDev.getTracks().forEach(t=>t.stop()); } catch(_){} }
                        };
                        started = true;
                        break;
                    }
                    lastErr = e;
                }
            }

            if (!started) {
                throw lastErr || new Error('Could not start video source');
            }

            // start live preprocessing loop: sample frames and attempt decode
            try {
                if (!liveCanvas) {
                    liveCanvas = document.createElement('canvas');
                    liveCtx = liveCanvas.getContext('2d', { willReadFrequently: true });
                }
                livePreprocessInterval = setInterval(async () => {
                    if (scanLocked) return;
                    try {
                        const result = await tryDecodeFromVideoFrame();
                        if (result) {
                            await processDecodedResult(result);
                        }
                    } catch (outer) {
                        console.debug('live preprocess error', outer);
                    }
                }, LIVE_INTERVAL_MS);
                // start automatic capture-and-decode loop (aggressive)
                autoCaptureInterval = setInterval(async () => {
                    if (scanLocked) return;
                    try {
                        const result = await tryDecodeFromVideoFrame();
                        if (result) {
                            await processDecodedResult(result);
                        }
                    } catch (outer) { console.debug('auto capture decode error', outer); }
                }, AUTO_CAPTURE_MS);
            } catch (liveErr) {
                console.warn('Failed to start live preprocess loop', liveErr);
            }
            // try one immediate decode right after starting to avoid waiting the first interval
            try {
                const res0 = await tryDecodeFromVideoFrame();
                if (res0) {
                    await processDecodedResult(res0);
                }
            } catch (immErr) { console.debug('immediate capture error', immErr); }

            setScanningState();
        } catch (error) {
            console.error(error);
            scanResultEmpty.textContent = 'Tidak dapat membuka kamera: ' + (error.message || 'Unknown error');
            setIdleState();
        }
    }

    try {
        btnStartScan.addEventListener('click', startScan);
        btnStopScan.addEventListener('click', stopScan);
        btnRefreshCams.addEventListener('click', async () => {
            try {
                const devices = await codeReader.listVideoInputDevices();
                cameraSelect.innerHTML = '';
                devices.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.deviceId;
                    opt.textContent = d.label || d.deviceId;
                    cameraSelect.appendChild(opt);
                });
            } catch (e) { console.warn('refresh cams', e); }
        });
    } catch (e) {
        console.error('[scanner] failed to attach listeners', e);
        // fallback: ensure start is enabled so user can try
        try { btnStartScan.disabled = false; } catch (_) {}
    }
    btnResetScan.addEventListener('click', async () => {
        await stopScan();
        scanResultEmpty.classList.remove('d-none');
        scanResultEmpty.textContent = 'Belum ada hasil scan.';
        scanResultBox.classList.add('d-none');
        scannedCode.textContent = '';
        resultId.textContent = '';
        resultNama.textContent = '';
        resultHarga.textContent = '';
        resultStok.textContent = '';
    });

    setIdleState();
    // If opened as a popup (from POS), start scanning automatically so user doesn't need to press Start
    try {
        if (window.opener && !window.opener.closed) {
            // small timeout to allow UI to render and permissions prompt to show
            setTimeout(() => {
                try { btnStartScan.click(); } catch(_) { try { startScan(); } catch(__){} }
            }, 250);
        }
    } catch(e) { console.debug('auto start check failed', e); }
    // log available devices when user focuses the start button
    btnStartScan.addEventListener('mouseenter', async () => {
        try {
            const devices = await codeReader.listVideoInputDevices();
            console.log('[scanner] video devices:', devices);
        } catch (err) {
            console.warn('[scanner] listVideoInputDevices error', err);
        }
    });
    // image decode helper
    const uploadImage = document.getElementById('uploadImage');
    const btnDecodeImage = document.getElementById('btnDecodeImage');
    async function loadImageFromFile(file) {
        return new Promise((resolve, reject) => {
            const i = new Image();
            i.onload = () => resolve(i);
            i.onerror = reject;
            i.src = URL.createObjectURL(file);
        });
    }

    function createCanvasFromCrop(img, sx, sy, sw, sh, targetW = 1600) {
        const canvas = document.createElement('canvas');
        // request willReadFrequently for faster repeated readback
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        const aspect = sw / sh;
        canvas.width = targetW;
        canvas.height = Math.max(80, Math.floor(targetW / aspect));
        ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);
        return canvas;
    }

    function applyGrayscaleContrast(ctx, w, h, contrast = 1.4) {
        try {
            const imageData = ctx.getImageData(0,0,w,h);
            const data = imageData.data;
            for (let i=0;i<data.length;i+=4){
                const r = data[i], g = data[i+1], b = data[i+2];
                let v = 0.299*r + 0.587*g + 0.114*b;
                v = ((v - 128) * contrast) + 128;
                v = Math.max(0, Math.min(255, v));
                data[i]=data[i+1]=data[i+2]=v;
            }
            ctx.putImageData(imageData,0,0);
        } catch (e) {
            console.warn('applyGrayscaleContrast failed', e);
        }
    }

    // simple binary dilation (max filter) to thicken dark regions — helps with thin bars
    function applyDilation(ctx, w, h, iterations = 1) {
        try {
            let imageData = ctx.getImageData(0,0,w,h);
            let data = imageData.data;
            const copy = new Uint8ClampedArray(data);
            const get = (x,y,channel)=>{
                if (x<0||x>=w||y<0||y>=h) return 255;
                return copy[(y*w + x)*4 + channel];
            };
            for (let it=0; it<iterations; it++) {
                const out = new Uint8ClampedArray(copy.length);
                out.set(copy);
                for (let y=0;y<h;y++){
                    for (let x=0;x<w;x++){
                        const idx = (y*w + x)*4;
                        // assume grayscale in r channel
                        const neighbors = [get(x,y-1,0), get(x-1,y,0), get(x+1,y,0), get(x,y+1,0), get(x, y, 0)];
                        const minv = Math.min(...neighbors);
                        // for dilation on dark shapes we take min (darker)
                        const v = minv;
                        out[idx]=out[idx+1]=out[idx+2]=v;
                        out[idx+3]=255;
                    }
                }
                copy.set(out);
            }
            imageData.data.set(copy);
            ctx.putImageData(imageData,0,0);
        } catch (e) {
            console.debug('applyDilation failed', e);
        }
    }

    async function tryPreprocessAndDecode(imgFile) {
        const img = await loadImageFromFile(imgFile);
        const sw = img.width;
        const sh = img.height;
        // candidate crops: full, center 60%, center 40%, top 40%, bottom 40%
        const crops = [];
        crops.push([0,0,sw,sh]);
        const h60 = Math.floor(sh * 0.6);
        const h40 = Math.floor(sh * 0.4);
        crops.push([0, Math.floor((sh - h60)/2), sw, h60]);
        crops.push([0, Math.floor((sh - h40)/2), sw, h40]);
        crops.push([0, 0, sw, h40]);
        crops.push([0, sh - h40, sw, h40]);

        for (const [sx, sy, cw, ch] of crops) {
            try {
                const canvas = createCanvasFromCrop(img, sx, sy, cw, ch, 1600);
                const ctx = canvas.getContext('2d');
                applyGrayscaleContrast(ctx, canvas.width, canvas.height, 1.5);
                // try direct
                const dataUrl = canvas.toDataURL('image/png');
                console.debug('[scanner] trying decode crop]', sx, sy, cw, ch);
                try {
                    const res = await codeReader.decodeFromImage(undefined, dataUrl);
                    if (res && (res.getText || res.text)) return res;
                } catch (e) {
                    console.debug('[scanner] decode failed for crop, will try threshold', e && e.message, e);
                }
                // apply simple threshold and try again
                try {
                    const imageData = ctx.getImageData(0,0,canvas.width,canvas.height);
                    const data = imageData.data;
                    // Otsu-like simple thresholding
                    for (let i=0;i<data.length;i+=4){
                        const v = data[i];
                        const t = v > 140 ? 255 : 0;
                        data[i]=data[i+1]=data[i+2]=t;
                    }
                    ctx.putImageData(imageData,0,0);
                    const dataUrl2 = canvas.toDataURL('image/png');
                    try {
                        const res2 = await codeReader.decodeFromImage(undefined, dataUrl2);
                        if (res2 && (res2.getText || res2.text)) return res2;
                    } catch (e2) {
                        console.debug('[scanner] decode failed after threshold', e2 && e2.message, e2);
                    }
                } catch (thrErr) {
                    console.debug('thresholding failed', thrErr);
                }
            } catch (inner) {
                console.warn('preprocess step failed', inner);
            }
        }
        throw new Error('No MultiFormat Readers were able to detect the code.');
    }

    async function tryDecodeCanvas(canvas) {
        // similar to live preprocess: try direct, thresholds, invert, dilation
        const ctx = canvas.getContext('2d');
        const tryDecode = async (c) => {
            const d = c.toDataURL('image/png');
            try {
                const r = await codeReader.decodeFromImage(undefined, d);
                return r;
            } catch (err) { return null; }
        };

        // direct
        let r = await tryDecode(canvas);
        if (!r) {
            const thresholds = [140,120,160];
            for (const t of thresholds) {
                try {
                    const imageData = ctx.getImageData(0,0,canvas.width,canvas.height);
                    const data = imageData.data;
                    for (let i=0;i<data.length;i+=4){ const v=data[i]; const thr = v>t?255:0; data[i]=data[i+1]=data[i+2]=thr; }
                    ctx.putImageData(imageData,0,0);
                    applyDilation(ctx, canvas.width, canvas.height, 1);
                    r = await tryDecode(canvas);
                    if (r) break;
                    ctx.drawImage(readerElement, 0, 0, canvas.width, canvas.height);
                } catch(_){}
            }
        }
        if (!r) {
            try {
                const imageData = ctx.getImageData(0,0,canvas.width,canvas.height);
                const data = imageData.data;
                for (let i=0;i<data.length;i+=4){ data[i]=255-data[i]; data[i+1]=255-data[i+1]; data[i+2]=255-data[i+2]; }
                ctx.putImageData(imageData,0,0);
                r = await tryDecode(canvas);
                ctx.drawImage(readerElement, 0, 0, canvas.width, canvas.height);
            } catch(_){}
        }
        return r;
    }

    async function tryDecodeFromVideoFrame() {
        if (!readerElement || readerElement.readyState < 2) {
            return null;
        }

        const cw = readerElement.videoWidth;
        const ch = readerElement.videoHeight;
        if (!cw || !ch) {
            return null;
        }

        const c = document.createElement('canvas');
        const targetW = 1600;
        c.width = Math.min(targetW, cw);
        c.height = Math.max(80, Math.floor(c.width * (ch / cw)));
        const ctx = c.getContext('2d', { willReadFrequently: true });
        ctx.drawImage(readerElement, 0, 0, cw, ch, 0, 0, c.width, c.height);
        applyGrayscaleContrast(ctx, c.width, c.height, 1.5);

        // same decode helper as Capture & Decode
        return await tryDecodeCanvas(c);
    }

    async function processDecodedResult(res) {
        if (!res) {
            return false;
        }

        let kode = null;
        if (typeof res.getText === 'function') kode = res.getText();
        else if (res.text) kode = res.text;
        else kode = String(res);

        kode = (kode || '').trim();
        if (!kode) {
            return false;
        }

        scanLocked = true;
        try { await beep(); } catch(_){ }
        try { await stopScan(); } catch(_){ }

        const response = await fetch(apiBase + '/' + encodeURIComponent(kode), { headers: { 'Accept': 'application/json' } });
        const json = await response.json();
        if (!response.ok) {
            throw new Error(json.message || 'Barang tidak ditemukan');
        }
        showResult(json.data, kode);
        return true;
    }

    const btnCaptureDecode = document.getElementById('btnCaptureDecode');
    if (btnCaptureDecode) {
        btnCaptureDecode.addEventListener('click', async () => {
            try {
                if (!readerElement || !readerElement.videoWidth) {
                    alert('Video belum aktif atau tidak tersedia. Pastikan kamera sudah berjalan.');
                    return;
                }
                const cw = readerElement.videoWidth;
                const ch = readerElement.videoHeight;
                const c = document.createElement('canvas');
                c.width = Math.min(1600, cw);
                c.height = Math.max(80, Math.floor(c.height = Math.floor(c.width * (ch / cw))));
                const ctx = c.getContext('2d');
                ctx.drawImage(readerElement, 0, 0, cw, ch, 0, 0, c.width, c.height);
                applyGrayscaleContrast(ctx, c.width, c.height, 1.5);
                const res = await tryDecodeCanvas(c);
                if (res && (res.getText || res.text)) {
                    const kode = (typeof res.getText === 'function') ? res.getText().trim() : (res.text || '').trim();
                    const response = await fetch(apiBase + '/' + encodeURIComponent(kode), { headers: { 'Accept': 'application/json' } });
                    const json = await response.json();
                    if (!response.ok) throw new Error(json.message || 'Barang tidak ditemukan');
                    showResult(json.data, kode);
                } else {
                    alert('Gagal mendecode frame yang di-capture. Coba dekatkan kamera atau gunakan "Choose File".');
                }
            } catch (e) {
                console.error('capture decode failed', e);
                alert('Gagal capture/decode: ' + (e.message || e));
            }
        });
    }

    btnDecodeImage.addEventListener('click', async () => {
        if (!uploadImage.files || uploadImage.files.length === 0) {
            alert('Pilih file gambar barcode terlebih dulu.');
            return;
        }
        const file = uploadImage.files[0];
        try {
            console.log('[scanner] preprocessing image', file.name);
            const result = await tryPreprocessAndDecode(file);
            console.log('[scanner] image decode result', result);
            if (result && (result.getText || result.text)) {
                const kode = (typeof result.getText === 'function') ? result.getText().trim() : (result.text || '').trim();
                const response = await fetch(apiBase + '/' + encodeURIComponent(kode), { headers: { 'Accept': 'application/json' } });
                const json = await response.json();
                if (!response.ok) throw new Error(json.message || 'Barang tidak ditemukan');
                showResult(json.data, kode);
            } else {
                alert('Gagal mendecode gambar.');
            }
        } catch (e) {
            console.error('[scanner] decodeFromImage error', e);
            alert('Gagal decode gambar: ' + (e.message || e));
        }
    });
    })();
});
</script>
@endpush
