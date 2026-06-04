<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cetak Barcode</title>
  <style>
    @page { size: A4 portrait; margin: 10mm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12pt; }
    .label { width: 100%; margin-bottom: 8mm; text-align: center; }
    .qr { display: inline-block; width: 60mm; height: 60mm; margin-bottom: 4mm; }
    .kode { font-size: 10pt; color: #333; margin-top: 2mm; }
    .row { display: flex; justify-content: space-around; align-items: center; margin-bottom: 6mm; }
  </style>
</head>
<body>
@foreach(array_chunk($items->all(), 2) as $pair)
  <div class="row">
    @foreach($pair as $it)
      <div class="label">
        @if(!empty($it->qrCodeBase64))
          <div class="qr"><img src="{{ $it->qrCodeBase64 }}" style="width:100%;height:100%;" /></div>
        @elseif(!empty($it->barcodeBase64))
          <div class="qr"><img src="{{ $it->barcodeBase64 }}" style="width:100%;height:100%;object-fit:contain;" /></div>
        @else
          <div class="qr" style="background:#eee;">&nbsp;</div>
        @endif
        <div class="kode">{{ $it->id_barang }}</div>
      </div>
    @endforeach
  </div>
@endforeach
</body>
</html>
