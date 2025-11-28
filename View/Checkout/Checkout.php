<?php
// 1. Hàm xử lý ảnh sản phẩm
function checkProductImg($img)
{
    if (empty($img)) return 'Assets/Images/placeholder-product-1.jpg';
    if (@getimagesizefromstring($img)) return 'data:image/jpeg;base64,' . base64_encode($img);
    return $img;
}

// 2. Tính toán tổng tiền để hiển thị
$totalPrice = 0;
if (isset($cart) && is_array($cart)) {
    foreach ($cart as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }
} else {
    $cart = [];
}

// 3. Phí ship & Tổng
$shippingFee = 30000;
$grandTotal = $totalPrice + $shippingFee;
?>

<?php require_once './View/Layout/Header.php'; ?>

<link rel="stylesheet" href="./Assets/CSS/Cart/Checkout.css">

<main class="checkout-page-lazada">
    <div class="container">

        <form action="index.php?controller=checkout&action=order" method="post" id="checkoutForm">
            <div class="checkout-layout">

                <div class="col-left">

                    <div class="section-box address-section">
                        <div class="section-header">
                            <h3 class="section-title">Địa chỉ giao hàng</h3>
                            <a href="index.php?controller=user&action=address" class="btn-edit">Chỉnh sửa</a>
                        </div>

                        <div class="address-content">
                            <?php if (!empty($deliveryAddress)): ?>
                                <div class="addr-info">
                                    <span class="addr-name"><?= htmlspecialchars($deliveryAddress['name']) ?></span>
                                    <span class="addr-phone"><?= htmlspecialchars($deliveryAddress['phone']) ?></span>
                                </div>
                                <div class="addr-text">
                                    <span class="badge-home"><?= htmlspecialchars($deliveryAddress['label'] == 'office' ? 'Cơ quan' : 'Nhà riêng') ?></span>
                                    <?= htmlspecialchars($deliveryAddress['address']) ?>
                                </div>

                                <div class="addr-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>Địa chỉ của bạn đã được tự động cập nhật theo hệ thống địa chỉ mới. Vui lòng kiểm tra lại trước khi đặt đơn</span>
                                </div>

                                <input type="hidden" name="name" value="<?= htmlspecialchars($deliveryAddress['name']) ?>">
                                <input type="hidden" name="phone" value="<?= htmlspecialchars($deliveryAddress['phone']) ?>">
                                <input type="hidden" name="address" value="<?= htmlspecialchars($deliveryAddress['address']) ?>">
                            <?php else: ?>
                                <p>Chưa có địa chỉ. <a href="index.php?controller=user&action=addAddress">Thêm ngay</a></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section-box package-section">
                        <div class="package-header">
                            <span class="package-title">Kiện 1 trong số 1</span>
                            <span class="delivery-source">Được giao bởi <strong>Shop Chính Hãng</strong></span>
                        </div>

                        <p class="opt-title">Chọn tùy chọn giao hàng</p>

                        <div class="delivery-card selected">
                            <div class="card-header">
                                <i class="fas fa-check-circle check-icon"></i>
                                <span class="shipping-price"><?= number_format($shippingFee, 0, ',', '.') ?> ₫</span>
                            </div>
                            <div class="card-body">
                                <div class="ship-method">Giao tiêu chuẩn</div>
                                <div class="ship-date">Đảm bảo nhận vào 29 thg 11 - 8 thg 12. Nhận 15.000₫ LazRewards nếu đơn hàng giao trễ.</div>
                            </div>
                        </div>

                        <div class="product-list-wrapper">
                            <?php if (!empty($cart)): ?>
                                <?php foreach ($cart as $item): ?>
                                    <?php $imgSrc = checkProductImg($item['image'] ?? ''); ?>
                                    <div class="item-row">

                                    <input type="hidden" name="selected_items[]" value="<?= $item['cart_id'] ?>">
                                    
                                        <div class="item-img">
                                            <img src="<?= $imgSrc ?>" alt="Product">
                                        </div>

                                        <div class="item-info">
                                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                                            <div class="item-meta">Màu sắc: Mặc định, Size: L</div>
                                        </div>

                                        <div class="item-price-qty">
                                            <div class="item-price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</div>
                                            <div class="item-qty-trash">
                                                <span class="qty-text">Số lượng: <?= $item['quantity'] ?></span>
                                                <a href="index.php?controller=cart&action=delete&id=<?= $item['cart_id'] ?>" class="trash-btn">
                                                    <i class="far fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-right">

                    <div class="section-box payment-section">
                        <div class="section-header">
                            <h3>Phương thức thanh toán</h3>
                        </div>
                        <div class="payment-options">
                            <label class="payment-method active">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div class="method-icon"><i class="fa fa-money-bill-wave"></i></div>
                                <span class="method-name">Thanh toán khi nhận hàng (COD)</span>
                            </label>
                        </div>
                    </div>

                    <div class="section-box summary-section">
                        <h3>Chi tiết thanh toán</h3>
                        <div class="summary-row">
                            <span>Tổng tiền hàng</span>
                            <span><?= number_format($totalPrice, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <span><?= number_format($shippingFee, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="summary-total">
                            <span>Tổng thanh toán:</span>
                            <span class="total-price"><?= number_format($grandTotal, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="vat-note">(Đã bao gồm VAT)</div>

                        <button type="submit" class="btn-checkout">ĐẶT HÀNG</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

