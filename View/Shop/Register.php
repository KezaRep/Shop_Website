<?php require_once "./View/Layout/Header.php"; ?>

<link rel="stylesheet" href="Assets/Css/Shop/Register.css">
<div class="register-shop-container">
    <div class="register-header">
        <h2>Chào mừng đến với Gitraell!</h2>
        <p>Để bắt đầu bán hàng, vui lòng cung cấp thông tin cửa hàng của bạn.</p>
    </div>

    <form action="index.php?controller=shop&action=store" method="POST" enctype="multipart/form-data">
        
        <div class="row-input">
            <div class="form-group col-half">
                <label class="form-label">Tên Shop <span style="color:red">*</span></label>
                <input type="text" name="shop_name" class="form-control" placeholder="Ví dụ: Sanxinh.Store" required>
                <div class="note">Tên shop không được trùng với các shop khác.</div>
            </div>
            <div class="form-group col-half">
                <label class="form-label">Số điện thoại liên hệ <span style="color:red">*</span></label>
                <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại shop" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Địa chỉ lấy hàng/Kho <span style="color:red">*</span></label>
            <input type="text" name="address" class="form-control" placeholder="Số nhà, đường, phường/xã, quận/huyện..." required>
        </div>

        <div class="form-group">
            <label class="form-label">Mô tả giới thiệu</label>
            <textarea name="description" class="form-control" rows="4" placeholder="Viết vài dòng giới thiệu về shop của bạn..."></textarea>
        </div>

        <div class="row-input">
            <div class="form-group col-half">
                <label class="form-label">Ảnh đại diện (Avatar)</label>
                <div class="upload-box">
                    <input type="file" name="avatar" accept="image/*" style="width: 100%;">
                </div>
            </div>
            <div class="form-group col-half">
                <label class="form-label">Ảnh bìa (Cover)</label>
                <div class="upload-box">
                    <input type="file" name="cover_image" accept="image/*" style="width: 100%;">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit-shop">ĐĂNG KÝ MỞ SHOP</button>
    </form>
</div>

<?php require_once "./View/Layout/Footer.php"; ?>