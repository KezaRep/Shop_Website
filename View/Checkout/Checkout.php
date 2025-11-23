<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<main class="checkout-page">
    <div class="container">
        <h1 class="page-title">Thanh toán</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (empty($cart)): ?>
            <p>Giỏ hàng của bạn đang trống.</p>
        <?php else: ?>
            <div class="checkout-grid">
                <!-- Danh sách sản phẩm -->
                <div class="checkout-left">
                    <div class="cart-list">
                        <?php foreach ($cart as $item): 
                            $itemTotal = $item['price'] * $item['quantity'];
                        ?>
                        <div class="cart-item">
                            <img src="<?= $item['image'] ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img">
                            <div class="cart-item-info">
                                <span class="cart-item-name"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="cart-item-qty">Số lượng: <?= $item['quantity'] ?></span>
                                <span class="cart-item-price">₫<?= number_format($itemTotal,0,',','.') ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Form thanh toán -->
                <div class="checkout-right">
                    <div class="checkout-summary">
                        <h3>Tổng tiền</h3>
                        <div class="grand-total">₫<?= number_format($total,0,',','.') ?></div>
                    </div>

                    <form action="index.php?controller=cart&action=checkout" method="post" class="checkout-form">
                        <div class="form-group">
                            <label for="address">Địa chỉ nhận hàng</label>
                            <input type="text" id="address" name="address" placeholder="Nhập địa chỉ" required>
                        </div>

                        <div class="form-group">
                            <label for="payment_method">Phương thức thanh toán</label>
                            <select name="payment_method" id="payment_method" required>
                                <option value="balance">Thanh toán bằng số dư</option>
                                <option value="cod">Thanh toán khi nhận hàng (COD)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Thanh toán</button>
                        <a href="index.php?controller=cart&action=index" class="btn btn-cancel">Quay lại giỏ hàng</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>
<link rel="stylesheet" href="/Shop_Website/Assets/CSS/Cart/Checkout.css">
