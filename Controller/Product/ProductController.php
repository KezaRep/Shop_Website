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
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        // Lấy danh sách sản phẩm gần đúng theo từ khóa
        $productList = $this->productModel->searchProducts($keyword);

        $Shop = $this->userModel->searchOneUser($keyword); // dùng hàm mới

        // Chuyển biến Shop xuống view
        $shop = $Shop; // đặt lại biến để view dùng thống nhất

        include("View/Layout/Header.php");
        include("View/Product/ProductList.php");
        include("View/Layout/Footer.php");
    }
    public function sortAction() {
        // Lấy sort từ GET
        $sort = $_GET['sort'] ?? '';

        // Gọi model để lấy danh sách đã sắp xếp
        $productModel = new ProductModel();
        if ($sort === 'price_asc') {
            $productList = $productModel->getProductsPriceAsc();
        }
        else if ($sort === 'price_desc') {
            $productList = $productModel->getProductsPriceDesc();
        }
        else {
            $productList = $productModel->getAllProducts();
        }

        // Gọi view
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

            $targetDir = "Assets/Uploads/Products/";

            $imagePath = "";
            if (!empty($_FILES['image']['name'])) {
                $fileName = time() . "_" . basename($_FILES["image"]["name"]);
                $targetFilePath = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                    $imagePath = $targetFilePath;
                }
            }

            $videoPath = NULL;
            if (!empty($_FILES['video']['name'])) {
                $videoName = time() . "_" . basename($_FILES["video"]["name"]);
                $targetVideoPath = $targetDir . $videoName;

                if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetVideoPath)) {
                    $videoPath = $targetVideoPath;
                }
            }

            // Thêm product
            $ok = $this->productModel->addProduct($name, $price, $imagePath, $videoPath, $description, $seller_id, $quantity, $category_id);

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

            require_once "Model/Category/CategoryModel.php";
            $cateModel = new CategoryModel();
            $categories = $cateModel->getCategoryList();

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
        
        $shop_data = null;
        if (!empty($product->seller_id)) {
            // 1. Gọi file Database để kết nối (giống hàm submitOrderAction ông đã làm)
            require_once __DIR__ . '/../../Core/Database.php';
            $db = new Database();
            $conn = $db->getConnection();

            // 2. Query bảng shops
            $stmt_shop = $conn->prepare("SELECT * FROM shops WHERE user_id = ?");
            $stmt_shop->bind_param("i", $product->seller_id);
            $stmt_shop->execute();
            $result_shop = $stmt_shop->get_result();
            $shop_data = $result_shop->fetch_object();
            
            // (Nếu muốn kỹ tính thì đóng stmt lại, không thì PHP tự lo)
            $stmt_shop->close();
        }

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

            require_once "Model/Category/CategoryModel.php";
            $cateModel = new CategoryModel();
            $categories = $cateModel->getCategoryList();

            include("View/Layout/Header.php");
            include("View/Product/ProductEdit.php");
            include("View/Layout/Footer.php");
        }
    }
    public function checkoutAction()
    {
        // Khởi động session nếu chưa có
        if (session_status() === PHP_SESSION_NONE)
            session_start();

        $cart = [];

        // TRƯỜNG HỢP 1: Bấm nút "MUA NGAY" (POST từ trang chi tiết)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            $productId = (int) $_POST['product_id'];
            $qty = (int) ($_POST['quantity'] ?? 1);

            // Gọi Model lấy thông tin sản phẩm
            $product = $this->productModel->getProductById($productId);

            if ($product) {
                // Tạo mảng sản phẩm
                $item = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'image' => $product->image
                ];

                // --- FIX QUAN TRỌNG ---
                // Lưu ngay sản phẩm này vào Session Cart
                // Để tí nữa bấm "Đặt hàng" thì hàm submitOrderAction nó mới thấy dữ liệu
                $_SESSION['cart'] = [$item];

                $cart = $_SESSION['cart'];
            }
        }
        // TRƯỜNG HỢP 2: Vào Checkout từ Giỏ hàng (nếu sau này bạn làm)
        else {
            $cart = $_SESSION['cart'] ?? [];
        }

        // Kiểm tra nếu giỏ hàng vẫn trống
        if (empty($cart)) {
            echo "<script>alert('Chưa có sản phẩm nào để thanh toán!'); window.location.href='index.php';</script>";
            return;
        }

        // --- ĐOẠN DƯỚI GIỮ NGUYÊN ---
        // Lấy thông tin người dùng để điền sẵn vào form
        $profileName = "";
        $profilePhone = "";
        $profileAddress = "";
        $savedAddresses = [];

        if (isset($_SESSION['user'])) {
            $userId = (int) $_SESSION['user']['id'];
            $currentUser = $this->userModel->getUserById($userId);

            if ($currentUser) {
                $profileName = $currentUser->full_name ?? $currentUser->username ?? "";
                $profilePhone = $currentUser->phone ?? "";
                $profileAddress = $currentUser->address ?? "";
            }
            // Lấy danh sách địa chỉ đã lưu (nếu có)
            if (method_exists($this->userModel, 'getUserAddresses')) {
                $savedAddresses = $this->userModel->getUserAddresses($userId);
            }

            $profileAsAddress = [
                'id' => 'profile',
                'name' => $profileName,
                'phone' => $profilePhone,
                'address' => $profileAddress,
                'label' => 'Mặc định'
            ];
            array_unshift($savedAddresses, $profileAsAddress);
        }

        // Gọi View hiển thị
        include("View/Layout/Header.php");
        include("View/Checkout/Checkout.php"); // File HTML form thanh toán
        include("View/Layout/Footer.php");
    }
    public function submitOrderAction()
    {

        if (!isset($_SESSION['user'])) {
            echo "<script>alert('Vui lòng đăng nhập để thanh toán!'); window.location.href='index.php?controller=user&action=login';</script>";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../../Core/Database.php';
            $db = new Database();
            $conn = $db->getConnection();

            $userId = $_SESSION['user']['id'];
            $recName = $_POST['name'] ?? '';
            $recPhone = $_POST['phone'] ?? '';
            $recAddress = $_POST['address'] ?? '';
            $note = $_POST['note'] ?? '';

            $cart = $_SESSION['cart'] ?? [];

            $productId = $_POST['product_id'] ?? 0;
            $qty = $_POST['quantity'] ?? 1;

            if (empty($cart) && !empty($_POST['cart_items'])) {
                $cart = json_decode($_POST['cart_items'], true);
            }
            if (empty($cart)) {
                echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php';</script>";
                return;
            }

            $subtotal = 0;
            foreach ($cart as $item) {
                // Đảm bảo kiểu số để tính toán
                $price = floatval($item['price']);
                $qty = intval($item['quantity']);
                $subtotal += $price * $qty;
            }

            $shipping = ($subtotal > 500000) ? 0 : 30000;
            $totalMoney = $subtotal + $shipping;

            $sqlOrder = "INSERT INTO orders (user_id, total_money, note, status, created_at, recipient_name, recipient_phone, recipient_address) 
                         VALUES (?, ?, ?, 'pending', NOW(), ?, ?, ?)";

            $stmt = $conn->prepare($sqlOrder);
            if ($stmt) {
                $stmt->bind_param("idssss", $userId, $totalMoney, $note, $recName, $recPhone, $recAddress);

                if ($stmt->execute()) {
                    $orderId = $conn->insert_id; // Lấy ID đơn hàng vừa tạo

                    // -- BƯỚC 2: INSERT BẢNG ORDER_DETAILS --
                    $sqlDetail = "INSERT INTO order_details (order_id, product_id, price, quantity) VALUES (?, ?, ?, ?)";
                    $stmtDetail = $conn->prepare($sqlDetail);

                    foreach ($cart as $item) {
                        $pId = intval($item['product_id']);
                        $pPrice = floatval($item['price']);
                        $pQty = intval($item['quantity']);

                        // Lưu từng dòng sản phẩm
                        $stmtDetail->bind_param("idii", $orderId, $pId, $pPrice, $pQty);
                        $stmtDetail->execute();
                    }
                    $stmtDetail->close();

                    // -- BƯỚC 3: HOÀN TẤT --
                    unset($_SESSION['cart']); // Xóa giỏ hàng sau khi mua xong

                    echo "<script>alert('Đặt hàng thành công! Mã đơn: #$orderId'); window.location.href='index.php';</script>";
                } else {
                    echo "Lỗi khi tạo đơn hàng: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Lỗi kết nối cơ sở dữ liệu.";
            }
        } else {
            header("Location: index.php");
        }
    }
    public function deleteAction()
    {
        // 1. Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        // 2. Lấy ID sản phẩm cần xóa
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        // 3. Lấy thông tin sản phẩm
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "<script>alert('Sản phẩm không tồn tại!'); window.history.back();</script>";
            return;
        }

        // 4. KIỂM TRA QUYỀN: Chỉ chủ shop (người đăng) hoặc Admin mới được xóa
        $currentUserId = (int) $_SESSION['user']['id'];
        $isOwner = $currentUserId === (int) $product->seller_id;
        $isAdmin = ($_SESSION['user']['role'] ?? '') === 'admin';

        if (!$isOwner && !$isAdmin) {
            echo "<script>alert('Bạn không có quyền xóa sản phẩm này!'); window.history.back();</script>";
            return;
        }

        // 5. Xóa ảnh cũ trên server (Dọn dẹp bộ nhớ)
        if (!empty($product->image) && file_exists($product->image)) {
            if (strpos($product->image, 'placeholder') === false) {
                @unlink($product->image);
            }
        }
        // Xóa video cũ nếu có
        if (!empty($product->video_url) && file_exists($product->video_url)) {
             @unlink($product->video_url);
        }

        // 6. Gọi Model để xóa trong Database
        $isDeleted = $this->productModel->deleteProduct($id);

        if ($isDeleted) {
            // --- SỬA Ở ĐÂY: Chuyển về controller=user&action=profile ---
            echo "<script>
                alert('Xóa sản phẩm thành công!'); 
                window.location.href='index.php?controller=user&action=profile';
            </script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra, không thể xóa!'); window.history.back();</script>";
        }
    }
}
