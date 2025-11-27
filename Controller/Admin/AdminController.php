<?php
include_once "Model/Admin/AdminModel.php";

class AdminController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new AdminModel();
    }

    // ==============================
    // User management
    // ==============================
    public function listUsers() {
        $users = $this->adminModel->getAllUsers();
        include "View/Admin/UserList.php";
    }

    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminModel->createUser($_POST);
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }
        include "View/Admin/UserForm.php";
    }

    public function editUser($id) {
        $user = $this->adminModel->getUser($id);
        if (!$user) die("User không tồn tại");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminModel->updateUser($id, $_POST);
            header("Location: index.php?controller=admin&action=listUsers");
            exit;
        }
        include "View/Admin/UserForm.php";
    }

    public function deleteUser($id) {
        $this->adminModel->deleteUser($id);
        header("Location: index.php?controller=admin&action=listUsers");
        exit;
    }

    // ==============================
    // Product management
    // ==============================
    public function listProducts() {
        $products = $this->adminModel->getAllProducts();
        include "View/Admin/ProductList.php";
    }

    public function addProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminModel->createProduct($_POST);
            header("Location: index.php?controller=admin&action=listProducts");
            exit;
        }
        include "View/Admin/ProductForm.php";
    }

    public function editProduct($id) {
        $product = $this->adminModel->getProduct($id);
        if (!$product) die("Product không tồn tại");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->adminModel->updateProduct($id, $_POST);
            header("Location: index.php?controller=admin&action=listProducts");
            exit;
        }
        include "View/Admin/ProductForm.php";
    }

    public function deleteProduct($id) {
        $this->adminModel->deleteProduct($id);
        header("Location: index.php?controller=admin&action=listProducts");
        exit;
    }
}
