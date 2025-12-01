<?php
class CartModel extends Database
{
    public function checkProductInCart($user_id, $product_id)
    {
        $sql = "SELECT * FROM carts WHERE user_id = '$user_id' AND product_id = '$product_id'";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }
    public function addToCart($user_id, $product_id, $quantity)
    {
        $sql = "INSERT INTO carts(user_id, product_id, quantity) VALUES ('$user_id','$product_id','$quantity')";
        $result = mysqli_query($this->conn, $sql);
    }
    public function updateQuantity($cartId, $newQuantity)
    {
        $sql = "UPDATE carts SET quantity = '$newQuantity' WHERE id= '$cartId'";
        return mysqli_query($this->conn, $sql);
    }
    public function getCartByUser($userId)
    {
        $sql = "SELECT c.id as cart_id, 
                       c.quantity, 
                       p.id as product_id, 
                       p.name, 
                       p.price, 
                       p.image, 
                       p.quantity as stock,
                       p.seller_id,            
                       s.shop_name, 
                       s.avatar as shop_avatar
                FROM carts c 
                JOIN products p ON c.product_id = p.id 
                LEFT JOIN shops s ON p.seller_id = s.user_id
                WHERE c.user_id = '$userId'";

        return mysqli_query($this->conn, $sql);
    }
    public function getCartItemById($cartId)
    {
        $cartId = intval($cartId);
        $sql = "SELECT * FROM carts WHERE id = $cartId";
        $result = mysqli_query($this->conn, $sql);
        return mysqli_fetch_assoc($result);
    }
    public function deleteCart($cartId)
    {
        $cartId = intval($cartId);
        $sql = "DELETE FROM carts WHERE id = $cartId";
        return mysqli_query($this->conn, $sql);
    }
}
