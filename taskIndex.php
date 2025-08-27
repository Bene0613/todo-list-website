<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}

include_once 'Database.php';
$db = new Database();

$user_id = $_SESSION['user_id'];

$list_id = $_SESSION['list_id'] ?? 0;

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];
    $del_stmt = $db->prepare("DELETE FROM tasks WHERE id = ?");         //web tech knowledge youtube
    $del_stmt->bind_param("i", $task_id);
    $del_stmt->execute();

    header("Location: taskIndex.php");
    exit();
}

if (isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $priority = $_POST['priority'];
    $list_id = $_POST['list_id'];

    $check_stmt = $db->prepare("SELECT id FROM lists WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $list_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $stmt = $db->prepare("INSERT INTO tasks (list_id, title, priority, status, created_at) 
                              VALUES (?, ?, ?, 'To Do', NOW())");
        $stmt->bind_param("iss", $list_id, $task_name, $priority);
        $stmt->execute();

        $_SESSION['list_id'] = $list_id; 
    }
}

if (isset($_POST['save_edit'])) {
    $task_id = $_POST['task_id'];
    $new_title = $_POST['new_title'];

    if (!empty($new_title)) {                   //chatgpt
        $stmt = $db->prepare("UPDATE tasks t 
                              JOIN lists l ON t.list_id = l.id 
                              SET t.title = ? 
                              WHERE t.id = ? AND l.user_id = ?");
        $stmt->bind_param("sii", $new_title, $task_id, $user_id);
        $stmt->execute();
    }

    header("Location: taskIndex.php");
    exit();
}

$orderBy = "title ASC";  //chatgpt
if (isset($_GET['sort']) && isset($_GET['type'])) {
    $sort = $_GET['sort'];  
    $type = $_GET['type']; 

    if (($type == "title" || $type == "priority") && 
        ($sort == "ascending" || $sort == "descending")) {
        
        $direction = ($sort == "ascending") ? "ASC" : "DESC";
        $orderBy = "$type $direction";
    }
}
$tasks = [];
if ($list_id > 0) {
    $query = "SELECT id, title, priority, status FROM tasks WHERE list_id = ? ORDER BY $orderBy";
    $tasks_stmt = $db->prepare($query);
    $tasks_stmt->bind_param("i", $list_id);
    $tasks_stmt->execute();
    $result = $tasks_stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

$lists_stmt = $db->prepare("SELECT id, title FROM lists WHERE user_id = ?");
$lists_stmt->bind_param("i", $user_id);
$lists_stmt->execute();
$result = $lists_stmt->get_result();

$lists = [];
while ($row = $result->fetch_assoc()) {
    $lists[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task List</title>
<style>
body {
    background-color: #353F4D;
    font-family: 'Nunito', sans-serif;
    color: aliceblue;
    margin: 0;
    padding: 30px;
}

h1 {
    text-align: center;
    margin-bottom: 20px;
}

.tasky {
    max-width: 700px;
    margin: 0 auto;
}

.addForm {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.addForm input[type="text"],
.addForm select {
    padding: 10px;
    margin-right: 10px;
    border-radius: 5px;
    border: none;
    background-color: #212A39;
    color: aliceblue;
}

.addForm input[type="submit"] {
    background-color: #5C6B7D;
    color: aliceblue;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.2s;
}

.addForm input[type="submit"]:hover {
    background-color: aquamarine;
    color: #353F4D;
}

.sorting {
    text-align: center;
    margin-bottom: 20px;
}

.sorting a {
    color: aquamarine;
    text-decoration: none;
    margin: 0 5px;
}

.sorting a:hover {
    text-decoration: underline;
}

.task {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #212A39;
    padding: 15px 20px;
    margin-bottom: 10px;
    border-radius: 8px;
    box-shadow: inset 0 0 5px #000000;
}

.task .name {
    flex: 2;
}

.task .priority {
    flex: 1;
    font-weight: bold;
    text-align: center;
}

.task .status {
    flex: 1;
    text-align: center;
}

.task .actions {
    flex: 1;
    text-align: right;
}

.task .priority.high { color: #FF6B6B; }
.task .priority.medium { color: #FFD700; }
.task .priority.low { color: #4CAF50; }

.task a {
    margin-left: 10px;
    color: aliceblue;
    text-decoration: none;
}

.task a:hover { color: aquamarine; }

.task form input[type="text"] {
    padding: 5px;
    border-radius: 5px;
    border: none;
    background-color: #353F4D;
    color: aliceblue;
}

.task form input[type="submit"] {
    background-color: aquamarine;
    border: none;
    border-radius: 5px;
    padding: 5px 10px;
    cursor: pointer;
    color: #212A39;
    font-weight: bold;
}

.back {
    display: block;
    text-align: center;
    margin-top: 20px;
}

.back a {
    color: aquamarine;
    text-decoration: none;
    font-weight: bold;
}

.back a:hover {
    text-decoration: underline;
}
</style>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

<h1>Your tasks</h1>

<div class="tasky">

    <form class="addForm" method="POST">
        <input type="text" name="task_name" placeholder="Task name" required>
        <select name="priority" required>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        </select>
        <select name="list_id">
            <?php foreach ($lists as $list): ?>
                <option value="<?php echo $list['id']; ?>" <?php echo ($list['id'] == $list_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($list['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="add_task" value="Add Task">
    </form>

    <div class="sorting">
        <strong>Sort by:</strong>          
        <a href="taskIndex.php?sort=ascending&type=title">Title ↑</a> | 
        <a href="taskIndex.php?sort=descending&type=title">Title ↓</a> |
        <a href="taskIndex.php?sort=ascending&type=priority">Priority ↑</a> | 
        <a href="taskIndex.php?sort=descending&type=priority">Priority ↓</a>
    </div>

    <?php foreach($tasks as $task): ?>
    <div class="task">
        <div class="name">
            <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $task['id']): ?>
                <form method="POST" style="display:flex; gap:10px;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <input type="text" name="new_title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                    <input type="submit" name="save_edit" value="Save">
                    <a href="taskIndex.php">Cancel</a>
                </form>
            <?php else: ?>
                <a href="item.php?id=<?php echo $task['id']; ?>">
                    <?php echo htmlspecialchars($task['title']); ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="priority <?php echo strtolower($task['priority']); ?>">
            <?php echo $task['priority']; ?>
        </div>
        <div class="status">
            <?php echo $task['status']; ?>
        </div>
        <div class="actions">
            <a href="taskIndex.php?edit_id=<?php echo $task['id']; ?>">✏️</a>
            <a href="taskIndex.php?id=<?php echo $task['id']; ?>" onclick="return confirm('Delete this task?');">🗑️</a> 
        </div>
    </div>
    <?php endforeach; ?>

    <div class="back">
        <a href="index.php">← Back to lists</a>
    </div>

</div>

</body>
</html>
