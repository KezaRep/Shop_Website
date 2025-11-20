<?php
// Page header title (used by Header.php)
$headerTitle = "Kết quả tìm kiếm";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Danh sách sản phẩm</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- This page style -->
    <link rel="stylesheet" href="Assets/Css/Product/List.css">
</head>

<body>
    <main class="page-container">
        <div class="page-inner">
            <!-- Sidebar filters -->
            <aside class="sidebar">
                <h3 class="sidebar-title"><i class="fas fa-filter"></i> Bộ lọc tìm kiếm</h3>

                <section class="filter-block">
                    <h4 class="filter-title">Nơi Bán</h4>
                    <label><input type="checkbox" name="place[]" value="hn"> Hà Nội</label>
                    <label><input type="checkbox" name="place[]" value="hcm"> TP. Hồ Chí Minh</label>
                    <label><input type="checkbox" name="place[]" value="tn"> Thái Nguyên</label>
                    <label><input type="checkbox" name="place[]" value="vp"> Vĩnh Phúc</label>
                </section>

                <section class="filter-block">
                    <h4 class="filter-title">Theo Danh Mục</h4>
                    <label><input type="checkbox" name="cat[]" value="hair"> Chăm sóc tóc</label>
                    <label><input type="checkbox" name="cat[]" value="face"> Chăm sóc da mặt</label>
                    <label><input type="checkbox" name="cat[]" value="home"> Nhà cửa & Đời sống</label>
                </section>

                <section class="filter-block promo-block">
                    <h4 class="filter-title">Dịch Vụ & Khuyến Mãi</h4>
                    <label><input type="checkbox" name="promo[]" value="sale"> Đang giảm giá</label>
                    <label><input type="checkbox" name="promo[]" value="free_ship"> Miễn phí vận chuyển</label>
                    <label><input type="checkbox" name="promo[]" value="in_stock"> Hàng có sẵn</label>
                    <label><input type="checkbox" name="promo[]" value="wholesale"> Mua giá bán buôn/bán sỉ</label>

                </section>
                <section class="filter-block">
                    <h4 class="filter-title">Đơn vị vận chuyển</h4>
                    <label><input type="checkbox" name="ship[]" value="fast"> Nhanh</label>
                    <label><input type="checkbox" name="ship[]" value="Hỏa tốc">Hỏa tốc</label>
                    <label><input type="checkbox" name="ship[]" value="Tiết kiệm">Tiết kiệm</label>
                </section>
                <button type="button" class="clear-filters">XÓA TẤT CẢ</button>


            </aside>

            <!-- Main content -->
            <section class="content">
                <!-- Shop header card -->
                <div class="shop-card">
                    <div class="shop-card__left">
                        <img src="Assets/Images/placeholder-avatar.png" alt="shop" class="shop-avatar">
                        <div>
                            <h3 class="shop-name">Sản phẩm An</h3>
                            <div class="shop-meta">trammhokami • 10 Người Theo Dõi • 24 Đang Theo</div>
                        </div>
                    </div>
                    <div class="shop-stats">
                        <div><strong>10</strong><span>Sản Phẩm</span></div>
                        <div><strong>5</strong><span>Đánh Giá</span></div>
                        <div><strong>66%</strong><span>Tỷ Lệ Phản Hồi</span></div>
                        <div><strong>trong vài giờ</strong><span>Thời gian phản hồi</span></div>
                    </div>
                </div>

                <!-- Info line -->
                <div class="results-info">
                    Kết quả tìm kiếm cho từ khoá <strong>'sản phẩm'</strong>
                </div>

                <!-- Sort bar -->
                <div class="sort-bar">
                    <div class="sort-left">
                        <button class="btn active">Liên Quan</button>
                        <button class="btn">Mới Nhất</button>
                        <button class="btn">Bán Chạy</button>
                    </div>
                    <div class="sort-right">
                        <select>
                            <option>Giá</option>
                        </select>
                        <div class="pagination">1 / 17 <button class="arrow">&lt;</button><button
                                class="arrow">&gt;</button></div>
                    </div>
                </div>

                <!-- Product grid -->
                <div class="products-grid">
                    <!-- Repeat card: sample items -->
                    <?php if (!empty($productList)) {
                        foreach ($productList as $product) { ?>
                            <article class="product-card">
                                <a
                                    href="index.php?controller=product&action=detail&id=<?= intval($product->id ?? $product->p_id ?? 0) ?>">
                                    <div class="product-media">
                                        <?php if (!empty($product->image)) { ?>
                                            <img src="data:image/jpeg;base64,<?= base64_encode($product->image) ?>"
                                                alt="<?= htmlspecialchars($product->name) ?>">

                                        <?php } else { ?>
                                            <div class="no-image">Không hiển thị ảnh</div>
                                        <?php } ?>
                                    </div>
                                    <div class="product-body">
                                        <h4 class="product-title"><?= htmlspecialchars($product->name) ?></h4>
                                        <div class="product-price"><?= number_format((float) $product->price, 0, ',', '.') ?>
                                            VNĐ
                                        </div>
                                        <div class="product-meta">
                                            <span class="rating"><i class="fas fa-star"></i></span>
                                            <span class="sold">• Đã bán <?= number_format($product->sold, 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php }
                    } ?>
                </div>

            </section>
        </div>
    </main>

    <script>
        // highlight active sort button
        document.querySelectorAll('.sort-left .btn').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('.sort-left .btn').forEach(x => x.classList.remove('active'));
                b.classList.add('active');
            });
        });

        // clear filters button
        document.querySelector('.clear-filters')?.addEventListener('click', function () {
            document.querySelectorAll('.sidebar input[type="checkbox"]').forEach(cb => cb.checked = false);
        });
    </script>
</body>

</html>