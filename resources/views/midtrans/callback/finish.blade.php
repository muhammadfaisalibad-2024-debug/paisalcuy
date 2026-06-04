<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - Kantin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            animation: slideIn 0.6s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 0.6s ease-out;
        }
        @keyframes bounce {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
            font-weight: 700;
        }
        .message {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .order-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: left;
        }
        .order-info p {
            margin-bottom: 8px;
            font-size: 14px;
        }
        .label {
            color: #666;
            font-weight: 500;
        }
        .value {
            color: #333;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        .btn-group {
            gap: 10px;
        }
        .btn {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(120deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .btn-outline-secondary:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        
        <h2>{{ $message }}</h2>
        
        <p class="message">
            Transaksi Anda telah berhasil diproses. Pesanan siap untuk diambil di kantin.
        </p>

        <div class="order-info">
            <p><span class="label">Order ID:</span> <br><span class="value">{{ $order_id }}</span></p>
            <p><span class="label">Transaction ID:</span> <br><span class="value">{{ $transaction_id }}</span></p>
            <p><span class="label">Status:</span> <br><span class="badge bg-success">SETTLEMENT</span></p>
        </div>

        <div class="btn-group d-flex">
            <a href="{{ route('kantin.customer') }}" class="btn btn-primary flex-grow-1">
                <i class="bi bi-arrow-left"></i> Pesan Lagi
            </a>
            <button class="btn btn-outline-secondary flex-grow-1" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
