<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nếu chưa đăng nhập → đẩy về trang chủ
if (!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập để xem danh sách yêu thích!'); window.location.href='index.php';</script>";
    exit;
}

require_once "Model/Product/ProductModel.php";
$productModel = new ProductModel();
$userId = $_SESSION['user']['id'];
$wishlist = $productModel->getWishlist($userId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản phẩm yêu thích</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Product/WishList.css">
</head>
<body>

<?php include("View/Layout/Header.php"); ?>

<main class="wishlist-page container">
    
    <?php if (!empty($wishlist) && is_array($wishlist)): ?>
        <div class="wishlist-grid">
            <?php foreach ($wishlist as $item): ?>
                <?php $product = $item; ?>
                <div class="wishlist-item">
                    <a href="index.php?controller=product&action=detail&id=<?= $product->id ?>" class="product-link">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($product->image ? '/Shop_Website/' . $product->image : '/Shop_Website/Assets/Images/placeholder-product-1.jpg') ?>" 
                                 alt="<?= htmlspecialchars($product->name) ?>">
                            <div class="remove-wishlist" data-id="<?= $product->id ?>">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?= htmlspecialchars($product->name) ?></h3>
                            <div class="product-price">₫<?= number_format($product->price, 0, ',', '.') ?></div>
                            <div class="product-sold">Đã bán: <?= $product->sold ?? 0 ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-wishlist">
            <i class="far fa-heart"></i>
            <h3>Chưa có sản phẩm nào trong danh sách yêu thích</h3>
            <p>Hãy khám phá và thêm những sản phẩm bạn thích nhé!</p>
            <a href="index.php" class="btn primary" style="margin-top: 20px;">
                <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
            </a>
        </div>
    <?php endif; ?>
</main>

<?php include("View/Layout/Footer.php"); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.remove-wishlist').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const productId = $(this).data('id');
        const item = $(this).closest('.wishlist-item');

        if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi danh sách yêu thích?')) {
            $.ajax({
                url: 'index.php?controller=product&action=toggleWishlist',
                type: 'POST',
                data: { product_id: productId },
                success: function(res) {
                    try {
                        const data = JSON.parse(res);
                        if (data.status === 'unliked') {
                            item.fadeOut(400, function() { $(this).remove(); });
                            if ($('.wishlist-item').length <= 1) {
                                setTimeout(() => location.reload(), 500);
                            }
                        }
                    } catch(e) {
                        alert('Lỗi hệ thống!');
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>
