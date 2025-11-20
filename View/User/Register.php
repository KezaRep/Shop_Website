<?php
$headerTitle = "Đăng ký";
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/User/Register.css">
</head>

<body>
    <!-- Main Container -->
    <div class="register-wrapper">
        <div class="register-left">
            <div class="logo-large">
                <i class="fas fa-user-plus"></i>
            </div>
            <h1>Tham gia cùng chúng tôi</h1>
            <p>Tạo tài khoản để bắt đầu mua sắm ngay hôm nay</p>
        </div>

        <div class="register-right">
            <form action="index.php?controller=user&action=register" method="POST" class="register-box">
                <div class="form-header">
                    <h2>Đăng ký</h2>
                    <a href="index.php?controller=user&action=login" class="login-link">Đăng nhập</a>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-user"></i> Tên người dùng</label>
                    <input type="text" name="username" placeholder="Nhập tên người dùng" required>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" name="email" placeholder="Nhập email của bạn" required>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" placeholder="Nhập mật khẩu" required id="passwordInput">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                    </div>
                </div>

                <div class="input-group">
                    <label><i class="fas fa-lock"></i> Xác nhận mật khẩu</label>
                    <div class="password-wrapper">
                        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required
                            id="confirmPasswordInput">
                        <i class="fas fa-eye toggle-password-confirm" onclick="togglePasswordConfirm()"></i>
                    </div>
                </div>

                <button type="submit" class="btn">Tạo tài khoản</button>

                <div class="divider">HOẶC</div>

                <div class="social-login">
                    <button type="button" class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                    <button type="button" class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </button>
                </div>

                <p class="login-text">Bạn đã có tài khoản? <a href="index.php?controller=user&action=login">Đăng nhập
                        ngay</a></p>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
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

        function togglePasswordConfirm() {
            const confirmPasswordInput = document.getElementById('confirmPasswordInput');
            const icon = event.target;

            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>

</body>

</html>