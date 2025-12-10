<?php
// Load ngôn ngữ
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}

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
                            <h3 class="section-title"><?= $lang['checkout_address_title'] ?></h3>
                            <a href="index.php?controller=user&action=address" class="btn-edit"><?= $lang['checkout_btn_edit'] ?></a>
                        </div>

                        <div class="address-content">
                            <?php if (!empty($deliveryAddress)): ?>
                                <div class="addr-info">
                                    <span class="addr-name"><?= htmlspecialchars($deliveryAddress['name']) ?></span>
                                    <span class="addr-phone"><?= htmlspecialchars($deliveryAddress['phone']) ?></span>
                                </div>
                                <div class="addr-text">
                                    <span class="badge-home">
                                        <?= htmlspecialchars($deliveryAddress['label'] == 'office' ? $lang['checkout_label_office'] : $lang['checkout_label_home']) ?>
                                    </span>
                                    <?= htmlspecialchars($deliveryAddress['address']) ?>
                                </div>

                                <div class="addr-warning">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span><?= $lang['checkout_addr_warning'] ?></span>
                                </div>

                                <input type="hidden" name="name" value="<?= htmlspecialchars($deliveryAddress['name']) ?>">
                                <input type="hidden" name="phone" value="<?= htmlspecialchars($deliveryAddress['phone']) ?>">
                                <input type="hidden" name="address" value="<?= htmlspecialchars($deliveryAddress['address']) ?>">
                            <?php else: ?>
                                <p><?= $lang['checkout_no_addr'] ?> <a href="index.php?controller=user&action=addAddress"><?= $lang['checkout_add_addr'] ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section-box package-section">
                        <div class="package-header">
                            <span class="package-title"><?= $lang['checkout_package_info'] ?></span>
                            <span class="delivery-source"><?= $lang['checkout_delivery_by'] ?> <strong>Shop Chính Hãng</strong></span>
                        </div>

                        <p class="opt-title"><?= $lang['checkout_delivery_opt'] ?></p>

                        <div class="delivery-card selected">
                            <div class="card-header">
                                <i class="fas fa-check-circle check-icon"></i>
                                <span class="shipping-price"><?= number_format($shippingFee, 0, ',', '.') ?> ₫</span>
                            </div>
                            <div class="card-body">
                                <div class="ship-method"><?= $lang['checkout_standard'] ?></div>
                                <div class="ship-date"><?= $lang['checkout_delivery_guarantee'] ?></div>
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
                                            <div class="item-meta">Size: L</div>
                                        </div>

                                        <div class="item-price-qty">
                                            <div class="item-price"><?= number_format($item['price'], 0, ',', '.') ?> ₫</div>
                                            <div class="item-qty-trash">
                                                <span class="qty-text">Qty: <?= $item['quantity'] ?></span>
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
                            <h3><?= $lang['checkout_payment_method'] ?></h3>
                        </div>
                        <div class="payment-options">
                            <label class="payment-method active" onclick="selectPayment('cod')">
                                <input type="radio" name="payment_method" value="cod" checked id="payment_cod">
                                <div class="method-icon"><i class="fa fa-money-bill-wave"></i></div>
                                <span class="method-name"><?= $lang['checkout_cod'] ?></span>
                            </label>

                            <label class="payment-method" onclick="selectPayment('vnpay')">
                                <input type="radio" name="payment_method" value="vnpay" id="payment_vnpay">
                                <div class="method-icon">
                                    <img src="https://vnpay.vn/assets/images/logo-icon/logo-primary.svg" alt="VNPAY" style="height: 20px;">
                                </div>
                                <span class="method-name" style="font-weight: bold; color: #005baa;"><?= $lang['checkout_vnpay'] ?></span>
                            </label>
                        </div>
                    </div>

                    <div class="section-box summary-section">
                        <h3><?= $lang['checkout_summary_title'] ?></h3>
                        <div class="summary-row">
                            <span><?= $lang['checkout_total_goods'] ?></span>
                            <span><?= number_format($totalPrice, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="summary-row">
                            <span><?= $lang['checkout_shipping_fee'] ?></span>
                            <span><?= number_format($shippingFee, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="summary-total">
                            <span><?= $lang['checkout_total_payment'] ?></span>
                            <span class="total-price"><?= number_format($grandTotal, 0, ',', '.') ?> ₫</span>
                        </div>
                        <div class="vat-note"><?= $lang['checkout_vat_note'] ?></div>

                        <button type="submit" class="btn-checkout"><?= $lang['checkout_btn_order'] ?></button>
                    </div>
                </div>
            </div>
            <input type="hidden" name="total_amount" value="<?= $grandTotal ?>">
        </form>
    </div>
</main>
<script>
    function selectPayment(method) {
        var form = document.getElementById('checkoutForm');

        // Xóa class active cũ
        var labels = document.querySelectorAll('.payment-method');
        labels.forEach(l => l.classList.remove('active'));

        if (method === 'vnpay') {
            document.getElementById('payment_vnpay').parentElement.classList.add('active');
            document.getElementById('payment_vnpay').checked = true;

            form.action = "index.php?controller=checkout&action=order";

        } else {
            document.getElementById('payment_cod').parentElement.classList.add('active');
            document.getElementById('payment_cod').checked = true;

            // Trả về mặc định
            form.action = "index.php?controller=checkout&action=order";
        }
    }
</script>