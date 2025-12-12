<?php
require_once "./View/Layout/Header.php";
include_once('Model/Shop/ShopModel.php');
$shopModel = new ShopModel();
// Đảm bảo biến $lang tồn tại
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}

// --- Helper Functions ---

function getImgUrl($path)
{
    if (!empty($path) && file_exists($path)) {
        return $path;
    }
    return 'https://via.placeholder.com/150';
}

// Helper tính thời gian tham gia (Đã sửa để dùng đa ngôn ngữ)
function timeAgo($datetime, $lang)
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60)
        return $lang['time_just_now'];

    $years = floor($diff / (365 * 60 * 60 * 24));
    if ($years > 0)
        return $years . ' ' . $lang['time_year_ago'];

    $months = floor($diff / (30 * 60 * 60 * 24));
    if ($months > 0)
        return $months . ' ' . $lang['time_month_ago'];

    $days = floor($diff / (60 * 60 * 24));
    if ($days > 0)
        return $days . ' ' . $lang['time_day_ago'];

    return $lang['time_new'];
}
?>

<link rel="stylesheet" href="Assets/Css/Shop/Profile.css">

<div class="shop-page-wrapper">
    <div class="container-shop">
        <div class="shop-header">
            <div class="shop-info-card"
                style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?= getImgUrl($shop->cover_image) ?>'); background-size: cover; background-position: center;">
                <div class="shop-avatar">
                    <img src="<?= getImgUrl($shop->avatar) ?>" alt="Avatar">
                </div>
                <div class="shop-actions">
                    <h3><?= htmlspecialchars($shop->shop_name) ?></h3>
                    <span class="shop-status">
                        <i class="fas fa-circle" style="font-size: 8px; color: #00bfa5;"></i>
                        <?= ($shop->is_online ?? 1) ? $lang['profile_status_online'] : $lang['profile_status_offline'] ?>
                    </span>
                    <div>
                        <?php  ?>
                            <a href="index.php?controller=product&action=add"><button
                                    class="btn-shop-action"><?= $lang['profile_add_product'] ?></button></a>
                            <a href="index.php?controller=shop&action=orderManager"><button
                                    class="btn-shop-action"><?= $lang['seller_nav_approve'] ?></button></a>
                    </div>
                </div>
            </div>

            <div class="shop-stats">
                <div class="stat-item"><i class="fas fa-box stat-icon"></i> <?= $lang['profile_products'] ?>: <span
                        class="stat-value"><?= isset($totalProducts) ? $totalProducts : 0 ?></span></div>
                <div class="stat-item"><i class="fas fa-users stat-icon"></i> <?= $lang['profile_followers'] ?>: <span
                        class="stat-value"><?= $shop->follower_count ?? 0 ?></span></div>
                <div class="stat-item"><i class="fas fa-star stat-icon"></i> <?= $lang['profile_rating'] ?>: <span
                        class="stat-value"><?= $shop->rating ?? '5.0' ?></span></div>
                <div class="stat-item"><i class="fas fa-clock stat-icon"></i> <?= $lang['profile_joined'] ?>: <span
                        class="stat-value"><?= timeAgo($shop->created_at, $lang) ?></span></div>
            </div>
        </div>
    </div>

    <div class="shop-nav">
        <div class="container-shop">
            <ul>
                <li onclick="switchTab('products', this)" class="active"><?= $lang['profile_tab_feed'] ?></li>
                <li onclick="switchTab('products', this)"><?= $lang['profile_tab_products'] ?></li>
                <li onclick="switchTab('products', this)"><?= $lang['profile_tab_categories'] ?></li>

               
                    <li onclick="switchTab('stats', this)" style="margin-left: auto; color: #333;">
                        <i class="fas fa-chart-line"></i> <?= $lang['profile_tab_stats'] ?>
                    </li>
                 
            </ul>
        </div>
    </div>

    <div class="container-shop">

        <div id="section-products" class="active-section">
            <h3 class="section-title"><?= $lang['profile_suggest'] ?></h3>
            <?php if (!empty($products)): ?>
                <div class="product-grid">
                    <?php foreach ($products as $p): ?>
                        <a href="index.php?controller=product&action=detail&id=<?= $p->id ?>" class="product-card"
                            style="text-decoration: none; color: inherit;">
                            <div class="product-img">
                                <img src="<?= !empty($p->image) ? $p->image : 'Assets/Images/placeholder-product-1.jpg' ?>"
                                    alt="<?= htmlspecialchars($p->name) ?>">
                            </div>
                            <div class="product-details">
                                <div class="product-name"><?= htmlspecialchars($p->name) ?></div>
                                <div class="product-price">
                                    <span>₫<?= number_format($p->price, 0, ',', '.') ?></span>
                                    <span class="product-sold"><?= $lang['dash_sold'] ?>         <?= $p->sold ?? 0 ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 50px; text-align: center; color: #777;">
                    <?= $lang['profile_empty_products'] ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($isOwner) && $isOwner): ?>
            <div id="section-stats" class="dashboard-wrapper" style="display: none;">
                <div class="stats-grid">

                    <div class="d-card blue">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <h4><?= $lang['dash_revenue'] ?></h4>
                                <div class="value">
                                    <?= number_format($revenue ?? 0, 0, ',', '.') ?>đ
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    <span style="color: #10b981;"><i class="fas fa-arrow-up"></i></span>
                                    <?= $lang['dash_total_revenue'] ?>
                                </div>
                            </div>
                            <div style="font-size: 2rem; color: #3b82f6; opacity: 0.2;">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-card green">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <h4><?= $lang['dash_orders'] ?></h4>
                                <div class="value">
                                    <?= $newOrdersCount ?? 0 ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    <?= $lang['dash_current_orders'] ?>
                                </div>
                            </div>
                            <div style="font-size: 2rem; color: #10b981; opacity: 0.2;">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-card yellow">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <h4><?= $lang['dash_sold'] ?></h4>
                                <div class="value">
                                    <?= number_format($totalSold ?? 0) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    <?= $lang['dash_sold_products'] ?>
                                </div>
                            </div>
                            <div style="font-size: 2rem; color: #f59e0b; opacity: 0.2;">
                                <i class="fas fa-box-open"></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-card red">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <h4 style="color: #dc2626;"><?= $lang['dash_low_stock'] ?></h4>
                                <div class="value">
                                    <?= $lowStockCount ?? 0 ?>
                                    <?php if (isset($lowStockCount) && $lowStockCount > 0): ?>
                                        <span class="badge"
                                            style="background: #fee2e2; color: #dc2626; font-size: 0.7rem;"><?= $lang['dash_need_import'] ?></span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    <?= $lang['dash_low_stock_desc'] ?>
                                </div>
                            </div>
                            <div style="font-size: 2rem; color: #dc2626; opacity: 0.2;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="chart-section">
                    <div class="content-box">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h4 style="margin: 0; color: #333;"><?= $lang['dash_chart_title'] ?></h4>
                            <button
                                style="border: none; background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-download"></i> <?= $lang['dash_export_report'] ?>
                            </button>
                        </div>

                        <div class="chart-container">
                            <div class="chart-grid-lines">
                                <div class="grid-line"></div>
                                <div class="grid-line"></div>
                                <div class="grid-line"></div>
                                <div class="grid-line"></div>
                            </div>

                            <?php
                            $maxVal = 0;
                            if (!empty($chartData)) {
                                foreach ($chartData as $day) {
                                    if ($day['value'] > $maxVal)
                                        $maxVal = $day['value'];
                                }
                            }
                            if ($maxVal == 0)
                                $maxVal = 1;

                            if (!empty($chartData)):
                                foreach ($chartData as $day):
                                    $percent = ($day['value'] / $maxVal) * 85;

                                    $displayMoney = $day['value'];
                                    if ($day['value'] >= 1000000) {
                                        $displayMoney = round($day['value'] / 1000000, 1) . 'M';
                                    } elseif ($day['value'] >= 1000) {
                                        $displayMoney = round($day['value'] / 1000, 0) . 'k';
                                    } else {
                                        $displayMoney = $day['value'];
                                    }

                                    $hasDataClass = ($day['value'] > 0) ? 'has-data' : '';
                                    ?>
                                    <div class="bar-wrapper <?= $hasDataClass ?>">

                                        <div class="bar-tooltip"><?= $displayMoney ?></div>

                                        <div class="bar-fill" style="height: <?= $percent ?>%;"></div>

                                        <div class="bar-label"><?= $day['label'] ?></div>
                                    </div>

                                <?php endforeach;
                            else: ?>
                                <div style="width: 100%; text-align: center; color: #999; z-index: 2;">
                                    <?= $lang['dash_no_data'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="content-box">
                        <h4 style="margin: 0 0 20px 0; color: #333;"><?= $lang['dash_recent_orders'] ?></h4>
                        <div class="activity-list">
                            <?php if (!empty($recentOrders)): ?>
                                <?php foreach ($recentOrders as $order): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon" style="background: #e0e7ff; color: #4338ca;">
                                            <?= strtoupper(substr($order->recipient_name ?? 'K', 0, 1)) ?>
                                        </div>

                                        <div style="flex: 1;">
                                            <div style="display:flex; justify-content:space-between;">
                                                <span style="font-weight: 600; font-size: 0.9rem;">
                                                    <?= htmlspecialchars($order->recipient_name ?? $lang['dash_customer_guest']) ?>
                                                </span>
                                                <span
                                                    style="font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; background: <?= $order->status == 'completed' ? '#d1fae5' : '#fee2e2' ?>; color: <?= $order->status == 'completed' ? '#059669' : '#dc2626' ?>;">
                                                    <?= $order->status == 'completed' ? $lang['dash_status_completed'] : $lang['dash_status_pending'] ?>
                                                </span>
                                            </div>

                                            <div style="font-size: 0.8rem; color: #888; margin-top: 2px;">
                                                #<?= $order->id ?> - <?= number_format($order->total_money, 0, ',', '.') ?>₫
                                                <span style="float: right; font-size: 0.75rem;">
                                                    <?= date('d/m H:i', strtotime($order->created_at)) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p style="text-align:center; color:#999; padding: 20px;"><?= $lang['dash_no_orders'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="content-box" style="margin-top: 20px;">

                    <div class="top-products-header">
                        <h4><?= $lang['dash_top_products'] ?></h4>
                        <a href="#" class="view-all-link"><?= $lang['dash_view_all'] ?> <i
                                class="fas fa-angle-right"></i></a>
                    </div>

                    <div class="top-products-list">
                        <?php if (!empty($topProducts)): ?>
                            <table class="top-products-table">
                                <thead>
                                    <tr>
                                        <th style="width: 45%;"><?= $lang['dash_col_product'] ?></th>
                                        <th style="width: 15%;"><?= $lang['dash_col_price'] ?></th>
                                        <th style="width: 25%;"><?= $lang['dash_col_progress'] ?></th>
                                        <th style="width: 15%; text-align: right; padding-right: 10px;">
                                            <?= $lang['dash_col_revenue'] ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topProducts as $idx => $prod):
                                        $percent = ($prod->sold / 50) * 100;
                                        if ($percent > 100)
                                            $percent = 100;

                                        $rankClass = 'rank-other';
                                        if ($idx == 0)
                                            $rankClass = 'rank-1';
                                        elseif ($idx == 1)
                                            $rankClass = 'rank-2';
                                        elseif ($idx == 2)
                                            $rankClass = 'rank-3';
                                        ?>
                                        <tr>
                                            <td>
                                                <div class="product-cell-wrapper">
                                                    <div class="rank-badge <?= $rankClass ?>">
                                                        <?= $idx + 1 ?>
                                                    </div>

                                                    <img src="<?= !empty($prod->image) ? $prod->image : 'Assets/Images/placeholder-product.jpg' ?>"
                                                        class="product-thumb-img" alt="<?= htmlspecialchars($prod->name) ?>">

                                                    <div class="product-info-text">
                                                        <div class="product-title" title="<?= htmlspecialchars($prod->name) ?>">
                                                            <?= htmlspecialchars($prod->name) ?>
                                                        </div>
                                                        <div class="stock-status">
                                                            <span class="stock-dot"
                                                                style="background: <?= $prod->quantity < 10 ? '#ef4444' : '#22c55e' ?>"></span>
                                                            <?= $lang['dash_stock'] ?>: <?= $prod->quantity ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="price-text">
                                                <?= number_format($prod->price, 0, ',', '.') ?>đ
                                            </td>

                                            <td>
                                                <div class="sold-wrapper">
                                                    <span class="sold-number"><?= $prod->sold ?></span>
                                                    <div class="progress-bg">
                                                        <div class="progress-bar" style="width: <?= $percent ?>%;"></div>
                                                    </div>
                                                </div>
                                            </td>

                                            <td style="text-align: right; padding-right: 10px;">
                                                <span class="revenue-text">
                                                    <?= number_format($prod->price * $prod->sold, 0, ',', '.') ?>đ
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-box-open" style="font-size: 40px; color: #eee; margin-bottom: 10px;"></i>
                                <p><?= $lang['dash_no_data'] ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    function switchTab(tabName, element) {
        // 1. Xử lý giao diện Tab menu
        const navItems = document.querySelectorAll('.shop-nav ul li');
        navItems.forEach(item => item.classList.remove('active'));
        if (element) element.classList.add('active');

        // 2. Lấy 2 vùng nội dung
        const productSection = document.getElementById('section-products');
        const statsSection = document.getElementById('section-stats');

        if (tabName === 'stats' && statsSection) {
            // --- BẬT CHẾ ĐỘ THỐNG KÊ ---
            productSection.style.setProperty('display', 'none', 'important');
            statsSection.style.display = 'block';
        } else {
            // --- BẬT CHẾ ĐỘ SẢN PHẨM ---
            if (productSection) productSection.style.display = 'block';
            if (statsSection) statsSection.style.display = 'none';
        }
    }
</script>

<?php
require_once "./View/Layout/Footer.php";
?>