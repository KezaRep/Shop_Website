<?php
// product_list.php
// Cải tiến từ file gốc: bảo mật (prepared statements), xử lý input an toàn,
// pagination chuẩn, giữ param khi chuyển trang, sửa JS selector, cải thiện accessibility + SEO.

$headerTitle = "Kết quả tìm kiếm";
require_once('Core/Database.php');
 $db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    // Thực tế bạn có thể log lỗi vào file log thay vì hiển thị trực tiếp
    http_response_code(500);
    echo "Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.";
    exit;
}

mysqli_set_charset($conn, "utf8mb4");

// --- Cấu hình ---
$limit = 40;

// Lấy và kiểm tra các input
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1)
    $page = 1;

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
// Chỉ cho phép một tập hợp sort hợp lệ để tránh SQL injection qua ORDER BY
$allowed_sorts = ['', 'new'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts, true) ? $_GET['sort'] : '';

// Tạo order clause an toàn
$order_sql = ($sort === 'new') ? "ORDER BY id DESC" : "ORDER BY id ASC";

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
if ($page > $total_page)
    $page = $total_page;
$offset = ($page - 1) * $limit;

// Lấy dữ liệu sản phẩm (prepared statement nếu có keyword)
$productList = [];
if ($keyword !== '') {
    $sql = "SELECT id, name, price, image, created_at FROM products WHERE name LIKE ? $order_sql LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    // 'sii' => string, int, int
    $like = '%' . $keyword . '%';
    $stmt->bind_param('sii', $like, $limit, $offset);
} else {
    // Không có tham số LIKE => đơn giản hơn
    $sql = "SELECT id, name, price, image, created_at FROM products $order_sql LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
while ($r = $result->fetch_object()) {
    $productList[] = $r;
}
$stmt->close();

// Hàm hỗ trợ tạo query string giữ các param hiện tại (keyword, sort) và đặt page
function build_query(array $extras = [])
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

// Đóng kết nối khi không dùng nữa
// mysqli_close($conn); // optional

?><!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($headerTitle) ?> - Danh sách sản phẩm</title>
    <meta name="description" content="Tìm kiếm sản phẩm - hiển thị <?= $total_records ?> kết quả" />
    <link rel="stylesheet" href="Assets/Css/Product/List.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity=""
        crossorigin="anonymous">
</head>

<body>
    <main class="page-container" role="main">
        <div class="banner-wrapper-full" aria-hidden="true">
            <div class="promo-banner-section">
                <div class="banner-large">
                    <a href="#"><img src="Assets/img/banner-main.jpg" alt="Banner chính"></a>
                </div>
                <div class="banner-column-right">
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub1.jpg" alt="Banner nhỏ 1"></a>
                    </div>
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub2.jpg" alt="Banner nhỏ 2"></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-inner">
            <aside class="sidebar" aria-labelledby="filter-heading">
                <h3 id="filter-heading" class="sidebar-title"><i class="fas fa-filter"></i> Bộ lọc tìm kiếm</h3>

                <form id="filters-form" method="get" action="product_list.php" aria-label="Bộ lọc sản phẩm">
                    <!-- giữ keyword và sort khi submit filter -->
                    <input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">

                    <section class="filter-block" aria-labelledby="place-title">
                        <h4 id="place-title" class="filter-title">Nơi Bán</h4>
                        <label><input type="checkbox" name="place[]" value="hn"> Hà Nội</label>
                        <label><input type="checkbox" name="place[]" value="hcm"> TP. Hồ Chí Minh</label>
                        <label><input type="checkbox" name="place[]" value="tn"> Thái Nguyên</label>
                        <label><input type="checkbox" name="place[]" value="vp"> Vĩnh Phúc</label>
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
                        <label><input type="checkbox" name="promo[]" value="in_stock"> Hàng có sẵn</label>
                        <label><input type="checkbox" name="promo[]" value="wholesale"> Mua bán sỉ</label>
                    </section>

                    <section class="filter-block" aria-labelledby="ship-title">
                        <h4 id="ship-title" class="filter-title">Đơn vị vận chuyển</h4>
                        <label><input type="checkbox" name="ship[]" value="fast"> Nhanh</label>
                        <label><input type="checkbox" name="ship[]" value="rocket"> Hỏa tốc</label>
                        <label><input type="checkbox" name="ship[]" value="cheap"> Tiết kiệm</label>
                    </section>

                    <div class="filter-actions">
                        <button type="submit" class="apply-filters">Áp dụng</button>
                        <button type="button" id="clear-filters" class="clear-filters">XÓA TẤT CẢ</button>
                    </div>
                </form>
            </aside>

            <section class="content" aria-labelledby="results-heading">
                <div class="sort-bar" role="region" aria-label="Thanh sắp xếp">
                    <div class="sort-left">
                        <span class="sort-label">Sắp xếp theo</span>
                        <a href="product_list.php?<?= build_query(['sort' => '']) ?>"
                            class="btn-sort <?= ($sort === '') ? 'active' : '' ?>">Liên Quan</a>
                        <a href="product_list.php?<?= build_query(['sort' => 'new']) ?>"
                            class="btn-sort <?= ($sort === 'new') ? 'active' : '' ?>">Mới Nhất</a>
                        <a href="#" class="btn-sort">Bán Chạy</a>
                    </div>

                    <div class="sort-right">
                        <label for="price-sort" class="sr-only">Sắp xếp giá</label>
                        <select id="price-sort" onchange="onPriceSort(this.value)">
                            <option value="">Lọc theo giá</option>
                            <option value="low-high">Giá: Thấp đến Cao</option>
                            <option value="high-low">Giá: Cao đến Thấp</option>
                        </select>
                    </div>
                </div>

                <h2 id="results-heading" class="visually-hidden">Kết quả tìm kiếm</h2>

                <div style="margin-bottom: 15px; font-size: 14px; color: #555;">
                    Kết quả tìm kiếm cho từ khoá:
                    <strong
                        style="color: #ee4d2d;"><?= htmlspecialchars($keyword === '' ? 'Tất cả sản phẩm' : $keyword) ?></strong>
                    <span aria-live="polite" style="margin-left:12px; color:#777; font-size:13px;">
                        (<?= $total_records ?> kết quả)
                    </span>
                </div>

                <div class="products-grid" role="list">
                    <?php if (!empty($productList)): ?>
                        <?php foreach ($productList as $product):
                            $p_id = (int) $product->id;
                            $imgSrc = !empty($product->image) ? $product->image : 'https://via.placeholder.com/300x190?text=No+Image';
                            // an toàn hiển thị dữ liệu
                            $title = htmlspecialchars($product->name);
                            $price_text = '₫' . number_format((float) $product->price, 0, ',', '.');
                            ?>
                            <a role="listitem" href="index.php?controller=product&action=detail&id=<?= $p_id ?>"
                                class="product-card" aria-label="<?= $title ?>">
                                <div class="product-media">
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= $title ?>">
                                    <div class="badge-mall">Mall</div>
                                    <div class="discount-tag">-10%</div>
                                </div>
                                <div class="product-body">
                                    <div class="product-title"><?= $title ?></div>
                                    <div class="price-row">
                                        <div class="price"><?= $price_text ?></div>
                                        <div class="sold">Đã bán 1.2k</div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column:1 / -1; text-align:center; padding:40px; color:#888;">
                            <p>Không tìm thấy sản phẩm nào phù hợp.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_page > 1): ?>
                    <nav class="pagination" aria-label="Trang kết quả"
                        style="margin-top:30px; display:flex; justify-content:center; gap:10px;">
                        <?php
                        // Hiển thị tối đa 7 trang quanh current page
                        $start = max(1, $page - 3);
                        $end = min($total_page, $page + 3);
                        if ($start > 1) {
                            echo '<a class="page-link" href="product_list.php?' . build_query(['page' => 1]) . '">&laquo; 1</a>';
                            if ($start > 2)
                                echo '<span class="dots">…</span>';
                        }
                        for ($i = $start; $i <= $end; $i++):
                            $active = ($i === $page) ? 'style="background:#ee4d2d;color:#fff"' : '';
                            ?>
                            <a class="page-link" href="product_list.php?<?= build_query(['page' => $i]) ?>" <?= $active ?>><?= $i ?></a>
                        <?php endfor;
                        if ($end < $total_page) {
                            if ($end < $total_page - 1)
                                echo '<span class="dots">…</span>';
                            echo '<a class="page-link" href="product_list.php?' . build_query(['page' => $total_page]) . '">' . $total_page . '</a>';
                        }
                        ?>
                    </nav>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script>
        // Sửa selector cũ và fix JS để highlight đúng nút sort
        document.querySelectorAll('.sort-left .btn-sort').forEach(b => {
            b.addEventListener('click', () => {
                document.querySelectorAll('.sort-left .btn-sort').forEach(x => x.classList.remove('active'));
                b.classList.add('active');
            });
        });

        // Clear filters (client-side)
        document.getElementById('clear-filters').addEventListener('click', function () {
            document.querySelectorAll('#filters-form input[type="checkbox"]').forEach(c => c.checked = false);
        });

        function onPriceSort(mode) {
            // placeholder: có thể submit form hoặc thay đổi query string để server xử lý
            if (!mode) return;
            // Hiện tại chỉ alert để tránh thay đổi logic phía server trong bản demo
            alert('Sắp xếp theo: ' + mode + '. (Tính năng có thể được xử lý phía server hoặc JS nâng cao)');
        }
    </script>

</body>

</html>