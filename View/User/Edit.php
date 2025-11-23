<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect nếu chưa đăng nhập
if (empty($_SESSION['user'])) {
    header('Location: index.php?controller=user&action=login');
    exit;
}

// Hàm hiển thị avatar
function avatarSrc($avatar) {
    if (empty($avatar)) return '/Shop_Website/Assets/Images/avatar-placeholder.png';
    if (@getimagesizefromstring($avatar)) {
        return 'data:image/jpeg;base64,' . base64_encode($avatar);
    }
    return $avatar;
}
?>
<link rel="stylesheet" href="/Shop_Website/Assets/Css/User/Edit.css">

<main class="edit-user-page">
    <div class="container">
        <h2>Chỉnh sửa người dùng</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="index.php?controller=user&action=edit&id=<?= htmlspecialchars($user->id) ?>" method="post" enctype="multipart/form-data" class="edit-user-form">

            <div class="row">
                <label for="username">Tên người dùng</label>
                <input id="username" name="username" type="text" value="<?= htmlspecialchars($user->username ?? '') ?>" required>
            </div>

            <div class="row">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>
            </div>

            <div class="row">
                <label for="password">Mật khẩu mới</label>
                <input id="password" name="password" type="password" placeholder="Để trống nếu không đổi">
            </div>

           

            <div class="row">
                <label for="role">Vai trò</label>
                <select id="role" name="role">
                    <option value="0" <?= ($user->role==0) ? 'selected' : '' ?>>Người dùng</option>
                    <option value="1" <?= ($user->role==1) ? 'selected' : '' ?>>Quản trị</option>
                </select>
            </div>

            <div class="row">
                <label for="avatar">Avatar</label>
                <div class="current-avatar">
                    <img id="previewAvatar" src="<?= avatarSrc($user->avatar) ?>" alt="avatar">
                </div>
                <input id="avatar" name="avatar" type="file" accept="image/*">
                <small class="hint">Chọn ảnh mới để thay thế avatar hiện tại.</small>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">Lưu</button>
                <a class="btn btn-cancel" href="index.php?controller=user&action=profile">Hủy</a>
            </div>

        </form>
    </div>
</main>

<script>
document.getElementById('avatar')?.addEventListener('change', function (e) {
    const f = e.target.files[0];
    if (!f) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const img = document.getElementById('previewAvatar');
        if (img) img.src = ev.target.result;
    };
    reader.readAsDataURL(f);
});
</script>
