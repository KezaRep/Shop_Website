<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once("Controller/Product/ProductController.php");
include_once("Controller/User/UserController.php");
include_once("Controller/Comment/CommentController.php");
include_once("Controller/Checkout/CheckoutController.php");
include_once("Controller/Cart/CartController.php");
include_once("Controller/Map/MapController.php");
include_once("Controller/Shop/ShopController.php");
include_once("Controller/Exchange/ExchangeController.php");

$controllerName = isset($_GET['controller']) ? $_GET['controller'] : 'Product';
$actionName = isset($_GET['action']) ? $_GET['action'] : 'List';
$controllerClass = ucfirst($controllerName) . "Controller";
$actionMethod = $actionName . "Action";

if (class_exists($controllerClass)) {
    $controller = new $controllerClass();

    // Kiểm tra hàm (method) tồn tại trong controller
    if (method_exists($controller, $actionMethod)) {
        $controller->$actionMethod();
    } else {
        echo "<h3>❌ Action '$actionMethod' không tồn tại trong $controllerClass!</h3>";
    }
} else {
    echo "<h3>❌ Controller '$controllerClass' không tồn tại!</h3>";
}
?>
