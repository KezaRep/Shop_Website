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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=user&action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userId = $_SESSION['user']['id'];

            $selectedIds = isset($_POST['selected_items']) ? $_POST['selected_items'] : [];


            $cartResult = $this->cartModel->getCartByUser($userId);

            $cartItems = [];
            $totalPrice = 0;

            if ($cartResult) {
                while ($row = mysqli_fetch_assoc($cartResult)) {
                    if (in_array($row['cart_id'], $selectedIds)) {
                        $cartItems[] = $row;
                        $totalPrice += $row['price'] * $row['quantity'];
                    }
                }
            }

            if (empty($cartItems)) {
                echo "<script>alert('Vui lòng chọn sản phẩm để thanh toán!'); history.back();</script>";
                exit;
            }

            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $note = isset($_POST['note']) ? $_POST['note'] : '';

            $shippingFee = 30000;
            $grandTotal = $totalPrice + $shippingFee;

            $orderId = $this->checkoutModel->createOrder($userId, $name, $phone, $address, $grandTotal, $note);

            if ($orderId) {
                foreach ($cartItems as $item) {
                    $this->checkoutModel->addOrderDetail(
                        $orderId,
                        $item['product_id'],
                        $item['name'],
                        $item['price'],
                        $item['quantity']
                    );

                    $this->cartModel->deleteCart($item['cart_id']);
                }

                header("Location: index.php?controller=checkout&action=success&order_id=$orderId");
                exit();
            } else {
                echo "<script>alert('Có lỗi xảy ra khi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }
    public function successAction()
    {
        if (!isset($_GET['order_id'])) {
            header("Location: index.php");
            exit();
        }

        $orderId = intval($_GET['order_id']);

        $order = $this->checkoutModel->findOrder($orderId);
        $order_details = $this->checkoutModel->findOrderDetails($orderId);
        if (!$order) {
            die("Không tìm thấy đơn hàng");
        }

        require_once __DIR__ . '/../../View/Checkout/Success.php';
    }
}
