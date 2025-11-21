<?php
// Optional: set page title for header if Header.php reads it
$headerTitle = "Thêm sản phẩm";
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Thêm sản phẩm</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Product/Add.css">
</head>
<body>
<main class="add-product-page">
    <div class="container">
        <h2>Chỉnh sửa thông tin sản phẩm</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?controller=product&action=edit" method="post" enctype="multipart/form-data" class="add-product-form">
            <div class="form-row">
                <label for="name">Tên sản phẩm</label>
                <input id="name" name="name" type="text" required>
            </div>

            <div class="form-row">
                <label for="price">Giá (VNĐ)</label>
                <input id="price" name="price" type="number" step="0.01" required>
            </div>

            <div class="form-row">
                <label for="image">Ảnh sản phẩm</label>
                <input id="image" name="image" type="file" accept="image/*">
                <small class="hint">Ảnh sẽ được lưu vào cơ sở dữ liệu (BLOB)</small>
            </div>

            <div class="form-row">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-row">
                <label for="quantity">Số lượng</label>
                <input id="quantity" name="quantity" type="number" value="1" min="0" required>
            </div>

            <div class="form-row">
                <label for="category_id">Category ID</label>
                <input id="category_id" name="category_id" type="number" value="0" min="0">
            </div>

    

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                <a class="btn btn-cancel" href="index.php?controller=user&action=profile">Hủy</a>
            </div>
        </form>
    </div>
</main>
</body>
</html>