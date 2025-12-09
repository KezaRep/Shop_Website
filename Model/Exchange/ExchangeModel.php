<?php
// Model/Exchange/ExchangeModel.php
include_once('Core/Database.php');
class ExchangeModel {
    
    public $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Tạo yêu cầu trao đổi mới
    public function createExchange($data) {
        $sql = "INSERT INTO exchanges (user_id, product_id, desired_product_id, title, description, condition_item, exchange_type, location, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiisssss", 
            $data['user_id'], 
            $data['product_id'], 
            $data['desired_product_id'],
            $data['title'], 
            $data['description'], 
            $data['condition_item'],
            $data['exchange_type'],
            $data['location']
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    // Lấy tất cả bài đăng trao đổi
    public function getAllExchanges($limit = 20, $offset = 0, $filters = []) {
        $sql = "SELECT e.*, u.username, u.phone, p.name as product_name, p.image as product_image
                FROM exchanges e
                LEFT JOIN users u ON e.user_id = u.id
                LEFT JOIN products p ON e.product_id = p.id
                WHERE e.status = 'active'";
        
        // Thêm filters
        if (!empty($filters['exchange_type'])) {
            $sql .= " AND e.exchange_type = '" . mysqli_real_escape_string($this->conn, $filters['exchange_type']) . "'";
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND e.location LIKE '%" . mysqli_real_escape_string($this->conn, $filters['location']) . "%'";
        }
        
        if (!empty($filters['condition'])) {
            $sql .= " AND e.condition_item = '" . mysqli_real_escape_string($this->conn, $filters['condition']) . "'";
        }
        
        if (!empty($filters['keyword'])) {
            $keyword = mysqli_real_escape_string($this->conn, $filters['keyword']);
            $sql .= " AND (e.title LIKE '%$keyword%' OR e.description LIKE '%$keyword%')";
        }
        
        $sql .= " ORDER BY e.created_at DESC LIMIT $limit OFFSET $offset";
        
        return mysqli_query($this->conn, $sql);
    }

    // Đếm tổng số bài đăng
    public function countExchanges($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM exchanges e WHERE e.status = 'active'";
        
        if (!empty($filters['exchange_type'])) {
            $sql .= " AND e.exchange_type = '" . mysqli_real_escape_string($this->conn, $filters['exchange_type']) . "'";
        }
        
        if (!empty($filters['location'])) {
            $sql .= " AND e.location LIKE '%" . mysqli_real_escape_string($this->conn, $filters['location']) . "%'";
        }
        
        if (!empty($filters['condition'])) {
            $sql .= " AND e.condition_item = '" . mysqli_real_escape_string($this->conn, $filters['condition']) . "'";
        }
        
        if (!empty($filters['keyword'])) {
            $keyword = mysqli_real_escape_string($this->conn, $filters['keyword']);
            $sql .= " AND (e.title LIKE '%$keyword%' OR e.description LIKE '%$keyword%')";
        }
        
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    // Lấy chi tiết một bài trao đổi
    public function getExchangeById($id) {
        $sql = "SELECT e.*, u.username, u.phone, u.email, p.name as product_name, p.image as product_image, p.price as product_price
                FROM exchanges e
                LEFT JOIN users u ON e.user_id = u.id
                LEFT JOIN products p ON e.product_id = p.id
                WHERE e.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exchange = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $exchange;
    }

    // Lấy bài đăng của user
    public function getExchangesByUser($userId) {
        $sql = "SELECT e.*, p.name as product_name, p.image as product_image
                FROM exchanges e
                LEFT JOIN products p ON e.product_id = p.id
                WHERE e.user_id = ?
                ORDER BY e.created_at DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    // Cập nhật trạng thái
    public function updateStatus($id, $status) {
        $sql = "UPDATE exchanges SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // Xóa bài đăng
    public function deleteExchange($id, $userId) {
        $sql = "DELETE FROM exchanges WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $id, $userId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // Tạo yêu cầu trao đổi (offer)
    public function createExchangeOffer($data) {
        $sql = "INSERT INTO exchange_offers (exchange_id, user_id, product_id, message, status, created_at) 
                VALUES (?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiis", 
            $data['exchange_id'], 
            $data['user_id'], 
            $data['product_id'], 
            $data['message']
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    // Lấy các offer cho một bài đăng
    public function getOffersByExchange($exchangeId) {
        $sql = "SELECT eo.*, u.username, p.name as product_name, p.image as product_image, p.price
                FROM exchange_offers eo
                LEFT JOIN users u ON eo.user_id = u.id
                LEFT JOIN products p ON eo.product_id = p.id
                WHERE eo.exchange_id = ?
                ORDER BY eo.created_at DESC";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $exchangeId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    // Cập nhật trạng thái offer
    public function updateOfferStatus($offerId, $status) {
        $sql = "UPDATE exchange_offers SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $offerId);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}
?>