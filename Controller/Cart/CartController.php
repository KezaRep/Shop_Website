<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "Model/Product/ProductModel.php";

class CartController
{
    // ============================
    // ADD TO CART
    // ============================
    public function addAction()
    {
        if (!isset($_POST['product_id'])) {
            die("Thiếu product_id");
        }

        $id = (int) $_POST['product_id'];
        $qty = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        $productModel = new ProductModel();
        $product = $productModel->getProductById($id);

        if (!$product) {
            die("Sản phẩm không tồn tại!");
        }

        // Convert object -> array để lưu vào session
        $item = [
            'id'       => $product->id,
            'name'     => $product->name,
            'price'    => (int) $product->price,
            'quantity' => $qty,
            'image'    => $product->image
        ];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu đã tồn tại thì tăng số lượng
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

        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    // ============================
    // SHOW CART
    // ============================
    public function indexAction()
    {
        $cart = $_SESSION['cart'] ?? [];
        include "View/Cart/CartIndex.php";
    }

    // ============================
    // UPDATE ITEM
    // ============================
    public function updateAction()
    {
        if (!isset($_POST['id']) || !isset($_POST['change'])) {
            die("Thiếu dữ liệu cập nhật giỏ hàng");
        }

        $id = (int) $_POST['id'];
        $change = $_POST['change']; // "+" hoặc "-"

        if (!isset($_SESSION['cart'])) {
            header("Location: index.php?controller=cart&action=index");
            exit;
        }

        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $id) {

                if ($change === '+') {
                    $item['quantity']++;
                }

                if ($change === '-') {
                    $item['quantity']--;
                    if ($item['quantity'] <= 0) {
                        $item['quantity'] = 1;
                    }
                }

                break;
            }
        }

        header("Location: index.php?controller=cart&action=index");
        exit;
    }

ư
    public function deleteAction()
    {
        if (!isset($_GET['id'])) {
            die("Thiếu id sản phẩm để xóa");
        }

        $id = (int) $_GET['id'];

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
        }

        header("Location: index.php?controller=cart&action=index");
        exit;
    }
}
