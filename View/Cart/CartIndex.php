<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

$cart = $_SESSION['cart'] ?? [];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>

    <link rel="stylesheet" href="Assets/Css/Cart/Cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <main class="cart-page">
        <div class="container">

            <h1 class="cart-title"><i class="fas fa-shopping-cart"></i> Giỏ hàng</h1>

            <?php if (empty($cart)): ?>
                <p class="empty-cart">Giỏ hàng của bạn đang trống.</p>
            <?php else: ?>

                <div class="cart-table">
                    <div class="cart-row cart-header">
                        <span class="c-product">Sản phẩm</span>
                        <span class="c-price">Đơn giá</span>
                        <span class="c-qty">Số lượng</span>
                        <span class="c-total">Thành tiền</span>
                        <span class="c-action"></span>
                    </div>

                    <?php
                    $grandTotal = 0;

                    foreach ($cart as $item):

                        // Đảm bảo dữ liệu không null
                        $price = $item['price'] ?? 0;
                        $qty   = $item['quantity'] ?? 1;
                        $name  = $item['name'] ?? '';
                        $image = $item['image'] ?? 'Assets/Images/placeholder-product-1.jpg';

                        // Tính tiền sản phẩm
                        $total = $price * $qty;
                        $grandTotal += $total;
                        ?>
                        
                        <div class="cart-row">
                            <div class="c-product">
                                <img src="<?= $image ?>" class="cart-img">
                                <span><?= htmlspecialchars($name) ?></span>
                            </div>

                            <div class="c-price">
                                ₫<?= number_format($price, 0, ',', '.') ?>
                            </div>
                        
                            <div class="c-qty">
                                <form action="index.php?controller=cart&action=update" method="post">
                                    <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                    <button type="submit" name="change" value="-" class="qty-btn">−</button>
                                    <input type="text" class="qty-input" value="<?= $qty ?>" readonly>
                                    <button type="submit" name="change" value="+" class="qty-btn">+</button>
                                </form>
                            </div>

                            <div class="c-total">
                                ₫<?= number_format($total, 0, ',', '.') ?>
                            </div>

                            <div class="c-action">
                                <a href="index.php?controller=cart&action=delete&id=<?= $item['id'] ?>" class="delete-btn">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>

                    <?php endforeach; ?>

                </div>

                <div class="cart-summary">
                    <span>Tổng tiền:</span>
                    <strong class="grand-total">₫<?= number_format($grandTotal, 0, ',', '.') ?></strong>
                    <a href="index.php?controller=cart&action=checkout" class="checkout-btn">Mua hàng</a>
                </div>

            <?php endif; ?>

        </div>
    </main>
</body>

</html>
