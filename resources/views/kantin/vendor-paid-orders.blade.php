@extends('layouts.app')

@section('title', 'Vendor - Pesanan Lunas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-success text-white me-2">
                    <i class="mdi mdi-cash-check"></i>
                </span> Vendor - Pesanan Status Lunas
            </h3>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <h4 class="card-title mb-0">Riwayat Pesanan Lunas</h4>
                    <a href="{{ route('kantin.vendor.scan-qr') }}" class="btn btn-success btn-sm">
                        <i class="mdi mdi-qrcode-scan"></i> Buka Scanner QR
                    </a>
                </div>
                @forelse($orders as $order)
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <strong>Pesanan #{{ $order->idpesanan }}</strong> - {{ $order->nama }}
                                <div class="text-muted small">{{ $order->timestamp }}</div>
                            </div>
                            <div>
                                <span class="badge badge-success">LUNAS</span>
                                <span class="ms-2 fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Menu</th>
                                        <th>Qty</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($details[$order->idpesanan] ?? collect()) as $detail)
                                    <tr>
                                        <td>{{ $detail->nama_vendor }}</td>
                                        <td>{{ $detail->nama_menu }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                        <td>Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                        <td>{{ $detail->catatan ?: '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Tidak ada detail item.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Belum ada pesanan dengan status lunas.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
