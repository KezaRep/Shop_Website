<?php
require_once 'Core/Database.php';
require_once 'Model/Category/Category.php'; // Gọi file Class Category

class CategoryModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getCategoryList()
    {
        $query = "SELECT * FROM categories ORDER BY id ASC";
        $result = mysqli_query($this->conn, $query);

        $categories = [];

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $categories[] = new Category(
                    $row['id'],
                    $row['name'],
                    isset($row['description']) ? $row['description'] : null
                );
            }
        }
        return $categories;
    }
}
?>