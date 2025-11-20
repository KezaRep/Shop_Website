<?php
class User
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $role;
    public $balance;
    public $created_at;
    public function __construct($id, $username, $password, $email, $role, $balance, $created_at)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->balance = $balance;
        $this->created_at = $created_at;
    }
}