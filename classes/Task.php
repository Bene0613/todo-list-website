<?php
class Task {
    private $id;
    private $list_id;
    private $title;
    private $priority;
    private $status;
    private $created_at;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getPriority() { return $this->priority; }
    public function getStatus() { return $this->status; }

    // Setters
    public function setTitle($title) { if(!empty($title)) $this->title = $title; }
    public function setPriority($priority) { $this->priority = $priority; }
    public function setListId($list_id) { $this->list_id = $list_id; }

    // Add task
    public function add() {
        $stmt = $this->conn->prepare("INSERT INTO tasks (list_id, title, priority, status, created_at) VALUES (?, ?, ?, 'To Do', NOW())");
        $stmt->bind_param("iss", $this->list_id, $this->title, $this->priority);
        return $stmt->execute();
    }

    // Get tasks by list
    public function getAllByList($list_id) {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE list_id = ?");
        $stmt->bind_param("i", $list_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
