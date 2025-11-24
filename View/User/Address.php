<link rel="stylesheet" href="Assets/Css/User/Address.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <tbody class="table-body">
                <?php
                if ($addresses && $addresses->num_rows > 0):
                ?>
                    <?php while ($row = $addresses->fetch_assoc()): ?>
                        <tr>
                            <td style="font-weight: 500;">
                                <?= htmlspecialchars($row['name']) ?>
                            </td>

                            <td>
                                <?php if ($row['label'] == 'Nhà riêng'): ?>
                                    <span class="tag-type tag-home">Nhà riêng</span>
                                <?php else: ?>
                                    <span class="tag-type tag-office">Văn phòng</span>
                                <?php endif; ?>

                                <?= htmlspecialchars($row['address']) ?>
                            </td>

                            <td>
                                <span style="color: #777; font-size: 12px;">(Đã gộp trong địa chỉ)</span>
                            </td>

                            <td><?= htmlspecialchars($row['phone']) ?></td>

                            <td>
                                <div class="action-group">
                                    <div class="action-links">
                                        <a href="index.php?controller=user&action=editAddress&id=<?= $row['id'] ?>">Chỉnh sửa</a>

                                        <a href="javascript:void(0);"
                                            onclick="confirmDelete('index.php?controller=user&action=deleteAddress&id=<?= $row['id'] ?>')"
                                            style="color:#ff424e;">Xóa</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            Bạn chưa lưu địa chỉ nào. Hãy thêm địa chỉ mới nhé!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php?controller=user&action=addAddress" class="btn-add-new">+ Thêm địa chỉ mới</a>
    </div>
</div>
<script>
    function confirmDelete(deleteUrl) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Xóa địa chỉ này sẽ không thể khôi phục lại!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',   
            cancelButtonColor: '#d33',       
            confirmButtonText: 'Vâng, xóa đi!',
            cancelButtonText: 'Hủy bỏ'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        })
    }
</script>