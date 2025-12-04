<?php
$headerTitle = "Kết quả tìm kiếm";
include_once("Core/Database.php");

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

$sql_cate = "SELECT * FROM categories";
$result_cate = mysqli_query($conn, $sql_cate);

$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET["page"] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$productList = [];
$whereConditions = [];
$param = '';

$keyword = '';
if (!empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $safe_keyword = mysqli_real_escape_string($conn, $keyword);
    $whereConditions[] = "products.name LIKE '%$safe_keyword%'";
    $param .= "&keyword=" . urlencode($keyword);
}

if (!empty($_GET['cat'])) {
    $cat_ids = array_map('intval', $_GET['cat']);
    if (!empty($cat_ids)) {
        $whereConditions[] = "products.category_id IN (" . implode(',', $cat_ids) . ")";
        foreach ($cat_ids as $id) $param .= "&cat[]=$id";
    }
}

$provinces_list = [
    "An Giang",
    "Bà Rịa - Vũng Tàu",
    "Bắc Giang",
    "Bắc Kạn",
    "Bạc Liêu",
    "Bắc Ninh",
    "Bến Tre",
    "Bình Định",
    "Bình Dương",
    "Bình Phước",
    "Bình Thuận",
    "Cà Mau",
    "Cần Thơ",
    "Cao Bằng",
    "Đà Nẵng",
    "Đắk Lắk",
    "Đắk Nông",
    "Điện Biên",
    "Đồng Nai",
    "Đồng Tháp",
    "Gia Lai",
    "Hà Giang",
    "Hà Nam",
    "Hà Nội",
    "Hà Tĩnh",
    "Hải Dương",
    "Hải Phòng",
    "Hậu Giang",
    "Hòa Bình",
    "Hưng Yên",
    "Khánh Hòa",
    "Kiên Giang",
    "Kon Tum",
    "Lai Châu",
    "Lâm Đồng",
    "Lạng Sơn",
    "Lào Cai",
    "Long An",
    "Nam Định",
    "Nghệ An",
    "Ninh Bình",
    "Ninh Thuận",
    "Phú Thọ",
    "Phú Yên",
    "Quảng Bình",
    "Quảng Nam",
    "Quảng Ngãi",
    "Quảng Ninh",
    "Quảng Trị",
    "Sóc Trăng",
    "Sơn La",
    "Tây Ninh",
    "Thái Bình",
    "Thái Nguyên",
    "Thanh Hóa",
    "Thừa Thiên Huế",
    "Tiền Giang",
    "TP Hồ Chí Minh",
    "Trà Vinh",
    "Tuyên Quang",
    "Vĩnh Long",
    "Vĩnh Phúc",
    "Yên Bái"
];

if (!empty($_GET['place'])) {
    $place_sql_parts = [];
    foreach ($_GET['place'] as $p) {
        if (in_array($p, $provinces_list)) {
            $safe_place = mysqli_real_escape_string($conn, $p);
            $place_sql_parts[] = "shops.address LIKE '%$safe_place%'";
            $param .= "&place[]=" . urlencode($p);
        }
    }

    if (!empty($place_sql_parts)) {
        $whereConditions[] = "(" . implode(' OR ', $place_sql_parts) . ")";
    }
}

$whereSQL = "";
if (!empty($whereConditions)) {
    $whereSQL = " WHERE " . implode(' AND ', $whereConditions);
}


$base_param = $param;
$sort = $_GET['sort'] ?? '';
$order_sql = "";

switch ($sort) {
    case 'new':
        $order_sql = " ORDER BY products.id DESC"; // Mới nhất
        break;
    case 'price_asc':
        $order_sql = " ORDER BY products.price ASC"; // Giá thấp đến cao
        break;
    case 'price_desc':
        $order_sql = " ORDER BY products.price DESC"; // Giá cao đến thấp
        break;
    default:
        $order_sql = " ORDER BY products.id ASC"; // Mặc định (Liên quan)
        break;
}
if ($sort) $param .= "&sort=$sort";

$joinQuery = " FROM products 
               JOIN shops ON products.seller_id = shops.user_id 
               $whereSQL";


$sql_count = "SELECT COUNT(*) as total $joinQuery";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];
$total_page = ceil($total_records / $limit);

$sql = "SELECT products.* $joinQuery $order_sql LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_object($result)) {
        $productList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Danh sách sản phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Assets/Css/Product/List.css">
</head>

