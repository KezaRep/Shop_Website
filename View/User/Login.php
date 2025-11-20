<?php
$headerTitle = "Đăng nhập";
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/User/Login.css">
</head>

<body>
    <!-- Main Container -->
    <div class="login-wrapper">
        <div class="login-left">
            <div class="logo-large">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h1>Mua sắm thông minh</h1>
            <p>Trải nghiệm mua sắm tốt nhất cùng chúng tôi</p>
        </div>

        <div class="login-right">
            <form action="index.php?controller=user&action=login" method="POST" class="login-box">
                <div class="form-header">
                    <h2>Đăng nhập</h2>
                    <a href="index.php?controller=user&action=register" class="signup-link">Đăng ký</a>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email/Tên đăng nhập</label>
                    <input type="text" name="identifier" placeholder="Nhập email hoặc tên đăng nhập" required>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" placeholder="Nhập mật khẩu" required id="passwordInput">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword(event)"></i>
                    </div>
                </div>

                <a href="#" class="forgot-password">Quên mật khẩu?</a>
                <?php if (!empty($error)) { ?>
                    <div class="error-message"><?= htmlspecialchars($error) ?></div>
                <?php } ?>
                <button type="submit" class="btn">Đăng nhập</button>

                <div class="divider">HOẶC</div>

                <div class="social-login">
                    <button type="button" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                    <button type="button" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </button>
                </div>

                <p class="signup-text">Bạn mới biết đến Shop? <a href="index.php?controller=user&action=register">Đăng
                        ký ngay</a></p>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(event) {
            const passwordInput = document.getElementById('passwordInput');
            const icon = event.target;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

    </script>

</body>

</html>