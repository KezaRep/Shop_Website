<?php

if (session_status() === PHP_SESSION_NONE) session_start();

function productImageSrc($img)
{
    if (empty($img)) return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
    if (@getimagesizefromstring($img)) return 'data:image/jpeg;base64,' . base64_encode($img);
    return $img;
}

$user = $_SESSION['user'] ?? [];
$profileName = $user['fullname'] ?? $user['name'] ?? $user['username'] ?? '';
$profilePhone = $user['phone'] ?? '';
$profileAddress = $user['address'] ?? '';

if (!isset($cart)) {
    $cart = $_SESSION['cart'] ?? [];
    if (empty($cart) && !empty($_GET['product_id'])) {
        include_once __DIR__ . '/../../Model/Product/ProductModel.php';
        $pm = new ProductModel();
        $p = $pm->getProductById((int)$_GET['product_id']);
        if ($p) {
            $qty = max(1, (int)($_GET['qty'] ?? 1));
            $cart = [[
                'product_id' => intval($p->id),
                'name' => $p->name ?? 'Sản phẩm',
                'price' => floatval($p->price ?? 0),
                'quantity' => $qty,
                'image' => !empty($p->image) ? productImageSrc($p->image) : '/Shop_Website/Assets/Images/placeholder-product-1.jpg'
            ]];
        }
    }
}

function currency($v)
{
    return '₫' . number_format($v, 0, ',', '.');
}

// totals
$subtotal = 0;
foreach ($cart as $it) $subtotal += intval($it['quantity'] ?? 1) * floatval($it['price'] ?? 0);
$shipping = ($subtotal > 500000) ? 0 : 30000;
$discount = 0;
$total = $subtotal + $shipping - $discount;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Thanh toán</title>
    <link rel="stylesheet" href="/Shop_Website/Assets/Css/Checkout/Checkout.css">
</head>

