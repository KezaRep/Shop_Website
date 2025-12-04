<?php
// View/Exchange/Detail.php
require_once './View/Layout/Header.php';

function exchangeImageSrc($img) {
    if (empty($img)) return '/Shop_Website/Assets/Images/placeholder-product-1.jpg';
    if (@getimagesizefromstring($img)) {
        return 'data:image/jpeg;base64,' . base64_encode($img);
    }
    return $img;
}

$isOwner = !empty($_SESSION['user']) && $_SESSION['user']['id'] == $exchange['user_id'];
?>

<link rel="stylesheet" href="/Shop_Website/Assets/CSS/Exchange/Detail.css">

<div class="exchange-detail-page">
    <div class="container">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="index.php">Trang chủ</a>
            <span>/</span>
            <a href="index.php?controller=exchange&action=index">Trao đổi</a>
            <span>/</span>
            <span><?= htmlspecialchars($exchange['title']) ?></span>
        </nav>

        <div class="detail-wrapper">
            <!-- Left Column - Image & Info -->
            <div class="detail-left">
                <!-- Image Section -->
                <div class="image-section">
                    <img src="<?= exchangeImageSrc($exchange['product_image']) ?>" 
                         alt="<?= htmlspecialchars($exchange['title']) ?>" 
                         class="main-image">
                    
                    <!-- Badges -->
                    <div class="badges-overlay">
                        <span class="type-badge type-<?= $exchange['exchange_type'] ?>">
                            <?php
                            $typeLabels = [
                                'product' => 'Trao đổi',
                                'sell' => 'Bán',
                                'free' => 'Tặng miễn phí'
                            ];
                            echo $typeLabels[$exchange['exchange_type']] ?? 'Trao đổi';
                            ?>
                        </span>
                        
                        <span class="condition-badge">
                            <?php
                            $conditionLabels = [
                                'new' => 'Mới 100%',
                                'like-new' => 'Như mới',
                                'good' => 'Tốt',
                                'fair' => 'Khá'
                            ];
                            echo $conditionLabels[$exchange['condition_item']] ?? 'Tốt';
                            ?>
                        </span>
                    </div>
                </div>

                <!-- Product Info Card -->
                <?php if (!empty($exchange['product_name'])): ?>
                <div class="product-info-card">
                    <h3>Thông tin sản phẩm</h3>
                    <div class="info-row">
                        <span class="label">Sản phẩm:</span>
                        <span class="value"><?= htmlspecialchars($exchange['product_name']) ?></span>
                    </div>
                    <?php if (!empty($exchange['product_price'])): ?>
                    <div class="info-row">
                        <span class="label">Giá gốc:</span>
                        <span class="value price">₫<?= number_format($exchange['product_price'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Details -->
            <div class="detail-right">
                <!-- Title & Meta -->
                <div class="title-section">
                    <h1 class="exchange-title"><?= htmlspecialchars($exchange['title']) ?></h1>
                    
                    <div class="meta-info">
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?= date('d/m/Y H:i', strtotime($exchange['created_at'])) ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= htmlspecialchars($exchange['location']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="description-section">
                    <h3>Mô tả chi tiết</h3>
                    <p><?= nl2br(htmlspecialchars($exchange['description'])) ?></p>
                </div>

                <!-- User Info -->
                <div class="user-section">
                    <h3>Người đăng</h3>
                    <div class="user-card">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?= htmlspecialchars($exchange['username']) ?></div>
                            <div class="user-contact">
                                <?php if (!empty($exchange['phone'])): ?>
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span><?= htmlspecialchars($exchange['phone']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($exchange['email'])): ?>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span><?= htmlspecialchars($exchange['email']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-section">
                    <?php if ($isOwner): ?>
                        <a href="index.php?controller=exchange&action=edit&id=<?= $exchange['id'] ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i>
                            Chỉnh sửa
                        </a>
                        <a href="index.php?controller=exchange&action=delete&id=<?= $exchange['id'] ?>" 
                           class="btn btn-delete" onclick="return confirm('Bạn có chắc muốn xóa?')">
                            <i class="fas fa-trash"></i>
                            Xóa bài đăng
                        </a>
                    <?php elseif (!empty($_SESSION['user'])): ?>
                        <button class="btn btn-offer" onclick="showOfferModal()">
                            <i class="fas fa-handshake"></i>
                            Gửi đề xuất trao đổi
                        </button>
                        <a href="tel:<?= htmlspecialchars($exchange['phone']) ?>" class="btn btn-contact">
                            <i class="fas fa-phone"></i>
                            Liên hệ
                        </a>
                    <?php else: ?>
                        <a href="index.php?controller=user&action=login" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i>
                            Đăng nhập để trao đổi
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Offers Section (Only for owner) -->
                <?php if ($isOwner && $offers && mysqli_num_rows($offers) > 0): ?>
                <div class="offers-section">
                    <h3>Các đề xuất trao đổi (<?= mysqli_num_rows($offers) ?>)</h3>
                    <?php while ($offer = mysqli_fetch_assoc($offers)): ?>
                        <div class="offer-card <?= $offer['status'] ?>">
                            <div class="offer-header">
                                <div class="offer-user">
                                    <i class="fas fa-user-circle"></i>
                                    <span><?= htmlspecialchars($offer['username']) ?></span>
                                </div>
                                <span class="offer-status status-<?= $offer['status'] ?>">
                                    <?php
                                    $statusLabels = [
                                        'pending' => 'Chờ xử lý',
                                        'accepted' => 'Đã chấp nhận',
                                        'rejected' => 'Đã từ chối'
                                    ];
                                    echo $statusLabels[$offer['status']] ?? $offer['status'];
                                    ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($offer['product_name'])): ?>
                            <div class="offer-product">
                                <img src="<?= exchangeImageSrc($offer['product_image']) ?>" alt="">
                                <div class="offer-product-info">
                                    <div class="offer-product-name"><?= htmlspecialchars($offer['product_name']) ?></div>
                                    <?php if (!empty($offer['price'])): ?>
                                    <div class="offer-product-price">₫<?= number_format($offer['price'], 0, ',', '.') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($offer['message'])): ?>
                            <div class="offer-message">
                                <i class="fas fa-comment"></i>
                                <?= nl2br(htmlspecialchars($offer['message'])) ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="offer-time">
                                <i class="fas fa-clock"></i>
                                <?= date('d/m/Y H:i', strtotime($offer['created_at'])) ?>
                            </div>
                            
                            <?php if ($offer['status'] === 'pending'): ?>
                            <div class="offer-actions">
                                <a href="index.php?controller=exchange&action=updateOfferStatus&offer_id=<?= $offer['id'] ?>&status=accepted&exchange_id=<?= $exchange['id'] ?>" 
                                   class="btn-accept">
                                    <i class="fas fa-check"></i> Chấp nhận
                                </a>
                                <a href="index.php?controller=exchange&action=updateOfferStatus&offer_id=<?= $offer['id'] ?>&status=rejected&exchange_id=<?= $exchange['id'] ?>" 
                                   class="btn-reject">
                                    <i class="fas fa-times"></i> Từ chối
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Offer Modal -->
<?php if (!$isOwner && !empty($_SESSION['user'])): ?>
<div id="offerModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeOfferModal()">&times;</span>
        <h2>Gửi đề xuất trao đổi</h2>
        
        <form action="index.php?controller=exchange&action=makeOffer" method="POST" class="offer-form">
            <input type="hidden" name="exchange_id" value="<?= $exchange['id'] ?>">
            
            <div class="form-group">
                <label>Sản phẩm bạn muốn đổi:</label>
                <select name="product_id" required class="form-control">
                    <option value="">-- Chọn sản phẩm --</option>
                    <!-- Thêm danh sách sản phẩm của user từ controller -->
                </select>
            </div>
            
            <div class="form-group">
                <label>Lời nhắn:</label>
                <textarea name="message" rows="4" class="form-control" placeholder="Nhập lời nhắn của bạn..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Gửi đề xuất
                </button>
                <button type="button" class="btn btn-cancel" onclick="closeOfferModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
function showOfferModal() {
    document.getElementById('offerModal').style.display = 'flex';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('offerModal');
    if (event.target === modal) {
        closeOfferModal();
    }
}
</script>
<?php endif; ?>

<?php require_once './View/Layout/Footer.php'; ?>