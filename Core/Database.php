<?php
class Database {
    private $conn;

    public function __construct() {
        $host = "bpx2cyjcgumovul3e4rg-mysql.services.clever-cloud.com";
        $user = "uyunudashin4neng";
        $dbname = "bpx2cyjcgumovul3e4rg";
        
        $pass = "rLMTiq0xefl6vXAiQOVh"; 

        $this->conn = mysqli_connect($host, $user, $pass, $dbname);


        if (!$this->conn) {
            die("Kết nối thất bại: " . mysqli_connect_error());
        }

        mysqli_set_charset($this->conn, "utf8");
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>