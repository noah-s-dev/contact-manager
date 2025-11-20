<?php
/**
 * User Model
 * Handles user authentication and management
 */

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password_hash;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register a new user
     * @return bool
     */
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                  SET username=:username, email=:email, password_hash=:password_hash";

        $stmt = $this->conn->prepare($query);

        // Sanitize input
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password_hash = password_hash($this->password_hash, PASSWORD_DEFAULT);

        // Bind values
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $this->password_hash);

        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    /**
     * Login user
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login($username, $password) {
        $query = "SELECT id, username, email, password_hash 
                  FROM " . $this->table_name . " 
                  WHERE username = :username OR email = :username 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            
            if(password_verify($password, $row['password_hash'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                return true;
            }
        }

        return false;
    }

    /**
     * Check if username exists
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Check if email exists
     * @param string $email
     * @return bool
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Get user by ID
     * @param int $id
     * @return bool
     */
    public function getUserById($id) {
        $query = "SELECT id, username, email, created_at 
                  FROM " . $this->table_name . " 
                  WHERE id = :id LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->email = $row['email'];
            $this->created_at = $row['created_at'];
            return true;
        }

        return false;
    }
}
?>

