<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['user'])) {
    header('Location: index.php?controller=user&action=login');
    exit;
}

$user = $_SESSION['user'];

?>

<link rel="stylesheet" href="Assets/Css/User/Edit.css">
<main class="profile-container">
    <h2 class="page-title">Quản lý tài khoản</h2>

    <div class="account-grid">
        <div class="info-card">
            <div class="card-header">
                <h3>Thông tin cá nhân</h3>
                <a href="index.php?controller=user&action=edit" class="link-edit">Chỉnh sửa</a>
            </div>

            <div class="card-body">
                <div class="user-display-name">
                    <?= htmlspecialchars($user['username'] ?? 'Chưa cập nhật tên') ?>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="sms-notif" name="sms-notif">
                    <label for="sms-notif">Nhận thông tin ưu đãi qua SMS</label>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header">
                <h3>Sổ địa chỉ</h3>
                <a href="index.php?controller=user&action=address" class="link-edit">Quản lý</a>
            </div>

            <div class="card-body address-container">

                <div class="address-col">
                    <div class="addr-label">Địa chỉ nhận hàng mặc định</div>

                    <div class="addr-name">
                        <?= htmlspecialchars($defaultAddress['name'] ?? $user['fullname'] ?? $user['username']) ?>
                    </div>

                    <div class="addr-detail">
                        <?php
                        if (!empty($defaultAddress['address'])) {
                            echo htmlspecialchars($defaultAddress['address']);
                        } elseif (!empty($user['address'])) {
                            echo htmlspecialchars($user['address']);
                            echo !empty($user['city']) ? ' - ' . htmlspecialchars($user['city']) : '';
                        } else {
                            echo "Chưa thiết lập địa chỉ";
                        }
                        ?>
                    </div>

                    <div class="addr-detail">
                        (+84) <?= htmlspecialchars($defaultAddress['phone'] ?? $user['phone'] ?? '---') ?>
                    </div>
                </div>

                <div class="address-col has-border">
                    <div class="addr-label">Địa chỉ thanh toán mặc định</div>

                    <div class="addr-name">
                        <?= htmlspecialchars($defaultAddress['name'] ?? $user['fullname'] ?? $user['username']) ?>
                    </div>

                    <div class="addr-detail">
                        <?php
                        if (!empty($defaultAddress['address'])) {
                            echo htmlspecialchars($defaultAddress['address']);
                        } else {
                            echo htmlspecialchars($user['billing_address'] ?? $user['address'] ?? 'Giống địa chỉ nhận hàng');
                        }
                        ?>
                    </div>

                    <div class="addr-detail">
                        (+84) <?= htmlspecialchars($defaultAddress['phone'] ?? $user['phone'] ?? '---') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>