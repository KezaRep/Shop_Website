<?php
// Đảm bảo biến $lang tồn tại (fallback nếu chưa có)
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi';
$lang = include "Assets/Lang/$current_lang.php";

// Sau khi có biến $lang rồi thì mới dùng
$headerTitle = $lang['search_results'];

$db = new Database();
$conn = $db->getConnection();

if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu.");
}
mysqli_set_charset($conn, "utf8mb4");

// 1. LẤY DANH MỤC
$sql_cate = "SELECT * FROM categories";
$result_cate = mysqli_query($conn, $sql_cate);

// 2. CẤU HÌNH PHÂN TRANG
$limit = 40;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// 3. XỬ LÝ LỌC & TÌM KIẾM
$whereConditions = [];
$param = '';

// A. Tìm kiếm từ khóa
$keyword = '';
if (!empty($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
    $safe_keyword = mysqli_real_escape_string($conn, $keyword);
    $whereConditions[] = "products.name LIKE '%$safe_keyword%'";
    $param .= "&keyword=" . urlencode($keyword);
}

// B. Lọc theo Danh mục
if (!empty($_GET['cat'])) {
    $cat_ids = array_map('intval', $_GET['cat']);
    if (!empty($cat_ids)) {
        $ids_string = implode(',', $cat_ids);
        $whereConditions[] = "products.category_id IN ($ids_string)";
        foreach ($cat_ids as $id) $param .= "&cat[]=$id";
    }
}

// C. Lọc theo Nơi bán
if (!empty($_GET['place'])) {
    $place_sql_parts = [];
    foreach ($_GET['place'] as $p) {
        $p_clean = mysqli_real_escape_string($conn, $p);
        $place_sql_parts[] = "shops.address LIKE '%$p_clean%'";
        $param .= "&place[]=" . urlencode($p);
    }
    if (!empty($place_sql_parts)) {
        $whereConditions[] = "(" . implode(' OR ', $place_sql_parts) . ")";
    }
}

// D. Ghép câu lệnh WHERE
$whereSQL = "";
if (!empty($whereConditions)) {
    $whereSQL = " WHERE " . implode(' AND ', $whereConditions);
}

// E. Xử lý Sắp xếp
$sort = $_GET['sort'] ?? '';
$order_sql = "ORDER BY products.id ASC";

if ($sort == 'new') $order_sql = "ORDER BY products.id DESC";
elseif ($sort == 'price_asc') $order_sql = "ORDER BY products.price ASC";
elseif ($sort == 'price_desc') $order_sql = "ORDER BY products.price DESC";

if ($sort) $param .= "&sort=$sort";

// 4. THỰC THI QUERY
$joinQuery = " FROM products 
               JOIN shops ON products.seller_id = shops.user_id 
               $whereSQL";

// Đếm tổng số
$sql_count = "SELECT COUNT(*) as total $joinQuery";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_records = $row_count['total'];
$total_page = ceil($total_records / $limit);

// Lấy dữ liệu sản phẩm
$sql = "SELECT products.*, shops.address as shop_address 
        $joinQuery 
        $order_sql 
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
$productList = [];
if ($result) {
    while ($row = mysqli_fetch_object($result)) {
        $productList[] = $row;
    }
}

// Hàm hỗ trợ tạo link
function build_full_url($extras = [])
{
    $params = $_GET;
    unset($params['controller'], $params['action']);
    $params = array_merge($params, $extras);
    return http_build_query($params);
}
?>

<!doctype html>
<html lang="<?= isset($_SESSION['lang']) ? $_SESSION['lang'] : 'vi' ?>">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?= htmlspecialchars($headerTitle) ?></title>
    <link rel="stylesheet" href="Assets/Css/Product/List.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <main class="page-container">
        <div class="banner-wrapper-full">
            <div class="promo-banner-section">
                <div class="banner-large"><a href="#"><img src="Assets/img/banner-main.jpg" alt="Banner"></a></div>
                <div class="banner-column-right">
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub1.jpg" alt="Small 1"></a></div>
                    <div class="banner-small"><a href="#"><img src="Assets/img/banner-sub2.jpg" alt="Small 2"></a></div>
                </div>
            </div>
        </div>

        <div class="page-inner">
            <aside class="sidebar">
                <h3 class="sidebar-title"><i class="fas fa-filter"></i> <?= $lang['filter_title'] ?></h3>

                <form id="filters-form" method="get" action="index.php">
                    <input type="hidden" name="controller" value="product">
                    <input type="hidden" name="action" value="list">
                    <?php if ($keyword): ?><input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>"><?php endif; ?>

                    <section class="filter-block">
                        <h4 class="filter-title"><?= $lang['filter_location'] ?></h4>

                        <div id="province-list" class="province-scroll-box" style="max-height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; border-radius: 4px;">
                            <p style="color:#999; font-size:12px;"><?= $lang['loading_places'] ?></p>
                        </div>

                        <script>
                            var checkedPlaces = <?php echo json_encode($_GET['place'] ?? []); ?>;
                            // Tạo biến lỗi cho JS dùng
                            var errorLoadingText = "<?= $lang['error_loading'] ?>";
                        </script>
                    </section>

                    <section class="filter-block">
                        <h4 class="filter-title"><?= $lang['filter_category'] ?></h4>
                        <div class="category-list" style="display: flex; flex-direction: column; gap: 5px;">
                            <?php
                            if ($result_cate):
                                mysqli_data_seek($result_cate, 0);
                                while ($cat = mysqli_fetch_assoc($result_cate)):
                                    $isChecked = (isset($_GET['cat']) && in_array($cat['id'], $_GET['cat'])) ? 'checked' : '';
                            ?>
                                    <label style="cursor: pointer;">
                                        <input type="checkbox" name="cat[]" value="<?= $cat['id'] ?>" <?= $isChecked ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </label>
                            <?php endwhile;
                            endif; ?>
                        </div>
                    </section>

                    <section class="filter-block promo-block">
                        <h4 class="filter-title"><?= $lang['filter_service_promo'] ?></h4>
                        <label><input type="checkbox" name="promo[]" value="sale"> <?= $lang['promo_sale'] ?></label>
                        <label><input type="checkbox" name="promo[]" value="free_ship"> <?= $lang['promo_freeship'] ?></label>
                    </section>

                    <button type="submit" class="apply-filters" style="width:100%; padding:10px; background:#ee4d2d; color:#fff; border:none; margin-top:15px; cursor:pointer; font-weight:bold;">
                        <?= $lang['btn_apply'] ?>
                    </button>
                    <a href="index.php?controller=product&action=list" style="display:block; text-align:center; margin-top:10px; color:#555; text-decoration:none; font-size: 13px;">
                        <?= $lang['btn_clear_all'] ?>
                    </a>
                </form>
            </aside>

            <section class="content">
                <div class="sort-bar">
                    <div class="sort-left">
                        <span class="sort-label"><?= $lang['sort_label'] ?></span>
                        <a href="index.php?controller=product&action=list&<?= build_full_url(['sort' => '']) ?>" class="btn-sort <?= $sort == '' ? 'active' : '' ?>"><?= $lang['sort_relevant'] ?></a>
                        <a href="index.php?controller=product&action=list&<?= build_full_url(['sort' => 'new']) ?>" class="btn-sort <?= $sort == 'new' ? 'active' : '' ?>"><?= $lang['sort_newest'] ?></a>
                    </div>
                    <div class="sort-right">
                        <select onchange="location = this.value;" style="padding:5px; border:1px solid #ddd;">
                            <option value="index.php?controller=product&action=list&<?= build_full_url(['sort' => '']) ?>"><?= $lang['sort_price_default'] ?></option>
                            <option value="index.php?controller=product&action=list&<?= build_full_url(['sort' => 'price_asc']) ?>" <?= $sort == 'price_asc' ? 'selected' : '' ?>><?= $lang['sort_price_asc'] ?></option>
                            <option value="index.php?controller=product&action=list&<?= build_full_url(['sort' => 'price_desc']) ?>" <?= $sort == 'price_desc' ? 'selected' : '' ?>><?= $lang['sort_price_desc'] ?></option>
                        </select>
                    </div>
                </div>

                <?php if ($keyword): ?>
                    <div style="margin-bottom:15px; color:#555;"><?= $lang['result_for'] ?> <strong style="color:#ee4d2d"><?= htmlspecialchars($keyword) ?></strong> (<?= $total_records ?>)</div>
                <?php endif; ?>

                <div class="products-grid">
                    <?php if (!empty($productList)): ?>
                        <?php foreach ($productList as $product): ?>
                            <a href="index.php?controller=product&action=detail&id=<?= $product->id ?>" class="product-card">
                                <div class="product-media">
                                    <?php $imgSrc = !empty($product->image) ? $product->image : 'Assets/Images/no-image.jpg'; ?>
                                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($product->name) ?>">
                                    <div class="badge-mall"><?= $lang['mall'] ?></div>
                                    <div class="discount-tag">-10%</div>
                                </div>
                                <div class="product-body">
                                    <div class="product-title"><?= htmlspecialchars($product->name) ?></div>
                                    <div class="price-row">
                                        <div class="price">₫<?= number_format($product->price, 0, ',', '.') ?></div>
                                        <div class="sold"><?= $lang['sold'] ?> <?= number_format($product->sold ?? 0) ?></div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="grid-column:1/-1; text-align:center; padding:50px;">
                            <p><?= $lang['no_products_found'] ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($total_page > 1): ?>
                    <div class="pagination" style="display:flex; justify-content:center; gap:5px; margin-top:30px;">
                        <?php for ($i = 1; $i <= $total_page; $i++): ?>
                            <a href="index.php?controller=product&action=list&<?= build_full_url(['page' => $i]) ?>"
                                style="padding:6px 12px; border:1px solid #ddd; text-decoration:none; color:#333; <?= $i == $page ? 'background:#ee4d2d; color:#fff; border-color:#ee4d2d' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gọi API lấy danh sách tỉnh thành
            $.getJSON('https://esgoo.net/api-tinhthanh/1/0.htm', function(response) {
                if (response.error == 0) {
                    var html = '';

                    $.each(response.data, function(key, val) {
                        var fullName = val.full_name;
                        var cleanName = fullName.replace(/^(Tỉnh|Thành phố)\s+/, "");
                        var isChecked = checkedPlaces.includes(cleanName) ? 'checked' : '';

                        html += `
                            <label style="display: block; margin-bottom: 5px; cursor: pointer;">
                                <input type="checkbox" name="place[]" value="${cleanName}" ${isChecked}> 
                                ${fullName}
                            </label>
                        `;
                    });

                    $('#province-list').html(html);
                } else {
                    // Dùng biến lỗi đã khai báo bằng PHP ở trên
                    $('#province-list').html('<p style="color:red">' + errorLoadingText + '</p>');
                }
            });
        });
    </script>
</body>

</html>