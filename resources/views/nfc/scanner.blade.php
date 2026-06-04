@extends('layouts.app')

@section('title', 'NFC Scanner')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Web NFC Scanner</h4>
            <p class="text-muted">Gunakan Android Chrome (≥ v89). Klik tombol di bawah untuk mengaktifkan NFC, lalu dekatkan kartu ke belakang HP.</p>

            <div class="mb-3">
                <button id="btnStart" class="btn btn-primary">Aktifkan NFC</button>
                <button id="btnStop" class="btn btn-outline-secondary" disabled>Stop</button>
            </div>

            <div id="status" class="mb-3 text-muted">Status: belum aktif</div>

            <div id="resultBox" class="d-none">
                <h5>Hasil Scan</h5>
                <div><strong>Serial:</strong> <span id="serial"></span></div>
                <div><strong>Isi:</strong> <span id="payload"></span></div>
                <div class="mt-3">
                    <form id="registerForm" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" id="regSerial" name="serial" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="regName" name="owner_name" class="form-control" placeholder="Nama pemilik (opsional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success w-100">Daftarkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const btnStart = document.getElementById('btnStart');
    const btnStop = document.getElementById('btnStop');
    const statusEl = document.getElementById('status');
    const resultBox = document.getElementById('resultBox');
    const serialEl = document.getElementById('serial');
    const payloadEl = document.getElementById('payload');
    const regSerial = document.getElementById('regSerial');
    const regName = document.getElementById('regName');
    const registerForm = document.getElementById('registerForm');

    let ndef = null;
    let scanning = false;

    function beep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const o = ctx.createOscillator();
            const g = ctx.createGain();
            o.type = 'sine'; o.frequency.value = 880; g.gain.value = 0.06;
            o.connect(g); g.connect(ctx.destination); o.start();
            setTimeout(() => { o.stop(); ctx.close(); }, 140);
        } catch (e) {
            console.warn('Beep error', e);
        }
    }

    async function startScan() {
        if (!('NDEFReader' in window)) {
            statusEl.textContent = 'Browser tidak mendukung Web NFC API.';
            return;
        }

        try {
            ndef = new NDEFReader();
            await ndef.scan();
            scanning = true;
            statusEl.textContent = 'NFC aktif. Dekatkan kartu...';
            btnStart.disabled = true; btnStop.disabled = false;

            ndef.onreading = async (event) => {
                const { serialNumber, message } = event;
                let text = '';
                for (const record of message.records) {
                    if (record.recordType === 'text') {
                        const txt = new TextDecoder(record.encoding || 'utf-8').decode(record.data);
                        text += txt;
                    } else if (record.recordType === 'url') {
                        const url = new TextDecoder().decode(record.data);
                        text += url;
                    } else if (record.mediaType) {
                        try { text += new TextDecoder().decode(record.data); } catch(e) { text += '[binary data]'; }
                    }
                }

                // stop scanning after read
                try { await ndef.abort(); } catch (_) {}
                scanning = false;
                beep();

                serialEl.textContent = serialNumber;
                payloadEl.textContent = text || '(kosong)';
                regSerial.value = serialNumber;
                resultBox.classList.remove('d-none');
                statusEl.textContent = 'Tag terbaca.';

                // send to backend attendance record
                try {
                    const resp = await fetch('/nfc/attendance', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ serial: serialNumber, note: text })
                    });
                    const json = await resp.json();
                    console.log('Attendance response', json);
                } catch (e) {
                    console.error('Record attendance error', e);
                }
            };

        } catch (err) {
            console.error(err);
            statusEl.textContent = 'Error: ' + (err.message || err);
            btnStart.disabled = false; btnStop.disabled = true;
        }
    }

    async function stopScan() {
        if (ndef && scanning) {
            try { await ndef.abort(); } catch (e) {}
        }
        scanning = false;
        btnStart.disabled = false; btnStop.disabled = true;
        statusEl.textContent = 'Scan dihentikan.';
    }

    registerForm.addEventListener('submit', async (ev) => {
        ev.preventDefault();
        const payload = { serial: regSerial.value, owner_name: regName.value };
        try {
            const resp = await fetch('/nfc/cards/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });
            const json = await resp.json();
            if (json.success) {
                alert('Kartu berhasil didaftarkan.');
            } else {
                alert('Registrasi gagal.');
            }
        } catch (e) {
            console.error(e);
            alert('Gagal mendaftarkan kartu.');
        }
    });

    btnStart.addEventListener('click', startScan);
    btnStop.addEventListener('click', stopScan);
})();
</script>
@endpush
