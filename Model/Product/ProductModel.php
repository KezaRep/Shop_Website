<?php
include_once("Core/Database.php");
include("Model/Product/Product.php");
class ProductModel
{
    private $conn;

    public function __construct()
    {
        $Database = new Database();
        $this->conn = $Database->getConnection();
    }

    public function getAllProducts()
    {
        $sql = "SELECT * FROM products";
        $result = $this->conn->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['description'],
                $row['seller_id'],
                $row['quantity'],
                $row['sold'],
                $row['rating'],
                $row['category_id'],
                $row['created_at']
            );
        }
        return $products;
    }
    public function getProductByCategory($category_id)
    {
        $sql = "SELECT * FROM products WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['description'],
                $row['seller_id'],
                $row['quantity'],
                $row['sold'],
                $row['rating'],
                $row['category_id'],
                $row['created_at']
            );
        }
        return $products;
    }
    public function getProductById($product_id)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['description'],
                $row['seller_id'],
                $row['quantity'],
                $row['sold'],
                $row['rating'],
                $row['category_id'],
                $row['created_at']
            );
        }
        return null;
    }
    public function getProductsBySeller($seller_id)
    {
        $sql = "SELECT * FROM products WHERE seller_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['description'],
                $row['seller_id'],
                $row['quantity'],
                $row['sold'],
                $row['rating'],
                $row['category_id'],
                $row['created_at']
            );
        }
        return $products;
    }
    public function searchProducts($keyword)
    {
        $sql = "SELECT * FROM products WHERE name LIKE ?";
        $likeKeyword = "%" . $keyword . "%";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $likeKeyword);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['description'],
                $row['seller_id'],
                $row['quantity'],
                $row['sold'],
                $row['rating'],
                $row['category_id'],
                $row['created_at']
            );
        }
        return $products;
    }
    public function addProduct($name, $price, $image, $video_url, $description, $seller_id, $quantity, $category_id)
    {
        // Câu lệnh SQL thêm cột video_url
        // Cột 'image' bây giờ lưu chuỗi đường dẫn, không phải BLOB
        $sql = "INSERT INTO products (name, price, image, video_url, description, seller_id, quantity, sold, rating, category_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        
        // Kiểu dữ liệu: 
        // s (name), d (price), s (image path), s (video path), s (desc), i (seller), i (qty), i (category)
        $stmt->bind_param("sdsssiii", $name, $price, $image, $video_url, $description, $seller_id, $quantity, $category_id);
        
        return $stmt->execute();
    }
    public function updateProductQuantity($product_id, $new_quantity)
    {
        $sql = "UPDATE products SET quantity = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $new_quantity, $product_id);
        return $stmt->execute();
    }
    public function deleteProduct($product_id)
    {
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        return $stmt->execute();
    }
    public function updateProductRating($product_id, $new_rating)
    {
        $sql = "UPDATE products SET rating = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $new_rating, $product_id);
        return $stmt->execute();
    }
    public function updateProductSoldCount($product_id, $new_sold_count)
    {
        $sql = "UPDATE products SET sold = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $new_sold_count, $product_id);
        return $stmt->execute();
    }
    public function updateProductDetails($product_id, $name, $price, $image, $description, $quantity, $category_id)
    {
        $sql = "UPDATE products SET name = ?, price = ?, image = ?, description = ?, quantity = ?, category_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdssiii", $name, $price, $image, $description, $quantity, $category_id, $product_id);
        return $stmt->execute();
    }
}