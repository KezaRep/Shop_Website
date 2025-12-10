<?php
// Load ngôn ngữ
if (!isset($lang)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}

if (empty($_SESSION['user'])) {
    header('Location: index.php?controller=user&action=login');
    exit;
}

$user = $_SESSION['user'];
?>

<link rel="stylesheet" href="Assets/Css/User/Edit.css">
<main class="profile-container">
    <h2 class="page-title"><?= $lang['acct_page_title'] ?></h2>

    <div class="account-grid">
        <div class="info-card">
            <div class="card-header">
                <h3><?= $lang['acct_personal_info'] ?></h3>
                <a href="index.php?controller=user&action=edit" class="link-edit"><?= $lang['acct_btn_edit'] ?></a>
            </div>

            <div class="card-body">
                <div class="user-display-name">
                    <?= htmlspecialchars($user['username'] ?? $lang['acct_no_name']) ?>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" id="sms-notif" name="sms-notif">
                    <label for="sms-notif"><?= $lang['acct_sms_notif'] ?></label>
                </div>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header">
                <h3><?= $lang['acct_address_book'] ?></h3>
                <a href="index.php?controller=user&action=address" class="link-edit"><?= $lang['acct_btn_manage'] ?></a>
            </div>

            <div class="card-body address-container">

                <div class="address-col">
                    <div class="addr-label"><?= $lang['acct_default_shipping'] ?></div>

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
                            echo $lang['acct_no_address'];
                        }
                        ?>
                    </div>

                    <div class="addr-detail">
                        (+84) <?= htmlspecialchars($defaultAddress['phone'] ?? $user['phone'] ?? '---') ?>
                    </div>
                </div>

                <div class="address-col has-border">
                    <div class="addr-label"><?= $lang['acct_default_billing'] ?></div>

                    <div class="addr-name">
                        <?= htmlspecialchars($defaultAddress['name'] ?? $user['fullname'] ?? $user['username']) ?>
                    </div>

                    <div class="addr-detail">
                        <?php
                        if (!empty($defaultAddress['address'])) {
                            echo htmlspecialchars($defaultAddress['address']);
                        } else {
                            echo htmlspecialchars($user['billing_address'] ?? $user['address'] ?? $lang['acct_same_as_shipping']);
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