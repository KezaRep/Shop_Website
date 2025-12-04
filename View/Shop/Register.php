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
            <label class="form-label">Địa chỉ kho hàng <span style="color:red">*</span></label>
            
            <div class="address-group">
                <select id="province" name="province_id" required>
                    <option value="">Chọn Tỉnh / Thành phố</option>
                </select>
                <select id="district" name="district_id" required>
                    <option value="">Chọn Quận / Huyện</option>
                </select>
                <select id="ward" name="ward_id" required>
                    <option value="">Chọn Phường / Xã</option>
                </select>
            </div>

            <input type="text" name="address_detail" class="form-control" placeholder="Số nhà, tên đường, khu phố..." required>
            
            <input type="hidden" name="province_text" id="province_text" value="">
            <input type="hidden" name="district_text" id="district_text" value="">
            <input type="hidden" name="ward_text" id="ward_text" value="">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(data_tinh) {
            if (data_tinh.error == 0) {
                $.each(data_tinh.data, function(key, val) {
                    $("#province").append('<option value="' + val.id + '">' + val.full_name + '</option>');
                });

                $("#province").change(function(e) {
                    var idtinh = $(this).val();
                    var tenTinh = $("#province option:selected").text();
                    $("#province_text").val(tenTinh);

                    $.getJSON('https://esgoo.net/api-tinhthanh/2/' + idtinh + '.htm', function(data_quan) {
                        if (data_quan.error == 0) {
                            $("#district").html('<option value="">Chọn Quận / Huyện</option>');
                            $("#ward").html('<option value="">Chọn Phường / Xã</option>');
                            $.each(data_quan.data, function(key, val) {
                                $("#district").append('<option value="' + val.id + '">' + val.full_name + '</option>');
                            });
                        }
                    });
                });

                $("#district").change(function(e) {
                    var idquan = $(this).val();
                    var tenQuan = $("#district option:selected").text();
                    $("#district_text").val(tenQuan);

                    $.getJSON('https://esgoo.net/api-tinhthanh/3/' + idquan + '.htm', function(data_phuong) {
                        if (data_phuong.error == 0) {
                            $("#ward").html('<option value="">Chọn Phường / Xã</option>');
                            $.each(data_phuong.data, function(key, val) {
                                $("#ward").append('<option value="' + val.id + '">' + val.full_name + '</option>');
                            });
                        }
                    });
                });
                
                $("#ward").change(function(e) {
                    var tenPhuong = $("#ward option:selected").text();
                    $("#ward_text").val(tenPhuong);
                });
            }
        });
    });
</script>

<?php require_once "./View/Layout/Footer.php"; ?>