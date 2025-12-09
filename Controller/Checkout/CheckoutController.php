<?php
require_once __DIR__ . '/../../Model/Checkout/CheckoutModel.php';
require_once __DIR__ . '/../../Model/Cart/CartModel.php';
require_once __DIR__ . '/../../Model/Payment/PaymentModel.php';

class CheckoutController
{
    private $checkoutModel;
    private $cartModel;
    private $paymentModel;

    public function __construct()
    {
        $this->checkoutModel = new CheckoutModel();
        $this->cartModel = new CartModel();
        $this->paymentModel = new PaymentModel();
    }

    public function orderAction()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $info = [
                'user_id' => $_SESSION['user']['id'],
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address'],
                'note' => $_POST['note'] ?? '',
                'selected_items' => $_POST['selected_items'] ?? [],
                'payment_method' => $_POST['payment_method'] ?? 'cod',
                'total_amount' => $_POST['total_amount']
            ];

            if ($info['payment_method'] == 'vnpay') {
                $_SESSION['pending_order'] = $info;
                
                $vnp_TxnRef = time(); 
                
                $url = $this->paymentModel->createPaymentUrl($vnp_TxnRef, $info['total_amount']);
                
                header("Location: $url");
                exit();

            } else {
                $result = $this->saveOrderToDatabase($info, 'pending');

                if ($result) {
                     echo "<script>
                        alert('Đặt hàng thành công! (COD)');
                        window.location.href = 'index.php?controller=user&action=purchaseHistory';
                    </script>";
                } else {
                    echo "<script>alert('Lỗi: Không có sản phẩm nào được chọn hoặc lỗi hệ thống.'); history.back();</script>";
                }
            }
        }
    }

    public function vnpayReturnAction()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $check = $this->paymentModel->checkResponse($_GET);
        
        $data = []; 

        if ($check['isValid']) {
            if ($check['responseCode'] == '00') {
                if (isset($_SESSION['pending_order'])) {
                    $info = $_SESSION['pending_order'];

                    $this->saveOrderToDatabase($info, 'pending');

                    unset($_SESSION['pending_order']);

                    $data['status'] = 'success';
                    $data['amount'] = $check['amount'];
                    $data['order_id'] = $check['orderId'];
                    $data['info'] = [
                        'customer_name' => $info['name'],
                        'customer_phone' => $info['phone'],
                        'customer_address' => $info['address']
                    ];
                } else {
                    $data['status'] = 'failed';
                    $data['msg'] = 'Lỗi: Phiên giao dịch đã hết hạn.';
                }
            } else {
                $data['status'] = 'failed';
                $data['msg'] = 'Giao dịch bị hủy hoặc thất bại.';
            }
        } else {
            $data['status'] = 'error';
            $data['msg'] = 'Chữ ký bảo mật không hợp lệ.';
        }

        require_once("View/Payment/result.php");
    }

    private function saveOrderToDatabase($info, $status)
    {
        $cartResult = $this->cartModel->getCartByUser($info['user_id']);
        $ordersByShop = [];

        if ($cartResult) {
            while ($row = mysqli_fetch_assoc($cartResult)) {
                if (in_array($row['cart_id'], $info['selected_items'])) {
                    $shopId = $row['seller_id'];
                    if (!isset($ordersByShop[$shopId])) {
                        $ordersByShop[$shopId] = ['seller_id' => $shopId, 'items' => [], 'subtotal' => 0];
                    }
                    $ordersByShop[$shopId]['items'][] = $row;
                    $ordersByShop[$shopId]['subtotal'] += $row['price'] * $row['quantity'];
                }
            }
        }

        if (empty($ordersByShop)) return false;

        foreach ($ordersByShop as $shopData) {
            $shippingFee = 30000;
            $grandTotal = $shopData['subtotal'] + $shippingFee;

            $orderId = $this->checkoutModel->createOrder(
                $info['user_id'], 
                $info['name'], 
                $info['phone'], 
                $info['address'], 
                $grandTotal, 
                $shopData['seller_id'], 
                $info['note'], 
                $info['payment_method'] // 'cod' hoặc 'vnpay'
            );
            
            if($status == 'pending') {
                 $this->checkoutModel->updateOrderStatus($orderId, 'pending');
            }

            if ($orderId) {
                foreach ($shopData['items'] as $item) {
                    $this->checkoutModel->addOrderDetail($orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
                    $this->cartModel->deleteCart($item['cart_id']);
                }
            }
        }
        return true;
    }
}
?>