<?php
// product_list.php
// Đã làm sạch xung đột. Ưu tiên logic bảo mật (Prepared Statements) từ nhánh mới.

$headerTitle = "Kết quả tìm kiếm";
require_once('Core/Database.php');
$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    http_response_code(500);
    echo "Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.";
    exit;
}

mysqli_set_charset($conn, "utf8mb4");

// --- Cấu hình ---
$limit = 40;

// Lấy và kiểm tra các input
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Chỉ cho phép một tập hợp sort hợp lệ để tránh SQL injection
$allowed_sorts = ['', 'new', 'price_asc', 'price_desc'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts, true) ? $_GET['sort'] : '';

// Tạo order clause an toàn
$order_sql = "ORDER BY id ASC"; // Mặc định
if ($sort === 'new') {
    $order_sql = "ORDER BY id DESC";
} elseif ($sort === 'price_asc') {
    $order_sql = "ORDER BY price ASC";
} elseif ($sort === 'price_desc') {
    $order_sql = "ORDER BY price DESC";
}

// Tổng số bản ghi (sử dụng prepared statement)
if ($keyword !== '') {
    $count_sql = "SELECT COUNT(*) AS total FROM products WHERE name LIKE ?";
    $stmt = $conn->prepare($count_sql);
    $like = '%' . $keyword . '%';
    $stmt->bind_param('s', $like);
} else {
    $count_sql = "SELECT COUNT(*) AS total FROM products";
    $stmt = $conn->prepare($count_sql);
}

// Execute count
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$total_records = (int) ($row['total'] ?? 0);
$stmt->close();

$total_page = ($total_records > 0) ? (int) ceil($total_records / $limit) : 1;
if ($page > $total_page) $page = $total_page;
$offset = ($page - 1) * $limit;

