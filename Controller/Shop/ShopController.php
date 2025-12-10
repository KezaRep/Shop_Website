<?php
// File: Controller/Shop/ShopController.php

require_once "Core/Database.php";

class ShopController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }


    public function registerAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }

        if (isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 1) {
            echo "<script>alert('Bạn đã có Shop rồi!'); window.location.href='index.php';</script>";
            exit;
        }

        require_once "View/Shop/Register.php";
    }

    public function storeAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $conn = $this->db->getConnection();

            $user_id = $_SESSION['user']['id'];

            $shop_name = mysqli_real_escape_string($conn, $_POST['shop_name']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $detail = $_POST['address_detail'];
            $ward   = $_POST['ward_text'];
            $dist   = $_POST['district_text'];
            $prov   = $_POST['province_text'];
            $description = mysqli_real_escape_string($conn, $_POST['description']);

            $full_address = "$detail, $ward, $dist, $prov";

            $avatarPath = "Assets/Images/default_shop.png";
            $coverPath = "Assets/Images/default_cover.png";

            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $target_dir = "Assets/Uploads/";
                $fileName = time() . "_avt_" . basename($_FILES["avatar"]["name"]);
                $target_file = $target_dir . $fileName;

                if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                    $avatarPath = $target_file;
                }
            }

            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] == 0) {
                $target_dir = "Assets/Uploads/";
                $fileName = time() . "_cover_" . basename($_FILES["cover_image"]["name"]);
                $target_file = $target_dir . $fileName;

                if (move_uploaded_file($_FILES["cover_image"]["tmp_name"], $target_file)) {
                    $coverPath = $target_file;
                }
            }

            $sqlInsert = "INSERT INTO shops (user_id, shop_name, avatar, cover_image, description, address) 
                          VALUES ('$user_id', '$shop_name', '$avatarPath', '$coverPath', '$description', '$full_address')";

            if (mysqli_query($conn, $sqlInsert)) {
                $sqlUpdate = "UPDATE users SET role = 1 WHERE id = $user_id";
                mysqli_query($conn, $sqlUpdate);
                $_SESSION['user']['role'] = 1;


                echo "<script>
                    alert('Chúc mừng! Bạn đã mở Shop thành công.'); 
                    window.location.href='index.php';
                </script>";
            } else {
                echo "Lỗi SQL: " . mysqli_error($conn);
            }
        }
    }

    public function profileAction()
    {
        // 1. Lấy ID chủ shop từ URL
        $owner_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        require_once "Model/Shop/ShopModel.php";
        require_once "Model/Product/ProductModel.php";
        $shopModel = new ShopModel();
        $productModel = new ProductModel();
        $shop = $shopModel->getShopByUserId($owner_id);

        if ($shop) {
            $current_user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

            $isOwner = ($current_user_id == $owner_id); 

            $products = $productModel->getProductsBySeller($owner_id);
            $totalProducts = count($products);

            if ($isOwner) {
                $revenue        = $shopModel->getRevenue($owner_id);
                $newOrdersCount = $shopModel->getNewOrdersCount($owner_id);
                $totalSold      = $shopModel->getTotalSold($owner_id);
                $lowStockCount  = $shopModel->getLowStockCount($owner_id, 10);
                $recentOrders   = $shopModel->getRecentOrders($owner_id);
            }
            $lowStockCount  = $shopModel->getLowStockCount($owner_id, 10);
            $recentOrders   = $shopModel->getRecentOrders($owner_id);

            $chartData = $shopModel->getRevenueLast7Days($owner_id);

            $topProducts = $shopModel->getTopSellingProducts($owner_id);

            // 3. Gọi View
            require_once "View/Shop/Profile.php";
        } else {
            echo "<script>alert('Shop không tồn tại!'); window.location.href='index.php';</script>";
        }
    }

    public function orderManagerAction()
    {
        if (!isset($_SESSION['user'])) {
            header("Location: index.php?controller=user&action=login");
            exit;
        }

        $seller_id = $_SESSION['user']['id'];

        require_once "Model/Order/OrderModel.php";
        $orderModel = new OrderModel();

        $ordersRaw = $orderModel->getOrdersBySeller($seller_id);

        $orders = [];
        foreach ($ordersRaw as $ord) {
            $items = $orderModel->getOrderItems($ord->id, $seller_id);
            $ord->items = $items;
            $orders[] = $ord;
        }

        // 4. Gọi View
        require_once "View/Shop/OrderManager.php";
    }

    public function updateStatusAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION['user'])) {
                header("Location: index.php?controller=user&action=login");
                exit;
            }

            $order_id = $_POST['order_id'];
            $new_status = $_POST['status'];

            $conn = $this->db->getConnection();

            $sql_check = " SELECT status from orders WHERE id = $order_id";
            $query_check = mysqli_query($conn, $sql_check);
            $row_check = mysqli_fetch_assoc($query_check);
            $old_status = $row_check['status'];

            $sql_update = "UPDATE orders SET status = '$new_status' WHERE id = $order_id";
            mysqli_query($conn, $sql_update);

            if ($new_status == 'completed' && $old_status != 'completed') {

                $sql_items = "SELECT product_id, quantity FROM order_details WHERE order_id = $order_id";
                $result_items = mysqli_query($conn, $sql_items);

                if ($result_items) {
                    while ($item = mysqli_fetch_assoc($result_items)) {
                        $pro_id = intval($item['product_id']);
                        $buy_qty = intval($item['quantity']);

                        $sql_stock = "UPDATE products 
                                      SET quantity = quantity - $buy_qty, 
                                          sold = sold + $buy_qty 
                                      WHERE id = $pro_id";

                        mysqli_query($conn, $sql_stock);
                    }
                }
            }

            echo "<script>
                    alert('Cập nhật trạng thái và kho hàng thành công!'); 
                    window.location.href='index.php?controller=shop&action=orderManager';
                  </script>";
        }
    }

    public function manager()
    {
        echo "Đây là trang quản lý đơn hàng/sản phẩm (Kênh người bán)";
    }
}
