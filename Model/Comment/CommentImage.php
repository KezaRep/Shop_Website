<?php
// filepath: c:\xampp\htdocs\Shop_Website\Model\Comment\CommentImage.php
class CommentImage
{
    public $id;
    public $comment_id;
    public $image_data;  // BLOB hoặc path
    public $created_at;

    public function __construct($id, $comment_id, $image_data, $created_at)
    {
        $this->id = $id;
        $this->comment_id = $comment_id;
        $this->image_data = $image_data;
        $this->created_at = $created_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCommentId()
    {
        return $this->comment_id;
    }

    public function getImageData()
    {
        return $this->image_data;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
?>