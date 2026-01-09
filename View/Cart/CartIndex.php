<?php
// Load ngôn ngữ
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}

// 1. Hàm helper
function productImageSrc($img)
{
    if (empty($img)) {
        return 'Assets/Images/placeholder-product-1.jpg';
    }
    // Kiểm tra nhanh xem chuỗi có phải là ảnh binary không
    if (@getimagesizefromstring($img)) {
        return 'data:image/jpeg;base64,' . base64_encode($img);
    }
    return $img;
}    public function vnpayReturnAction()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $check = $this->paymentModel->checkResponse($_GET);
        
        $data = []; 

        if ($check['isValid']) {
            if ($check['responseCode'] == '00') {
                if (isset($_SESSION['pending_order'])) {
                    $info = $_SESSION['pending_order'];

                    $this->saveOrderToDatabase($info, 'pending');

                    unset($_SESSION['pending_order']);

                    $data['status'] = 'success';
                    $data['amount'] = $check['amount'];
                    $data['order_id'] = $check['orderId'];
                    $data['info'] = [
                        'customer_name' => $info['name'],
                        'customer_phone' => $info['phone'],
                        'customer_address' => $info['address']
                    ];
                } else {
                    $data['status'] = 'failed';
                    $data['msg'] = 'Lỗi: Phiên giao dịch đã hết hạn.';
                }
            } else {
                $data['status'] = 'failed';
                $data['msg'] = 'Giao dịch bị hủy hoặc thất bại.';
            }
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'Chữ ký bảo mật không hợp lệ.';
        }

        require_once("View/Payment/result.php");
    }

