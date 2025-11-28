<?php
include_once './View/Layout/Header.php';
?>

<style>
    .page-404 {
        padding: 80px 0;
        text-align: center;
        background: #fff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 60vh; /* Đẩy footer xuống dưới */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .page-404 h1 {
        font-size: 120px;
        margin: 0;
        font-weight: 900;
        color: #e6e6e6; /* Màu xám nhạt làm nền */
        line-height: 1;
        position: relative;
    }

    .page-404 h1 span {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 24px;
        color: #333;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 2px;
        white-space: nowrap;
    }

    .page-404 p {
        color: #666;
        font-size: 16px;
        margin-top: 20px;
        margin-bottom: 30px;
        max-width: 400px;
    }

    .btn-home {
        display: inline-block;
        padding: 12px 35px;
        background: #333; /* Màu đen hoặc đổi thành màu chủ đạo của shop */
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s;
        font-weight: 500;
    }

    .btn-home:hover {
        background: #555;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        color: #fff;
    }
</style>

<div class="container"> <div class="page-404">
        <h1>
            404
            <span>Page Not Found</span>
        </h1>
        
        <h3>Rất tiếc, trang bạn tìm kiếm không tồn tại!</h3>
        <p>Có thể đường dẫn đã bị hỏng hoặc trang này đã bị xóa. Hãy thử quay lại trang chủ để tiếp tục mua sắm nhé.</p>
        
        <a href="index.php" class="btn-home">
            QUAY VỀ TRANG CHỦ
        </a>
    </div>
</div>

<?php
// 4. Nhúng Footer
include_once './View/Layout/Footer.php';
?>