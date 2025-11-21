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
            <h2>Thêm sản phẩm mới</h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="index.php?controller=product&action=add" method="post" enctype="multipart/form-data"
                class="add-product-form">
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
                    <div class="current-image" id="currentImageArea" style="display:none;">
                        <img id="previewCurrent" src="" alt="preview" style="display:block">
                        <div class="preview-meta" id="previewMeta"></div>
                        <button type="button" id="previewRemove" class="preview-remove" style="display:none">Xóa</button>
                    </div>
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
                    <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
                    <a class="btn btn-cancel" href="index.php?controller=product&action=list">Hủy</a>
                </div>
            </form>
        </div>
    </main>

    <style>
        /* preview helper styles (quick inline to ensure present) */
        .current-image { display:flex; align-items:center; gap:12px; margin-bottom:8px; }
        .current-image img { width:160px; height:160px; object-fit:cover; border-radius:6px; border:1px solid #f0f0f0; }
        .preview-meta { font-size:13px; color:#555; }
        .preview-remove { background:#fff; border:1px solid #eee; padding:6px 10px; border-radius:6px; cursor:pointer; color:#c00; }
    </style>

    <script>
    (function(){
        const input = document.getElementById('image');
        const img = document.getElementById('previewCurrent');
        const area = document.getElementById('currentImageArea');
        const previewMeta = document.getElementById('previewMeta');
        const removeBtn = document.getElementById('previewRemove');
        const placeholderSrc = ''; // no placeholder

        function humanFileSize(bytes) {
            const thresh = 1024;
            if (Math.abs(bytes) < thresh) return bytes + ' B';
            const units = ['KB','MB','GB','TB'];
            let u = -1;
            do {
                bytes /= thresh;
                ++u;
            } while(Math.abs(bytes) >= thresh && u < units.length - 1);
            return bytes.toFixed(1)+' '+units[u];
        }

        function showArea(){
            if (area) area.style.display = 'flex';
        }

        function hideArea(){
            if (area) area.style.display = 'none';
            if (img) { img.src = ''; img.style.display = 'none'; }
            if (previewMeta) previewMeta.textContent = '';
            if (removeBtn) removeBtn.style.display = 'none';
        }

        function resetPreview(){
            hideArea();
            if (input) input.value = '';
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', resetPreview);
        }

        if (!input) return;
        input.addEventListener('change', function (e) {
            const f = e.target.files && e.target.files[0];
            if (!f) {
                resetPreview();
                return;
            }

            if (!f.type || !f.type.startsWith('image/')) {
                alert('Vui lòng chọn file hình ảnh (jpg/png/gif...).');
                resetPreview();
                return;
            }

            const maxBytes = 2 * 1024 * 1024; // 2MB
            if (f.size > maxBytes) {
                alert('Kích thước ảnh quá lớn. Tối đa 2MB.');
                resetPreview();
                return;
            }

            const reader = new FileReader();
            reader.onload = function (ev) {
                if (img) {
                    img.src = ev.target.result;
                    img.style.display = 'block';
                }
                if (previewMeta) previewMeta.textContent = `${f.name} — ${humanFileSize(f.size)}`;
                if (removeBtn) removeBtn.style.display = 'inline-block';
                showArea();
            };
            reader.readAsDataURL(f);
        });
    })();
    </script>
</body>

</html>