<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once("Model/User/UserModel.php");

class UserController
{
    public $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function loginAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $identifier = isset($_POST['identifier']) ? trim($_POST['identifier']) : ''; 
            $password = isset($_POST['password']) ? $_POST['password'] : '';

            $user = $this->userModel->loginUser($identifier, $password);

            if ($user instanceof User) {
                // Đăng nhập thành công
                $_SESSION['user'] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'balance' => $user->balance,
                    'role' => $user->role
                ];

                header("Location: index.php?controller=product&action=list");
                exit;
            } elseif ($user === false) {
                $error = "Mật khẩu không chính xác!";
            } else {
                $error = "Tài khoản không tồn tại!";
            }
        }

        include("View/Layout/Header.php");
        include("View/User/Login.php");
        include("View/Layout/Footer.php");
    }


    public function registerAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = isset($_POST['username']) ? trim($_POST['username']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

            // Validate
            if (empty($username) || empty($email) || empty($password)) {
                $error = "Vui lòng điền đầy đủ thông tin!";
            } elseif ($password !== $confirmPassword) {
                $error = "Mật khẩu không khớp!";
            } elseif ($this->userModel->isUsernameExists($username)) {
                $error = "Tên người dùng đã tồn tại!";
            } elseif ($this->userModel->isEmailExists($email)) {
                $error = "Email đã tồn tại!";
            } else {
                $result = $this->userModel->createUser($username, $password, $email, 0, 0);

                if ($result) {
                    header("Location: index.php?controller=user&action=login");
                    exit;
                } else {
                    $error = "Lỗi hệ thống!";
                }
            }
        }

        include("View/Layout/Header.php");
        include("View/User/Register.php");
        include("View/Layout/Footer.php");
    }
    public function editAction()
    {
        // Redirect nếu chưa đăng nhập
        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }

        include("View/Layout/Header.php");
        include("View/User/Edit.php");
        include("View/Layout/Footer.php");
    }
    public function profileAction()
    {
        // Redirect nếu chưa đăng nhập
        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }

        include("View/Layout/Header.php");
        include("View/User/Profile.php");
        include("View/Layout/Footer.php");
    }

    public function logoutAction()
    {
        session_destroy();
        header('Location: index.php');
        exit;
    }

}
?>