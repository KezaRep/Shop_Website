<?php 
require_once "Core/Database.php";

class ShopModel {
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    public function getShopByUserId($userId) {
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
}
?>