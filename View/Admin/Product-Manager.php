<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title>
  <link rel="stylesheet" href="Assets/css/Admin/product-manager.css">
</head>

<body>
  <div class="admin-container">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><a href="index.php?controller=admin&action=dashboard">📊 Dashboard</a></li>
        <li class="active"><a href="#">🛍 Quản lý sản phẩm</a></li>
        <li><a href="index.php?controller=admin&action=user">👥 Quản lý người dùng</a></li>
        <li><a href="index.php?controller=user&action=logout">🚪 Đăng xuất</a></li>
      </ul>
    </aside>

    <main class="content">
      <div class="header">
        <h1>Danh sách sản phẩm</h1>
        <a href="index.php?controller=product&action=addProduct" class="btn-add">+ Thêm sản phẩm</a>
      </div>

      <table class="product-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Hình ảnh</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <?php
          include_once('Model/Product/ProductModel.php');
          $productModel = new ProductModel();
          $products = $productModel->getProductList();

          if (!empty($products)) {
            foreach ($products as $p) { ?>
              <tr>
                <td><?= htmlspecialchars($p->p_id) ?></td>
                <td>
                  <?php if (!empty($p->p_image)) { ?>
                    <img src="data:image/jpeg;base64,<?= base64_encode($p->p_image) ?>" alt="Ảnh" class="thumb">
                  <?php } else { ?>
                    <span>Không có ảnh</span>
                  <?php } ?>
                </td>
                <td><?= htmlspecialchars($p->p_name) ?></td>
                <td><?= number_format($p->p_price, 0, ',', '.') ?> đ</td>
                <td><?= htmlspecialchars($p->p_quantity) ?></td>
                <td>
                  <a href="index.php?controller=product&action=edit&id=<?=$p->p_id ?>" class="btn-edit">Sửa</a>
                  <a href="index.php?controller=product&action=delete&id=<?= $p->p_id ?>" class="btn-delete" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
                </td>
              </tr>
            <?php }
          } else {
            echo "<tr><td colspan='6' style='text-align:center;'>Chưa có sản phẩm nào</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
