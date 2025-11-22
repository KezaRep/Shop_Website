<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect nếu chưa đăng nhập
if (empty($_SESSION['user'])) {
    header('Location: index.php?controller=user&action=login');
    exit;
}

function productImageSrc($img) {
    if (empty($img)) return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
    if (@getimagesizefromstring($img)) {
        return 'data:image/jpeg;base64,' . base64_encode($img);
    }
    return $img;
}
?>
<link rel="stylesheet" href="/Shop_Website/Assets/Css/User/Edit.css">
<main class="edit-product-page">
    <div class="container">
        <h2></h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?controller=product&action=edit&id=<?= htmlspecialchars($product->id) ;?>" method="post" enctype="multipart/form-data" class="edit-product-form">
            <?php if ($product): ?>
                <input type="hidden" name="id" value="<?= intval($product->id) ?>">
            <?php endif; ?>

            <div class="row">
                <label for="name">Tên sản phẩm</label>
                <input id="name" name="name" type="text" value="<?= htmlspecialchars($product->name ?? '') ?>" required>
            </div>

            <div class="row">
                <label for="price">Giá (VNĐ)</label>
                <input id="price" name="price" type="number" step="1" value="<?= htmlspecialchars($product->price ?? '') ?>" required>
            </div>

            <!-- Chỉ giữ ảnh chính -->
            <div class="row">
                <label for="image">Ảnh chính</label>
                <?php if (!empty($product->image)): ?>
                    <div class="current-image">
                        <img id="previewCurrent" src="<?= productImageSrc($product->image) ?>" alt="current image">
                    </div>
                <?php else: ?>
                    <div class="current-image">
                        <img id="previewCurrent" src="/Shop_Website/Assets/Images/placeholder-product-1.jpg" alt="placeholder">
                    </div>
                <?php endif; ?>
                <input id="image" name="image" type="file" accept="image/*">
                <small class="hint">Chọn ảnh mới để thay thế ảnh hiện tại (tùy model lưu BLOB hoặc path).</small>
            </div>

            <div class="row">
                <label for="description">Mô tả</label>
                <textarea id="description" name="description" rows="6"><?= htmlspecialchars($product->description ?? '') ?></textarea>
            </div>

            <div class="row inline">
                <div class="col">
                    <label for="quantity">Số lượng</label>
                    <input id="quantity" name="quantity" type="number" value="<?= intval($product->quantity ?? 1) ?>" min="0" required>
                </div>

                <div class="col">
                    <label for="category_id">Category ID</label>
                    <input id="category_id" name="category_id" type="number" value="<?= intval($product->category_id ?? 0) ?>" min="0">
                </div>
            </div>

            <div class="row">
                <label for="status">Trạng thái</label>
                <select id="status" name="status">
                    <option value="1" <?= (isset($product->status) && $product->status==1) ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="0" <?= (isset($product->status) && $product->status==0) ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a class="btn btn-cancel" href="index.php?controller=user&action=profile">Hủy</a>
            </div>
        </form>
    </div>
</main>

<script>
document.getElementById('image')?.addEventListener('change', function (e) {
    const f = e.target.files[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = document.getElementById('previewCurrent');
        if (img) img.src = ev.target.result;
    };
    reader.readAsDataURL(f);
});
</script>

