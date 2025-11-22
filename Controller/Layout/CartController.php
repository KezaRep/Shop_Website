<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class CartController
{
    public function addAction()
    {
        $id = $_POST['product_id'];
        $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;
        // Lấy sản phẩm từ DB dựa theo $id
        $item = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $qty,
            'image' => $product['image']
        ];
        if (!isset($_SESSION['cart']))
            $_SESSION['cart'] = [];
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id'] == $id) {
                $cart_item['quantity'] += $qty;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = $item;
        }
        header('Location: index.php?controller=cart&action=index');
    }

    public function indexAction()
    {
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        include_once "View/Cart/CartIndex.php";
    }

}
?>