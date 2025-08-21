<?php
class User {
    private $id;
    private $username;
    private $email;
    private $password; 
    private $created_at;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }

    public function setUsername($username) { $this->username = $username; }
    public function setEmail($email) { $this->email = $email; }
    public function setPassword($password) { 
        $this->password = password_hash($password, PASSWORD_BCRYPT); 
    }

    public function register() {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $this->username, $this->email, $this->password);
        return $stmt->execute();
    }

    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($password, $result['password'])) {
            $this->id = $result['id'];
            $this->username = $result['username'];
            return true;
        }
        return false;
    }
}
?>
