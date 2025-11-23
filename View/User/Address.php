<link rel="stylesheet" href="Assets/Css/User/Address.css">
<div class="address-page-container">
    <div class="address-box clearfix">
        <div class="address-header">
            <span>Sổ địa chỉ</span>
            <div class="header-links">
                <a href="#">Chọn địa chỉ nhận hàng mặc định</a> |
                <a href="#">Chọn địa chỉ thanh toán mặc định</a>
            </div>
        </div>

        <div class="alert-warning">
            Địa chỉ của bạn đã được tự động cập nhật theo hệ thống địa chỉ mới. Vui lòng kiểm tra lại trước khi đặt đơn.
        </div>

        <table class="table-address">
            <thead>
                <tr>
                    <th width="18%">Họ tên</th>
                    <th width="35%">Địa chỉ</th>
                    <th width="25%">Mã vùng</th>
                    <th width="12%">Số điện thoại</th>
                    <th width="10%"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: 500;">Lê Hoàng Nam</td>
                    <td>
                        <span class="tag-type tag-home">Nhà riêng</span>
                        Kiệt 4 thôn Xuân Thiên Thượng
                    </td>
                    <td>Thành phố Huế (mới) - Xã Phú Vinh (mới)</td>
                    <td>0385306400</td>
                    <td>
                        <div class="action-group">
                            <div class="action-links">
                                <a href="#">Chỉnh sửa</a>
                                <a href="#" style="color:#ff424e;">Xóa</a>
                            </div>
                            <div class="default-text">Địa chỉ nhận hàng mặc định</div>
                            <div class="default-text">Địa chỉ thanh toán mặc định</div>
                        </div>
                    </td>
                </tr>
                
                </tbody>
        </table>

        <a href="index.php?controller=user&action=addAddress" class="btn-add-new">+ Thêm địa chỉ mới</a>
    </div>
</div>