<?php
include_once "Core/Database.php";

class AdminModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // ==============================
    // USER MANAGEMENT
    // ==============================
    public function getAllUsers()
    {
        $result = $this->conn->query("SELECT * FROM users ORDER BY id ASC");
        $users = [];
        while ($row = $result->fetch_object())
            $users[] = $row;
        return $users;
    }

    public function getUser($id)
    {
        $id = (int) $id;
        $result = $this->conn->query("SELECT * FROM users WHERE id=$id LIMIT 1");
        return $result->fetch_object();
    }

    public function createUser($data)
    {
        $username = $this->conn->real_escape_string($data['username']);
        $email = $this->conn->real_escape_string($data['email']);
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $this->conn->real_escape_string($data['role'] ?? 'user');

        $sql = "INSERT INTO users(username,email,password,role) VALUES('$username','$email','$password','$role')";
        return $this->conn->query($sql);
    }

    public function updateUser($id, $data)
    {
        $id = (int) $id;
        $username = $this->conn->real_escape_string($data['username']);
        $email = $this->conn->real_escape_string($data['email']);
        $role = $this->conn->real_escape_string($data['role'] ?? 'user');

        $sql = "UPDATE users SET username='$username', email='$email', role='$role' WHERE id=$id";
        return $this->conn->query($sql);
    }

    public function deleteUser($id)
    {
        $id = (int) $id;
        return $this->conn->query("DELETE FROM users WHERE id=$id");
    }

    // ==============================
    // PRODUCT MANAGEMENT
    // ==============================
    public function getAllProducts()
    {
        $result = $this->conn->query("SELECT * FROM products ORDER BY id ASC");
        $products = [];
        while ($row = $result->fetch_object())
            $products[] = $row;
        return $products;
    }

    public function getProduct($id)
    {
        $id = (int) $id;
        $result = $this->conn->query("SELECT * FROM products WHERE id=$id LIMIT 1");
        return $result->fetch_object();
    }

    public function createProduct($data)
    {
        $name = $this->conn->real_escape_string($data['name']);
        $price = (float) $data['price'];
        $category = $this->conn->real_escape_string($data['category'] ?? '');
        $image = $this->conn->real_escape_string($data['image'] ?? '');
        $user_id = (int) ($data['user_id'] ?? 0);

        $sql = "INSERT INTO products(name,price,category,image,user_id) 
                VALUES('$name',$price,'$category','$image',$user_id)";
        return $this->conn->query($sql);
    }

    public function updateProduct($id, $data)
    {
        $id = (int) $id;
        $name = $this->conn->real_escape_string($data['name']);
        $price = (float) $data['price'];
        $category = $this->conn->real_escape_string($data['category'] ?? '');
        $image = $this->conn->real_escape_string($data['image'] ?? '');
        $user_id = (int) ($data['user_id'] ?? 0);

        $sql = "UPDATE products SET name='$name', price=$price, category='$category', image='$image', user_id=$user_id WHERE id=$id";
        return $this->conn->query($sql);
    }

    public function deleteProduct($id)
    {
        $id = (int) $id;
        return $this->conn->query("DELETE FROM products WHERE id=$id");
    }
}
