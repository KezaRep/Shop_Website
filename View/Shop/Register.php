<?php
require_once "./View/Layout/Header.php";

// Đảm bảo biến $lang tồn tại
if (!isset($lang)) {
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}
?>

<link rel="stylesheet" href="Assets/Css/Shop/Register.css">

<div class="register-shop-container">
    <div class="register-header">
        <h2><?= $lang['reg_welcome'] ?></h2>
        <p><?= $lang['reg_subtitle'] ?></p>
    </div>

    <form action="index.php?controller=shop&action=store" method="POST" enctype="multipart/form-data">

        <div class="row-input">
            <div class="form-group col-half">
                <label class="form-label"><?= $lang['reg_shop_name'] ?> <span style="color:red">*</span></label>
                <input type="text" name="shop_name" class="form-control" placeholder="<?= $lang['reg_shop_name_ph'] ?>" required>
                <div class="note"><?= $lang['reg_shop_name_note'] ?></div>
            </div>
            <div class="form-group col-half">
                <label class="form-label"><?= $lang['reg_phone'] ?> <span style="color:red">*</span></label>
                <input type="text" name="phone" class="form-control" placeholder="<?= $lang['reg_phone_ph'] ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label"><?= $lang['reg_address'] ?> <span style="color:red">*</span></label>

            <div class="address-group">
                <select id="province" name="province_id" required>
                    <option value=""><?= $lang['reg_province_default'] ?></option>
                </select>
                <select id="district" name="district_id" required>
                    <option value=""><?= $lang['reg_district_default'] ?></option>
                </select>
                <select id="ward" name="ward_id" required>
                    <option value=""><?= $lang['reg_ward_default'] ?></option>
                </select>
            </div>

            <input type="text" name="address_detail" class="form-control" placeholder="<?= $lang['reg_address_detail_ph'] ?>" required>

            <input type="hidden" name="province_text" id="province_text" value="">
            <input type="hidden" name="district_text" id="district_text" value="">
            <input type="hidden" name="ward_text" id="ward_text" value="">
        </div>
        <div class="form-group">
            <label class="form-label"><?= $lang['reg_desc'] ?></label>
            <textarea name="description" class="form-control" rows="4" placeholder="<?= $lang['reg_desc_ph'] ?>"></textarea>
        </div>

        <div class="row-input">
            <div class="form-group col-half">
                <label class="form-label"><?= $lang['reg_avatar'] ?></label>
                <div class="upload-box">
                    <input type="file" name="avatar" accept="image/*" style="width: 100%;">
                </div>
            </div>
            <div class="form-group col-half">
                <label class="form-label"><?= $lang['reg_cover'] ?></label>
                <div class="upload-box">
                    <input type="file" name="cover_image" accept="image/*" style="width: 100%;">
                </div>
            </div>
        </div>

        <button type="submit" class="btn-submit-shop"><?= $lang['reg_btn_submit'] ?></button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Truyền biến ngôn ngữ vào JS để xử lý dropdown
    const langData = {
        province_default: "<?= $lang['reg_province_default'] ?>",
        district_default: "<?= $lang['reg_district_default'] ?>",
        ward_default: "<?= $lang['reg_ward_default'] ?>"
    };

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
                            // Sử dụng biến langData
                            $("#district").html('<option value="">' + langData.district_default + '</option>');
                            $("#ward").html('<option value="">' + langData.ward_default + '</option>');
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
                            // Sử dụng biến langData
                            $("#ward").html('<option value="">' + langData.ward_default + '</option>');
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