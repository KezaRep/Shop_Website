<?php
require_once "./View/Layout/Header.php";

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
    if ($diff < 60)
        return 'Vừa xong';
    $years = floor($diff / (365 * 60 * 60 * 24));
    if ($years > 0)
        return $years . ' Năm trước';
    $months = floor($diff / (30 * 60 * 60 * 24));
    if ($months > 0)
        return $months . ' Tháng trước';
    $days = floor($diff / (60 * 60 * 24));
    if ($days > 0)
        return $days . ' Ngày trước';
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
                <div class="stat-item">
                    <i class="fas fa-box stat-icon"></i> Sản Phẩm:
                    <span class="stat-value"><?= isset($totalProducts) ? $totalProducts : 0 ?></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-users stat-icon"></i> Người Theo Dõi:
                    <span class="stat-value"><?= $shop->follower_count ?? 0 ?></span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-star stat-icon"></i> Đánh Giá:
                    <span class="stat-value"><?= $shop->rating ?? '5.0' ?> (Draft)</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-comment-dots stat-icon"></i> Tỉ Lệ Phản Hồi:
                    <span class="stat-value"><?= $shop->response_rate ?? 100 ?>%</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-clock stat-icon"></i> Tham Gia:
                    <span class="stat-value"><?= timeAgo($shop->created_at) ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="shop-nav">
        <div class="container-shop">
            <ul>
                <li class="active">Dạo</li>
                <li>Sản phẩm</li>
                <li>Danh mục</li>
            </ul>
        </div>
    </div>

    <div class="container-shop">
        <h3 class="section-title">GỢI Ý CHO BẠN</h3>

        <?php if (!empty($products)): ?>
            <div class="product-grid">
                <?php foreach ($products as $p): ?>
                    <a href="index.php?controller=product&action=detail&id=<?= $p->id ?>" class="product-card"
                        style="text-decoration: none; color: inherit;">
                        <div class="product-img">
                            <?php
                            $imgSrc = 'Assets/Images/placeholder-product-1.jpg';
                            if (!empty($p->image)) {
                                if (file_exists($p->image)) {
                                    $imgSrc = $p->image;
                                } elseif (strpos($p->image, 'data:image') === 0) {
                                    $imgSrc = $p->image;
                                }
                            }
                            ?>
                            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($p->name) ?>">
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
</div>

<?php
require_once "./View/Layout/Footer.php";
?>