<link rel="stylesheet" href="Assets/Css/User/AddAddress.css">
<div class="add-addr-container">
    <div class="form-box">
        <h2 class="form-title">Thêm địa chỉ mới</h2>

        <form action="index.php?controller=user&action=addAddress" method="POST">
            <div class="form-grid">
                <div class="col-left">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="fullname" class="form-control" placeholder="Họ Tên">
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="Xin vui lòng nhập số điện thoại của bạn">
                    </div>


                    <div class="form-group">
                        <label>Địa chỉ cụ thể (Số nhà, tên đường)</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Ví dụ: Số 12, ngõ 5..."></textarea>
                    </div>
                </div>

                <div class="col-right">
                    <div class="form-group">
                        <label>Tỉnh/ Thành phố</label>
                        <select name="city" id="city" class="form-control" onchange="updateDistricts()">
                            <option value="">Vui lòng chọn tỉnh/thành phố</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quận/ Huyện</label>
                        <select name="district" id="district" class="form-control">
                            <option value="">Vui lòng chọn quận/huyện</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <label style="margin-bottom: 10px;">Lựa chọn tên cho địa chỉ thường dùng:</label>
                        <div class="radio-group-box">
                            <label class="radio-option">
                                <input type="radio" name="address_type" value="office">
                                <span class="radio-face">🏢 Văn phòng</span>
                            </label>

                            <label class="radio-option">
                                <input type="radio" name="address_type" value="home" checked>
                                <span class="radio-face">🏠 Nhà riêng</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="index.php?controller=user&action=address" class="btn btn-cancel">HUỶ</a>
                <button type="submit" class="btn btn-save">LƯU</button>
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
    promise.then(function (result) {
        renderCity(result.data);
    });

    function renderCity(data) {
        for (const x of data) {
            cities.options[cities.options.length] = new Option(x.name, x.name);
        }

        cities.onchange = function () {
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