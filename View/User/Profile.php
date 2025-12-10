<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Redirect nếu chưa đăng nhập
if (empty($_SESSION['user'])) {
  header('Location: index.php?controller=user&action=login');
  exit;
}

$user = $_SESSION['user']; // mảng user: id, username, email, balance...

$isSeller = isset($user['role']) && $user['role'] == 1;

$productModel = new ProductModel();
if ($isSeller) {
  $productModel = new ProductModel();
  $products = $productModel->getProductsBySeller(intval($user['id'] ?? 0));
}

// helper: hiển thị ảnh (path hoặc binary)
function productImageSrc($img)
{
  if (empty($img))
    return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
  if (@getimagesizefromstring($img)) {
    return 'data:image/jpeg;base64,' . base64_encode($img);
  }
  return $img;
}
?>
<link rel="stylesheet" href="Assets/Css/User/Profile.css">

<main class="profile-container">
  <aside class="profile-sidebar">
    <div class="user-card">
      <div class="user-info">
        <h3 class="username"><?= htmlspecialchars($user['username'] ?? '—') ?></h3>
        <div class="email"><?= htmlspecialchars($user['email'] ?? '—') ?></div>
        <div class="balance">Số dư: <strong><?= number_format($user['balance'] ?? 0, 0, ',', '.') ?> ₫</strong></div>
      </div>
    </div>

    <nav class="profile-actions">
      <a class="btn" href="index.php?controller=user&action=edit">Cập nhật thông tin</a>

      <a class="btn" href="index.php?controller=user&action=purchaseHistory">
        <i class="fas fa-file-invoice-dollar" style="width:20px; text-align:center; margin-right:8px"></i> 
        Đơn mua
      </a>

      <?php if ($isSeller): ?>

        <a class="btn" href="index.php?controller=product&action=list&seller=<?= intval($user['id']) ?>">Chỉnh sửa sản phẩm</a>
        <a class="btn"href="index.php?controller=product&action=wishlistList">Đã thích</a>
        <a class="btn primary" href="index.php?controller=product&action=add">Thêm sản phẩm</a>
        <a class="btn shop-task" href="index.php?controller=shop&action=orderManager">
            <i class="fas fa-clipboard-check" style="color: #ee4d2d;"></i> 
            Duyệt đơn hàng
            <span class="badge-count">New</span> 
        </a>
      <?php endif; ?>
      
      <a class="btn logout" href="index.php?controller=user&action=logout"
        onclick="return confirm('Bạn có chắc muốn đăng xuất?')">Đăng xuất</a>
    </nav>
  </aside>

  <section class="profile-main">
    <div class="main-header">
      <h2>Sản phẩm của bạn</h2>
      <div class="tools">
        <a class="btn small" href="index.php?controller=product&action=add">+ Thêm sản phẩm</a>
      </div>
    </div>

    <?php if (!empty($products)): ?>
      <div class="products-grid">
        <?php foreach ($products as $p): ?>
          <article class="product-card">
            <a href="index.php?controller=product&action=detail&id=<?= intval($p->id ?? $p->p_id ?? 0) ?>">
              <div class="thumb">
                <img src="<?= productImageSrc($p->image ?? $p->p_image ?? '') ?>"
                  alt="<?= htmlspecialchars($p->name ?? $p->p_name ?? '') ?>">
              </div>
              <div class="info">
                <div class="title"><?= htmlspecialchars($p->name ?? $p->p_name ?? '') ?></div>
                <div class="price">₫<?= number_format($p->price ?? $p->p_price ?? 0, 0, ',', '.') ?></div>
              </div>
            </a>
            <div class="card-actions">
              <a class="btn small"
                href="index.php?controller=product&action=edit&id=<?= intval($p->id ?? $p->p_id ?? 0) ?>">Sửa</a>
              <a class="btn danger small"
                href="index.php?controller=product&action=delete&id=<?= intval($p->id ?? $p->p_id ?? 0) ?>"
                onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty">Bạn chưa có sản phẩm nào. <a href="index.php?controller=product&action=add">Thêm ngay</a></div>
    <?php endif; ?>
  </section>
</main>