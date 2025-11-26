<?php
// Nhớ include Model
include_once("Model/Cart/CartModel.php");

class CartController {
    public $cartModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    public function addAction() {
        // 1. Kiểm tra đăng nhập
        // Vì lưu vào DB nên bắt buộc phải có User ID
        if (empty($_SESSION['user'])) {
            // Chưa đăng nhập thì đá về trang login
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        // 2. Lấy dữ liệu
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $productId = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);

            if ($quantity <= 0) $quantity = 1; // Validate số lượng tối thiểu

            // 3. Kiểm tra sản phẩm đã tồn tại trong giỏ chưa
            $existingItem = $this->cartModel->checkProductInCart($userId, $productId);

            if ($existingItem) {
                // TRƯỜNG HỢP A: Đã có -> Cộng dồn số lượng
                $newQuantity = $existingItem['quantity'] + $quantity;
                $this->cartModel->updateQuantity($existingItem['id'], $newQuantity);
            } else {
                // TRƯỜNG HỢP B: Chưa có -> Thêm mới
                $this->cartModel->addToCart($userId, $productId, $quantity);
            }

            // 4. Thông báo và chuyển hướng
            // Có thể set session thông báo thành công nếu muốn
            // Chuyển hướng về trang giỏ hàng hoặc trang sản phẩm vừa xem
            header("Location: index.php?controller=cart&action=index"); 
            exit;
        }
    }
    
    // Hàm hiển thị giỏ hàng (Cập nhật lại từ bài trước để dùng DB)
    public function indexAction() {
         if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
        // Lấy giỏ hàng từ DB thay vì Session
        $cartResult = $this->cartModel->getCartByUser($userId);
        
        $cart = [];
        while($row = mysqli_fetch_assoc($cartResult)) {
            $cart[] = $row;
        }

        include("View/Layout/Header.php");
        include("View/Cart/CartIndex.php"); // View giỏ hàng
        include("View/Layout/Footer.php");
    }
    public function deleteAction() {
        // Kiểm tra đăng nhập
        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        // Lấy ID từ URL (phương thức GET)
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $this->cartModel->deleteCart($id);
        }

        // Quay lại trang giỏ hàng
        header("Location: index.php?controller=cart&action=index");
        exit;
    }

    public function updateAjaxAction() {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            echo json_encode(['status' => 'error', 'message' => 'Chưa đăng nhập']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = $_POST['cart_id'] ?? 0;
            $qty = intval($_POST['quantity'] ?? 0);

            if ($qty < 1) $qty = 1;

            $result = $this->cartModel->updateQuantity($cartId, $qty);

            if ($result) {
                $item = $this->cartModel->getCartItemById($cartId);
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Cập nhật thành công'
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi DB']);
            }
            exit;
        }
    }
}
?>