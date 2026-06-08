<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Pesanan #{{ $pesanan->idpesanan }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: radial-gradient(circle at top right, #f4f8ff, transparent 30%),
                        radial-gradient(circle at bottom left, #effcf8, transparent 34%),
                        #f7fafc;
            min-height: 100vh;
        }
        .shell { max-width: 920px; }
        .hero {
            background: linear-gradient(135deg, #0f6d8c, #14a58f);
            color: #fff;
            border-radius: 20px;
            box-shadow: 0 18px 35px rgba(17, 71, 99, .22);
        }
        .card-soft {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(21, 54, 82, .08);
        }
    </style>
</head>
<body>
<div class="container shell py-4">
    <div class="hero p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <h2 class="mb-1">QR Pesanan #{{ $pesanan->idpesanan }}</h2>
                <div>QR ini bisa dibuka ulang kapan saja selama pesanan tersimpan di sistem.</div>
            </div>
            <span class="badge bg-light text-dark fs-6 px-3 py-2">{{ (int) $pesanan->status_bayar === 1 ? 'LUNAS' : 'BELUM LUNAS' }}</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-soft h-100">
                <div class="card-body text-center">
                    <h5 class="card-title mb-3">QR Code</h5>
                    @if($qrCodeUrl)
                        <img src="{{ asset('storage/qrcodes/pesanan-8.png') }}"
                            alt="QR Pesanan"
                            class="img-fluid mb-3"
                            style="max-width: 280px;">

                        <div class="small text-muted mb-3">Scan QR ini di halaman vendor untuk melihat detail pesanan.</div>
                        <a href="{{ $qrCodeUrl }}" class="btn btn-dark" target="_blank" rel="noopener">Buka Gambar QR</a>
                    @else
                        <div class="alert alert-warning mb-0">QR code belum tersedia. Pastikan pembayaran sudah lunas agar QR dibuat otomatis.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-soft h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detail Pesanan</h5>
                    <div class="mb-3">
                        <div class="text-muted small">Customer</div>
                        <div class="fw-bold">{{ $pesanan->nama }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Status Pembayaran</div>
                        <div class="fw-bold">{{ (int) $pesanan->status_bayar === 1 ? 'Lunas' : 'Belum Lunas' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small">Total</div>
                        <div class="fw-bold">Rp {{ number_format($pesanan->total, 0, ',', '.') }}</div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th width="80">Qty</th>
                                    <th width="130">Harga</th>
                                    <th width="140">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pesanan->detailPesanan as $detail)
                                    <tr>
                                        <td>{{ $detail->menu->nama_menu ?? '-' }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada detail pesanan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('kantin.customer') }}" class="btn btn-outline-primary">Kembali ke Pemesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
