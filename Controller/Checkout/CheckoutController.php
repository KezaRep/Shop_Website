<?php
require_once __DIR__ . '/../../Model/Checkout/CheckoutModel.php';
require_once __DIR__ . '/../../Model/Cart/CartModel.php';

class CheckoutController
{
    private $checkoutModel;
    private $cartModel;

    public function __construct()
    {
        if (class_exists('CheckoutModel')) {
            $this->checkoutModel = new CheckoutModel();
        } else {
            die('Lỗi: Không tìm thấy Class CheckoutModel.');
        }

        if (class_exists('CartModel')) {
            $this->cartModel = new CartModel();
        } else {
            die('Lỗi: Không tìm thấy Class CartModel.');
        }
    }

    public function orderAction()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (empty($_SESSION['user'])) { /* ... */
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user']['id'];
            $selectedIds = $_POST['selected_items'] ?? [];
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $note = $_POST['note'] ?? '';

            // 2. Lấy dữ liệu giỏ hàng
            $cartResult = $this->cartModel->getCartByUser($userId);

            // --- BƯỚC QUAN TRỌNG: GOM NHÓM THEO SHOP ---
            $ordersByShop = [];

            if ($cartResult) {
                while ($row = mysqli_fetch_assoc($cartResult)) {
                    if (in_array($row['cart_id'], $selectedIds)) {
                        // Lấy ID người bán (Shop ID)
                        $shopId = $row['seller_id'];

                        // Nếu chưa có nhóm cho shop này thì tạo mới
                        if (!isset($ordersByShop[$shopId])) {
                            $ordersByShop[$shopId] = [
                                'seller_id' => $shopId,
                                'items' => [],
                                'subtotal' => 0
                            ];
                        }

                        // Thêm sản phẩm vào nhóm
                        $ordersByShop[$shopId]['items'][] = $row;
                        $ordersByShop[$shopId]['subtotal'] += $row['price'] * $row['quantity'];
                    }
                }
            }

            if (empty($ordersByShop)) {
                echo "<script>alert('Vui lòng chọn sản phẩm!'); history.back();</script>";
                exit;
            }

            // --- BƯỚC 3: VÒNG LẶP TẠO ĐƠN HÀNG (TÁCH ĐƠN) ---
            $createdOrderIds = []; // Mảng lưu các mã đơn vừa tạo

            foreach ($ordersByShop as $shopData) {
                $shopTotal = $shopData['subtotal'];
                $shippingFee = 30000; // Phí ship cho mỗi đơn (hoặc tính logic khác tùy ông)
                $grandTotal = $shopTotal + $shippingFee;
                $sellerId = $shopData['seller_id'];

                // Tạo đơn hàng cho Shop này (Lưu ý: CheckoutModel cần hỗ trợ thêm seller_id)
                // Ông cần sửa thêm hàm createOrder trong CheckoutModel để nhận thêm $sellerId
                $orderId = $this->checkoutModel->createOrder($userId, $name, $phone, $address, $grandTotal, $sellerId, $note);

                if ($orderId) {
                    $createdOrderIds[] = $orderId;

                    // Thêm chi tiết sản phẩm vào đơn hàng này
                    foreach ($shopData['items'] as $item) {
                        $this->checkoutModel->addOrderDetail(
                            $orderId,
                            $item['product_id'],
                            $item['name'], // Hoặc lấy từ DB nếu cần chính xác
                            $item['price'],
                            $item['quantity']
                        );

                        // Xóa khỏi giỏ hàng
                        $this->cartModel->deleteCart($item['cart_id']);
                    }
                }
            }
            if (!empty($createdOrderIds)) {
                echo "<script>
                    alert('Đặt hàng thành công! Đã tạo " . count($createdOrderIds) . " đơn hàng.');
                    window.location.href = 'index.php?controller=user&action=purchaseHistory';
                </script>";
                exit();
            } else {
                echo "<script>alert('Lỗi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }
}
