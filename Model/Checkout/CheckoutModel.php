<?php
class CheckoutModel extends Database
{
    public function createOrder($userId, $recipientName, $recipientPhone, $recipientAddress, $totalMoney, $seller_id, $note = '')
    {
        // Câu SQL giữ nguyên thứ tự cột, chỉ đổi biến truyền vào cho đúng
        $sql = "INSERT INTO orders(user_id, seller_id, recipient_name, recipient_phone, recipient_address, total_money, note, status, created_at)
            VALUES ('$userId', '$seller_id', '$recipientName', '$recipientPhone', '$recipientAddress', '$totalMoney', '$note', 'pending', NOW())";

        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        }
        return false;
    }

    public function addOrderDetail($orderId, $productId, $productName, $price, $quantity)
    {
        $sql = "INSERT INTO order_details (order_id, product_id, product_name, price, quantity) 
                VALUES ('$orderId', '$productId', '$productName', '$price', '$quantity')";

        return $this->conn->query($sql);
    }
    public function findOrder($orderId)
    {

        $orderId = intval($orderId);

        $sql = "SELECT * FROM orders WHERE id = $orderId";
        $result = $this->conn->query($sql);

        return $result->fetch_assoc();
    }
    public function findOrderDetails($orderId)
    {
        $orderId = intval($orderId);

        $sql = "SELECT od.*, p.image 
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = $orderId";

        $result = $this->conn->query($sql);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
