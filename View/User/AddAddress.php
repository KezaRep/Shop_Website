<?php
// Load ngôn ngữ
if (!isset($lang)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}
?>
<link rel="stylesheet" href="Assets/Css/User/AddAddress.css">
<div class="add-addr-container">
    <div class="form-box">
        <h2 class="form-title"><?= $lang['addr_title'] ?></h2>

        <form action="index.php?controller=user&action=addAddress" method="POST">
            <div class="form-grid">
                <div class="col-left">
                    <div class="form-group">
                        <label><?= $lang['addr_fullname'] ?></label>
                        <input type="text" name="fullname" class="form-control" placeholder="<?= $lang['addr_fullname_ph'] ?>">
                    </div>

                    <div class="form-group">
                        <label><?= $lang['addr_phone'] ?></label>
                        <input type="text" name="phone" class="form-control" placeholder="<?= $lang['addr_phone_ph'] ?>">
                    </div>


                    <div class="form-group">
                        <label><?= $lang['addr_detail'] ?></label>
                        <textarea name="address" class="form-control" rows="3" placeholder="<?= $lang['addr_detail_ph'] ?>"></textarea>
                    </div>
                </div>

                <div class="col-right">
                    <div class="form-group">
                        <label><?= $lang['addr_city'] ?></label>
                        <select name="city" id="city" class="form-control" onchange="updateDistricts()">
                            <option value=""><?= $lang['addr_city_default'] ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><?= $lang['addr_district'] ?></label>
                        <select name="district" id="district" class="form-control">
                            <option value=""><?= $lang['addr_district_default'] ?></option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label style="margin-bottom: 10px;"><?= $lang['addr_type_label'] ?></label>
                        <div class="radio-group-box">
                            <label class="radio-option">
                                <input type="radio" name="address_type" value="office">
                                <span class="radio-face"><?= $lang['addr_type_office'] ?></span>
                            </label>

                            <label class="radio-option">
                                <input type="radio" name="address_type" value="home" checked>
                                <span class="radio-face"><?= $lang['addr_type_home'] ?></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?controller=user&action=address" class="btn btn-cancel"><?= $lang['addr_btn_cancel'] ?></a>
                <button type="submit" class="btn btn-save"><?= $lang['addr_btn_save'] ?></button>
            </div>
        </form>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.1/axios.min.js"></script>
<script>
    var cities = document.getElementById("city");
    var districts = document.getElementById("district");
    var Parameter = {
        url: "https://provinces.open-api.vn/api/?depth=2",
        method: "GET",
        responseType: "application/json",
    };

    // Gọi API lấy dữ liệu
    var promise = axios(Parameter);
    promise.then(function(result) {
        renderCity(result.data);
    });

    function renderCity(data) {
        for (const x of data) {
            cities.options[cities.options.length] = new Option(x.name, x.name);
        }

        cities.onchange = function() {
            districts.length = 1; 
            const dataCity = data.filter((n) => n.name === this.value);

            if (this.value != "") {
                const dataWards = dataCity[0].districts;
                for (const w of dataWards) {
                    districts.options[districts.options.length] = new Option(w.name, w.name);
                }
            }
        };
    }
</script>