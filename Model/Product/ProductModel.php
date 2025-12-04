<?php
include_once("Core/Database.php");
include_once("Model/Product/Product.php"); // Nên dùng include_once

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
                $row['video_url'],
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
            // Đã thêm $row['video_url']
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['video_url'], // <--- THÊM Ở ĐÂY
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
            // Đã thêm $row['video_url']
            return new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['video_url'], // <--- QUAN TRỌNG NHẤT LÀ Ở ĐÂY (CHO TRANG CHI TIẾT)
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
            // Đã thêm $row['video_url']
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['video_url'], // <--- THÊM Ở ĐÂY
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
            // Đã thêm $row['video_url']
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['video_url'], // <--- THÊM Ở ĐÂY
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

    public function getProductsPriceAsc()
    {
        $sql = "SELECT * FROM products ORDER BY price ASC";
        return $this->fetchProducts($sql);
    }

    public function getProductsPriceDesc()
    {
        $sql = "SELECT * FROM products ORDER BY price DESC";
        return $this->fetchProducts($sql);
    }

    public function fetchProducts($sql)
    {
        $result = $this->conn->query($sql);
        $products = [];

        while ($row = $result->fetch_assoc()) {
            $products[] = new Product(
                $row['id'],
                $row['name'],
                $row['price'],
                $row['image'],
                $row['video_url'],
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



    // Các hàm add/update giữ nguyên vì không ảnh hưởng việc LẤY dữ liệu hiển thị
    public function addProduct($name, $price, $image, $video_url, $description, $seller_id, $quantity, $category_id)
    {
        $sql = "INSERT INTO products (name, price, image, video_url, description, seller_id, quantity, sold, rating, category_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, 0, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
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
    
    // Lưu ý: Hàm này bạn chưa update video_url, nếu cần sửa video thì nhớ thêm vào nhé
    public function updateProductDetails($product_id, $name, $price, $image, $description, $quantity, $category_id)
    {
        $sql = "UPDATE products SET name = ?, price = ?, image = ?, description = ?, quantity = ?, category_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sdssiii", $name, $price, $image, $description, $quantity, $category_id, $product_id);
        return $stmt->execute();
    }

            public function addToWishlist($user_id, $product_id)
        {
            $sql = "INSERT INTO wishlist (user_id, product_id)
                    VALUES (?, ?)";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id, $product_id]);
        }

        public function removeFromWishlist($user_id, $product_id)
        {
            $sql = "DELETE FROM wishlist
                    WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([$user_id, $product_id]);
        }

        public function isWishlisted($userId, $productId)
        {
            $sql = "SELECT * FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);

            $stmt->bind_param("ii", $userId, $productId);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->num_rows > 0;
        }


        public function getWishlist($user_id)
        {
            $sql = "SELECT p.* FROM wishlist w 
                    JOIN products p ON w.product_id = p.id 
                    WHERE w.user_id = ? 
                    ORDER BY w.created_at DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $products = [];
            while ($row = $result->fetch_object()) {
                $products[] = new Product(
                    $row->id, $row->name, $row->price, $row->image, $row->video_url,
                    $row->description, $row->seller_id, $row->quantity, $row->sold,
                    $row->rating, $row->category_id, $row->created_at
                );
            }
            return $products;
        }

        // ProductModel.php – thêm hoặc sửa hàm này
    public function toggleWishlist($user_id, $product_id)
    {
        // Kiểm tra đã thích chưa
        $sql_check = "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?";
        $stmt_check = $this->conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $user_id, $product_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows > 0) {
            // Đã thích → xóa (unlike)
            $sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            return false; // đã unlike
        } else {
            // Chưa thích → thêm
            $sql = "INSERT INTO wishlist (user_id, product_id, created_at) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            return true; // đã like
        }
    }

}
?>