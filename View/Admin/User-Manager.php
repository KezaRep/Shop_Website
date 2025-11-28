<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
    <link rel="stylesheet" href="Assets/css/Admin/user-manager.css">
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="index.php?controller=admin&action=dashboard">📊 Dashboard</a></li>
                <li><a href="index.php?controller=admin&action=product">🛍 Quản lý sản phẩm</a></li>
                <li class="active"><a href="#">👥 Quản lý người dùng</a></li>
                <li><a href="index.php?controller=user&action=logout">🚪 Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="content">
            <div class="header">
                <h1>Danh sách người dùng</h1>
            </div>

            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số dư (VNĐ)</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)) {
                        foreach ($users as $u) { ?>
                            <tr>
                                <td><?= htmlspecialchars($u->u_id) ?></td>
                                <td><?= htmlspecialchars($u->u_name) ?></td>
                                <td><?= htmlspecialchars($u->u_email) ?></td>
                                <td><?= number_format($u->balance, 0, ',', '.') ?> đ</td>
                                <td><?= $u->u_role == 1 ? 'Admin' : 'User' ?></td>
                                <td><?= htmlspecialchars($u->u_created_at) ?></td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Không có người dùng nào</td></tr>";
                    } ?>
                </tbody>
            </table>
        </main>
    </div>
</body>

</html>