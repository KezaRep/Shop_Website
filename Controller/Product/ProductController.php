<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("Model/Product/ProductModel.php");
include_once("Model/User/UserModel.php");
class ProductController
{
    public $productModel;
    public $userModel;
    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }
    public function listAction()
    {
        $productList = $this->productModel->getAllProducts();
        include("View/Layout/Header.php");
        include("View/Product/ProductList.php");
        include("View/Layout/Footer.php");
    }
    public function searchAction()
    {
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $productList = $this->productModel->searchProducts($keyword);
        include("View/Layout/Header.php");
        include("View/Product/ProductList.php");
        include("View/Layout/Footer.php");
    }
    public function addAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $seller_id = isset($_SESSION['user']) ? (int) $_SESSION['user']['id'] : 0;
            $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
            $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;

            // Xử lý ảnh upload (lưu vào DB dưới dạng binary BLOB)
            $imageData = '';
            if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
            }

            // Thêm product
            $ok = $this->productModel->addProduct($name, $price, $imageData, $description, $seller_id, $quantity, $category_id);

            // Redirect về danh sách hoặc hiển thị thông báo
            if ($ok) {
                header("Location: index.php?controller=product&action=list");
                exit;
            } else {
                $error = "Không thể thêm sản phẩm. Vui lòng thử lại.";
                include("View/Layout/Header.php");
                include("View/Product/ProductAdd.php");
                include("View/Layout/Footer.php");
            }
        } else {
            // GET: hiển thị form
            include("View/Layout/Header.php");
            include("View/Product/ProductAdd.php");
            include("View/Layout/Footer.php");
        }

    }
    public function detailAction()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            echo "Sản phẩm không tồn tại.";
            exit;
        }

        // load comment model và lấy comment cho product này
        include_once("Model/Comment/CommentModel.php");
        $commentModel = new CommentModel();
        $comments = $commentModel->getCommentsByProductId($id);

        // load seller (nếu có)
        include_once("Model/User/UserModel.php");
        $userModel = new UserModel();
        $seller = $userModel->getUserById(intval($product->seller_id ?? 0));

        // related products
        $related = $this->productModel->getProductsBySeller($product->seller_id);

        // truyền $product, $related, $comments, $seller vào view
        include("View/Layout/Header.php");
        include("View/Product/ProductDetail.php");
        include("View/Layout/Footer.php");

    }
    public function editAction()
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            echo "Sản phẩm không tồn tại.";
            exit;
        }

        // quyền: chỉ owner hoặc admin
        $currentUserId = $_SESSION['user']['id'] ?? 0;
        $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';
        if ($currentUserId !== intval($product->seller_id) && !$isAdmin) {
            header('HTTP/1.0 403 Forbidden');
            echo "Bạn không có quyền chỉnh sửa sản phẩm này.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $price = isset($_POST['price']) ? (float) $_POST['price'] : 0;
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
            $category_id = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;

            // Nếu có file mới thì đọc, ngược lại để null (model sẽ bỏ qua update ảnh)
            $imageData = null;
            if (!empty($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
                // kiểm tra type/size nếu cần
                $maxBytes = 2 * 1024 * 1024; // 2MB
                if ($_FILES['image']['size'] > $maxBytes) {
                    $error = "Ảnh quá lớn (tối đa 2MB).";
                    // reload product để view có dữ liệu
                    include("View/Layout/Header.php");
                    include("View/Product/ProductEdit.php");
                    include("View/Layout/Footer.php");
                    return;
                }
                $imageData = file_get_contents($_FILES['image']['tmp_name']);
            }

            // Gọi model: nếu imageData === null thì model KHÔNG cập nhật cột ảnh
            $ok = $this->productModel->updateProductDetails($id, $name, $price, $imageData, $description, $quantity, $category_id);

            if ($ok) {
                header("Location: index.php?controller=product&action=list");
                exit;
            } else {
                $error = "Không thể lưu thông tin sản phẩm. Vui lòng thử lại.";
                // reload product (lấy lại dữ liệu mới nhất)
                $product = $this->productModel->getProductById($id);
                include("View/Layout/Header.php");
                include("View/Product/ProductEdit.php");
                include("View/Layout/Footer.php");
            }

        } else {
            include("View/Layout/Header.php");
            include("View/Product/ProductEdit.php");
            include("View/Layout/Footer.php");
        }
    }
}

?>