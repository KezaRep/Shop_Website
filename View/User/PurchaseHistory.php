<?php
require_once "./View/Layout/Header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
$user = $_SESSION['user'];

// Load ngôn ngữ
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="Assets/Css/User/Profile.css">


<main class="profile-container">
    <aside class="profile-sidebar">
        <div class="user-card">

            <div class="user-info">
                <h3 class="username"><?= htmlspecialchars($user['username']) ?></h3>
                <div class="balance"><?= $lang['user_balance'] ?>: 0 ₫</div>
            </div>
        </div>

        <nav class="profile-actions">
            <a class="btn" href="index.php?controller=user&action=edit"><?= $lang['user_update_info'] ?></a>
            <a class="btn active" href="index.php?controller=user&action=purchaseHistory" style="background-color: #ee4d2d; color: white;">
                <i class="fas fa-file-invoice-dollar" style="margin-right:8px"></i> <?= $lang['user_purchase_history'] ?>
            </a>

            <?php if (isset($user['role']) && $user['role'] == 1): ?>
                <div style="margin:10px 0; border-top:1px solid #eee"></div>
                <a class="btn" href="index.php?controller=shop&action=orderManager"><?= $lang['user_seller_channel'] ?></a>
            <?php endif; ?>

            <a class="btn logout" href="index.php?controller=user&action=logout"><?= $lang['user_logout'] ?></a>
        </nav>
    </aside>

    <section class="profile-main" style="background: transparent; border: none; box-shadow: none; padding: 0;">

        <div class="purchase-tabs">
            <a href="#" class="purchase-tab active"><?= $lang['purchase_tab_all'] ?></a>
            <a href="#" class="purchase-tab"><?= $lang['purchase_tab_pending'] ?></a>
            <a href="#" class="purchase-tab"><?= $lang['purchase_tab_shipping'] ?></a>
            <a href="#" class="purchase-tab"><?= $lang['purchase_tab_completed'] ?></a>
            <a href="#" class="purchase-tab"><?= $lang['purchase_tab_cancelled'] ?></a>
        </div>

        <div class="purchase-search">
            <i class="fas fa-search" style="color:#888; padding: 8px;"></i>
            <input type="text" placeholder="<?= $lang['purchase_search_placeholder'] ?>">
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
                                    } else {
                                        $sAvatar = (strpos($order->shop_avatar, 'Assets') === 0) ? $order->shop_avatar : 'Assets/Uploads/' . $order->shop_avatar;
                                    }
                                }
                                ?>
                                <img src="<?= $sAvatar ?>" style="width:20px; height:20px; border-radius:50%; margin-right:5px; object-fit:cover; border:1px solid #eee;" onerror="this.src='Assets/Images/placeholder-avatar.png'">

                                <span class="shop-name" style="font-weight:bold;">
                                    <?= htmlspecialchars($order->shop_name ?? 'Shop') ?>
                                </span>

                                <a href="#" class="shop-btn"><i class="fas fa-store"></i> <?= $lang['purchase_view_shop'] ?></a>
                                <a href="#" class="shop-btn" style="background:#26aa99"><i class="fas fa-comment"></i> <?= $lang['purchase_chat'] ?></a>
                            </div>

                            <?php
                            $st = strtolower($order->status);
                            // Xử lý trạng thái đa ngôn ngữ
                            $stText = $lang['purchase_status_pending']; // Mặc định
                            if ($st == 'shipping') $stText = $lang['purchase_status_shipping'];
                            if ($st == 'completed') $stText = $lang['purchase_status_completed'];
                            if ($st == 'cancelled') $stText = $lang['purchase_status_cancelled'];
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
                                                <div class="pc-variant"><?= $lang['purchase_variant_default'] ?></div>
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
                                <span class="total-label"><?= $lang['purchase_total_label'] ?></span>
                                <span class="total-amount">₫<?= number_format($order->total_money, 0, ',', '.') ?></span>
                            </div>

                            <div class="pc-actions">
                                <?php if ($st == 'completed' || $st == 'cancelled'): ?>
                                    <a href="#" class="btn-reorder"><?= $lang['purchase_btn_buy_again'] ?></a>
                                <?php else: ?>
                                    <a href="#" class="btn-contact"><?= $lang['purchase_btn_contact_seller'] ?></a>
                                <?php endif; ?>

                                <a href="#" class="btn-contact"><?= $lang['purchase_btn_view_details'] ?></a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align:center; padding: 50px; background:#fff">
                    <img src="https://deo.shopeemobile.com/shopee/shopee-pcmall-live-sg/5fafbb923393b712b96488590b8f781f.png" style="width:100px">
                    <p style="margin-top:10px; color:#888"><?= $lang['purchase_empty'] ?></p>
                    <a href="index.php" class="btn-reorder" style="margin-top:20px; display:inline-block"><?= $lang['purchase_btn_shop_now'] ?></a>
                </div>
            <?php endif; ?>
        </div>

    </section>
</main>
<?php
require_once "./View/Layout/Footer.php";
?>