<body>
    <main class="page-container">
        <div class="banner-wrapper-full">
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
            <form action="index.php" method="GET">
                <input type="hidden" name="controller" value="product">
                <input type="hidden" name="action" value="list">

                <?php if (!empty($keyword)): ?>
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                <?php endif; ?>

                <aside class="sidebar">
                    <h3 class="sidebar-title"><i class="fas fa-filter"></i> Bộ lọc tìm kiếm</h3>

                    <section class="filter-block">
                        <h4 class="filter-title">Nơi Bán</h4>

                        <?php $places_checked = $_GET['place'] ?? []; ?>

                        <div class="province-scroll-box" style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; border-radius: 4px;">

                            <?php foreach ($provinces_list as $province_name): ?>
                                <?php
                                // Kiểm tra xem tỉnh này có được chọn chưa
                                $isChecked = in_array($province_name, $places_checked) ? 'checked' : '';
                                ?>
                                <label style="display: block; margin-bottom: 5px; cursor: pointer;">
                                    <input type="checkbox" name="place[]" value="<?= $province_name ?>" <?= $isChecked ?>>
                                    <?= $province_name ?>
                                </label>
                            <?php endforeach; ?>

                        </div>
                    </section>

                    <section class="filter-block">
                        <h4 class="filter-title">Theo Danh Mục</h4>
                        <div class="category-list" style="display: flex; flex-direction: column; gap: 8px;">
                            <?php
                            if ($result_cate && mysqli_num_rows($result_cate) > 0):
                                mysqli_data_seek($result_cate, 0);
                                while ($cat = mysqli_fetch_assoc($result_cate)):
                                    $isChecked = (isset($_GET['cat']) && in_array($cat['id'], $_GET['cat'])) ? 'checked' : '';
                            ?>
                                    <label style="cursor: pointer;">
                                        <input type="checkbox" name="cat[]" value="<?= $cat['id'] ?>" <?= $isChecked ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </label>
                            <?php
                                endwhile;
                            else:
                                echo "<p style='color:#888; font-size:13px;'>Đang cập nhật...</p>";
                            endif;
                            ?>
                        </div>
                    </section>
                    <section class="filter-block promo-block">
                        <h4 class="filter-title">Dịch Vụ & Khuyến Mãi</h4>
                        <label><input type="checkbox" name="promo[]" value="sale"> Đang giảm giá</label>
                        <label><input type="checkbox" name="promo[]" value="free_ship"> Miễn phí vận chuyển</label>
                    </section>

                    <button type="submit" style="width:100%; padding:10px; background:#ee4d2d; color:#fff; border:none; margin-top:15px; cursor:pointer; font-weight:bold; text-transform:uppercase;">
                        Áp Dụng
                    </button>

                    <a href="index.php?controller=product&action=list" class="clear-filters" style="display:block; text-align:center; margin-top:10px; text-decoration:none; color:#555; font-size:13px;">
                        XÓA TẤT CẢ
                    </a>

                </aside>
            </form>
            <section class="content">

                <div class="sort-bar">
                    <div class="sort-left">
                        <span class="sort-label">Sắp xếp theo</span>

                        <?php
                        $baseUrl = "index.php?controller=product&action=list" . $param;
                        ?>

                        <a href="index.php?controller=product&action=list&sort=rel<?= $param ?>"
                            class="btn-sort <?= ($sort == '' || $sort == 'rel') ? 'active' : '' ?>">
                            Liên Quan
                        </a>

                        <a href="index.php?controller=product&action=list&sort=new<?= $param ?>"
                            class="btn-sort <?= ($sort == 'new') ? 'active' : '' ?>">
                            Mới Nhất
                        </a>

                        <a href="#" class="btn-sort">Bán Chạy</a>
                    </div>
                    <div class="sort-right">
                        <select onchange="if(this.value) location = this.value;" style="padding: 5px; border-radius: 4px; border: 1px solid #ddd;">

                            <option value="index.php?controller=product&action=list<?= $base_param ?>">
                                Sắp xếp: Mặc định
                            </option>

                            <option value="index.php?controller=product&action=list<?= $base_param ?>&sort=price_asc"
                                <?= ($sort == 'price_asc') ? 'selected' : '' ?>>
                                Giá: Thấp đến Cao
                            </option>

                            <option value="index.php?controller=product&action=list<?= $base_param ?>&sort=price_desc"
                                <?= ($sort == 'price_desc') ? 'selected' : '' ?>>
                                Giá: Cao đến Thấp
                            </option>

                        </select>
                    </div>
                </div>

                <?php if (!empty($keyword)): ?>
                    <div style="margin-bottom: 15px; font-size: 14px; color: #555;">
                        Kết quả tìm kiếm cho: <strong style="color: #ee4d2d;"><?= htmlspecialchars($keyword) ?></strong>
                    </div>
                <?php endif; ?>

                <div class="products-grid">
                    <?php if (!empty($productList)) {
                        foreach ($productList as $product) {
                            $p_id = $product->id ?? $product->p_id ?? 0;
                    ?>
                            <a href="index.php?controller=product&action=detail&id=<?= $p_id ?>" class="product-card">
                                <div class="product-media">
                                    <?php
                                    $imgSrc = !empty($product->image) ? $product->image : 'https://via.placeholder.com/300';
                                    ?>
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="Product">
                                    <div class="badge-mall">Mall</div>
                                    <div class="discount-tag">-10%</div>
                                </div>
                                <div class="product-body">
                                    <div class="product-title"><?= htmlspecialchars($product->name) ?></div>
                                    <div class="price-row">
                                        <div class="price">₫<?= number_format((float)$product->price, 0, ',', '.') ?></div>
                                        <div class="sold">Đã bán <?= number_format($product->sold ?? 0) ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php }
                    } else { ?>
                        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #888;">
                            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                        </div>
                    <?php } ?>
                </div>

                <?php if ($total_page > 1): ?>
                    <div class="pagination" style="margin-top: 30px; display: flex; justify-content: center; gap: 10px;">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <a href="?page=<?= $i ?><?= $param ?>"
                                style="padding: 5px 10px; border: 1px solid #ddd; text-decoration:none; color:#333; <?= $i == $page ? 'background:#ee4d2d; color:#fff; border-color:#ee4d2d' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        document.querySelectorAll('.sort-left .btn').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('.sort-left .btn').forEach(x => x.classList.remove('active'));
                b.classList.add('active');
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lấy các phần tử cần thiết
            const slides = document.querySelectorAll('.video-slide');
            const dots = document.querySelectorAll('.dot');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');

            let currentSlide = 0;
            let slideInterval;
            const autoPlayDelay = 5000; // Thời gian tự chuyển slide: 5000ms = 5 giây

            // Hàm hiển thị slide tại một chỉ số (index) cụ thể
            function showSlide(index) {
                // 1. Xử lý chỉ số index vòng lặp (vô tận)
                if (index >= slides.length) currentSlide = 0;
                else if (index < 0) currentSlide = slides.length - 1;
                else currentSlide = index;

                // 2. Xóa class 'active' ở tất cả slides và dots cũ
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));

                // 3. Thêm class 'active' cho slide và dot mới
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');

                // 4. Đảm bảo video của slide mới được phát lại từ đầu
                const video = slides[currentSlide].querySelector('video');
                if (video) {
                    video.currentTime = 0;
                    video.play();
                }
            }

            // Hàm chuyển sang slide tiếp theo
            function nextSlide() {
                showSlide(currentSlide + 1);
            }

            // Hàm chuyển về slide trước đó
            function prevSlide() {
                showSlide(currentSlide - 1);
            }

            // Hàm bắt đầu tự động chạy slider
            function startAutoPlay() {
                // Xóa interval cũ để tránh chạy chồng chéo
                if (slideInterval) clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, autoPlayDelay);
            }

            // Hàm reset lại bộ đếm tự động (dùng khi người dùng tương tác thủ công)
            function resetAutoPlay() {
                clearInterval(slideInterval);
                startAutoPlay();
            }

            // --- Gán sự kiện cho các nút điều khiển ---

            // Click nút Next
            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetAutoPlay(); // Reset bộ đếm sau khi click
            });

            // Click nút Prev
            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetAutoPlay();
            });

            // Click vào các chấm tròn (dots)
            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    // Lấy chỉ số slide từ thuộc tính data-slide của dot
                    const slideIndex = parseInt(e.target.getAttribute('data-slide'));
                    showSlide(slideIndex);
                    resetAutoPlay();
                });
            });

            // --- Khởi chạy slider ---
            // Hiển thị slide đầu tiên khi tải trang
            showSlide(currentSlide);
            // Bắt đầu tự động chạy slide
            startAutoPlay();
        });
    </script>
</body>

</html>
</body>

</html>