<!doctype html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <title>Camera Capture</title>
    <style>video{border:1px solid #ccc;width:320px;height:auto;}</style>
</head>
<body>
    <h3>Ambil Foto</h3>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display:none"></canvas>
    <div>
        <button id="btnCapture">Ambil Foto</button>
        <button id="btnStop">Stop Kamera</button>
    </div>
    <div id="result"></div>

    <script>
    const video = document.getElementById('video');
    let streamRef;

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            streamRef = stream;
            video.srcObject = stream;
        } catch (e) {
            document.getElementById('result').innerText = 'Gagal mengakses kamera: ' + e.message;
        }
    }

    document.getElementById('btnCapture').addEventListener('click', async () => {
        const canvas = document.getElementById('canvas');
        canvas.width = video.videoWidth || 320;
        canvas.height = video.videoHeight || 240;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const dataUrl = canvas.toDataURL('image/png');

        document.getElementById('result').innerText = 'Mengunggah...';

        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const res = await fetch('/kantin/api/photo/upload', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ image: dataUrl })
        });

        const json = await res.json();
        if (res.ok) {
            document.getElementById('result').innerHTML = 'Foto tersimpan: <a href="' + json.url + '" target="_blank">' + json.path + '</a>';
        } else {
            document.getElementById('result').innerText = json.message || 'Upload gagal';
        }
    });

    document.getElementById('btnStop').addEventListener('click', () => {
        if (streamRef) {
            streamRef.getTracks().forEach(t => t.stop());
            video.srcObject = null;
        }
    });

    startCamera();
    </script>
</body>
</html>
