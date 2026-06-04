<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label {{ $barang->id_barang }}</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif}
        .label{width:320px;padding:10px;border:1px solid #ddd;text-align:center}
        img{max-width:100%;height:auto}
        .qr-wrap{width:240px;height:240px;margin:8px auto 6px auto;display:flex;align-items:center;justify-content:center}
    </style>
</head>
<body>
    <div class="label">
        <h4>{{ $barang->nama_barang }}</h4>
        @if(!empty($qrCodeBase64))
            <div class="qr-wrap">
                <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="width:240px;height:240px;">
            </div>
        @elseif(!empty($barcodeBase64))
            <img src="{{ $barcodeBase64 }}" alt="barcode">
        @elseif(!empty($barcodeSvg))
            <div class="barcode-svg">{!! $barcodeSvg !!}</div>
        @elseif(!empty($barcodeHtml))
            <div class="barcode-html">{!! $barcodeHtml !!}</div>
        @endif
        <div>{{ $barang->id_barang }}</div>
    </div>
</body>
</html>
