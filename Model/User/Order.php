<?php
require_once("Model/Database.php");

class OrderModel extends Database {
    
    // 1. Hàm tạo đơn hàng chung (Lưu vào bảng orders)
    // Hàm này trả về ID của đơn hàng vừa tạo (để dùng cho bảng chi tiết)
    public function createOrder($userId, $totalMoney, $note, $recName, $recPhone, $recAddress) {
        // status mặc định là 'pending' (Chờ xử lý)
        $sql = "INSERT INTO orders (user_id, total_money, note, recipient_name, recipient_phone, recipient_address, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param("idssss", $userId, $totalMoney, $note, $recName, $recPhone, $recAddress);
        
        if ($stmt->execute()) {
            // QUAN TRỌNG: Lấy ID của dòng vừa insert (Ví dụ đơn hàng số 50)
            return $this->conn->insert_id; 
        }
        return false;
    }

    public function createOrderDetail($orderId, $productId, $productName, $price, $quantity) {
        $sql = "INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        // 'iisdi': int, int, string, double, int
        $stmt->bind_param("iisdi", $orderId, $productId, $productName, $price, $quantity);
        return $stmt->execute();
    }
}
?>