<?php
// Load ngôn ngữ
if (!isset($lang)) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
    $lang = include "Assets/Lang/$current_lang.php";
}
// Optional: set page title for header if Header.php reads it
$headerTitle = $lang['add_prod_title'];
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= $lang['add_prod_title'] ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Product/Add.css">
</head>

<body>
    <main class="add-product-page">
        <div class="container">
            <h2><?= $lang['add_prod_heading'] ?></h2>

            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="index.php?controller=product&action=add" method="post" enctype="multipart/form-data"
                class="add-product-form">
                <div class="form-row">
                    <label for="name"><?= $lang['add_prod_name'] ?></label>
                    <input id="name" name="name" type="text" required>
                </div>

                <div class="form-row">
                    <label for="price"><?= $lang['add_prod_price'] ?></label>
                    <input id="price" name="price" type="number" step="0.01" required>
                </div>

                <div class="form-row">
                    <label for="image"><?= $lang['add_prod_image'] ?></label>
                    <div class="current-image" id="currentImageArea" style="display:none;">
                        <img id="previewCurrent" src="" alt="preview" style="display:block">
                        <div class="preview-meta" id="previewMeta"></div>
                        <button type="button" id="previewRemove" class="preview-remove" style="display:none"><?= $lang['add_prod_preview_remove'] ?></button>
                    </div>
                    <input id="image" name="image" type="file" accept="image/*">

                    <small class="hint"><?= $lang['add_prod_img_hint'] ?></small>
                </div>
                <div class="form-row">
                    <label for="video"><?= $lang['add_prod_video'] ?></label>

                    <div class="current-video" id="currentVideoArea" style="display:none; margin-bottom: 10px;">
                        <video id="previewVideo" controls style="width: 300px; max-height: 200px; border-radius: 6px; border: 1px solid #ccc; background: #000;">
                            Trình duyệt của bạn không hỗ trợ thẻ video.
                        </video>
                        <div class="preview-meta" id="videoMeta" style="margin-top: 5px; font-size: 13px; color: #555;"></div>
                        <button type="button" id="removeVideoBtn" class="preview-remove" style="margin-top: 5px;"><?= $lang['add_prod_video_remove'] ?></button>
                    </div>

                    <input id="video" name="video" type="file" accept="video/mp4,video/x-m4v,video/*">
                    <small class="hint"><?= $lang['add_prod_video_hint'] ?></small>
                </div>

                <div class="form-row">
                    <label for="description"><?= $lang['add_prod_desc'] ?></label>
                    <textarea id="description" name="description" rows="4"></textarea>
                </div>

                <div class="form-row">
                    <label for="quantity"><?= $lang['add_prod_qty'] ?></label>
                    <input id="quantity" name="quantity" type="number" value="1" min="0" required>
                </div>

                <div class="form-row">
                    <label for="category_id"><?= $lang['add_prod_category'] ?></label>
                    <select id="category_id" name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value=""><?= $lang['add_prod_cat_default'] ?></option>

                        <?php
                        if (isset($categories) && !empty($categories)):
                            foreach ($categories as $cat):
                        ?>
                                <option value="<?= $cat->id ?>">
                                    <?= htmlspecialchars($cat->name) ?>
                                </option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= $lang['add_prod_btn_submit'] ?></button>
                    <a class="btn btn-cancel" href="index.php?controller=product&action=list"><?= $lang['add_prod_btn_cancel'] ?></a>
                </div>
            </form>
        </div>
    </main>

    <style>
        /* preview helper styles (quick inline to ensure present) */
        .current-image {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .current-image img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #f0f0f0;
        }

        .preview-meta {
            font-size: 13px;
            color: #555;
        }

        .preview-remove {
            background: #fff;
            border: 1px solid #eee;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            color: #c00;
        }
    </style>

    <script>
        // Truyền thông báo lỗi từ PHP sang JS
        const langData = {
            err_img_type: "<?= $lang['err_img_type'] ?>",
            err_img_size: "<?= $lang['err_img_size'] ?>",
            err_vid_type: "<?= $lang['err_vid_type'] ?>",
            err_vid_size: "<?= $lang['err_vid_size'] ?>"
        };

        (function() {
            // --- XỬ LÝ PREVIEW ẢNH ---
            const input = document.getElementById('image');
            const img = document.getElementById('previewCurrent');
            const area = document.getElementById('currentImageArea');
            const previewMeta = document.getElementById('previewMeta');
            const removeBtn = document.getElementById('previewRemove');

            function humanFileSize(bytes) {
                const thresh = 1024;
                if (Math.abs(bytes) < thresh) return bytes + ' B';
                const units = ['KB', 'MB', 'GB', 'TB'];
                let u = -1;
                do {
                    bytes /= thresh;
                    ++u;
                } while (Math.abs(bytes) >= thresh && u < units.length - 1);
                return bytes.toFixed(1) + ' ' + units[u];
            }

            function showArea() {
                if (area) area.style.display = 'flex';
            }

            function hideArea() {
                if (area) area.style.display = 'none';
                if (img) {
                    img.src = '';
                    img.style.display = 'none';
                }
                if (previewMeta) previewMeta.textContent = '';
                if (removeBtn) removeBtn.style.display = 'none';
            }

            function resetPreview() {
                hideArea();
                if (input) input.value = '';
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', resetPreview);
            }

            if (input) {
                input.addEventListener('change', function(e) {
                    const f = e.target.files && e.target.files[0];
                    if (!f) {
                        resetPreview();
                        return;
                    }

                    if (!f.type || !f.type.startsWith('image/')) {
                        alert(langData.err_img_type);
                        resetPreview();
                        return;
                    }

                    const maxBytes = 2 * 1024 * 1024; // 2MB
                    if (f.size > maxBytes) {
                        alert(langData.err_img_size);
                        resetPreview();
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(ev) {
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
            }

            // --- XỬ LÝ PREVIEW VIDEO ---
            const videoInput = document.getElementById('video');
            const videoPreview = document.getElementById('previewVideo');
            const videoArea = document.getElementById('currentVideoArea');
            const videoMeta = document.getElementById('videoMeta');
            const removeVideoBtn = document.getElementById('removeVideoBtn');

            function resetVideoPreview() {
                videoArea.style.display = 'none';
                videoPreview.src = '';
                if (videoInput) videoInput.value = '';
            }

            if (removeVideoBtn) {
                removeVideoBtn.addEventListener('click', resetVideoPreview);
            }

            if (videoInput) {
                videoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];

                    if (!file) {
                        resetVideoPreview();
                        return;
                    }

                    // Kiểm tra định dạng video
                    if (!file.type.startsWith('video/')) {
                        alert(langData.err_vid_type);
                        resetVideoPreview();
                        return;
                    }

                    // Kiểm tra dung lượng (Ví dụ giới hạn 50MB)
                    const maxBytes = 50 * 1024 * 1024;
                    if (file.size > maxBytes) {
                        alert(langData.err_vid_size);
                        resetVideoPreview();
                        return;
                    }

                    // Tạo URL để preview
                    const fileURL = URL.createObjectURL(file);
                    videoPreview.src = fileURL;
                    videoArea.style.display = 'block';
                    videoMeta.textContent = `${file.name} — ${humanFileSize(file.size)}`;
                });
            }
        })();
    </script>
</body>

</html>