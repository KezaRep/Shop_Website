<?php
class Database {
    protected $conn;

    public function __construct() {
        // 1. Cấu hình cho Localhost
        $host = "localhost";
        $user = "root";      // Mặc định của XAMPP/WAMP là root
        $pass = "";          // Mặc định thường để trống (nếu dùng MAMP thì điền là "root")
        
        // 2. QUAN TRỌNG: Bạn cần điền đúng tên database bạn đã tạo trong máy của bạn ở đây
        // Ví dụ: "ban_hang", "shop_db", v.v.
        $dbname = "shop_website"; 

        $this->conn = mysqli_connect($host, $user, $pass, $dbname);

        if (!$this->conn) {
            // Thêm chi tiết lỗi để dễ sửa nếu không kết nối được
            die("Kết nối localhost thất bại: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, "utf8");
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>