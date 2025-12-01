<?php
require_once './Core/Database.php'; 

class MapModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllStores() {
        $sql = "SELECT * FROM stores WHERE status = 1";
        $result = $this->conn->query($sql);

        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['latitude'] = (float)$row['latitude'];
                $row['longitude'] = (float)$row['longitude'];
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>