// Lấy dữ liệu sản phẩm (prepared statement nếu có keyword)
$productList = [];
if ($keyword !== '') {
    $sql = "SELECT id, name, price, image, created_at, sold FROM products WHERE name LIKE ? $order_sql LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $like = '%' . $keyword . '%';
    $stmt->bind_param('sii', $like, $limit, $offset);
} else {
    $sql = "SELECT id, name, price, image, created_at, sold FROM products $order_sql LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
while ($r = $result->fetch_object()) {
    $productList[] = $r;
}
$stmt->close();

// Hàm hỗ trợ tạo query string giữ các param hiện tại
function build_query_custom(array $extras = []) // Đổi tên hàm tránh trùng lặp nếu có
{
    $base = [];
    if (isset($_GET['keyword']) && $_GET['keyword'] !== '') {
        $base['keyword'] = $_GET['keyword'];
    }
    if (isset($_GET['sort']) && $_GET['sort'] !== '') {
        $base['sort'] = $_GET['sort'];
    }
    $params = array_merge($base, $extras);
    return http_build_query($params);
}
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($headerTitle) ?> - Danh sách sản phẩm</title>
    <meta name="description" content="Tìm kiếm sản phẩm - hiển thị <?= $total_records ?> kết quả" />
    <link rel="stylesheet" href="Assets/Css/Product/List.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        crossorigin="anonymous">
</head>

<body>
    <main class="page-container" role="main">
        <div class="banner-wrapper-full" aria-hidden="true">
            <div class="promo-banner-section">
                <div class="banner-large video-slider-container">
                    <div class="slider-wrapper">

                        <div class="video-slide active">
                            <a href="#">
                                <video autoplay muted loop playsinline class="promo-video">
                                    <source src="Assets/video/slide1.mp4" type="video/mp4">
                                    <img src="Assets/img/banner-main.jpg" alt="Slide 1">
                                </video>
                            </a>
                        </div>

                        <div class="video-slide">
                            <a href="#">
                                <video autoplay muted loop playsinline class="promo-video">
                                    <source src="Assets/video/slide2.mp4" type="video/mp4">
                                    <img src="Assets/img/banner-sub1.jpg" alt="Slide 2">
                                </video>
                            </a>
                        </div>

                        <div class="video-slide">
                            <a href="#">
                                <video autoplay muted loop playsinline class="promo-video">
                                    <source src="Assets/video/slide3.mp4" type="video/mp4">
                                    <img src="Assets/img/banner-sub2.jpg" alt="Slide 3">
                                </video>
                            </a>
                        </div>
                    </div>

                    <button class="slider-btn prev-btn"><i class="fas fa-chevron-left"></i></button>
                    <button class="slider-btn next-btn"><i class="fas fa-chevron-right"></i></button>

                    <div class="slider-dots">
                        <span class="dot active" data-slide="0"></span> <span class="dot" data-slide="1"></span> <span class="dot" data-slide="2"></span>
                    </div>
                </div>
                <div class="banner-column-right">
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub1.jpg" alt="Banner Nhỏ 1"></a></div>
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub2.jpg" alt="Banner Nhỏ 2"></a></div>
                </div>
            </div>
        </div>

        <div class="page-inner">
            <aside class="sidebar" aria-labelledby="filter-heading">
                <h3 id="filter-heading" class="sidebar-title">
                    <i class="fas fa-filter"></i> Bộ lọc tìm kiếm
                </h3>

                <form id="filters-form" method="get" action="index.php" aria-label="Bộ lọc sản phẩm">
                    <input type="hidden" name="controller" value="product">
                    <input type="hidden" name="action" value="list">
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

                    <section class="filter-block" aria-labelledby="place-title">
                        <h4 id="place-title" class="filter-title">Nơi Bán</h4>
                        <label><input type="checkbox" name="place[]" value="hn"> Hà Nội</label>
                        <label><input type="checkbox" name="place[]" value="hcm"> TP. Hồ Chí Minh</label>
                    </section>

                    <section class="filter-block" aria-labelledby="category-title">
                        <h4 id="category-title" class="filter-title">Theo Danh Mục</h4>
                        <label><input type="checkbox" name="cat[]" value="hair"> Chăm sóc tóc</label>
                        <label><input type="checkbox" name="cat[]" value="face"> Chăm sóc da mặt</label>
                        <label><input type="checkbox" name="cat[]" value="home"> Nhà cửa & Đời sống</label>
                    </section>

                    <section class="filter-block promo-block" aria-labelledby="promo-title">
                        <h4 id="promo-title" class="filter-title">Dịch Vụ & Khuyến Mãi</h4>
                        <label><input type="checkbox" name="promo[]" value="sale"> Đang giảm giá</label>
                        <label><input type="checkbox" name="promo[]" value="free_ship"> Miễn phí vận chuyển</label>
                    </section>

                    <div class="filter-actions">
                        <button type="submit" class="apply-filters" style="width:100%; padding:10px; background:#ee4d2d; color:#fff; border:none; margin-top:15px; cursor:pointer;">Áp dụng</button>
                        <button type="button" id="clear-filters" class="clear-filters" style="display:block; width:100%; text-align:center; margin-top:10px; border:none; background:transparent;">XÓA TẤT CẢ</button>
                    </div>
                </form>
            </aside>

            <section class="content" aria-labelledby="results-heading">
                <div class="sort-bar" role="region" aria-label="Thanh sắp xếp">
                    <div class="sort-left">
                        <span class="sort-label">Sắp xếp theo</span>
                        <a href="index.php?controller=product&action=list&<?= build_query_custom(['sort' => '']) ?>"
                            class="btn-sort <?= ($sort === '') ? 'active' : '' ?>">
                            Liên Quan
                        </a>
                        <a href="index.php?controller=product&action=list&<?= build_query_custom(['sort' => 'new']) ?>"
                            class="btn-sort <?= ($sort === 'new') ? 'active' : '' ?>">
                            Mới Nhất
                        </a>
                    </div>

                    <div class="sort-right">
                        <label for="price-sort" class="sr-only">Sắp xếp giá</label>
                        <select id="price-sort" onchange="handlePriceSort(this.value)">
                            <option value="">Lọc theo giá</option>
                            <option value="price_asc" <?= ($sort === 'price_asc') ? 'selected' : '' ?>>
                                Giá: Thấp đến Cao
                            </option>
                            <option value="price_desc" <?= ($sort === 'price_desc') ? 'selected' : '' ?>>
                                Giá: Cao đến Thấp
                            </option>
                        </select>
                    </div>
                </div>

                <h2 id="results-heading" class="visually-hidden">Kết quả tìm kiếm</h2>

                <div class="search-results-info">
                    Kết quả tìm kiếm cho từ khoá:
                    <strong><?= htmlspecialchars($keyword === '' ? 'Tất cả sản phẩm' : $keyword) ?></strong>
                    <span aria-live="polite">(<?= $total_records ?> kết quả)</span>
                </div>

                <div class="products-grid" role="list">
                    <?php if (!empty($productList)): ?>
                        <?php foreach ($productList as $product):
                            $p_id = (int) $product->id;
                            $imgSrc = !empty($product->image) ? $product->image : 'https://via.placeholder.com/300x190?text=No+Image';
                            $title = htmlspecialchars($product->name);
                            $price_text = '₫' . number_format((float) $product->price, 0, ',', '.');
                        ?>
                            <a role="listitem"
                                href="index.php?controller=product&action=detail&id=<?= $p_id ?>"
                                class="product-card"
                                aria-label="<?= $title ?>">
                                <div class="product-media">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= $title ?>">
                                    <div class="badge-mall">Mall</div>
                                    <div class="discount-tag">-10%</div>
                                </div>
                                <div class="product-body">
                                    <div class="product-title"><?= $title ?></div>
                                    <div class="price-row">
                                        <div class="price"><?= $price_text ?></div>
                                        <div class="sold">Đã bán <?= $product->sold ?? 0 ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_page > 1): ?>
                    <nav class="pagination" aria-label="Trang kết quả">
                        <?php
                        $start = max(1, $page - 3);
                        $end = min($total_page, $page + 3);

                        // Link params base
                        $url_base = 'index.php?controller=product&action=list&';

                        if ($start > 1) {
                            echo '<a class="page-link" href="' . $url_base . build_query_custom(['page' => 1]) . '">&laquo; 1</a>';
                            if ($start > 2) echo '<span class="dots">…</span>';
                        }

                        for ($i = $start; $i <= $end; $i++):
                            $active = ($i === $page) ? 'style="background:#ee4d2d;color:#fff;border-color:#ee4d2d"' : '';
                        ?>
                            <a class="page-link"
                                href="<?= $url_base . build_query_custom(['page' => $i]) ?>"
                                <?= $active ?>>
                                <?= $i ?>
                            </a>
                        <?php
                        endfor;

                        if ($end < $total_page) {
                            if ($end < $total_page - 1) echo '<span class="dots">…</span>';
                            echo '<a class="page-link" href="' . $url_base . build_query_custom(['page' => $total_page]) . '">' . $total_page . ' &raquo;</a>';
                        }
                        ?>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.video-slide');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            if (slides.length === 0) return; // Nếu không có slider thì thôi

            let currentSlide = 0;
            let slideInterval;
            const autoPlayDelay = 5000;

            function showSlide(index) {
                if (index >= slides.length) currentSlide = 0;
                else if (index < 0) currentSlide = slides.length - 1;
                else currentSlide = index;

                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));

                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');

                const video = slides[currentSlide].querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play();
                }
            }

            function nextSlide() {
                showSlide(currentSlide + 1);
            }

            function prevSlide() {
                showSlide(currentSlide - 1);
            }

            function startAutoPlay() {
                if (slideInterval) clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, autoPlayDelay);
            }

            function resetAutoPlay() {
                clearInterval(slideInterval);
                startAutoPlay();
            }

            if (nextBtn) nextBtn.addEventListener('click', () => {
                nextSlide();
                resetAutoPlay();
            });
            if (prevBtn) prevBtn.addEventListener('click', () => {
                prevSlide();
                resetAutoPlay();
            });

            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    const slideIndex = parseInt(e.target.getAttribute('data-slide'));
                    showSlide(slideIndex);
                    resetAutoPlay();
                });
            });

            showSlide(currentSlide);
            startAutoPlay();
        });
    </script>

    <script>
        document.getElementById('clear-filters').addEventListener('click', function() {
            const form = document.getElementById('filters-form');
            form.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        function handlePriceSort(sortValue) {
            if (!sortValue) return;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', sortValue);
            urlParams.delete('page');
            window.location.href = 'index.php?' + urlParams.toString();
        }

        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <style>
        /* CSS hỗ trợ giao diện mới */
        .search-results-info {
            margin-bottom: 20px;
            padding: 16px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            font-size: 14px;
            color: #555;
        }

        .search-results-info strong {
            color: #ee4d2d;
            font-weight: 600;
        }

        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 16px;
            display: block;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            border: 0;
        }
    </style>
</body>

</html>