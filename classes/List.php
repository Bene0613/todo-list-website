<?php
class ListItem { 
    private $id;
    private $user_id;
    private $title;
    private $created_at;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }

    public function setTitle($title) { 
        if (!empty($title)) $this->title = $title;
    }
    public function setUserId($user_id) { $this->user_id = $user_id; }

    public function add() {
        $stmt = $this->conn->prepare("INSERT INTO lists (user_id, title, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $this->user_id, $this->title);
        return $stmt->execute();
    }

    public function getAllByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM lists WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>