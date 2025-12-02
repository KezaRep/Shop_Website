<?php
$headerTitle = "Kết quả tìm kiếm";
?>
<?php
include_once("Core/Database.php");

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

$limit = 40;
$page = isset($_GET['page']) ? (int) $_GET["page"] : 1;
if ($page < 1)
    $page = 1;
$offset = ($page - 1) * $limit;

$productList = [];
$keyword = '';
$param = '';

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order_sql = "";

if ($sort == 'new') {
    $order_sql = " ORDER BY id DESC";
} 
else if ($sort == 'price_asc') {
    $order_sql = " ORDER BY price ASC";
}
else if ($sort == 'price_desc') {
    $order_sql = " ORDER BY price DESC";
}
else {
    $order_sql = " ORDER BY id ASC";
}

if (isset($_GET['keyword']) && !empty($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
    $safe_keyword = mysqli_real_escape_string($conn, $keyword);

    $sql_count = "SELECT COUNT(*) as total FROM products WHERE name LIKE '%$safe_keyword%'";
    $sql = "SELECT * FROM products WHERE name LIKE '%$safe_keyword%' $order_sql LIMIT $limit OFFSET $offset";

    $param = "&keyword=" . urlencode($keyword);
} else {
    $sql_count = "SELECT COUNT(*) as total FROM products";
    $sql = "SELECT * FROM products $order_sql LIMIT $limit OFFSET $offset";
}

if ($sort) {
    $param .= "&sort=$sort";
}

$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];

$total_page = ceil($total_records / $limit);

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
                <div class="banner-large">
                    <a href="#">
                        <img src="Assets/img/banner-main.jpg" alt="Banner Lớn">
                    </a>
                </div>

                <div class="banner-column-right">
                    <div class="banner-small">
                        <a href="#">
                            <img src="Assets/img/banner-sub1.jpg" alt="Banner Nhỏ 1">
                        </a>
                    </div>
                    <div class="banner-small">
                        <a href="#">
                            <img src="Assets/img/banner-sub2.jpg" alt="Banner Nhỏ 2">
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-inner">
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

            <section class="content">
                <div class="sort-bar">
                    <div class="sort-left">
                        <span class="sort-label">Sắp xếp theo</span>

                        <a href="index.php?controller=product&action=list<?= isset($_GET['keyword']) ? '&keyword=' . $_GET['keyword'] : '' ?>"
                            class="btn-sort <?= ($sort == '') ? 'active' : '' ?>">
                            Liên Quan
                        </a>

                        <a href="index.php?controller=product&action=list&sort=new<?= isset($_GET['keyword']) ? '&keyword=' . $_GET['keyword'] : '' ?>"
                            class="btn-sort <?= ($sort == 'new') ? 'active' : '' ?>">
                            Mới Nhất
                        </a>

                        <a href="#" class="btn-sort">Bán Chạy</a>
                    </div>
                    <div class="sort-right">
                        <form method="GET" action="index.php">
                            <input type="hidden" name="controller" value="product">
                            <input type="hidden" name="action" value="sort">

                            <select name="sort" onchange="this.form.submit()">
                                <option value="" <?= ($sort == '') ? 'selected' : '' ?>>-- Sắp xếp --</option>
                                <option value="price_asc" <?= ($sort == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến Cao</option>
                                <option value="price_desc" <?= ($sort == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến Thấp</option>
                            </select>
                        </form>

                    </div>
                </div>

                <div style="margin-bottom: 15px; font-size: 14px; color: #555;">
                    Kết quả tìm kiếm cho từ khoá: <strong style="color: #ee4d2d;"><?= htmlspecialchars($keyword) ?></strong>
                </div>


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
                                        <div class="sold">Đã bán 1.2k</div>
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
                            <a href="?page=<?= $i ?>" style="padding: 5px 10px; border: 1px solid #ddd; <?= $i == $page ? 'background:#ee4d2d; color:#fff' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
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
    </script>
</body>

</html>