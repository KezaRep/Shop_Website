<?php
include_once('Core/Database.php');
include('Model/User/User.php');
class UserModel
{
    private $conn;

    public function __construct()
    {
        $Database = new Database();
        $this->conn = $Database->getConnection();
    }

    public function getUserByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return null;
    }
    public function createUser($username, $password, $email, $role = 0, $balance = 0)
    {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $nd = 0;
        $sql = "INSERT INTO users (username, password, email, role, balance, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssdi", $username, $hashedPassword, $email, $nd, $balance);

        return $stmt->execute();
    }

    public function updateUserBalance($userId, $newBalance)
    {
        $sql = "UPDATE users SET balance = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("di", $newBalance, $userId);
        return $stmt->execute();
    }

    public function getUserById($userId)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return null;
    }
    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        $result = $this->conn->query($sql);
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return $users;
    }
    public function deleteUser($userId)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        return $stmt->execute();
    }
    public function updateUserEmail($userId, $newEmail)
    {
        $sql = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $newEmail, $userId);
        return $stmt->execute();
    }
    public function updateUserPassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $hashedPassword, $userId);
        return $stmt->execute();
    }
    public function searchUsersByUsername($searchTerm)
    {
        $likeTerm = "%" . $searchTerm . "%";
        $sql = "SELECT * FROM users WHERE username LIKE ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $likeTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return $users;
    }
    public function countUsers()
    {
        $sql = "SELECT COUNT(*) as user_count FROM users";
        $result = $this->conn->query($sql);
        if ($row = $result->fetch_assoc()) {
            return $row['user_count'];
        }
        return 0;
    }
    public function getUsersByRole($role)
    {
        $sql = "SELECT * FROM users WHERE role = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return $users;
    }
    public function updateUserRole($userId, $newRole)
    {
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $newRole, $userId);
        return $stmt->execute();
    }
    public function getRecentUsers($days)
    {
        $sql = "SELECT * FROM users WHERE created_at >= NOW() - INTERVAL ? DAY";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }
        return $users;
    }
    public function loginUser($identifier, $password)
    {
        // Xác định xem identifier là email hay username
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM users WHERE email = ?";
        } else {
            $sql = "SELECT * FROM users WHERE username = ?";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            return null; // Không tìm thấy user
        }

        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            // Trả về object User
            return new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['email'],
                $row['role'],
                $row['balance'],
                $row['created_at']
            );
        }

        return false; // Mật khẩu sai
    }
    public function updateAvatar($userId, $avatarPath)
    {
        $sql = "UPDATE users SET avatar = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $avatarPath, $userId);
        return $stmt->execute();
    }
    public function isUsernameExists($username)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }

    public function isEmailExists($email)
    {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();
        return $count > 0;
    }
    public function getUserAddresses($userId)
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
    public function addAddress($userId, $fullname, $phone, $address, $city, $district, $type) {
        $userId   = mysqli_real_escape_string($this->conn, $userId); 
        $fullname = mysqli_real_escape_string($this->conn, $fullname);
        $phone    = mysqli_real_escape_string($this->conn, $phone);
        $address  = mysqli_real_escape_string($this->conn, $address);
        $city     = mysqli_real_escape_string($this->conn, $city);
        $type     = mysqli_real_escape_string($this->conn, $type);
        $district = mysqli_real_escape_string($this->conn, $district);

        $fullAddress = $address . ", " . $district . ", " . $city;

        $sql = "INSERT INTO user_addresses(user_id,name,phone,address,label) VALUES('$userId','$fullname','$phone','$fullAddress','$type')";

        return mysqli_query($this->conn,$sql);  
    }
}
