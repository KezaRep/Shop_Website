<?php
require_once "Core/Database.php";

class ShopModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getShopByUserId($userId)
    {
        $sql = "SELECT * FROM shops WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_object()) {
            return $row;
        }
        return null;
    }

    public function getRevenue($sellerId)
    {
        // TRIM(status) để loại bỏ dấu cách thừa, LIKE 'completed%' để bắt chữ completed
        $sql = "SELECT SUM(total_money) as total 
                FROM orders 
                WHERE seller_id = ? 
                AND (status LIKE 'completed%' OR status = 'completed')";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['total'] ?? 0;
    }

    public function getNewOrdersCount($sellerId)
    {
        // Đếm đơn hàng
        $sql = "SELECT COUNT(*) as count FROM orders WHERE seller_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['count'] ?? 0;
    }

    public function getTotalSold($sellerId)
    {
        $sql = "SELECT SUM(sold) as total_sold FROM products WHERE seller_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result['total_sold'] ?? 0;
    }

    public function getLowStockCount($sellerId, $threshold = 10)
    {
        $sql = "SELECT COUNT(*) as total FROM products WHERE seller_id = ? AND quantity <= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $sellerId, $threshold);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    }

    public function getRecentOrders($sellerId)
    {
        $sql = "SELECT id, recipient_name, total_money, created_at, status 
                FROM orders 
                WHERE seller_id = ? 
                ORDER BY created_at DESC 
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_object()) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function getProductsBySeller($sellerId)
    {
        $sql = "SELECT * FROM products WHERE seller_id = ? ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $result = $stmt->get_result();
        $products = [];

        while ($row = $result->fetch_object()) {
            $products[] = $row;
        }
        return $products;
    }
    public function getRevenueLast7Days($sellerId)
    {
        $data = [];

        $anchorDate = date('Y-m-d');

        for ($i = 6; $i >= 0; $i--) {
            $targetDate = date('Y-m-d', strtotime("$anchorDate -$i days"));
            $label      = date('d/m', strtotime("$anchorDate -$i days"));

            $sql = "SELECT SUM(total_money) as total 
                    FROM orders 
                    WHERE seller_id = ? 
                    AND DATE(created_at) = ? 
                    AND (status = 'completed' OR TRIM(status) LIKE 'completed%')";

            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("is", $sellerId, $targetDate);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $total = $result['total'] ?? 0;

            $data[] = [
                'label' => $label,
                'value' => (int)$total
            ];
        }
        return $data;
    }
    public function getTopSellingProducts($sellerId)
    {
        $sql = "SELECT id, name, price, image, sold, quantity 
                FROM products 
                WHERE seller_id = ? 
                ORDER BY sold DESC 
                LIMIT 5";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_object()) {
            $products[] = $row;
        }
        return $products;
    }
}
