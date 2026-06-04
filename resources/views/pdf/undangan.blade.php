{{-- UNDANGAN PELUNCURAN KOLEKSI BUKU - Portrait A4 - Koleksi Buku --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Serif',Georgia,serif; font-size:10.5pt; color:#222; background:#fff; width:210mm; }
.page { width:210mm; min-height:297mm; padding:0; }
/* HEADER */
.header { border-bottom:4px solid #1a3a6b; padding:5mm 15mm 4mm; background:#fff; }
.header-table { width:100%; border-collapse:collapse; }
.logo-cell { width:20mm; vertical-align:middle; }
.logo-box { width:18mm; height:18mm; line-height:18mm; background:#1a3a6b;
    color:#f5c842; font-size:14pt; font-weight:bold; text-align:center; border-radius:50%; }
.hdr-text-cell { vertical-align:middle; text-align:center; }
.inst-name { font-size:13pt; font-weight:bold; color:#1a3a6b; text-transform:uppercase; letter-spacing:1px; }
.faculty   { font-size:11pt; font-weight:bold; color:#1a3a6b; text-transform:uppercase; }
.address   { font-size:7.5pt; color:#555; margin-top:2px; }
.hdr-gold  { height:2px; background:#c9a227; margin-top:3mm; }
/* BODY */
.body-content { padding:6mm 20mm 8mm; }
.surat-info-table { width:auto; border-collapse:collapse; margin-bottom:5mm; }
.surat-info-table td { font-size:10pt; padding:0.5mm 0; vertical-align:top; }
.surat-info-table td:first-child { width:28mm; }
.surat-info-table td:nth-child(2){ width:5mm; }
.kepada { margin-bottom:4mm; font-size:10pt; line-height:1.7; }
.surat-title { text-align:center; margin:4mm 0; }
.surat-title h2 { font-size:14pt; font-weight:bold; text-transform:uppercase;
    text-decoration:underline; letter-spacing:2px; color:#1a3a6b; }
.surat-title p { font-size:9pt; color:#555; margin-top:1mm; }
.isi { line-height:1.75; margin-bottom:3mm; font-size:10.5pt; }
.indent { text-indent:10mm; }
.detail-box {
    border-left:4px solid #1a3a6b; background:#eef2ff;
    padding:3mm 5mm; margin:3mm 0 4mm;
}
.detail-box strong { color:#1a3a6b; }
.detail-table { width:100%; border-collapse:collapse; margin-top:2mm; }
.detail-table td { padding:1mm 0; font-size:10pt; vertical-align:top; }
.detail-table td:first-child { width:32mm; }
.detail-table td:nth-child(2){ width:6mm; }
/* Daftar buku */
.section-hdr { font-size:10pt; font-weight:bold; color:#1a3a6b; margin:4mm 0 2mm;
    border-left:3px solid #c9a227; padding-left:4px; text-transform:uppercase; letter-spacing:1px; }
table.buku { width:100%; border-collapse:collapse; font-size:8.5pt; }
table.buku th { background:#1a3a6b; color:#f5e6a3; padding:3px 5px; text-align:left; }
table.buku td { padding:3px 5px; border-bottom:1px solid #dde5f5; }
table.buku tr:nth-child(even) td { background:#f5f8ff; }
/* Kategori summary */
table.kat { width:100%; border-collapse:collapse; font-size:8.5pt; margin-top:2mm; }
table.kat th { background:#c9a227; color:#fff; padding:2px 5px; text-align:left; }
table.kat td { padding:2px 5px; border-bottom:1px solid #e8d99a; }
table.kat tr:nth-child(even) td { background:#fdf8ec; }
/* TTD */
.ttd-section { text-align:right; margin-top:5mm; }
.ttd-box { display:inline-block; width:65mm; text-align:center; font-size:10pt; }
.ttd-blank { height:14mm; }
.ttd-name { font-weight:bold; font-size:10.5pt; border-top:1px solid #333; padding-top:1mm; }
.ttd-title { font-size:9pt; color:#555; }
.tembusan { margin-top:5mm; font-size:9.5pt; }
.tembusan ol { margin-left:8mm; margin-top:1mm; line-height:1.8; }
/* FOOTER */
.footer { border-top:2px solid #1a3a6b; padding:2mm 15mm; background:#f8f9fa; }
.footer-text { font-size:7.5pt; color:#666; text-align:center; }
</style>
</head>
<body>
<div class="page">

  <div class="header">
    <table class="header-table">
      <tr>
        <td class="logo-cell"><div class="logo-box">KB</div></td>
        <td class="hdr-text-cell">
          <div class="inst-name">Perpustakaan Koleksi Buku Digital</div>
          <div class="faculty">Divisi Layanan &amp; Informasi</div>
          <div class="address">Jl. Akademika No. 1, Gedung Perpustakaan Lt. 2 &bull; Telp: (021) 5555-1234 &bull; perpus@koleksibuku.ac.id</div>
        </td>
      </tr>
    </table>
    <div class="hdr-gold"></div>
  </div>

  <div class="body-content">

    <table class="surat-info-table">
      <tr><td>Nomor</td><td>:</td><td>{{ $nomor }}</td></tr>
      <tr><td>Lampiran</td><td>:</td><td>1 (satu) lembar daftar koleksi</td></tr>
      <tr><td>Hal</td><td>:</td><td><strong>Undangan {{ $acara }}</strong></td></tr>
    </table>

    <div class="kepada">
      <div>Yth.</div>
      <div><strong>{{ $nama }}</strong></div>
      <div>di Tempat</div>
    </div>

    <div class="surat-title">
      <h2>Undangan</h2>
      <p>{{ $acara }}</p>
    </div>

    <div class="isi">
      <p class="indent">Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
      <p class="indent" style="margin-top:3mm;">
        Dengan hormat, kami dari <strong>Perpustakaan Koleksi Buku Digital</strong> mengundang
        Bapak/Ibu/Saudara/i untuk hadir dalam kegiatan yang akan kami selenggarakan berikut ini:
      </p>
    </div>

    <div class="detail-box">
      <strong>Detail Kegiatan:</strong>
      <table class="detail-table">
        <tr><td>Nama Kegiatan</td><td>:</td><td><strong>{{ $acara }}</strong></td></tr>
        <tr><td>Hari / Tanggal</td><td>:</td><td>{{ $hari }}, {{ $tanggal }}</td></tr>
        <tr><td>Waktu</td><td>:</td><td>{{ $waktu }} s.d. Selesai</td></tr>
        <tr><td>Tempat</td><td>:</td><td>{{ $tempat }}</td></tr>
        <tr><td>Dresscode</td><td>:</td><td>Pakaian Formal (Batik/Jas)</td></tr>
      </table>
    </div>

    <div class="section-hdr">Daftar Koleksi ({{ $totalBuku }} Buku &bull; {{ $totalKategori }} Kategori)</div>
    <table class="buku">
      <thead>
        <tr>
          <th style="width:5%">No</th>
          <th style="width:15%">Kode</th>
          <th style="width:37%">Judul Buku</th>
          <th style="width:25%">Pengarang</th>
          <th style="width:18%">Kategori</th>
        </tr>
      </thead>
      <tbody>
        @forelse($daftarBuku as $i => $buku)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $buku->kode }}</td>
          <td>{{ $buku->judul }}</td>
          <td>{{ $buku->pengarang }}</td>
          <td>{{ $buku->kategori->nama_kategori ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="5" style="text-align:center;color:#999;padding:5px">Belum ada data buku</td></tr>
        @endforelse
      </tbody>
    </table>

    @if($kategoriList->count())
    <div class="section-hdr" style="margin-top:4mm;">Ringkasan per Kategori</div>
    <table class="kat">
      <thead>
        <tr>
          <th style="width:60%">Kategori</th>
          <th style="width:40%">Jumlah Buku</th>
        </tr>
      </thead>
      <tbody>
        @foreach($kategoriList as $kat)
        <tr>
          <td>{{ $kat->nama_kategori }}</td>
          <td>{{ $kat->buku_count }} judul</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif

    <div class="isi" style="margin-top:4mm;">
      <p class="indent">
        Mengingat pentingnya acara ini, kami sangat mengharapkan kehadiran Bapak/Ibu/Saudara/i
        tepat pada waktunya. Konfirmasi kehadiran melalui email <strong>{{ $email }}</strong>
        paling lambat 3 hari sebelum pelaksanaan.
      </p>
      <p class="indent" style="margin-top:2mm;">
        Atas perhatian dan kehadiran Bapak/Ibu/Saudara/i, kami ucapkan terima kasih.
      </p>
    </div>

    <div class="isi" style="margin-top:3mm;">Wassalamu'alaikum Warahmatullahi Wabarakatuh,</div>

    <div class="ttd-section">
      <div class="ttd-box">
        <div>Jakarta, {{ $tanggal }}</div>
        <div>Kepala Perpustakaan,</div>
        <div class="ttd-blank"></div>
        <div class="ttd-name">Kepala Koleksi Buku</div>
        <div class="ttd-title">Perpustakaan Koleksi Buku Digital</div>
      </div>
    </div>

    <div class="tembusan">
      <em>Tembusan:</em>
      <ol>
        <li>Pimpinan Institusi</li>
        <li>Wakil Bidang Akademik</li>
        <li>Arsip</li>
      </ol>
    </div>

  </div>

  <div class="footer">
    <div class="footer-text">
      Diterbitkan secara digital oleh Sistem Koleksi Buku &bull; {{ $tanggal }} &bull; koleksibuku.ac.id
    </div>
  </div>

</div>
</body>
</html>
