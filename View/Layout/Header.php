<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nếu chưa có file ngôn ngữ thì mặc định là vi
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
$lang = include "Assets/Lang/$current_lang.php";

$cartCount = 0;
if (!empty($_SESSION['user'])) {
    include_once("Model/Cart/CartModel.php");

    $cartModelHeader = new CartModel();
    $userId = $_SESSION['user']['id'];

    $cartResult = $cartModelHeader->getCartByUser($userId);

    if ($cartResult) {
        $cartCount = mysqli_num_rows($cartResult);
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Shop_Website/Assets/Css/Layout/Header.css">
    <link rel="stylesheet" href="/Shop_Website/Assets/Css/Notify/Toast.css">
</head>

<body>
    <header class="site-header">
        <div class="site-header__inner">
            <div class="site-logo">
                <i class="fas fa-leaf"></i>
                <span>Gitraell</span>
            </div>

            <nav class="site-nav">
                <a href="index.php?controller=product&action=list" class="nav-link"><?= $lang['menu_product'] ?></a>
                <a href="index.php?controller=map&action=index" class="nav-link"><?= $lang['menu_discover'] ?></a>

                <?php
                if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 0): ?>
                    <a href="index.php?controller=shop&action=register" class="nav-link" style="color: #ee4d2d; font-weight: bold;"><?= $lang['menu_register_seller'] ?></a>
                <?php endif; ?>
                <?php
                if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 1): ?>
                    <a href="index.php?controller=shop&action=profile&id=<?= $_SESSION['user']['id'] ?>" class="nav-link"><?= $lang['menu_seller_channel'] ?></a>
                <?php endif; ?>
            </nav>

            <div class="header-right">
                <form action="index.php" method="GET" class="search-box" id="searchForm">
                    <input type="hidden" name="controller" value="product">
                    <input type="hidden" name="action" value="list">

                    <input type="text" name="keyword" id="searchInput"
                        value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>"
                        placeholder="<?= $lang['search_placeholder'] ?>" class="search-input">

                    <button type="button" id="micBtn" onclick="startVoiceSearch()" title="<?= $lang['voice_search'] ?>">
                        <i class="fas fa-microphone"></i>
                    </button>

                    <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="language-dropdown">
                <button class="header-icon lang-btn" onclick="toggleLangMenu()" title="Đổi ngôn ngữ">
                    <i class="fas fa-globe"></i>
                </button>
                <div class="lang-menu" id="langMenu">
                    <a href="index.php?lang=vi">
                        <img src="/Shop_Website/Assets/Uploads/Vietnam.png" alt="VN"> Tiếng Việt
                    </a>
                    <a href="index.php?lang=en">
                        <img src="/Shop_Website/Assets/Uploads/English.png" alt="EN"> English
                    </a>
                </div>
            </div>

            <?php if (!empty($_SESSION['user'])): ?>
                <a href="index.php?controller=user&action=profile" class="header-icon user-icon" title="<?= $lang['profile'] ?>">
                    <i class="fas fa-user-circle"></i>
                    <span class="username"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?></span>
                </a>
            <?php else: ?>
                <a href="index.php?controller=user&action=login" class="header-icon" title="<?= $lang['login'] ?>">
                    <i class="fas fa-user"></i>
                </a>
            <?php endif; ?>

            <a href="index.php?controller=cart&action=index" class="header-icon cart-icon" title="<?= $lang['cart'] ?>" id="cartBtn">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge" id="cartCount"><?php echo $cartCount ?></span>
            </a>

            <div class="mini-cart" id="miniCart">
                <div id="miniCartContent">
                    <em><?= $lang['cart_empty'] ?></em>
                </div>
            </div>
        </div>
        </div>
    </header>

    <script>
        function startVoiceSearch() {
            var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

            if (!SpeechRecognition) {
                alert("Trình duyệt không hỗ trợ tìm kiếm giọng nói (Hãy thử Chrome/Edge)");
                return;
            }

            var recognition = new SpeechRecognition();
            var micBtn = document.getElementById('micBtn');
            var searchInput = document.getElementById('searchInput');
            var searchForm = document.getElementById('searchForm');

            recognition.lang = 'vi-VN';
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.start();

            micBtn.classList.add('listening-animation');

            recognition.onresult = function(event) {
                var transcript = event.results[0][0].transcript;

                transcript = transcript.replace(/[.,;!?]$/, '');

                searchInput.value = transcript;

                searchForm.submit();
            };

            recognition.onend = function() {
                micBtn.classList.remove('listening-animation');
            };

            recognition.onerror = function(event) {
                console.error("Voice Error:", event.error);
                micBtn.classList.remove('listening-animation');
            };
        }

        function toggleLangMenu() {
            document.getElementById("langMenu").classList.toggle("show");
        }

        // Đóng menu nếu click ra ngoài
        window.onclick = function(event) {
            if (!event.target.matches('.lang-btn') && !event.target.matches('.lang-btn i')) {
                var dropdowns = document.getElementsByClassName("lang-menu");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>

    <div class="toast-container" id="toastContainer"></div>

        <script>
        // Hàm hiển thị toast đẹp
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <div class="message">${message}</div>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Tự động xóa sau 4 giây
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 600);
            }, 4000);
        }

        // Hiển thị toast nếu có flash từ PHP
        <?php 
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['flash_success'])): ?>
            showToast("<?= htmlspecialchars($_SESSION['flash_success']) ?>", "success");
            <?php unset($_SESSION['flash_success']); ?>
        <?php elseif (isset($_SESSION['flash_error'])): ?>
            showToast("<?= htmlspecialchars($_SESSION['flash_error']) ?>", "error");
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        </script>
</body>

</html>