<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lí Gitraell</title>
    <link rel="stylesheet" href="Assets/css/Admin/AdminDashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="stats-grid">
            <div class="stat-card users">
                <h2>Tổng người dùng</h2>
                <p><?= $totalUsers ?></p>
            </div>

            <div class="stat-card products">
                <h2>Tổng sản phẩm</h2>
                <p><?= $totalProducts ?></p>
            </div>

            <div class="stat-card revenue">
                <h2>Tổng doanh thu</h2>
                <p><?= number_format($totalRevenue, 0, ',', '.') ?> VNĐ</p>
            </div>
        </div>

        <div class="admin-actions">
            <a href="index.php?controller=admin&action=product">🛒 Quản lý sản phẩm</a>
            <a href="index.php?controller=admin&action=user">👥 Quản lý người dùng</a>
            <a href="index.php?controller=user&action=logout">🚪 Đăng xuất</a>
        </div>
    </div>
</body>
</html>
