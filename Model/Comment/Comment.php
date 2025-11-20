<?php
// filepath: c:\xampp\htdocs\Shop_Website\Model\Comment\Comment.php
class Comment
{
    public $id;
    public $user_id;
    public $product_id;
    public $content;
    public $rating;
    public $created_at;
    public $images = [];  // Mảng CommentImage

    public function __construct($id, $user_id, $product_id, $content, $rating = 5, $created_at = null)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->content = $content;
        $this->rating = $rating;
        $this->created_at = $created_at;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function getProductId()
    {
        return $this->product_id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getImages()
    {
        return $this->images;
    }
}
?>