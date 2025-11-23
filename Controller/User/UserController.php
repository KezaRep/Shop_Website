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

        $userId = $_SESSION['user']['id'];
        $user = $this->userModel->getUserById($userId);

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ form
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validate dữ liệu
            if (empty($username) || empty($email)) {
                $error = "Tên người dùng và email không được để trống!";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Email không hợp lệ!";
            } elseif ($password !== $confirmPassword) {
                $error = "Mật khẩu mới không khớp!";
            } else {
                // Kiểm tra username/email có trùng với user khác không
                $otherUsers = $this->userModel->searchUsersByUsername($username);
                $isUsernameTaken = false;
                foreach ($otherUsers as $u) {
                    if ($u->id != $userId) {
                        $isUsernameTaken = true;
                        break;
                    }
                }

                if ($isUsernameTaken) {
                    $error = "Tên người dùng đã tồn tại!";
                } elseif ($this->userModel->isEmailExists($email) && $email != $user->email) {
                    $error = "Email đã tồn tại!";
                } else {
                    $user->username = $username;
                    $user->email = $email;

                    $this->userModel->updateUserEmail($userId, $email);

                    if (!empty($password)) {
                        $this->userModel->updateUserPassword($userId, $password);
                    }

                    $_SESSION['user']['username'] = $username;
                    $_SESSION['user']['email'] = $email;

                    $success = "Cập nhật thông tin thành công!";
                }
            }

            $user = $this->userModel->getUserById($userId);
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
    public function addressAction()
    {
        // Kiểm tra đăng nhập
        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }

        // TODO: Sau này bạn sẽ gọi Model để lấy danh sách địa chỉ thật từ DB
        // $addresses = $this->userModel->getUserAddresses($_SESSION['user']['id']);

        include("View/Layout/Header.php");
        include("View/User/Address.php");
        include("View/Layout/Footer.php");
    }

    public function addAddressAction()
    {
        if (empty($_SESSION['user'])) {
            header('Location: index.php?controller=user&action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $fullname = $_POST['fullname'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $city = $_POST['city'] ?? '';
            $district = $_POST['district'] ?? '';
            $type = $_POST['address_type'] ?? 'home';

            if (empty($fullname) || empty($phone) || empty($address)) {
                $error = "Vui lòng nhập đủ thông tin!";
            } else {
                $result = $this->userModel->addAddress($userId, $fullname, $phone, $address, $city, $district, $type);

                if ($result) {
                    header('Location: index.php?controller=user&action=address');
                    exit;
                } else {
                    $error = "Lỗi khi lưu dữ liệu!";
                }
            }
        }

        include("View/Layout/Header.php");
        include("View/User/AddAddress.php");
        include("View/Layout/Footer.php");
    }
}
?>