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

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of task_id
     */ 
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * Set the value of task_id
     *
     * @return  self
     */ 
    public function setTaskId($task_id)
    {
        $this->task_id = $task_id;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        if(!empty($content)) $this->content = $content; 
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
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
