<?php
function productImageSrc($img)
{
    if (empty($img)) {
        return 'Assets/Images/placeholder-product-1.jpg'; // Ảnh mặc định nếu không có ảnh
    }
    // Nếu là ảnh lưu dưới dạng BLOB (dữ liệu nhị phân) trong DB
    if (@getimagesizefromstring($img)) {
        return 'data:image/jpeg;base64,' . base64_encode($img);
    }
    // Nếu là đường dẫn file bình thường
    return $img;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Cart/Cart.css">
</head>

<body>
    <main class="cart-page">
        <div class="container">
            <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> Giỏ hàng</h1>

            <?php if (empty($cart)): ?>
                <div class="empty-cart">
                    <img src="Assets/Images/empty-cart.png" alt="" style="width: 100px; margin-bottom: 15px;">
                    <p>Giỏ hàng của bạn đang trống.</p>
                    <a href="index.php" style="color: var(--primary-color);">Quay lại mua sắm</a>
                </div>
            <?php else: ?>

                <form action="index.php?controller=cart&action=checkout" method="POST" id="cartForm">

                    <div class="cart-wrapper">
                        <div class="cart-left">
                            <div class="cart-header-row">
                                <div class="col-checkbox">
                                    <input type="checkbox" id="checkAll" title="Chọn tất cả">
                                </div>
                                <div class="col-product">Sản phẩm (<?= count($cart) ?>)</div>
                                <div class="col-price">Đơn giá</div>
                                <div class="col-qty">Số lượng</div>
                                <div class="col-total">Thành tiền</div>
                                <div class="col-action"><i class="fas fa-trash-alt"></i></div>
                            </div>

                            <div class="shop-section">
                                <div class="shop-header">
                                    <div class="col-checkbox">
                                        <input type="checkbox" id="shopCheck">
                                    </div>
                                    <div class="shop-name-group">
                                        <i class="fas fa-store shop-icon"></i>
                                        <span>Shop Chính Hãng (Official)</span>
                                        <i class="fas fa-angle-right shop-arrow"></i>
                                    </div>
                                </div>

                                <?php foreach ($cart as $item):
                                    $cartId    = $item['cart_id'];
                                    $productId = $item['product_id'];
                                    $price     = $item['price'] ?? 0;
                                    $qty       = $item['quantity'] ?? 1;
                                    $name      = $item['name'] ?? 'Sản phẩm chưa có tên';
                                    $image     = productImageSrc($item['image'] ?? '');
                                    $total     = $price * $qty;
                                ?>
                                    <div class="cart-item-box">
                                        <div class="col-checkbox">
                                            <input type="checkbox" class="item-check"
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
                                                <div style="font-size: 12px; color: #ee4d2d; border: 1px solid #ee4d2d; display: inline-block; padding: 1px 4px; margin-top: 5px; border-radius: 2px;">
                                                    Đổi ý miễn phí 15 ngày
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-price">
                                            ₫<?= number_format($price, 0, ',', '.') ?>
                                        </div>

                                        <div class="col-qty">
                                            <div class="qty-control">
                                                <button type="button" class="qty-btn btn-minus"
                                                    data-id="<?= $cartId ?>"
                                                    data-price="<?= $price ?>">
                                                    -
                                                </button>

                                                <input type="text" class="qty-input" id="qty-<?= $cartId ?>" value="<?= $qty ?>" readonly>

                                                <button type="button" class="qty-btn btn-plus"
                                                    data-id="<?= $cartId ?>"
                                                    data-price="<?= $price ?>">
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <div class="col-total" id="total-text-<?= $cartId ?>">
                                            ₫<?= number_format($total, 0, ',', '.') ?>
                                        </div>

                                        <div class="col-action">
                                            <a href="index.php?controller=cart&action=delete&id=<?= $cartId ?>" class="delete-btn" onclick="return confirm('Bạn muốn xóa sản phẩm này?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>

                            </div>
                        </div>

                        <div class="cart-right">
                            <div class="order-summary">
                                <span class="summary-title">Thông tin đơn hàng</span>

                                <div class="summary-row">
                                    <span>Tạm tính (<span id="displayCount">0</span> sản phẩm)</span>
                                    <span id="displaySubTotal">0 ₫</span>
                                </div>

                                <div class="summary-row">
                                    <span>Phí vận chuyển</span>
                                    <span>0 ₫</span>
                                </div>

                                <div class="voucher-box">
                                    <input type="text" placeholder="Nhập mã voucher" class="voucher-input">
                                    <button class="voucher-btn">ÁP DỤNG</button>
                                </div>

                                <div class="summary-total">
                                    <span>Tổng cộng</span>
                                    <span class="total-price">
                                        <span id="displayGrandTotal">0 ₫</span>
                                        <div style="font-size: 12px; color: #666; font-weight: normal; text-align: right; margin-top: 5px;">Đã bao gồm VAT (nếu có)</div>
                                    </span>
                                </div>

                                <button type="submit" class="checkout-btn" name="btn_checkout">
                                    THANH TOÁN (<span id="btnCount">0</span>)
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
            // --- PHẦN 1: CÁC BIẾN TÍNH TỔNG TIỀN (GIỮ NGUYÊN TỪ BÀI TRƯỚC) ---
            const checkAll = document.getElementById('checkAll');
            const shopCheck = document.getElementById('shopCheck');
            const itemChecks = document.querySelectorAll('.item-check');
            const displayCount = document.getElementById('displayCount');
            const displaySubTotal = document.getElementById('displaySubTotal');
            const displayGrandTotal = document.getElementById('displayGrandTotal');
            const btnCount = document.getElementById('btnCount');

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
            }

            // --- PHẦN 2: XỬ LÝ AJAX CỘNG TRỪ (MỚI) ---

            // Hàm gửi AJAX
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
                        // Có thể thông báo nhỏ ở đây nếu muốn
                    })
                    .catch(error => console.error('Error:', error));
            }

            // Bắt sự kiện click cho tất cả nút cộng/trừ
            document.querySelectorAll('.qty-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const cartId = this.getAttribute('data-id');
                    const price = parseFloat(this.getAttribute('data-price'));
                    const inputQty = document.getElementById(`qty-${cartId}`);
                    const totalText = document.getElementById(`total-text-${cartId}`);
                    // Tìm cái checkbox tương ứng của dòng này để cập nhật data-total
                    const checkbox = document.querySelector(`.item-check[value="${cartId}"]`);

                    let currentQty = parseInt(inputQty.value);
                    let newQty = currentQty;

                    // Kiểm tra nút bấm là cộng hay trừ
                    if (this.classList.contains('btn-plus')) {
                        newQty++;
                    } else {
                        if (currentQty > 1) newQty--;
                    }

                    if (newQty !== currentQty) {
                        // 1. Cập nhật giao diện ngay lập tức (cho mượt)
                        inputQty.value = newQty;

                        // 2. Tính lại thành tiền của dòng đó
                        const newRowTotal = newQty * price;
                        totalText.innerText = formatCurrency(newRowTotal);

                        // 3. Cập nhật data-total vào checkbox để hàm tính tổng chạy đúng
                        checkbox.setAttribute('data-total', newRowTotal);

                        // 4. Tính lại tổng đơn hàng (nếu đang được tích chọn)
                        if (checkbox.checked) {
                            recalculateTotal();
                        }

                        // 5. Gửi AJAX về server lưu lại
                        updateCartAjax(cartId, newQty);
                    }
                });
            });

            // --- PHẦN 3: SỰ KIỆN CHECKBOX (GIỮ NGUYÊN) ---
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    if (shopCheck) shopCheck.checked = isChecked;
                    document.querySelectorAll('.item-check').forEach(cb => cb.checked = isChecked);
                    recalculateTotal();
                });
            }

            if (shopCheck) {
                shopCheck.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.querySelectorAll('.item-check').forEach(cb => cb.checked = isChecked);
                    if (!isChecked && checkAll) checkAll.checked = false;
                    recalculateTotal();
                });
            }

            itemChecks.forEach(cb => {
                cb.addEventListener('change', function() {
                    if (!this.checked) {
                        if (checkAll) checkAll.checked = false;
                        if (shopCheck) shopCheck.checked = false;
                    }
                    recalculateTotal();
                });
            });
        });
    </script>

</body>

</html>