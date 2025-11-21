<?php
// filepath: c:\xampp\htdocs\Shop_Website\Model\Comment\CommentModel.php
include_once("Core/Database.php");
include("Model/Comment/Comment.php");
include("Model/Comment/CommentImage.php");

class CommentModel
{
    private $conn;

    public function __construct()
    {
        $Database = new Database();
        $this->conn = $Database->getConnection();
    }

    public function getCommentsByProductId($productId)
    {
        $sql = "SELECT * FROM comments WHERE product_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $comments = [];

        while ($row = $result->fetch_assoc()) {
            $comment = new Comment(
                $row['id'],
                $row['user_id'],
                $row['product_id'],
                $row['content'],
                $row['rating'] ?? 5,
                $row['created_at']
            );

            // Lấy images của comment này
            $images = $this->getImagesByCommentId($row['id']);
            $comment->images = $images;

            $comments[] = $comment;
        }
        return $comments;
    }

    // Lấy images của 1 comment
    public function getImagesByCommentId($commentId)
    {
        $sql = "SELECT * FROM comment_images WHERE comment_id = ? ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $commentId);
        $stmt->execute();
        $result = $stmt->get_result();
        $images = [];

        while ($row = $result->fetch_assoc()) {
            $images[] = new CommentImage(
                $row['id'],
                $row['comment_id'],
                $row['image_data'],
                $row['created_at']
            );
        }
        return $images;
    }

    // Thêm comment
    public function addComment($userId, $productId, $content, $rating = 5)
    {
        $sql = "INSERT INTO comments (user_id, product_id, content, rating, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iisi", $userId, $productId, $content, $rating);
        if ($stmt->execute()) {
            return $this->conn->insert_id;  // Return comment ID
        }
        return false;
    }

    // Thêm ảnh cho comment
    public function addCommentImage($commentId, $imageData)
    {
        $sql = "INSERT INTO comment_images (comment_id, image_data, created_at) VALUES (?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $commentId, $imageData);
        return $stmt->execute();
    }

    // Xóa comment (cascade delete images)
    public function deleteComment($commentId)
    {
        // Xóa images trước
        $sql1 = "DELETE FROM comment_images WHERE comment_id = ?";
        $stmt1 = $this->conn->prepare($sql1);
        $stmt1->bind_param("i", $commentId);
        $stmt1->execute();

        // Xóa comment
        $sql2 = "DELETE FROM comments WHERE id = ?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $commentId);
        return $stmt2->execute();
    }

    // Update comment
    public function updateComment($commentId, $content, $rating)
    {
        $sql = "UPDATE comments SET content = ?, rating = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $content, $rating, $commentId);
        return $stmt->execute();
    }
}
?>