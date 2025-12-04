<?php
require_once "./View/Layout/Header.php";

// --- Helper Functions ---

// Helper xử lý ảnh (tránh lỗi nếu ảnh rỗng)
function getImgUrl($path)
{
    if (!empty($path) && file_exists($path)) {
        return $path;
    }
    return 'https://via.placeholder.com/150'; // Ảnh mặc định nếu lỗi
}

// Helper tính thời gian tham gia (VD: 3 năm trước)
function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return 'Vừa xong';

    $years = floor($diff / (365 * 60 * 60 * 24));
    if ($years > 0) return $years . ' Năm trước';

    $months = floor($diff / (30 * 60 * 60 * 24));
    if ($months > 0) return $months . ' Tháng trước';

    $days = floor($diff / (60 * 60 * 24));
    if ($days > 0) return $days . ' Ngày trước';

    return 'Mới tham gia';
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
                        <?= ($shop->is_online ?? 1) ? 'Online' : 'Offline' ?>
                    </span>
                    <div>
                        <button class="btn-shop-action">+ Theo Dõi</button>
                        <button class="btn-shop-action">+Thêm sản phẩm</button>
                    </div>
                </div>
            </div>

            <div class="shop-stats">
                <div class="stat-item"><i class="fas fa-box stat-icon"></i> Sản Phẩm: <span class="stat-value"><?= isset($totalProducts) ? $totalProducts : 0 ?></span></div>
                <div class="stat-item"><i class="fas fa-users stat-icon"></i> Người Theo Dõi: <span class="stat-value"><?= $shop->follower_count ?? 0 ?></span></div>
                <div class="stat-item"><i class="fas fa-star stat-icon"></i> Đánh Giá: <span class="stat-value"><?= $shop->rating ?? '5.0' ?></span></div>
                <div class="stat-item"><i class="fas fa-clock stat-icon"></i> Tham Gia: <span class="stat-value"><?= timeAgo($shop->created_at) ?></span></div>
            </div>
        </div>
    </div>

    <div class="shop-nav">
        <div class="container-shop">
            <ul>
                <li onclick="switchTab('products', this)" class="active">Dạo</li>
                <li onclick="switchTab('products', this)">Sản phẩm</li>
                <li onclick="switchTab('products', this)">Danh mục</li>

                <?php if (isset($isOwner) && $isOwner): ?>
                    <li onclick="switchTab('stats', this)" style="margin-left: auto; color: #333;">
                        <i class="fas fa-chart-line"></i> Thống kê doanh số
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="container-shop">

        <div id="section-products" class="active-section">
            <h3 class="section-title">GỢI Ý CHO BẠN</h3>
            <?php if (!empty($products)): ?>
                <div class="product-grid">
                    <?php foreach ($products as $p): ?>
                        <a href="index.php?controller=product&action=detail&id=<?= $p->id ?>" class="product-card" style="text-decoration: none; color: inherit;">
                            <div class="product-img">
                                <img src="<?= !empty($p->image) ? $p->image : 'Assets/Images/placeholder-product-1.jpg' ?>" alt="<?= htmlspecialchars($p->name) ?>">
                            </div>
                            <div class="product-details">
                                <div class="product-name"><?= htmlspecialchars($p->name) ?></div>
                                <div class="product-price">
                                    <span>₫<?= number_format($p->price, 0, ',', '.') ?></span>
                                    <span class="product-sold">Đã bán <?= $p->sold ?? 0 ?></span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="padding: 50px; text-align: center; color: #777;">
                    Shop này chưa đăng bán sản phẩm nào.
                </div>
            <?php endif; ?>
        </div>

        <?php if (isset($isOwner) && $isOwner): ?>
            <div id="section-stats" class="dashboard-wrapper" style="display: none;">
                <div class="stats-grid">

                    <div class="d-card blue">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <h4>Doanh Thu</h4>
                                <div class="value">
                                    <?= number_format($revenue ?? 0, 0, ',', '.') ?>đ
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    <span style="color: #10b981;"><i class="fas fa-arrow-up"></i></span> Tổng doanh thu shop
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
                                <h4>Đơn Hàng</h4>
                                <div class="value">
                                    <?= $newOrdersCount ?? 0 ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    Đơn hàng đang có
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
                                <h4>Đã Bán</h4>
                                <div class="value">
                                    <?= number_format($totalSold ?? 0) ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    Sản phẩm đã bán ra
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
                                <h4 style="color: #dc2626;">Sắp Hết Hàng</h4>
                                <div class="value">
                                    <?= $lowStockCount ?? 0 ?>
                                    <?php if (isset($lowStockCount) && $lowStockCount > 0): ?>
                                        <span class="badge" style="background: #fee2e2; color: #dc2626; font-size: 0.7rem;">Cần nhập</span>
                                    <?php endif; ?>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                    Tồn kho dưới 10
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
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h4 style="margin: 0; color: #333;">Biểu đồ tăng trưởng</h4>
                            <button style="border: none; background: #3b82f6; color: white; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-download"></i> Xuất Báo Cáo
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
                            // 1. TÍNH TOÁN DỮ LIỆU
                            $maxVal = 0;
                            if (!empty($chartData)) {
                                foreach ($chartData as $day) {
                                    if ($day['value'] > $maxVal) $maxVal = $day['value'];
                                }
                            }
                            // Tránh lỗi chia cho 0
                            if ($maxVal == 0) $maxVal = 1;

                            // 2. VẼ CỘT
                            if (!empty($chartData)):
                                foreach ($chartData as $day):
                                    // Tính % chiều cao (Tối đa 80% khung hình để chừa chỗ cho số tiền)
                                    $percent = ($day['value'] / $maxVal) * 85;

                                    // Format tiền (1.5M, 500k)
                                    $displayMoney = $day['value'];
                                    if ($day['value'] >= 1000000) {
                                        $displayMoney = round($day['value'] / 1000000, 1) . 'M';
                                    } elseif ($day['value'] >= 1000) {
                                        $displayMoney = round($day['value'] / 1000, 0) . 'k';
                                    } else {
                                        $displayMoney = $day['value'];
                                    }

                                    // Kiểm tra có dữ liệu không để đổi màu
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
                                    Chưa có dữ liệu
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="content-box">
                        <h4 style="margin: 0 0 20px 0; color: #333;">Đơn hàng mới nhất</h4>
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
                                                    <?= htmlspecialchars($order->recipient_name ?? 'Khách lẻ') ?>
                                                </span>
                                                <span style="font-size: 0.7rem; padding: 2px 6px; border-radius: 4px; background: <?= $order->status == 'completed' ? '#d1fae5' : '#fee2e2' ?>; color: <?= $order->status == 'completed' ? '#059669' : '#dc2626' ?>;">
                                                    <?= $order->status == 'completed' ? 'Hoàn thành' : 'Chờ xử lý' ?>
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
                                <p style="text-align:center; color:#999; padding: 20px;">Chưa có đơn hàng nào.</p>
                            <?php endif; ?>
                        </div>
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