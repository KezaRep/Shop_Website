<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thanh toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f5f5f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .result-box { 
            background: white;
            text-align: center; 
            margin: 50px auto; 
            padding: 40px; 
            border-radius: 15px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.1); 
            max-width: 600px; 
        }
        .icon-status { font-size: 80px; margin-bottom: 20px; }
        .success { color: #2ecc71; }
        .failed { color: #e74c3c; }
        
        .order-details {
            text-align: left;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px dashed #ccc;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 15px;
        }
        .detail-row strong { color: #333; }
        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: #ee4d2d;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-home:hover { background: #d03e1e; transform: translateY(-2px); }
    </style>
</head>
<body>
    <?php include "View/Layout/Header.php"; ?>

    <div class="result-box">
        <?php if ($data['status'] == 'success'): ?>
            
            <div class="icon-status success"><i class="fas fa-check-circle"></i></div>
            <h2 style="color: #2ecc71; margin-top: 0;">THANH TOÁN THÀNH CÔNG!</h2>
            <p style="color: #666;">Cảm ơn bạn đã mua sắm tại Gitraell.</p>

            <div class="order-details">
                <div class="detail-row">
                    <span>Mã đơn hàng:</span>
                    <strong>#<?= htmlspecialchars($data['order_id']) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Tổng thanh toán:</span>
                    <strong style="color: #ee4d2d; font-size: 18px;"><?= number_format($data['amount']) ?> VNĐ</strong>
                </div>
                <hr style="border: 0; border-top: 1px solid #eee; margin: 15px 0;">
                
                <?php if (isset($data['info'])): ?>
                <div class="detail-row">
                    <span>Người nhận:</span>
                    <strong><?= htmlspecialchars($data['info']['customer_name']) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Số điện thoại:</span>
                    <strong><?= htmlspecialchars($data['info']['customer_phone']) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Giao đến:</span>
                    <strong><?= htmlspecialchars($data['info']['customer_address']) ?></strong>
                </div>
                <?php endif; ?>
                
                <div class="detail-row">
                    <span>Phương thức:</span>
                    <strong><i class="fas fa-credit-card"></i> VNPAY (Online)</strong>
                </div>
            </div>

        <?php elseif ($data['status'] == 'failed'): ?>
            
            <div class="icon-status failed"><i class="fas fa-times-circle"></i></div>
            <h2 style="color: #e74c3c;">GIAO DỊCH THẤT BẠI</h2>
            <p><?= htmlspecialchars($data['msg']) ?></p>
            <p style="color: #999; font-size: 14px;">Vui lòng kiểm tra lại số dư hoặc thử lại sau.</p>

        <?php else: ?>
            <div class="icon-status failed"><i class="fas fa-exclamation-triangle"></i></div>
            <h2>LỖI BẢO MẬT</h2>
            <p>Dữ liệu chữ ký không hợp lệ.</p>
        <?php endif; ?>

        <br>
        <a href="index.php" class="btn-home">
            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
        </a>
    </div>
</body>
</html>