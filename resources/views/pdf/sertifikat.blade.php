{{-- SERTIFIKAT KEANGGOTAAN PERPUSTAKAAN - Landscape A4 - Koleksi Buku --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Serif',Georgia,serif; color:#1a1a2e; background:#fff; width:297mm; }
.page { width:100%; min-height:210mm; padding:10mm 14mm; }
.outer-border {
    border:5px double #8B6914; width:100%; min-height:186mm;
    padding:8mm 10mm; position:relative;
}
.outer-border::before {
    content:''; position:absolute; top:4px; left:4px; right:4px; bottom:4px;
    border:1.5px solid #c9a227;
}
.corner { position:absolute; font-size:20pt; color:#8B6914; line-height:1; }
.corner.tl { top:5px; left:8px; }
.corner.tr { top:5px; right:8px; }
.corner.bl { bottom:5px; left:8px; }
.corner.br { bottom:5px; right:8px; }
.watermark {
    position:absolute; top:50%; left:50%;
    transform:translate(-50%,-50%) rotate(-30deg);
    font-size:55pt; color:rgba(200,162,39,0.06); font-weight:bold; white-space:nowrap;
}
.content { position:relative; }
.header { text-align:center; border-bottom:2px solid #c9a227; padding-bottom:4mm; margin-bottom:4mm; }
.inst-name { font-size:14pt; font-weight:bold; letter-spacing:2px; text-transform:uppercase; color:#1a1a2e; }
.inst-sub  { font-size:8pt; color:#666; margin-top:2px; }
.title-main { font-size:24pt; font-weight:bold; color:#8B6914; letter-spacing:4px; text-transform:uppercase; text-align:center; }
.title-sub  { font-size:9pt; color:#555; letter-spacing:5px; text-transform:uppercase; text-align:center; margin-top:2px; }
.nomor-sert { text-align:center; font-size:8pt; color:#666; margin:3mm 0; }
.award-text { text-align:center; font-size:10pt; margin-bottom:2mm; color:#444; }
.award-name-wrap { text-align:center; margin-bottom:2mm; }
.award-name { font-size:20pt; font-weight:bold; color:#1a1a2e; font-style:italic;
    border-bottom:2px solid #8B6914; display:inline-block; padding:0 15mm 2px; }
.award-email { text-align:center; font-size:8.5pt; color:#777; margin-bottom:3mm; }
.award-desc  { text-align:center; font-size:9pt; color:#555; margin-bottom:4mm; }
hr.divider   { border:none; border-top:1px solid #c9a227; margin:3mm 0; }
.stats { width:100%; border-collapse:collapse; }
.stats td { text-align:center; padding:2mm 10mm; }
.stat-num { font-size:28pt; font-weight:bold; color:#8B6914; line-height:1; }
.stat-lbl { font-size:8pt; color:#555; text-transform:uppercase; }
.stat-sep { border-right:1px solid #c9a227; }
.section-title { font-size:9pt; font-weight:bold; color:#8B6914; text-transform:uppercase;
    margin:3mm 0 2mm; border-left:3px solid #8B6914; padding-left:4px; }
table.buku { width:100%; border-collapse:collapse; font-size:8pt; }
table.buku th { background:#1a1a2e; color:#f5e6a3; padding:3px 6px; text-align:left; }
table.buku td { padding:3px 6px; border-bottom:1px solid #e8d99a; }
table.buku tr:nth-child(even) td { background:#fdf8ec; }
.footer-table { width:100%; border-collapse:collapse; margin-top:4mm; }
.footer-table td { vertical-align:bottom; font-size:8pt; }
.sign-line { border-top:1px solid #333; width:48mm; margin-bottom:1px; }
.gen-date { font-size:8pt; color:#777; }
</style>
</head>
<body>
<div class="page">
<div class="outer-border">
  <span class="corner tl">&#10022;</span>
  <span class="corner tr">&#10022;</span>
  <span class="corner bl">&#10022;</span>
  <span class="corner br">&#10022;</span>
  <div class="watermark">KOLEKSI BUKU</div>
  <div class="content">

    <div class="header">
      <div class="inst-name">Perpustakaan Koleksi Buku Digital</div>
      <div class="inst-sub">Jl. Akademika No. 1 &bull; perpus@koleksibuku.ac.id &bull; koleksibuku.ac.id</div>
    </div>

    <div class="title-main">Sertifikat Keanggotaan</div>
    <div class="title-sub">Certificate of Library Membership</div>
    <div class="nomor-sert">Nomor: {{ $nomor }}</div>

    <div class="award-text">Diberikan kepada</div>
    <div class="award-name-wrap">
      <span class="award-name">{{ $nama }}</span>
    </div>
    <div class="award-email">{{ $email }}</div>
    <div class="award-desc">
      Sebagai anggota aktif yang telah mengakses dan memanfaatkan koleksi perpustakaan digital.
      Sertifikat ini diterbitkan sebagai bentuk apresiasi atas dedikasi dalam memanfaatkan
      teknologi informasi untuk pengembangan ilmu pengetahuan.
    </div>

    <hr class="divider">
    <table class="stats">
      <tr>
        <td class="stat-sep">
          <div class="stat-num">{{ $totalBuku }}</div>
          <div class="stat-lbl">Total Koleksi Buku</div>
        </td>
        <td>
          <div class="stat-num">{{ $totalKategori }}</div>
          <div class="stat-lbl">Total Kategori</div>
        </td>
      </tr>
    </table>
    <hr class="divider">

    <div class="section-title">&#9654; 5 Koleksi Buku Terbaru</div>
    <table class="buku">
      <thead>
        <tr>
          <th style="width:5%">No</th>
          <th style="width:15%">Kode</th>
          <th style="width:40%">Judul Buku</th>
          <th style="width:25%">Pengarang</th>
          <th style="width:15%">Kategori</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bukuTerbaru as $i => $buku)
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

    <table class="footer-table">
      <tr>
        <td style="width:60%">
          <div class="gen-date">Diterbitkan: {{ $tanggal }}</div>
          <div class="gen-date">Dokumen diterbitkan secara digital oleh Sistem Koleksi Buku.</div>
        </td>
        <td style="width:40%; text-align:center;">
          <div style="margin-bottom:10mm;">&nbsp;</div>
          <div class="sign-line"></div>
          <div style="font-size:8.5pt; font-weight:bold;">Kepala Perpustakaan</div>
          <div style="font-size:7.5pt; color:#888;">Koleksi Buku Digital</div>
        </td>
      </tr>
    </table>

  </div>
</div>
</div>
</body>
</html>
