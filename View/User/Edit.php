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
                <a href="index.php?controller=user&action=address" class="link-edit">Chỉnh sửa</a>
            </div>

            <div class="card-body address-container">
                
                <div class="address-col">
                    <div class="addr-label">Địa chỉ nhận hàng mặc định</div>
                    
                    <div class="addr-name"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></div>
                    
                    <div class="addr-detail">
                        <?= htmlspecialchars($user['address'] ?? 'Kiệt 4 thôn Xuân Thiên Thượng') ?>
                    </div>
                    <div class="addr-detail">
                        <?= htmlspecialchars($user['city'] ?? 'Thành phố Huế (mới) - Xã Phú Vinh (mới)') ?>
                    </div>
                    <div class="addr-detail">
                        (+84) <?= htmlspecialchars($user['phone'] ?? '0385306400') ?>
                    </div>
                </div>

                <div class="address-col has-border">
                    <div class="addr-label">Địa chỉ thanh toán mặc định</div>
                    
                    <div class="addr-name"><?= htmlspecialchars($user['fullname'] ?? $user['username']) ?></div>
                    
                    <div class="addr-detail">
                        <?= htmlspecialchars($user['billing_address'] ?? $user['address'] ?? 'Kiệt 4 thôn Xuân Thiên Thượng') ?>
                    </div>
                    <div class="addr-detail">
                        <?= htmlspecialchars($user['city'] ?? 'Thành phố Huế (mới) - Xã Phú Vinh (mới)') ?>
                    </div>
                    <div class="addr-detail">
                        (+84) <?= htmlspecialchars($user['phone'] ?? '0385306400') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>