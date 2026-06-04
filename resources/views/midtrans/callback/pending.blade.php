<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pending - Kantin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .pending-container {
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
        .pending-icon {
            font-size: 60px;
            color: #ffc107;
            margin-bottom: 20px;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.7;
                transform: scale(1.05);
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
        .btn-warning {
            background: linear-gradient(120deg, #fa709a 0%, #fee140 100%);
            border: none;
            color: white;
        }
        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(250, 112, 154, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="pending-container">
        <div class="pending-icon">
            <i class="bi bi-hourglass-split"></i>
        </div>
        
        <h2>Pembayaran Belum Selesai</h2>
        
        <p class="message">
            {{ $message }}
        </p>

        <div class="order-info">
            <p><span class="label">Order ID:</span> <br><span class="value">{{ $order_id }}</span></p>
            <p><span class="label">Transaction ID:</span> <br><span class="value">{{ $transaction_id }}</span></p>
            <p><span class="label">Status:</span> <br><span class="badge bg-warning text-dark">PENDING</span></p>
        </div>

        <p class="text-muted small mb-3">Kami akan mengupdate status pesanan Anda secara otomatis.</p>

        <div class="btn-group d-flex">
            <a href="{{ route('kantin.customer') }}" class="btn btn-warning flex-grow-1">
                <i class="bi bi-house-door"></i> Kembali
            </a>
            <button class="btn btn-outline-secondary flex-grow-1" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
