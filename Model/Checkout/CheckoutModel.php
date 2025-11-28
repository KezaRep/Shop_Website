<?php 
class CheckoutModel extends Database {
    public function createOrder($userId, $recipientName, $recipientPhone, $recipientAddress, $totalMoney, $note = '') {
        $sql = "INSERT INTO orders(user_id, recipient_name, recipient_phone, recipient_address, total_money, note, status, created_at)
        VALUES ('$userId', '$recipientName', '$recipientPhone', '$recipientAddress', '$totalMoney', '$note', 0, NOW())";
        
        $this->conn->query($sql);
        
        return $this->conn->insert_id;
    }

    public function addOrderDetail($orderId, $productId, $productName, $price, $quantity) {
        $sql = "INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                VALUES ('$orderId', '$productId', '$productName', '$price', '$quantity')";

        return $this->conn->query($sql);
    }
}
?>