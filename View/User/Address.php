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
<link rel="stylesheet" href="Assets/Css/User/Address.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="address-page-container">
    <div class="address-box clearfix">
        <div class="address-header">
            <span><?= $lang['address_book_title'] ?></span>
            <div class="header-links">
                <a href="#"><?= $lang['address_set_default_shipping'] ?></a> |
                <a href="#"><?= $lang['address_set_default_billing'] ?></a>
            </div>
        </div>

        <div class="alert-warning">
            <?= $lang['address_auto_update_warning'] ?>
        </div>

        <table class="table-address">
            <thead>
                <tr>
                    <th width="18%"><?= $lang['address_col_name'] ?></th>
                    <th width="35%"><?= $lang['address_col_address'] ?></th>
                    <th width="25%"><?= $lang['address_col_zip'] ?></th>
                    <th width="12%"><?= $lang['address_col_phone'] ?></th>
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
                                <?php if ($row['label'] == 'Nhà riêng' || $row['label'] == 'Home'): ?>
                                    <span class="tag-type tag-home"><?= $lang['addr_type_home'] ?></span>
                                <?php else: ?>
                                    <span class="tag-type tag-office"><?= $lang['addr_type_office'] ?></span>
                                <?php endif; ?>

                                <?= htmlspecialchars($row['address']) ?>
                            </td>

                            <td>
                                <span style="color: #777; font-size: 12px;"><?= $lang['address_zip_note'] ?></span>
                            </td>

                            <td><?= htmlspecialchars($row['phone']) ?></td>

                            <td>
                                <div class="action-group">
                                    <div class="action-links">
                                        <a href="index.php?controller=user&action=editAddress&id=<?= $row['id'] ?>"><?= $lang['address_btn_edit'] ?></a>

                                        <a href="javascript:void(0);"
                                            onclick="confirmDelete('index.php?controller=user&action=deleteAddress&id=<?= $row['id'] ?>')"
                                            style="color:#ff424e;"><?= $lang['address_btn_delete'] ?></a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            <?= $lang['address_empty_list'] ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="index.php?controller=user&action=addAddress" class="btn-add-new"><?= $lang['address_btn_add_new'] ?></a>
    </div>
</div>
<script>
    // Truyền biến ngôn ngữ cho SweetAlert
    const langData = {
        title_confirm: "<?= $lang['swal_title_confirm'] ?>",
        text_confirm: "<?= $lang['swal_text_confirm'] ?>",
        btn_confirm: "<?= $lang['swal_btn_confirm'] ?>",
        btn_cancel: "<?= $lang['swal_btn_cancel'] ?>"
    };

    function confirmDelete(deleteUrl) {
        Swal.fire({
            title: langData.title_confirm,
            text: langData.text_confirm,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: langData.btn_confirm,
            cancelButtonText: langData.btn_cancel
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteUrl;
            }
        })
    }
</script>