<?php
class Comment {
    private $id;
    private $task_id;
    private $content;
    private $created_at;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getId() { return $this->id; }
    public function getContent() { return $this->content; }

    public function setTaskId($task_id) { $this->task_id = $task_id; }
    public function setContent($content) { 
        if(!empty($content)) $this->content = $content; 
    }

    public function add() {
        $stmt = $this->conn->prepare("INSERT INTO comments (task_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $this->task_id, $this->content);
        return $stmt->execute();
    }

    public function getAllByTask($task_id) {
        $stmt = $this->conn->prepare("SELECT * FROM comments WHERE task_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
