<?php
require_once "Core/Database.php";

class ProfileController {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function indexAction() {
        $conn = $this->db->getConnection();
        
        $user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // 2. Tìm Shop của người này
        $sqlShop = "SELECT * FROM shops WHERE user_id = $user_id LIMIT 1";
        $resultShop = mysqli_query($conn, $sqlShop);
        
        // Kiểm tra xem người này có Shop chưa
        if (mysqli_num_rows($resultShop) > 0) {
            $shop = mysqli_fetch_object($resultShop);
            
            $productList = [];
            $sqlProduct = "SELECT * FROM products WHERE seller_id = $user_id ORDER BY id DESC";

            $resultProduct = mysqli_query($conn, $sqlProduct);
            if ($resultProduct) {
                while ($row = mysqli_fetch_object($resultProduct)) {
                    $productList[] = $row;
                }
            }
        } else {
            echo "<script>alert('Shop không tồn tại!'); window.location.href='index.php';</script>";
            exit();
        }

        require_once "View/Shop/Profile.php";
    }
}
?>