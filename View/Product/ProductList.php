<?php
$headerTitle = "Kết quả tìm kiếm";
?>
<?php
$conn = mysqli_connect('localhost', 'root', '', 'shop_website');
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8");

$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET["page"] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$productList = [];
$keyword = '';
$param = '';

$sort = isset($_GET['sort']) ? $_GET['sort'] : '';
$order_sql = "";

if ($sort == 'new') {
    $order_sql = " ORDER BY id DESC";
} else {
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
                        <a href="index.php?controller=product&action=list<?= isset($_GET['keyword']) ? '&keyword=' . $_GET['keyword'] : '' ?>"
                            class="btn <?= ($sort == '') ? 'active' : '' ?>">
                            Liên Quan
                        </a>

                        <a href="index.php?controller=product&action=list&sort=new<?= isset($_GET['keyword']) ? '&keyword=' . $_GET['keyword'] : '' ?>"
                            class="btn <?= ($sort == 'new') ? 'active' : '' ?>">
                            Mới Nhất
                        </a>
                    </div>
                    <div class="sort-right">
                        <select>
                            <option>Giá</option>
                        </select>
                    </div>
                </div>

                <!-- Product grid -->
                <div class="products-grid">
                    <?php if (!empty($productList)) {
                        foreach ($productList as $product) {
                            // Xử lý ID (đôi khi DB trả về p_id hoặc id)
                            $p_id = $product->id ?? $product->p_id ?? 0;
                    ?>
                            <article class="product-card">
                                <a href="index.php?controller=product&action=detail&id=<?= $p_id ?>">
                                    <div class="product-media">
                                        <?php if (!empty($product->image)) { ?>
                                            <img src="data:image/jpeg;base64,<?= base64_encode($product->image) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                        <?php } else { ?>
                                            <div class="no-image">No Image</div>
                                        <?php } ?>
                                    </div>
                                    <div class="product-body">
                                        <h4 class="product-title"><?= htmlspecialchars($product->name) ?></h4>
                                        <div class="product-price"><?= number_format((float)$product->price, 0, ',', '.') ?> VNĐ</div>
                                    </div>
                                </a>
                            </article>
                        <?php }
                    } else { ?>
                        <p style="text-align:center; width:100%">Không tìm thấy sản phẩm nào.</p>
                    <?php } ?>
                </div>
                <?php if ($total_page > 1): ?>
                    <div class="pagination">

                        <?php if ($page > 1): ?>
                            <a href="index.php?controller=product&action=list&page=<?= $page - 1 ?><?= $param ?>" class="arrow">&lt;</a>
                        <?php else: ?>
                            <span class="disabled">&lt;</span>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="index.php?controller=product&action=list&page=<?= $i ?><?= $param ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_page): ?>
                            <a href="index.php?controller=product&action=list&page=<?= $page + 1 ?><?= $param ?>" class="arrow">&gt;</a>
                        <?php else: ?>
                            <span class="disabled">&gt;</span>
                        <?php endif; ?>

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