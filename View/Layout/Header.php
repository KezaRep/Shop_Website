<?php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/Shop_Website/Assets/Css/Layout/Header.css">
</head>

<body>
    <!-- Header -->
    <header class="site-header">
        <div class="site-header__inner">
            <!-- Logo -->
            <div class="site-logo">
                <i class="fas fa-leaf"></i>
                <span>Gitraell</span>
            </div>

            <!-- Menu -->
            <nav class="site-nav">
                <a href="index.php?controller=product&action=list" class="nav-link">Sản phẩm</a>
            </nav>

            <!-- Right: Search, User, Cart -->
            <div class="header-right">
                <div class="search-box">
                    <input type="text" placeholder="Tìm kiếm sản phẩm..." class="search-input">
                    <button class="search-btn"><i class="fas fa-search"></i></button>
                </div>

                <?php if (!empty($_SESSION['user'])): ?>
                    <a href="index.php?controller=user&action=profile" class="header-icon user-icon" title="Hồ sơ">
                        <i class="fas fa-user-circle"></i>
                        <span class="username"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?></span>
                    </a>
                <?php else: ?>
                    <a href="index.php?controller=user&action=login" class="header-icon" title="Đăng nhập">
                        <i class="fas fa-user"></i>
                    </a>
                <?php endif; ?>

                <a href="#" class="header-icon cart-icon" title="Giỏ hàng">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge">0</span>
                </a>
            </div>
        </div>
    </header>
</body>

</html>