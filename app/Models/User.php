<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User {
    private $db;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $created_at;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function register() {
        $query = "INSERT INTO {$this->table} (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->db->prepare($query);

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);

        return $stmt->execute();
    }

    public function login() {
        $query = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($this->password, $user['password'])) {
                $this->id = $user['id'];
                $this->username = $user['username'];
                $this->email = $user['email'];
                return true;
            }
        }
        return false;
    }

    public function usernameExists() {
        $query = "SELECT id FROM {$this->table} WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $this->username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function emailExists() {
        $query = "SELECT id FROM {$this->table} WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
?>