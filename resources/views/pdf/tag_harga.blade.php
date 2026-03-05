<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    /*
     * Kertas TnJ No. 108 — A4 Portrait
     * 5 kolom × 8 baris = 40 label per halaman
     * Label: ±38mm lebar × ±33.9mm tinggi
     * Margin atas: 13mm, kiri: 7mm
     */
    @page {
        size: A4 portrait;
        margin: 13mm 5mm 8mm 7mm;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 8pt;
    }

    .page {
        width: 198mm;   /* 210 - 7 - 5 */
    }

    .page-break {
        page-break-after: always;
    }

    table.grid {
        width: 198mm;
        border-collapse: collapse;
        table-layout: fixed;
    }

    /* 5 label cols + 4 gap cols */
    col.lbl  { width: 38mm; }
    col.gap  { width: 2mm; }

    tr.lbl-row { height: 33.9mm; }

    td.label-cell {
        width: 38mm;
        height: 33.9mm;
        border: 0.4pt solid #bbb;
        padding: 2mm 2mm;
        vertical-align: middle;
        text-align: center;
        overflow: hidden;
    }

    td.label-cell.empty {
        border: 0.4pt dashed #ddd;
    }

    td.gap-col {
        width: 2mm;
        border: none;
        padding: 0;
    }

    .lbl-nama {
        font-size: 6.5pt;
        font-weight: bold;
        line-height: 1.25;
        margin-bottom: 1mm;
        word-wrap: break-word;
    }

    .lbl-harga {
        font-size: 10pt;
        font-weight: bold;
        color: #c0392b;
        line-height: 1.1;
        margin-bottom: 0.5mm;
    }

    .lbl-satuan {
        font-size: 5.5pt;
        color: #555;
        margin-bottom: 0.5mm;
    }

    .lbl-divider {
        border: none;
        border-top: 0.4pt solid #ccc;
        margin: 0.8mm auto;
        width: 80%;
    }

    .lbl-id {
        font-size: 4.5pt;
        color: #999;
        letter-spacing: 0.3pt;
    }
</style>
</head>
<body>
@foreach($pages as $pageIdx => $page)
    @php
        $rows = array_chunk($page, 5);
    @endphp

    <div class="{{ $pageIdx < count($pages) - 1 ? 'page page-break' : 'page' }}">
        <table class="grid">
            <colgroup>
                <col class="lbl"><col class="gap">
                <col class="lbl"><col class="gap">
                <col class="lbl"><col class="gap">
                <col class="lbl"><col class="gap">
                <col class="lbl">
            </colgroup>

            @foreach($rows as $row)
            <tr class="lbl-row">
                @foreach($row as $colIdx => $barang)
                    @if($barang)
                    <td class="label-cell">
                        <div class="lbl-nama">{{ $barang->nama_barang }}</div>
                        <hr class="lbl-divider">
                        <div class="lbl-harga">Rp {{ number_format($barang->harga, 0, ',', '.') }}</div>
                        <div class="lbl-satuan">/ {{ $barang->satuan }}</div>
                        <hr class="lbl-divider">
                        <div class="lbl-id">{{ $barang->id_barang }}</div>
                    </td>
                    @else
                    <td class="label-cell empty"></td>
                    @endif

                    @if($colIdx < 4)
                    <td class="gap-col"></td>
                    @endif
                @endforeach
            </tr>
            @endforeach
        </table>
    </div>
@endforeach
</body>
</html>
