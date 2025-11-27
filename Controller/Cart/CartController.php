<?php
// Nhớ include Model
include_once("Model/Cart/CartModel.php");

class CartController {
    public $cartModel;
    public $userModel;

    public function __construct() {
        $this->cartModel = new CartModel();
    }

    public function addAction() {
        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        // 2. Lấy dữ liệu
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $productId = intval($_POST['product_id']);
            $quantity = intval($_POST['quantity']);

            if ($quantity <= 0) $quantity = 1; 

            $existingItem = $this->cartModel->checkProductInCart($userId, $productId);

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + $quantity;
                $this->cartModel->updateQuantity($existingItem['id'], $newQuantity);
            } else {
                $this->cartModel->addToCart($userId, $productId, $quantity);
            }

            header("Location: index.php?controller=cart&action=index"); 
            exit;
        }
    }
    
    public function indexAction() {
         if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }
        
        $userId = $_SESSION['user']['id'];
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
        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $this->cartModel->deleteCart($id);
        }
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

    public function checkoutAction() {
        // 1. Kiểm tra đăng nhập
        if (empty($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        $userId = $_SESSION['user']['id'];
        $cart = []; // Mảng chứa sản phẩm thanh toán
        
        // --- PHẦN 1: XỬ LÝ SẢN PHẨM (Lấy những món được tick chọn) ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_cart_id'])) {
            $selectedIds = $_POST['selected_cart_id']; // Mảng ID các món được chọn

            // Lấy toàn bộ giỏ hàng của user
            $cartResult = $this->cartModel->getCartByUser($userId);

            // Lọc ra những món trùng ID với danh sách đã chọn
            while($row = mysqli_fetch_assoc($cartResult)) {
                if (in_array($row['cart_id'], $selectedIds)) {
                    $cart[] = $row;
                }
            }
        } 
        // Lưu ý: Nếu F5 trang, code sẽ không chạy vào if này -> $cart rỗng -> Tiền = 0

        // --- PHẦN 2: XỬ LÝ ĐỊA CHỈ (Code bạn vừa làm) ---
        // Khởi tạo UserModel nếu chưa có
        if (!isset($this->userModel)) {
            require_once './Model/User/UserModel.php';
            $this->userModel = new UserModel();
        }

        // Lấy danh sách địa chỉ
        $addresses = $this->userModel->getUserAddresses($userId);
        
        // Lấy địa chỉ đầu tiên làm mặc định
        $deliveryAddress = null;
        if (!empty($addresses)) {
            $deliveryAddress = $addresses[0]; 
        }

        // --- PHẦN 3: GỌI VIEW ---
        // Nếu giỏ hàng rỗng (do F5 hoặc hack), có thể đá về trang giỏ hàng
        if (empty($cart)) {
             // Tạm thời comment dòng này để bạn debug, 
             // nếu chạy thật thì nên mở ra để chặn đơn hàng 0 đồng
             // echo "<script>alert('Giỏ hàng rỗng! Vui lòng chọn sản phẩm.'); window.location.href='index.php?controller=cart&action=index';</script>";
             // exit;
        }

        include("View/Layout/Header.php");
        include("View/Checkout/Checkout.php");
        include("View/Layout/Footer.php");
    }
}
?>