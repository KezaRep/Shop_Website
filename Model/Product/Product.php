<?php
class Product
{
    public $id;
    public $name;
    public $price;
    public $image;
    public $description;
    public $seller_id;
    public $quantity;
    public $sold;
    public $rating;
    public $category_id;
    public $created_at;
    public function __construct($id, $name, $price, $image, $description,$seller_id, $quantity, $sold, $rating, $category_id, $created_at)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->image = $image;
        $this->description = $description;
        $this->seller_id = $seller_id;
        $this->quantity = $quantity;
        $this->sold = $sold;
        $this->rating = $rating;
        $this->category_id = $category_id;
        $this->created_at = $created_at;
    }
}
?>