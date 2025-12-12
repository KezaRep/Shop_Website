<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("Model/User/UserModel.php");


function productImageSrc($img)
{
    if (empty($img)) {
        return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
    }

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

                    <div class="main-media-viewer"
                        style="width: 100%; aspect-ratio: 1/1; border: 1px solid #eee; border-radius: 4px; overflow: hidden; position: relative; background: #fff;">
                        <?php if (!empty($product->video_url)): ?>
                            <video id="mainVideo" controls
                                style="width: 100%; height: 100%; object-fit: contain; background: #000; display: block;">
                                <source src="/Shop_Website/<?= htmlspecialchars($product->video_url) ?>" type="video/mp4">
                            </video>
                            <img id="mainImage" src=""
                                style="width: 100%; height: 100%; object-fit: contain; display: none;">

                        <?php else: ?>
                            <video id="mainVideo" controls
                                style="width: 100%; height: 100%; object-fit: contain; background: #000; display: none;"></video>

                            <img id="mainImage" src="<?= productImageSrc($product->image ?? '') ?>"
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

                        <div class="thumb-item"
                            onclick="showMainImage('<?= productImageSrc($product->image ?? '') ?>', this)"
                            style="width: 60px; height: 60px; cursor: pointer; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                            <img src="/Shop_Website/<?= htmlspecialchars($product->image ?? '') ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>

                        <?php if (!empty($product->extra_images) && is_array($product->extra_images)): ?>
                            <?php foreach ($product->extra_images as $ei): ?>
                                <div class="thumb-item" onclick="showMainImage('<?= productImageSrc($ei) ?>', this)"
                                    style="width: 60px; height: 60px; cursor: pointer; border: 1px solid #ddd; border-radius: 4px; overflow: hidden;">
                                    <img src="<?= productImageSrc($ei) ?>"
                                        style="width: 100%; height: 100%; object-fit: cover;">
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
                            <label style="margin-bottom: 8px; font-weight: 600; display: block;">Số Lượng</label>

                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="qty-control">
                                    <button class="qty-btn minus" type="button"><i class="fas fa-minus"></i></button>
                                    <input type="number" id="qty" name="quantity" value="1" min="1"
                                        max="<?= intval($product->quantity ?? 100) ?>" inputmode="numeric"
                                        pattern="[0-9]*">
                                    <button class="qty-btn plus" type="button"><i class="fas fa-plus"></i></button>
                                </div>

                                <span class="available-text" style="color: #757575; font-size: 13px;">
                                    <?= intval($product->quantity ?? 0) ?> sản phẩm có sẵn
                                </span>
                            </div>
                        </div>

                        <form
                            action="index.php?controller=cart&action=add&product_id=<?= htmlspecialchars($product->id) ?>"
                            method="post" id="addCartForm">
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

                        <?php
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        // THÊM 2 DÒNG NÀY VÀO ĐẦU FILE
                        require_once __DIR__ . '/../../Model/Product/ProductModel.php';
                        $productModel = new ProductModel(); // <--- TẠO MỚI MODEL Ở ĐÂY
                        
                        $userId = $_SESSION['user']['id'] ?? null;
                        $isLiked = false;
                        if ($userId) {
                            $isLiked = $productModel->isWishlisted($userId, $product->id);
                        }
                        ?>


                        <button class="wishlist-btn <?= $isLiked ? 'liked' : '' ?>" data-id="<?= $product->id ?>">
                            <i class="fa-heart <?= $isLiked ? 'fas' : 'far' ?>"></i>
                            Thích
                        </button>

                        (<?= intval($product->likes ?? 0) ?>)</a>
                    </div>
                </div>
            </div>

            <!-- Seller card -->
            <div class="seller-card">
                <div class="seller-left">
                    <?php
                    $shopAvatar = '/Shop_Website/Assets/Images/placeholder-avatar.png'; // Mặc định
                    
                    if (!empty($shop_data->avatar)) {
                        // Xử lý đường dẫn nếu trong DB đã có sẵn 'Assets/'
                        $shopAvatar = (strpos($shop_data->avatar, 'Assets/') === 0)
                            ? '/Shop_Website/' . $shop_data->avatar
                            : '/Shop_Website/Assets/Uploads/' . $shop_data->avatar;
                    }
                    ?>

                    <img src="<?= $shopAvatar ?>" alt="seller" class="seller-avatar">
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

                    <a href="index.php?controller=shop&action=profile&id=<?= $product->seller_id ?>" class="btn-view"
                        style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-store" style="margin-right: 5px;"></i> Xem Shop
                    </a>
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
        document.addEventListener("DOMContentLoaded", function () {
            // 1. Khoanh vùng container để tránh bắt nhầm nút ở Header hay Footer
            const qtyContainer = document.querySelector('.product-actions .qty-control');

            // Nếu không tìm thấy container thì dừng lại để tránh lỗi
            if (!qtyContainer) return;

            const qtyInput = document.getElementById('qty');
            const qtyField = document.getElementById('qtyField');

            // 2. Tìm nút Minus và Plus CHÍNH XÁC trong container này
            const btnMinus = qtyContainer.querySelector('.qty-btn.minus');
            const btnPlus = qtyContainer.querySelector('.qty-btn.plus');

            function updateHiddenField() {
                if (qtyField && qtyInput) {
                    qtyField.value = qtyInput.value;
                }
            }

            if (btnMinus) {
                btnMinus.addEventListener('click', function () {
                    let currentValue = parseInt(qtyInput.value) || 1;
                    let min = parseInt(qtyInput.getAttribute('min')) || 1;

                    if (currentValue > min) {
                        qtyInput.value = currentValue - 1;
                        updateHiddenField();
                    }
                });
            }

            if (btnPlus) {
                btnPlus.addEventListener('click', function () {
                    let currentValue = parseInt(qtyInput.value) || 1;
                    let max = parseInt(qtyInput.getAttribute('max')) || 100;

                    if (currentValue < max) {
                        qtyInput.value = currentValue + 1;
                        updateHiddenField();
                    }
                });
            }

            if (qtyInput) {
                qtyInput.addEventListener('change', function () {
                    let val = parseInt(this.value) || 1;
                    let max = parseInt(this.getAttribute('max')) || 100;
                    let min = parseInt(this.getAttribute('min')) || 1;

                    if (val > max) this.value = max;
                    if (val < min) this.value = min;

                    updateHiddenField();
                });
            }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.wishlist-btn').on('click', function () {
                var btn = $(this);
                var productId = btn.data('id');
                var heart = btn.find('.fa-heart');

                // Nếu chưa đăng nhập → báo luôn
                <?php if (!isset($_SESSION['user'])): ?>
                    alert('Vui lòng đăng nhập để thích sản phẩm!');
                    window.location.href = 'index.php?controller=user&action=login';
                    return;
                <?php endif; ?>

                $.ajax({
                    url: 'index.php?controller=product&action=toggleWishlist',
                    type: 'POST',
                    data: { product_id: productId },
                    success: function (res) {
                        console.log("Response:", res); // <<< MỞ DEVTOOL XEM DÒNG NÀY

                        try {
                            var data = JSON.parse(res);
                            if (data.status === 'liked') {
                                heart.removeClass('far').addClass('fas');
                                btn.addClass('liked');
                            } else if (data.status === 'unliked') {
                                heart.removeClass('fas').addClass('far');
                                btn.removeClass('liked');
                            }
                        } catch (e) {
                            alert('Lỗi hệ thống. Vui lòng thử lại!');
                            console.error(res);
                        }
                    },
                    error: function () {
                        alert('Lỗi kết nối server!');
                    }
                });
            });
        });
    </script>
    <script>
        // Script cho rating stars (chọn số sao)
        document.addEventListener("DOMContentLoaded", function () {
            const starsInput = document.getElementById('starsInput');
            const ratingValue = document.getElementById('ratingValue');

            if (starsInput && ratingValue) {
                const stars = starsInput.querySelectorAll('.star');

                // Click vào sao
                stars.forEach(star => {
                    star.addEventListener('click', function () {
                        const value = parseInt(this.getAttribute('data-value'));
                        ratingValue.value = value;

                        // Highlight các sao được chọn
                        stars.forEach((s, index) => {
                            if (index < value) {
                                s.classList.add('selected');
                                s.style.color = '#ffc107'; // Màu vàng
                            } else {
                                s.classList.remove('selected');
                                s.style.color = '#ddd'; // Màu xám
                            }
                        });
                    });

                    // Hover effect
                    star.addEventListener('mouseenter', function () {
                        const value = parseInt(this.getAttribute('data-value'));
                        stars.forEach((s, index) => {
                            if (index < value) {
                                s.style.color = '#ffc107';
                            }
                        });
                    });
                });

                // Reset khi rời chuột khỏi vùng sao
                starsInput.addEventListener('mouseleave', function () {
                    const currentValue = parseInt(ratingValue.value);
                    stars.forEach((s, index) => {
                        if (index < currentValue) {
                            s.style.color = '#ffc107';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });

                // Set mặc định 5 sao khi load trang
                ratingValue.value = 5;
                stars.forEach(s => {
                    s.style.color = '#ffc107';
                    s.classList.add('selected');
                });
            }
        });
    </script>

</body>

</html>