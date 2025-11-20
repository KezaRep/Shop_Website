<?php
// filepath: c:\xampp\htdocs\Shop_Website\Controller\Comment\CommentController.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once("Model/Comment/CommentModel.php");

class CommentController
{
    public $commentModel;

    public function __construct()
    {
        $this->commentModel = new CommentModel();
    }

    // Hiển thị form comment + danh sách (gọi từ ProductDetail)

    public function addAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'] ?? 0;
            $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;

            if (empty($userId) || empty($productId) || empty($content)) {
                $error = "Vui lòng điền đầy đủ thông tin!";
                header("Location: index.php?controller=product&action=detail&id=$productId");
                exit;
            }

            // Thêm comment
            $commentId = $this->commentModel->addComment($userId, $productId, $content, $rating);

            if ($commentId) {
                // Upload images (nếu có)
                if (!empty($_FILES['images']['tmp_name'])) {
                    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                        if (is_uploaded_file($tmp_name)) {
                            $imageData = file_get_contents($tmp_name);
                            $this->commentModel->addCommentImage($commentId, $imageData);
                        }
                    }
                }

                header("Location: index.php?controller=product&action=detail&id=$productId#comments");
                exit;
            } else {
                $error = "Không thể thêm bình luận!";
            }
        }

        // Nếu GET, redirect về product detail
        header("Location: index.php?controller=product&action=detail");
        exit;
    }

    // Xóa comment
    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentId = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;
            $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

            $ok = $this->commentModel->deleteComment($commentId);

            if ($ok) {
                header("Location: index.php?controller=product&action=detail&id=$productId#comments");
                exit;
            }
        }
    }

    // Update comment
    public function updateAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $commentId = isset($_POST['comment_id']) ? (int) $_POST['comment_id'] : 0;
            $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
            $content = isset($_POST['content']) ? trim($_POST['content']) : '';
            $rating = isset($_POST['rating']) ? (int) $_POST['rating'] : 5;

            if (empty($commentId) || empty($content)) {
                header("Location: index.php?controller=product&action=detail&id=$productId");
                exit;
            }

            $ok = $this->commentModel->updateComment($commentId, $content, $rating);

            if ($ok) {
                header("Location: index.php?controller=product&action=detail&id=$productId#comments");
                exit;
            }
        }
    }
}
?>