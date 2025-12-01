<?php
require_once("Core/Database.php");

class OrderModel
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getOrdersBySeller($seller_id)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT DISTINCT o.*, u.username as buyer_name 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN order_details od ON o.id = od.order_id
                JOIN products p ON od.product_id = p.id
                WHERE p.seller_id = ? 
                ORDER BY o.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_object()) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getOrderItems($order_id, $seller_id)
    {
        $conn = $this->db->getConnection();

        $sql = "SELECT od.*, p.name as product_name, p.image 
                FROM order_details od
                JOIN products p ON od.product_id = p.id
                WHERE od.order_id = ? AND p.seller_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $seller_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        return $items;
    }

    public function updateStatus($order_id, $status)
    {
        $conn = $this->db->getConnection();
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
    public function getOrderDetails($order_id)
    {
        $conn = $this->db->getConnection();

        // Join thêm bảng shops (s) để biết món hàng này của shop nào
        $sql = "SELECT od.*, 
                       p.name as product_name, 
                       p.image, 
                       p.seller_id,             -- Lấy ID người bán
                       s.shop_name,             -- Lấy tên Shop
                       s.avatar as shop_avatar  -- Lấy avatar Shop
                FROM order_details od 
                JOIN products p ON od.product_id = p.id 
                JOIN shops s ON p.seller_id = s.user_id 
                WHERE od.order_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_object()) {
            $items[] = $row;
        }
        return $items;
    }
    public function getOrdersByUser($user_id)
    {
        $conn = $this->db->getConnection();

        // QUERY NÀY RẤT QUAN TRỌNG:
        // 1. Từ bảng orders (o)
        // 2. Join vào order_details (od) để biết đơn có món gì
        // 3. Join vào products (p) để biết món đó của ai bán (seller_id)
        // 4. Join vào shops (s) để lấy tên và avatar shop
        // 5. GROUP BY o.id: Để gom nhóm lại, tránh việc 1 đơn hiện nhiều lần

        $sql = "SELECT o.*, s.shop_name, s.avatar as shop_avatar
                FROM orders o
                JOIN order_details od ON o.id = od.order_id
                JOIN products p ON od.product_id = p.id
                JOIN shops s ON p.seller_id = s.user_id
                WHERE o.user_id = ?
                GROUP BY o.id
                ORDER BY o.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_object()) {
            $orders[] = $row;
        }
        return $orders;
    }
}
