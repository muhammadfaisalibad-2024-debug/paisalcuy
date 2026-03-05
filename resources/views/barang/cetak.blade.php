{{-- Template PDF murni — tidak extend layout --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<style>
  /*
   * Kertas TnJ No. 108 — A4 Portrait
   * margin: 13mm top/bottom, 7mm left/right
   * area: 196mm × 271mm  → 5 kol × 8 baris = 40 label
   * label: 38mm × 33mm  |  gap horizontal (border-spacing): 1mm
   *
   * PENTING: border-collapse:separate + border-spacing
   * agar setiap <td> tepat berada di posisi labelnya
   * tanpa kolom gap ekstra yang menggeser posisi.
   */
  @page { size: A4 portrait; margin: 13mm 7mm 13mm 7mm; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 8pt; }

  table.grid {
    width: 196mm;
    border-collapse: separate;
    border-spacing: 1mm 0;
    table-layout: fixed;
  }

  table.grid td {
    width: 38mm;
    height: 33mm;
    overflow: hidden;
  }

  td.lbl {
    border: 0.4pt dashed #999;
    text-align: center;
    vertical-align: middle;
    padding: 1.5mm 1mm;
  }

  td.lbl-empty {
    border: 0.4pt solid #ececec;
    background: #f7f7f7;
  }

  .nama  { font-weight: bold; font-size: 7.5pt; line-height: 1.25; word-wrap: break-word; }
  .harga { font-weight: bold; font-size: 10.5pt; color: #cc0000; margin: 0.8mm 0 0.3mm; }
  .satuan{ font-size: 6pt; color: #555; }
  .hr    { border: none; border-top: 0.4pt solid #ddd; margin: 0.5mm auto; width: 80%; }
  .id    { font-size: 4.8pt; color: #bbb; margin-top: 0.6mm; letter-spacing: 0.2pt; }
</style>
</head>
<body>
@foreach ($halaman as $pageIdx => $page)
  @php
    $offset = $page['offset'];
    $items  = $page['items'];
    $slots  = array_merge(array_fill(0, $offset, null), $items->all());
    while (count($slots) < 40) { $slots[] = null; }
    $rows = array_chunk($slots, 5);
  @endphp
  @if($pageIdx > 0)<pagebreak />@endif
  <table class="grid">
    @foreach ($rows as $row)
    <tr>
      @foreach ($row as $item)
        @if($item)
        <td class="lbl">
          <div class="nama">{{ $item->nama_barang }}</div>
          <hr class="hr">
          <div class="harga">Rp&nbsp;{{ number_format($item->harga, 0, ',', '.') }}</div>
          <div class="satuan">/ {{ $item->satuan }}</div>
          <hr class="hr">
          <div class="id">{{ $item->id_barang }}</div>
        </td>
        @else
        <td class="lbl-empty"></td>
        @endif
      @endforeach
    </tr>
    @endforeach
  </table>
@endforeach
</body>
</html>
