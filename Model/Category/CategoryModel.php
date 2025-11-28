<?php
include_once('Core/Database.php');
include_once('Model/Category/Category.php'); // Class Category

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
        $query = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $categories = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = new Category(
                $row['id'],
                $row['name'],
                $row['description']
            );
        }

        return $categories;
    }
    public function addCategory($name, $description = null)
    {
        $query = "INSERT INTO categories (name, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($query);

        // Truyền giá trị theo thứ tự placeholder
        return $stmt->execute([$name, $description]);
    }

}
