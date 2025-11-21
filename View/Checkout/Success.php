<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$order = $_SESSION['last_order'] ?? null;
function currency($v){ return '₫' . number_format($v,0,',','.'); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Đặt hàng thành công</title>
  <link rel="stylesheet" href="/Shop_Website/Assets/Css/Checkout/checkout.css">
</head>
<body>
<main class="checkout-page">
  <div class="container">
    <div class="card">
      <?php if (!$order): ?>
        <h2 class="card-title">Không tìm thấy đơn hàng</h2>
        <p class="muted">Vui lòng kiểm tra lại.</p>
        <p><a class="btn btn-muted" href="index.php?controller=product&action=list">Quay lại mua sắm</a></p>
      <?php else: ?>
        <h2 class="card-title">Đặt hàng thành công</h2>
        <p>Mã đơn hàng: <strong><?= htmlspecialchars($order['id']) ?></strong></p>
        <p>Người nhận: <?= htmlspecialchars($order['name']) ?> — <?= htmlspecialchars($order['phone']) ?></p>
        <p>Địa chỉ: <?= nl2br(htmlspecialchars($order['address'])) ?></p>
        <h3 class="section-sub">Tóm tắt đơn hàng</h3>

        <ul class="items">
          <?php foreach ($order['items'] as $it):
            $qty = intval($it['quantity'] ?? 1);
            $price = floatval($it['price'] ?? 0);
          ?>
            <li class="item">
              <div class="left">
                <img src="<?= htmlspecialchars($it['image'] ?? '/Shop_Website/Assets/Images/placeholder-product-1.jpg') ?>" alt="">
                <div class="meta">
                  <div class="name"><?= htmlspecialchars($it['name'] ?? '') ?></div>
                  <div class="qty">x<?= $qty ?></div>
                </div>
              </div>
              <div class="right"><?= currency($qty * $price) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="summary-lines" style="margin-top:12px">
          <div class="line"><span>Tạm tính</span><span><?= currency($order['subtotal']) ?></span></div>
          <div class="line"><span>Phí vận chuyển</span><span><?= $order['shipping'] === 0 ? 'Miễn phí' : currency($order['shipping']) ?></span></div>
          <div class="line total"><span>Tổng</span><span><?= currency($order['total']) ?></span></div>
        </div>

        <div style="margin-top:14px;display:flex;gap:12px">
          <a class="btn btn-primary" href="index.php?controller=product&action=list">Tiếp tục mua sắm</a>
          <a class="btn btn-muted" href="index.php?controller=user&action=orders">Xem đơn hàng của tôi</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>
</body>
</html>