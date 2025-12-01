<?php
require_once "./View/Layout/Header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'];
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="Assets/Css/User/Profile.css">


<main class="profile-container">
    <aside class="profile-sidebar">
        <div class="user-card">
            <div class="avatar">
                <img src="<?= !empty($user['avatar']) ? $user['avatar'] : '/Shop_Website/Assets/Images/placeholder-avatar.png' ?>" alt="avatar">
            </div>
            <div class="user-info">
                <h3 class="username"><?= htmlspecialchars($user['username']) ?></h3>
                <div class="balance">Số dư: 0 ₫</div>
            </div>
        </div>

        <nav class="profile-actions">
            <a class="btn" href="index.php?controller=user&action=edit">Cập nhật thông tin</a>
            <a class="btn active" href="index.php?controller=user&action=purchaseHistory" style="background-color: #ee4d2d; color: white;">
                <i class="fas fa-file-invoice-dollar" style="margin-right:8px"></i> Đơn mua
            </a>

            <?php if (isset($user['role']) && $user['role'] == 1): ?>
                <div style="margin:10px 0; border-top:1px solid #eee"></div>
                <a class="btn" href="index.php?controller=shop&action=orderManager">Kênh người bán</a>
            <?php endif; ?>

            <a class="btn logout" href="index.php?controller=user&action=logout">Đăng xuất</a>
        </nav>
    </aside>

    <section class="profile-main" style="background: transparent; border: none; box-shadow: none; padding: 0;">

        <div class="purchase-tabs">
            <a href="#" class="purchase-tab active">Tất cả</a>
            <a href="#" class="purchase-tab">Chờ xác nhận</a>
            <a href="#" class="purchase-tab">Đang giao</a>
            <a href="#" class="purchase-tab">Hoàn thành</a>
            <a href="#" class="purchase-tab">Đã hủy</a>
        </div>

        <div class="purchase-search">
            <i class="fas fa-search" style="color:#888; padding: 8px;"></i>
            <input type="text" placeholder="Tìm kiếm theo Tên Shop, ID đơn hàng hoặc Tên sản phẩm...">
        </div>

        <div class="purchase-list">
            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>

                    <div class="purchase-card">
                        <div class="pc-header">
                            <div style="display:flex; align-items:center;">
                                <?php
                                $sAvatar = 'Assets/Images/placeholder-avatar.png'; 
                                if (!empty($order->shop_avatar)) {
                                    if (strpos($order->shop_avatar, 'http') === 0) {
                                        $sAvatar = $order->shop_avatar;
                                    }
                                    else {
                                        $sAvatar = (strpos($order->shop_avatar, 'Assets') === 0) ? $order->shop_avatar : 'Assets/Uploads/' . $order->shop_avatar;
                                    }
                                }
                                ?>
                                <img src="<?= $sAvatar ?>" style="width:20px; height:20px; border-radius:50%; margin-right:5px; object-fit:cover; border:1px solid #eee;" onerror="this.src='Assets/Images/placeholder-avatar.png'">

                                <span class="shop-name" style="font-weight:bold;">
                                    <?= htmlspecialchars($order->shop_name ?? 'Cửa hàng') ?>
                                </span>

                                <a href="#" class="shop-btn"><i class="fas fa-store"></i> Xem Shop</a>
                                <a href="#" class="shop-btn" style="background:#26aa99"><i class="fas fa-comment"></i> Chat</a>
                            </div>

                            <?php
                            $st = strtolower($order->status);
                            $stText = 'Chờ xác nhận';
                            if ($st == 'shipping') $stText = 'Đang vận chuyển';
                            if ($st == 'completed') $stText = 'HOÀN THÀNH';
                            if ($st == 'cancelled') $stText = 'ĐÃ HỦY';
                            ?>
                            <div class="order-status-text">
                                <?= $stText ?> <i class="fas fa-question-circle" style="color:#bbb; margin-left:5px"></i>
                            </div>
                        </div>

                        <div class="pc-body">
                            <?php if (!empty($order->items)): ?>
                                <?php foreach ($order->items as $item): ?>
                                    <a href="index.php?controller=product&action=detail&id=<?= $item->product_id ?>" style="text-decoration:none">
                                        <div class="pc-item">
                                            <?php
                                            $img = !empty($item->image) ? $item->image : 'Assets/Images/placeholder-product-1.jpg';
                                            if (strpos($img, 'Assets') === false && strpos($img, 'http') === false) {
                                                $img = 'Assets/Uploads/Products/' . $img;
                                            }
                                            ?>
                                            <img src="<?= $img ?>" class="pc-thumb">

                                            <div class="pc-info">
                                                <div class="pc-title"><?= htmlspecialchars($item->name ?? 'Sản phẩm') ?></div>
                                                <div class="pc-variant">Phân loại hàng: Mặc định</div>
                                                <div class="pc-qty">x<?= $item->quantity ?></div>
                                            </div>

                                            <div class="pc-price">
                                                <div class="price-old">₫<?= number_format($item->price * 1.2, 0, ',', '.') ?></div>
                                                <div class="price-current">₫<?= number_format($item->price, 0, ',', '.') ?></div>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="pc-footer">
                            <div style="display:flex; justify-content:flex-end; align-items:center; margin-bottom: 10px;">
                                <span class="total-label">Thành tiền:</span>
                                <span class="total-amount">₫<?= number_format($order->total_money, 0, ',', '.') ?></span>
                            </div>

                            <div class="pc-actions">
                                <?php if ($st == 'completed' || $st == 'cancelled'): ?>
                                    <a href="#" class="btn-reorder">Mua Lại</a>
                                <?php else: ?>
                                    <a href="#" class="btn-contact">Liên Hệ Người Bán</a>
                                <?php endif; ?>

                                <a href="#" class="btn-contact">Xem chi tiết đơn</a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding: 50px; background:#fff">
                    <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/5fafbb923393b712b96488590b8f781f.png" style="width:100px">
                    <p style="margin-top:10px; color:#888">Chưa có đơn hàng nào</p>
                    <a href="index.php" class="btn-reorder" style="margin-top:20px; display:inline-block">Mua sắm ngay</a>
                </div>
            <?php endif; ?>
        </div>

    </section>
</main>
<?php
require_once "./View/Layout/Footer.php";
?>