<body>
    <main class="checkout-page">
        <div class="container">
            <h1 class="page-title">Thanh toán</h1>

            <div class="checkout-grid">
                <form id="checkoutForm" class="checkout-form" action="index.php?controller=product&action=submitOrder" method="post">
                    <section class="card">
                        <h2 class="card-title">Thông tin người nhận</h2>

                        <div class="recipient-mode">
                            <label><input type="radio" name="recipient_mode" value="profile" checked> Dùng thông tin tài khoản</label>
                            <label><input type="radio" name="recipient_mode" value="saved"> Chọn địa chỉ đã lưu</label>
                            <label><input type="radio" name="recipient_mode" value="new"> Nhập thông tin mới</label>
                        </div>

                        <div id="savedAddressWrap" class="saved-wrap" style="display:none">
                            <label class="field">
                                <span class="label-title">Địa chỉ đã lưu</span>
                                <select id="savedAddressSelect">
                                    <option value="">-- Chọn địa chỉ --</option>
                                    <?php if (!empty($savedAddresses)): ?>
                                        <?php foreach ($savedAddresses as $addr): ?>
                                            <option
                                                value="<?= $addr['id'] ?>"
                                                data-name="<?= htmlspecialchars($addr['name']) ?>"
                                                data-phone="<?= htmlspecialchars($addr['phone']) ?>"
                                                data-address="<?= htmlspecialchars($addr['address']) ?>"
                                                data-label="<?= htmlspecialchars($addr['label']) ?>">
                                                <?= htmlspecialchars($addr['label']) ?> - <?= htmlspecialchars($addr['address']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </select>
                            </label>
                        </div>

                        <label class="field">
                            <span class="label-title">Họ & tên</span>
                            <input type="text" id="recName" name="name" value="<?= htmlspecialchars($profileName) ?>" required>
                        </label>

                        <label class="field">
                            <span class="label-title">Số điện thoại</span>
                            <input type="tel" id="recPhone" name="phone" value="<?= htmlspecialchars($profilePhone) ?>" required>
                        </label>

                        <label class="field">
                            <span class="label-title">Địa chỉ giao hàng</span>
                            <textarea id="recAddress" name="address" rows="3" required><?= htmlspecialchars($profileAddress) ?></textarea>
                        </label>

                        <div class="label-chooser">
                            <div class="chooser-title">Lựa chọn tên cho địa chỉ thường dùng:</div>
                            <div class="chooser-buttons">
                                <button type="button" class="addr-btn" data-val="office"><i class="fas fa-briefcase"></i> VĂN PHÒNG</button>
                                <button type="button" class="addr-btn" data-val="home"><i class="fas fa-home"></i> NHÀ RIÊNG</button>
                            </div>
                            <input type="hidden" name="address_label" id="address_label" value="">
                        </div>

                        <label class="field">
                            <span class="label-title">Ghi chú (tùy chọn)</span>
                            <input type="text" name="note" placeholder="Ví dụ: giao giờ hành chính">
                        </label>

                        <h3 class="section-sub">Phương thức thanh toán</h3>
                        <label class="radio"><input type="radio" name="payment_method" value="cod" checked> Thanh toán khi nhận hàng (COD)</label>
                        <label class="radio"><input type="radio" name="payment_method" value="card"> Thẻ / Ví (mô phỏng)</label>

                        <!-- pass cart to placeAction when session cart is empty -->
                        <input type="hidden" name="cart_items" id="cart_items" value='<?= htmlspecialchars(json_encode($cart), ENT_QUOTES) ?>'>

                        <div class="form-actions">
                            <a href="index.php?controller=product&action=list" class="btn btn-muted">Quay lại</a>
                            <button type="submit" class="btn btn-primary">Đặt hàng — <?= currency($total) ?></button>
                        </div>
                    </section>
                </form>

                <aside class="order-summary card">
                    <h2 class="card-title">Chi tiết đơn hàng</h2>

                    <?php if (empty($cart)): ?>
                        <p class="muted">Giỏ hàng trống.</p>
                    <?php else: ?>
                        <ul class="items">
                            <?php foreach ($cart as $it):
                                $qty = intval($it['quantity'] ?? 1);
                                $price = floatval($it['price'] ?? 0);
                                $line = $qty * $price;
                            ?>
                                <li class="item">
                                    <div class="left">
                                        <img src="<?= htmlspecialchars($it['image'] ?? '/Shop_Website/Assets/Images/placeholder-product-1.jpg') ?>" alt="" />
                                        <div class="meta">
                                            <div class="name"><?= htmlspecialchars($it['name'] ?? '') ?></div>
                                            <div class="qty">x<?= $qty ?></div>
                                        </div>
                                    </div>
                                    <div class="right"><?= currency($line) ?></div>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="summary-lines">
                            <div class="line"><span>Tạm tính</span><span><?= currency($subtotal) ?></span></div>
                            <div class="line"><span>Phí vận chuyển</span><span><?= $shipping === 0 ? 'Miễn phí' : currency($shipping) ?></span></div>
                            <?php if ($discount > 0): ?><div class="line"><span>Giảm giá</span><span>-<?= currency($discount) ?></span></div><?php endif; ?>
                            <div class="line total"><span>Tổng cộng</span><span><?= currency($total) ?></span></div>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // address label buttons
            document.querySelectorAll('.addr-btn').forEach(b => b.addEventListener('click', function() {
                document.querySelectorAll('.addr-btn').forEach(x => x.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('address_label').value = this.dataset.val || '';
            }));

            // recipient mode handling
            const mode = document.getElementsByName('recipient_mode');
            const savedWrap = document.getElementById('savedAddressWrap');
            const savedSelect = document.getElementById('savedAddressSelect');
            const recName = document.getElementById('recName');
            const recPhone = document.getElementById('recPhone');
            const recAddress = document.getElementById('recAddress');

            function useProfile() {
                recName.value = <?= json_encode($profileName) ?>;
                recPhone.value = <?= json_encode($profilePhone) ?>;
                recAddress.value = <?= json_encode($profileAddress) ?>;
                if (savedWrap) savedWrap.style.display = 'none';
            }

            function useSaved() {
                if (savedWrap) savedWrap.style.display = 'block';
                if (savedSelect && savedSelect.value) {
                    const o = savedSelect.options[savedSelect.selectedIndex];
                    recName.value = o.dataset.name || '';
                    recPhone.value = o.dataset.phone || '';
                    recAddress.value = o.dataset.address || '';
                    document.getElementById('address_label').value = o.dataset.label || '';
                }
            }

            function useNew() {
                recName.value = '';
                recPhone.value = '';
                recAddress.value = '';
                if (savedWrap) savedWrap.style.display = 'none';
            }

            mode.forEach(r => r.addEventListener('change', function() {
                if (this.value === 'profile') useProfile();
                else if (this.value === 'saved') useSaved();
                else useNew();
            }));

            if (savedSelect) savedSelect.addEventListener('change', function() {
                const o = this.options[this.selectedIndex];
                recName.value = o.dataset.name || '';
                recPhone.value = o.dataset.phone || '';
                recAddress.value = o.dataset.address || '';
                document.getElementById('address_label').value = o.dataset.label || '';
            });

            // init with profile
            useProfile();
        });
    </script>
</body>

</html>