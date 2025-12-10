<?php
require_once "./View/Layout/Header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user'])) {
    header('Location: index.php?controller=user&action=login');
    exit;
}
$user = $_SESSION['user'];

// Load ngôn ngữ (Logic không đổi)
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
                <div class="balance"><?= $lang['seller_balance'] ?>: <strong>0 ₫</strong></div>
            </div>
        </div>

        <nav class="profile-actions">
            <a class="btn" href="index.php?controller=user&action=edit"><?= $lang['seller_nav_update_info'] ?></a>
            <a class="btn" href="index.php?controller=product&action=list&seller=<?= $user['id'] ?>"><?= $lang['seller_nav_edit_prod'] ?></a>
            <a class="btn" href="index.php?controller=product&action=add"><?= $lang['seller_nav_add_prod'] ?></a>

            <a class="btn active" href="index.php?controller=shop&action=orderManager">
                <i class="fas fa-clipboard-check"></i> <?= $lang['seller_nav_approve'] ?>
            </a>

            <a class="btn logout" href="index.php?controller=user&action=logout"><?= $lang['seller_nav_logout'] ?></a>
        </nav>
    </aside>
    <section class="profile-main">

        <div class="main-header" style="border:none; padding-bottom:0;">
            <h2><?= $lang['seller_order_mgmt'] ?></h2>
        </div>

        <div class="order-tabs">
            <a href="#" class="tab-item active"><?= $lang['seller_tab_all'] ?></a>
            <a href="#" class="tab-item"><?= $lang['seller_tab_pending'] ?> <span style="color:red"></span></a>
            <a href="#" class="tab-item"><?= $lang['seller_tab_shipping'] ?></a>
            <a href="#" class="tab-item"><?= $lang['seller_tab_delivered'] ?></a>
            <a href="#" class="tab-item"><?= $lang['seller_tab_cancelled'] ?></a>
        </div>

        <div class="order-search-bar">
            <input type="text" class="search-input" placeholder="<?= $lang['seller_search_ph'] ?>">
            <button class="btn-search-order"><i class="fas fa-search"></i></button>
        </div>

        <div class="order-list-container">

            <?php if (!empty($orders)): ?>
                <?php foreach ($orders as $order): ?>

                    <div class="order-card">
                        <div class="order-header">
                            <div class="buyer-info">
                                <img src="Assets/Images/placeholder-avatar.png" class="buyer-avatar">
                                <?= htmlspecialchars($order->buyer_name ?? $order->recipient_name) ?>
                                <span style="font-weight:normal; color:#888; margin-left:5px"> (<?= $lang['seller_code'] ?>: #<?= $order->id ?>)</span>
                            </div>

                            <?php
                            // LOGIC GIỮ NGUYÊN - CHỈ THAY TEXT BẰNG BIẾN LANG
                            $st = $order->status;
                            $st_color = 'status-pending';
                            $st_text = $lang['seller_st_pending']; // Default text

                            if ($st == 'shipping') {
                                $st_color = 'status-shipping';
                                $st_text = $lang['seller_st_shipping'];
                            }
                            if ($st == 'completed') {
                                $st_color = 'status-completed';
                                $st_text = $lang['seller_st_completed'];
                            }
                            if ($st == 'cancelled') {
                                $st_color = 'status-cancelled';
                                $st_text = $lang['seller_st_cancelled'];
                            }
                            ?>
                            <div class="order-status <?= $st_color ?>">
                                <i class="fas fa-shipping-fast"></i> <?= $st_text ?>
                            </div>
                        </div>

                        <div class="order-body">
                            <?php if (!empty($order->items)): ?>
                                <?php foreach ($order->items as $item): ?>
                                    <div class="order-item-row">
                                        <?php
                                        // LOGIC XỬ LÝ ẢNH GIỮ NGUYÊN
                                        $imgSrc = 'Assets/Images/placeholder-product-1.jpg';

                                        if (!empty($item->image)) {
                                            if (strpos($item->image, 'http') === 0) {
                                                $imgSrc = $item->image;
                                            } else {
                                                $imgSrc = (strpos($item->image, 'Assets') === 0) ? $item->image : 'Assets/Uploads/Products/' . $item->image;
                                            }
                                        }
                                        ?>

                                        <img src="<?= $imgSrc ?>" class="item-thumb" onerror="this.src='https://via.placeholder.com/70'">

                                        <div class="item-details">
                                            <div class="item-name" style="font-weight: bold;"><?= htmlspecialchars($item->product_name) ?></div>
                                            <div class="item-meta"><?= $lang['add_prod_qty'] ?? 'Số lượng' ?>: x<?= $item->quantity ?></div>
                                        </div>

                                        <div class="item-price">₫<?= number_format($item->price, 0, ',', '.') ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding:10px; color:red">Không tìm thấy thông tin sản phẩm</div>
                            <?php endif; ?>
                        </div>

                        <div class="order-footer">
                            <div class="total-price">
                                <?= $lang['seller_total'] ?>: <strong>₫<?= number_format($order->total_money, 0, ',', '.') ?></strong>
                            </div>

                            <form action="index.php?controller=shop&action=updateStatus" method="POST" style="display:flex; gap:10px; align-items: center;">
                                <input type="hidden" name="order_id" value="<?= $order->id ?>">

                                <?php
                                $st = trim(strtolower($order->status));
                                if ($st == '0') $st = 'pending';
                                ?>

                                <?php if ($st == 'pending'): ?>
                                    <button type="submit" name="status" value="cancelled" class="btn-action btn-secondary-action" onclick="return confirm('Chắc chắn hủy?')">
                                        <?= $lang['seller_btn_cancel'] ?>
                                    </button>
                                    <button type="submit" name="status" value="preparing" class="btn-action btn-primary-action">
                                        <?= $lang['seller_btn_approve'] ?>
                                    </button>

                                <?php elseif ($st == 'preparing'): ?>
                                    <button type="submit" name="status" value="shipping" class="btn-action btn-primary-action" style="background:#0984e3;">
                                        <?= $lang['seller_btn_ship'] ?>
                                    </button>

                                <?php elseif ($st == 'shipping'): ?>
                                    <button type="submit" name="status" value="completed" class="btn-action btn-primary-action" style="background:#00b894;">
                                        <?= $lang['seller_btn_complete'] ?>
                                    </button>

                                <?php else: ?>
                                    <button type="button" class="btn-action btn-secondary-action"><?= $lang['seller_btn_detail'] ?></button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 50px; background: #fff;">
                    <img src="Assets/Images/empty-order.png" style="width: 100px; opacity: 0.5;">
                    <p style="color: #888; margin-top: 10px;"><?= $lang['seller_empty'] ?></p>
                </div>
            <?php endif; ?>

        </div>
    </section>
</main>
<?php
require_once "./View/Layout/Footer.php";
?>