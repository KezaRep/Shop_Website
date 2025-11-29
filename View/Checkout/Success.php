<?php
require_once './View/Layout/Header.php';
?>

<link rel="stylesheet" href="./Assets/CSS/Checkout/Success.css">

<main class="checkout-success-page">
  <div class="container">
    <div class="card success-card">
      <?php if (empty($order)): ?>
        <div class="empty-state">
          <i class="fas fa-search error-icon"></i>
          <h2 class="card-title">Không tìm thấy đơn hàng</h2>
          <p class="muted">Có vẻ như mã đơn hàng không tồn tại hoặc đã bị xóa.</p>
          <a class="btn btn-primary" href="index.php">Quay lại trang chủ</a>
        </div>
      <?php else: ?>

        <div class="success-header">
          <div class="icon-box">
            <i class="fas fa-check-circle"></i>
          </div>
          <h2 class="card-title">Đặt hàng thành công!</h2>
          <p class="sub-text">Cảm ơn bạn đã mua sắm. Đơn hàng của bạn đang được xử lý.</p>
        </div>

        <div class="order-info">
          <div class="row">
            <span class="label">Mã đơn hàng:</span>
            <span class="value code">#<?= htmlspecialchars($order['id']) ?></span>
          </div>
          <div class="row">
            <span class="label">Người nhận:</span>
            <span class="value"><?= htmlspecialchars($order['recipient_name']) ?></span>
          </div>
          <div class="row">
            <span class="label">Số điện thoại:</span>
            <span class="value"><?= htmlspecialchars($order['recipient_phone']) ?></span>
          </div>
          <div class="row">
            <span class="label">Địa chỉ:</span>
            <span class="value"><?= htmlspecialchars($order['recipient_address']) ?></span>
          </div>
        </div>

        <h3 class="section-sub">Chi tiết đơn hàng</h3>

        <ul class="items-list">
          <?php foreach ($order_details as $item): ?>
            <li class="item">
              <div class="item-left">
                <div class="img-box">
                  <div class="img-box">
                    <?php
                    $imgSrc = !empty($item['image']) ? $item['image'] : 'Assets/Images/placeholder-product-1.jpg';
                    ?>

                    <img src="<?= htmlspecialchars($imgSrc) ?>"
                      alt="Product"
                      onerror="this.src='Assets/Images/placeholder-product-1.jpg'">
                  </div>
                </div>

                <div class="item-meta">
                  <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
                  <div class="item-qty">Số lượng: <?= $item['quantity'] ?></div>
                </div>
              </div>
              <div class="item-right">
                <span class="price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</span>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>

        <div class="total-section">
          <span class="label">Tổng thanh toán:</span>
          <span class="total-price"><?= number_format($order['total_money'], 0, ',', '.') ?> ₫</span>
        </div>

        <div class="actions">
          <a href="index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
          <a href="index.php?controller=user&action=history" class="btn btn-primary">Xem lịch sử đơn hàng</a>
        </div>

      <?php endif; ?>
    </div>
  </div>
</main>

<?php
// 3. Nhúng Footer
require_once './View/Layout/Footer.php';
?>