$cartByShop = [];
if (!empty($cart)) {
    foreach ($cart as $item) {
        $shopId = $item['seller_id'] ?? 0;

        if (!isset($cartByShop[$shopId])) {
            $cartByShop[$shopId] = [
                'shop_name' => $item['shop_name'] ?? $lang['cart_shop_other'], // Dùng biến lang
                'shop_avatar' => $item['shop_avatar'] ?? '',
                'items' => []
            ];
        }
        $cartByShop[$shopId]['items'][] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['cart_title'] ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Cart/Cart.css">
</head>

<body>
    <main class="cart-page">
        <div class="container">
            <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> <?= $lang['cart_title'] ?></h1>

            <?php if (empty($cart)): ?>
                <div class="empty-cart">
                    <img src="Assets/Images/empty-cart.png" alt="" style="width: 100px; margin-bottom: 15px;">
                    <p><?= $lang['cart_empty_message'] ?></p>
                    <a href="index.php" style="color: var(--primary-color);"><?= $lang['cart_continue_shopping'] ?></a>
                </div>
            <?php else: ?>

                <form action="index.php?controller=cart&action=checkout" method="POST" id="cartForm">

                    <div class="cart-wrapper">
                        <div class="cart-left">
                            <div class="cart-header-row">
                                <div class="col-checkbox">
                                    <input type="checkbox" id="checkAll" title="Chọn tất cả">
                                </div>
                                <div class="col-product"><?= $lang['cart_col_product'] ?> (<?= count($cart) ?>)</div>
                                <div class="col-price"><?= $lang['cart_col_price'] ?></div>
                                <div class="col-qty"><?= $lang['cart_col_qty'] ?></div>
                                <div class="col-total"><?= $lang['cart_col_total'] ?></div>
                                <div class="col-action"><i class="fas fa-trash-alt"></i></div>
                            </div>

                            <?php foreach ($cartByShop as $shopId => $shopData): ?>

                                <div class="shop-section">
                                    <div class="shop-header">
                                        <div class="col-checkbox">
                                            <input type="checkbox" class="shop-check-all" data-shop="<?= $shopId ?>">
                                        </div>
                                        <div class="shop-name-group">
                                            <?php
                                            $shopAvt = !empty($shopData['shop_avatar']) ? productImageSrc($shopData['shop_avatar']) : 'Assets/Images/placeholder-avatar.png';
                                            ?>
                                            <img src="<?= $shopAvt ?>" class="shop-avatar-icon" alt="Shop Logo">
                                            <span style="font-weight:bold;"><?= htmlspecialchars($shopData['shop_name']) ?></span>
                                            <a href="#" class="btn-chat-shop"><i class="fas fa-comment-dots"></i></a>
                                        </div>
                                    </div>

                                    <?php foreach ($shopData['items'] as $item):
                                        $cartId    = $item['cart_id'];
                                        $productId = $item['product_id'];
                                        $price     = $item['price'] ?? 0;
                                        $qty       = $item['quantity'] ?? 1;
                                        $name      = $item['name'] ?? 'Sản phẩm';
                                        $image     = productImageSrc($item['image'] ?? '');
                                        $stock     = $item['stock'] ?? 100;
                                        $total     = $price * $qty;
                                    ?>
                                        <div class="cart-item-box">
                                            <div class="col-checkbox">
                                                <input type="checkbox" class="item-check shop-item-<?= $shopId ?>"
                                                    data-total="<?= $total ?>"
                                                    name="selected_cart_id[]"
                                                    value="<?= $cartId ?>">
                                            </div>

                                            <div class="col-product">
                                                <img src="<?= $image ?>" class="cart-img" alt="<?= htmlspecialchars($name) ?>">
                                                <div class="product-info">
                                                    <a href="index.php?controller=product&action=detail&id=<?= $productId ?>" class="product-name">
                                                        <?= htmlspecialchars($name) ?>
                                                    </a>
                                                    <div class="return-policy"><?= $lang['cart_return_policy'] ?></div>
                                                </div>
                                            </div>

                                            <div class="col-price">₫<?= number_format($price, 0, ',', '.') ?></div>

                                            <div class="col-qty">
                                                <div class="qty-control">
                                                    <button type="button" class="qty-btn btn-minus" data-id="<?= $cartId ?>" data-price="<?= $price ?>">-</button>
                                                    <input type="text" class="qty-input" id="qty-<?= $cartId ?>" value="<?= $qty ?>" data-max="<?= $stock ?>" readonly>
                                                    <button type="button" class="qty-btn btn-plus" data-id="<?= $cartId ?>" data-price="<?= $price ?>">+</button>
                                                </div>
                                            </div>

                                            <div class="col-total" id="total-text-<?= $cartId ?>">₫<?= number_format($total, 0, ',', '.') ?></div>

                                            <div class="col-action">
                                                <a href="index.php?controller=cart&action=delete&id=<?= $cartId ?>" class="delete-btn" onclick="return confirm('<?= $lang['cart_confirm_delete'] ?>');">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="cart-right">
                            <div class="order-summary">
                                <span class="summary-title"><?= $lang['cart_summary_title'] ?></span>

                                <div class="summary-row">
                                    <span><?= $lang['cart_summary_subtotal'] ?> (<span id="displayCount">0</span> <?= $lang['cart_text_items'] ?>)</span>
                                    <span id="displaySubTotal">0 ₫</span>
                                </div>

                                <div class="summary-row">
                                    <span><?= $lang['cart_summary_shipping'] ?></span>
                                    <span>0 ₫</span>
                                </div>

                                <div class="voucher-box">
                                    <input type="text" placeholder="<?= $lang['cart_voucher_placeholder'] ?>" class="voucher-input">
                                    <button class="voucher-btn"><?= $lang['cart_voucher_btn'] ?></button>
                                </div>

                                <div class="summary-total">
                                    <span><?= $lang['cart_summary_total'] ?></span>
                                    <span class="total-price">
                                        <span id="displayGrandTotal">0 ₫</span>
                                        <div style="font-size: 12px; color: #666; font-weight: normal; text-align: right; margin-top: 5px;"><?= $lang['cart_vat_note'] ?></div>
                                    </span>
                                </div>

                                <button type="submit" class="checkout-btn" name="btn_checkout">
                                    <?= $lang['cart_btn_checkout'] ?> (<span id="btnCount">0</span>)
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- PHẦN 1: CÁC BIẾN TÍNH TỔNG TIỀN ---
            const checkAll = document.getElementById('checkAll');
            const itemChecks = document.querySelectorAll('.item-check');
            const displayCount = document.getElementById('displayCount');
            const displaySubTotal = document.getElementById('displaySubTotal');
            const displayGrandTotal = document.getElementById('displayGrandTotal');
            const btnCount = document.getElementById('btnCount');
            const checkoutBtn = document.querySelector('.checkout-btn');

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount).replace('₫', '').trim() + ' ₫';
            }

            function recalculateTotal() {
                let total = 0;
                let count = 0;

                document.querySelectorAll('.item-check').forEach(checkbox => {
                    if (checkbox.checked) {
                        total += parseFloat(checkbox.getAttribute('data-total'));
                        count++;
                    }
                });

                displayCount.innerText = count;
                btnCount.innerText = count;
                const formattedTotal = formatCurrency(total);
                displaySubTotal.innerText = formattedTotal;
                displayGrandTotal.innerText = formattedTotal;

                if (count === 0) {
                    checkoutBtn.disabled = true;
                    checkoutBtn.style.opacity = '0.6';
                    checkoutBtn.style.cursor = 'not-allowed';
                } else {
                    checkoutBtn.disabled = false;
                    checkoutBtn.style.opacity = '1';
                    checkoutBtn.style.cursor = 'pointer';
                }
            }

            // --- PHẦN 2: XỬ LÝ AJAX CỘNG TRỪ ---
            function updateCartAjax(cartId, newQty) {
                const formData = new FormData();
                formData.append('cart_id', cartId);
                formData.append('quantity', newQty);

                fetch('index.php?controller=cart&action=updateAjax', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Saved:', data);
                    })
                    .catch(error => console.error('Error:', error));
            }

            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartId = this.getAttribute('data-id');
                    const price = parseFloat(this.getAttribute('data-price'));
                    const inputQty = document.getElementById(`qty-${cartId}`);
                    const totalText = document.getElementById(`total-text-${cartId}`);
                    const checkbox = document.querySelector(`.item-check[value="${cartId}"]`);

                    let currentQty = parseInt(inputQty.value);
                    let maxQty = parseInt(inputQty.getAttribute('data-max')) || 999;
                    let newQty = currentQty;

                    if (this.classList.contains('btn-plus')) {
                        if (currentQty < maxQty) newQty++;
                    } else {
                        if (currentQty > 1) newQty--;
                    }

                    if (newQty !== currentQty) {
                        inputQty.value = newQty;
                        const newRowTotal = newQty * price;
                        totalText.innerText = formatCurrency(newRowTotal);
                        if (checkbox) checkbox.setAttribute('data-total', newRowTotal);

                        if (checkbox && checkbox.checked) {
                            recalculateTotal();
                        }
                        updateCartAjax(cartId, newQty);
                    }
                });
            });

            // --- PHẦN 3: SỰ KIỆN CHECKBOX ---

            // Checkbox Tất Cả
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    // Check hết các shop header
                    document.querySelectorAll('.shop-check-all').forEach(sc => sc.checked = isChecked);
                    // Check hết các item
                    document.querySelectorAll('.item-check').forEach(cb => cb.checked = isChecked);
                    recalculateTotal();
                });
            }

            // Checkbox Shop Header (Gom nhóm)
            document.querySelectorAll('.shop-check-all').forEach(shopCheck => {
                shopCheck.addEventListener('change', function() {
                    const shopId = this.getAttribute('data-shop');
                    const isChecked = this.checked;
                    // Tìm tất cả item thuộc shop này và check theo
                    document.querySelectorAll(`.shop-item-${shopId}`).forEach(item => {
                        item.checked = isChecked;
                    });

                    // Nếu bỏ check shop thì bỏ check tất cả luôn
                    if (!isChecked && checkAll) checkAll.checked = false;

                    recalculateTotal();
                });
            });

            // Checkbox Từng Sản Phẩm
            itemChecks.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        if (checkAll) checkAll.checked = false;
                        // Cũng nên bỏ check ở header shop tương ứng (nâng cao, ko làm cũng ko sao)
                    }
                    recalculateTotal();
                });
            });

            recalculateTotal();
        });
    </script>
</body>

</html>