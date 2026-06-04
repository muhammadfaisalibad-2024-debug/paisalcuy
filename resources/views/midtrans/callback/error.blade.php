<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesalahan Pembayaran - Kantin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
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
        .error-icon {
            font-size: 60px;
            color: #dc3545;
            margin-bottom: 20px;
            animation: shake 0.5s ease-out;
        }
        @keyframes shake {
            0%, 100% {
                transform: translateX(0);
            }
            25% {
                transform: translateX(-5px);
            }
            75% {
                transform: translateX(5px);
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
        .btn-danger {
            background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
            border: none;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(245, 87, 108, 0.3);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-exclamation-circle-fill"></i>
        </div>
        
        <h2>Pembayaran Gagal</h2>
        
        <p class="message">
            {{ $message }}
        </p>

        <div class="order-info">
            <p><span class="label">Order ID:</span> <br><span class="value">{{ $order_id }}</span></p>
            <p><span class="label">Transaction ID:</span> <br><span class="value">{{ $transaction_id }}</span></p>
            <p><span class="label">Status:</span> <br><span class="badge bg-danger">ERROR</span></p>
        </div>

        <div class="btn-group d-flex">
            <a href="{{ route('kantin.customer') }}" class="btn btn-danger flex-grow-1">
                <i class="bi bi-arrow-left"></i> Coba Lagi
            </a>
            <a href="javascript:history.back()" class="btn btn-outline-secondary flex-grow-1">
                <i class="bi bi-arrow-counterclockwise"></i> Kembali
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
