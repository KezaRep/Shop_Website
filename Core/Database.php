<?php
class Database{
    private $conn;
    public function __construct(){
        $this->conn = mysqli_connect('localhost','root','','Shop_Website');
        if(!$this->conn){
            die("Kết nối thất bại".mysqli_connect_error());
        }
    }
    public function getConnection(){
        return $this->conn;
    }
}