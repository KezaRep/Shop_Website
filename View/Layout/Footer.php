<?php
// Đảm bảo có biến $lang phòng trường hợp file này được gọi lẻ
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
if (!isset($lang)) {
    $lang = include "Assets/Lang/$current_lang.php";
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Layout/Footer.css">
</head>
<footer class="site-footer">
    <div class="site-footer__inner">
        <div class="footer-cols">
            <div class="footer-col">
                <h4><?= $lang['footer_customer_care'] ?></h4>
                <ul>
                    <li><?= $lang['footer_help_center'] ?></li>
                    <li><?= $lang['footer_blog'] ?></li>
                    <li><?= $lang['footer_mall'] ?></li>
                    <li><?= $lang['footer_buying_guide'] ?></li>
                    <li><?= $lang['footer_selling_guide'] ?></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= $lang['footer_about_title'] ?></h4>
                <ul>
                    <li><?= $lang['footer_about_us'] ?></li>
                    <li><?= $lang['footer_recruitment'] ?></li>
                    <li><?= $lang['footer_terms'] ?></li>
                    <li><?= $lang['footer_privacy'] ?></li>
                    <li><?= $lang['footer_mall'] ?></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= $lang['footer_payment'] ?></h4>
                <div class="footer-logos payment-logos">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-visa.png" alt="Visa">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-mastercard.png" alt="Mastercard">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-amax.png" alt="Amex">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-cod.png" alt="COD">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-spay.png" alt="S Pay">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-spaylater.png" alt="S PayLater">
                </div>

                <h4 style="margin-top:14px"><?= $lang['footer_shipping'] ?></h4>
                <div class="footer-logos ship-logos">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-spx.png" alt="SPX">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-viettel.png" alt="Viettel">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-jt.png" alt="J&T">
                    <img src="/Shop_Website/Assets/img/layout/footer/logo-grab.png" alt="Grab">
                </div>
            </div>

            <div class="footer-col">
                <h4><?= $lang['footer_follow_us'] ?></h4>
                <ul class="social-list">
                    <li><img src="/Shop_Website/Assets/img/layout/footer/fb.png">Facebook</li>
                    <li><img src="/Shop_Website/Assets/img/layout/footer/ig.png">Instagram</li>
                    <li><img src="/Shop_Website/Assets/img/layout/footer/in.png">LinkedIn</li>
                </ul>

                <h4 style="margin-top:14px"><?= $lang['footer_download_app'] ?></h4>
                <div class="app-qr">
                    <img src="/Shop_Website/Assets/img/layout/footer/qr-placeholder.png" alt="QR" class="qr">
                    <div class="app-badges">
                        <img src="/Shop_Website/Assets/img/layout/footer/badge-appstore.png" alt="App Store">
                        <img src="/Shop_Website/Assets/img/layout/footer/badge-playstore.png" alt="Google Play">
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-divider"></div>

        <div class="site-footer__bottom">
            <div class="copyright"><?= $lang['footer_copyright'] ?></div>
            <div class="footer-links">
                <a href="#"><?= $lang['footer_policy_privacy'] ?></a>
                <a href="#"><?= $lang['footer_policy_operation'] ?></a>
                <a href="#"><?= $lang['footer_policy_shipping'] ?></a>
                <a href="#"><?= $lang['footer_policy_return'] ?></a>
            </div>
        </div>
    </div>
</footer>