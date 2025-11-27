<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("Model/User/UserModel.php");

$product->extra_images = [
    'Assets/Uploads/Products/image2.jpg' 
];

// $product, $related, $comments, $seller expected from controller
function productImageSrc($img)
{
    if (empty($img)) {
        return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
    }

    // Nếu ảnh là đường dẫn file (không phải base64)
    // Kiểm tra xem đã có dấu / ở đầu chưa, nếu chưa thì thêm tên dự án vào
    if (strpos($img, 'Assets/') === 0) {
        return '/Shop_Website/' . $img;
    }

    return $img;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($product->name ?? 'Sản phẩm') ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Product/Detail.css">
</head>

<body>
    <main class="product-detail-page">
        <div class="container">

            <!-- Top: gallery + summary -->
            <div class="product-top">
                <div class="product-gallery">

                    <div class="main-media-viewer" style="width: 100%; aspect-ratio: 1/1; border: 1px solid #eee; border-radius: 4px; overflow: hidden; position: relative; background: #fff;">
                        <?php if (!empty($product->video_url)): ?>
                            <video id="mainVideo" controls style="width: 100%; height: 100%; object-fit: contain; background: #000; display: block;">
                                <source src="/Shop_Website/<?= htmlspecialchars($product->video_url) ?>" type="video/mp4">
                            </video>
                            <img id="mainImage" src="" style="width: 100%; height: 100%; object-fit: contain; display: none;">

                        <?php else: ?>
                            <video id="mainVideo" controls style="width: 100%; height: 100%; object-fit: contain; background: #000; display: none;"></video>

                            <img id="mainImage"
                                src="<?= productImageSrc($product->image ?? '') ?>"
                                style="width: 100%; height: 100%; object-fit: contain; display: block;">
                        <?php endif; ?>
                    </div>

                    <div class="thumbnails-row" style="display: flex; gap: 10px; margin-top: 10px; overflow-x: auto;">

                        <?php if (!empty($product->video_url)): ?>
                            <div class="thumb-item active" onclick="showMainVideo()"
                                style="width: 60px; height: 60px; position: relative; cursor: pointer; border: 2px solid #ee4d2d; border-radius: 4px; overflow: hidden; background: #000;">

                                <img src="<?= productImageSrc($product->image ?? '') ?>"
                                    style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6;">

                                <i class="fas fa-play-circle"
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: #ee4d2d; font-size: 24px; text-shadow: 0 0 5px rgba(0,0,0,0.8); z-index: 10;">
                                </i>
                            </div>
                        <?php endif; ?>

                        <div class="thumb-item" onclick="showMainImage('<?= productImageSrc($product->image ?? '') ?>', this)"
                            style="width: 60px; height: 60px; cursor: pointer; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <img src="/Shop_Website/<?= htmlspecialchars($product->image ?? '') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <?php if (!empty($product->extra_images) && is_array($product->extra_images)): ?>
                            <?php foreach ($product->extra_images as $ei): ?>
                                <div class="thumb-item" onclick="showMainImage('<?= productImageSrc($ei) ?>', this)"
                                    style="width: 60px; height: 60px; cursor: pointer; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <img src="<?= productImageSrc($ei) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                </div>

                <div class="product-summary">
                    <div class="product-header">
                        <span class="badge">Yêu Thích</span>
                        <h1 class="product-title"><?= htmlspecialchars($product->name ?? '') ?></h1>
                    </div>

                    <div class="rating-section">
                        <div class="rating-stars">
                            <span
                                class="stars"><?= htmlspecialchars(number_format($product->rating ?? 4.8, 1)) ?></span>
                            <i class="fas fa-star"></i>
                            <span class="rating-count"><?= htmlspecialchars($product->rating_count ?? '0') ?> Đánh
                                Giá</span>
                        </div>
                        <span class="divider">|</span>
                        <span class="sold">Đã bán <?= intval($product->sold ?? 0) ?></span>
                    </div>

                    <div class="price-section">
                        <div class="price-group">
                            <span class="price-current">₫<?= number_format($product->price ?? 0, 0, ',', '.') ?></span>
                            <span
                                class="price-old">₫<?= number_format(($product->price ?? 0) * 1.2, 0, ',', '.') ?></span>
                            <span class="discount">-12%</span>
                        </div>
                    </div>

                    <div class="shipping-info">
                        <div class="ship-item">
                            <strong>Vận Chuyển</strong>
                            <span>Nhận từ 17 Th11 - 19 Th11, phí giao 0đ</span>
                        </div>
                        <p class="promo-hint">Tặng Voucher 15.000đ nếu đơn giao sau thời gian trên.</p>
                    </div>

                    <div class="product-actions">
                        <div class="quantity-box">
                            <label>Số Lượng</label>
                            <div class="qty-control">
                                <button class="qty-btn" type="button">−</button>
                                <input type="number" id="qty" name="quantity" value="1" min="1"
                                    max="<?= intval($product->quantity ?? 100) ?>">
                                <button class="qty-btn plus" type="button">+</button>
                            </div>
                        </div>

                        <form action="index.php?controller=cart&action=add&product_id=<?= htmlspecialchars($product->id) ?>" method="post" id="addCartForm">
                            <input type="hidden" name="product_id" value="<?= $product->id ?>">
                            <input type="hidden" name="quantity" id="qtyField" value="1" min="1"
                                max="<?= intval($product->quantity ?? 100) ?>">
                            <button type="submit" class="btn-cart"><i class="fas fa-shopping-cart"></i> Thêm Vào Giỏ
                                Hàng</button>
                        </form>
                    </div>

                    <div class="share-section">
                        <span>Chia sẻ:</span>
                        <a href="#" class="social-btn"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-pinterest"></i></a>
                        <a href="#" class="wishlist"><i class="far fa-heart"></i> Đã thích
                            (<?= intval($product->likes ?? 0) ?>)</a>
                    </div>
                </div>
            </div>

            <!-- Seller card -->
            <div class="seller-card">
                <div class="seller-left">
                    <img src="/Shop_Website/Assets/Images/placeholder-avatar.png" alt="seller" class="seller-avatar">
                    <div class="seller-info">
                        <h4><?= htmlspecialchars($seller->username ?? 'Gitraell Shop') ?></h4>
                        <p>Online gần đây</p>
                        <div class="seller-stats">
                            <span>Đánh Giá: <?= htmlspecialchars($seller->rating_count ?? '0') ?></span>
                            <span>Tỷ Lệ Phản Hồi: <?= htmlspecialchars($seller->reply_rate ?? '—') ?></span>
                            <span>Theo Dõi: <?= htmlspecialchars($seller->followers ?? '—') ?></span>
                        </div>
                    </div>
                </div>
                <div class="seller-actions">
                    <button class="btn-msg"><i class="fas fa-comment"></i> Chat Ngay</button>
                    <button class="btn-view"><i class="fas fa-store"></i> Xem Shop</button>
                </div>
            </div>

            <!-- Description (separate) -->
            <section class="product-description">
                <h3>Chi tiết sản phẩm</h3>
                <div class="desc-content">
                    <?= nl2br(htmlspecialchars($product->description ?? 'Không có mô tả')) ?>
                </div>
            </section>

            <!-- Reviews (tab) -->
            <div class="product-tabs">
                <div class="tabs-header">
                    <button class="tab-btn active" data-tab="review">Đánh Giá Sản Phẩm</button>
                </div>

                <div id="review" class="tab-content active">
                    <section id="comments" class="comments-section">
                        <h3>Bình luận sản phẩm</h3>

                        <?php if (!empty($_SESSION['user'])): ?>
                            <form action="index.php?controller=comment&action=add" method="post"
                                enctype="multipart/form-data" class="comment-form">
                                <input type="hidden" name="product_id" value="<?= intval($product->id ?? 0) ?>">

                                <div class="form-group rating-input">
                                    <label>Đánh giá</label>
                                    <div class="stars-input" id="starsInput" aria-hidden="false">
                                        <i class="fas fa-star star" data-value="1"></i>
                                        <i class="fas fa-star star" data-value="2"></i>
                                        <i class="fas fa-star star" data-value="3"></i>
                                        <i class="fas fa-star star" data-value="4"></i>
                                        <i class="fas fa-star star" data-value="5"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="ratingValue" value="5">
                                </div>

                                <div class="form-group">
                                    <label>Nội dung</label>
                                    <textarea name="content" rows="4" placeholder="Chia sẻ cảm nhận của bạn..."
                                        required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Hình ảnh (tối đa 5)</label>
                                    <input type="file" name="images[]" multiple accept="image/*">
                                </div>

                                <button type="submit" class="btn-submit">Gửi bình luận</button>
                            </form>
                        <?php else: ?>
                            <p class="login-prompt"><a href="index.php?controller=user&action=login">Đăng nhập</a> để bình
                                luận</p>
                        <?php endif; ?>

                        <div class="comments-list">
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <article class="comment-item">
                                        <div class="comment-header">
                                            <?php $userModel = new UserModel();
                                            $user = $userModel->getUserById(intval($comment->user_id ?? 0)); ?>
                                            <strong><?= htmlspecialchars($user->username) ?></strong>
                                            <span class="comment-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i
                                                        class="fas fa-star <?= $i <= intval($comment->rating ?? 5) ? 'filled' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </span>
                                        </div>
                                        <div class="comment-content"><?= nl2br(htmlspecialchars($comment->content ?? '')) ?>
                                        </div>
                                        <?php if (!empty($comment->images)): ?>
                                            <div class="comment-images">
                                                <?php foreach ($comment->images as $img): ?>
                                                    <img src="data:image/jpeg;base64,<?= base64_encode($img->image_data ?? '') ?>"
                                                        alt="comment-img" class="comment-img">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                        <small
                                            class="comment-date"><?= date('d/m/Y H:i', strtotime($comment->created_at ?? date('Y-m-d H:i'))) ?></small>
                                    </article>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-comment">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                            <?php endif; ?>
                        </div>
                    </section>
                </div>
            </div>

            <!-- Related -->
            <section class="related">
                <h3>Sản phẩm liên quan</h3>
                <div class="related-grid">
                    <?php if (!empty($related)): ?>
                        <?php foreach (array_slice($related, 0, 4) as $rp): ?>
                            <?php if (intval($rp->id ?? 0) == intval($product->id ?? 0))
                                continue; ?>
                            <article class="related-card">
                                <a href="index.php?controller=product&action=detail&id=<?= intval($rp->id ?? 0) ?>">
                                    <img src="<?= productImageSrc($rp->image ?? '') ?>"
                                        alt="<?= htmlspecialchars($rp->name ?? '') ?>">
                                    <div class="r-title"><?= htmlspecialchars($rp->name ?? '') ?></div>
                                    <div class="r-price">₫<?= number_format($rp->price ?? 0, 0, ',', '.') ?></div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Không có sản phẩm liên quan.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const qtyInput = document.getElementById('qty');
            const qtyField = document.getElementById('qtyField');
            const btnMinus = document.querySelector('.qty-btn.minus') || document.querySelectorAll('.qty-btn')[0];
            const btnPlus = document.querySelector('.qty-btn.plus') || document.querySelectorAll('.qty-btn')[1];

            function updateHiddenField() {
                qtyField.value = qtyInput.value;
            }

            btnMinus.addEventListener('click', function() {
                let currentValue = parseInt(qtyInput.value) || 1;
                if (currentValue > 1) {
                    qtyInput.value = currentValue - 1;
                    updateHiddenField();
                }
            });

            btnPlus.addEventListener('click', function() {
                let currentValue = parseInt(qtyInput.value) || 1;
                let max = parseInt(qtyInput.getAttribute('max')) || 100;

                if (currentValue < max) {
                    qtyInput.value = currentValue + 1;
                    updateHiddenField();
                }
            });

            qtyInput.addEventListener('input', function() {
                // Kiểm tra max min
                let val = parseInt(this.value);
                let max = parseInt(this.getAttribute('max'));
                let min = parseInt(this.getAttribute('min'));

                if (val > max) this.value = max;
                if (val < min) this.value = min;

                updateHiddenField();
            });
        });
    </script>
    <script>
        // Hàm hiển thị Video trên khung lớn
        function showMainVideo() {
            const video = document.getElementById('mainVideo');
            const img = document.getElementById('mainImage');

            // Ẩn ảnh, hiện video
            img.style.display = 'none';
            video.style.display = 'block';
            video.play(); // Tự động phát khi bấm vào

            // Xử lý viền cam active cho thumbnail
            updateActiveThumb(0); // Giả sử video luôn là thumb đầu tiên
        }

        // Hàm hiển thị Ảnh trên khung lớn
        function showMainImage(src, thumbElement) {
            const video = document.getElementById('mainVideo');
            const img = document.getElementById('mainImage');

            // Ẩn video, hiện ảnh
            video.pause(); // Dừng video nếu đang chạy
            video.style.display = 'none';

            img.src = src;
            img.style.display = 'block';

            // Xử lý viền cam
            document.querySelectorAll('.thumb-item').forEach(el => {
                el.style.border = '1px solid #ddd';
                el.classList.remove('active');
            });
            thumbElement.style.border = '2px solid #ee4d2d';
            thumbElement.classList.add('active');
        }

        // Hàm phụ trợ update viền (cho video)
        function updateActiveThumb(index) {
            const thumbs = document.querySelectorAll('.thumb-item');
            thumbs.forEach(el => {
                el.style.border = '1px solid #ddd';
                el.classList.remove('active');
            });
            if (thumbs[index]) {
                thumbs[index].style.border = '2px solid #ee4d2d';
                thumbs[index].classList.add('active');
            }
        }
    </script>
</body>

</html>