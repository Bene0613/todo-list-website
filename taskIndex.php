<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}

include_once './classes/Database.php';
$db = new Database("localhost", "root", "", "todo");

$user_id = (int)$_SESSION['user_id'];

if (isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'];
    $priority = $_POST['priority'];
    $list_id = (int)$_POST['list_id'];

    $check_stmt = $db->prepare("SELECT id FROM lists WHERE id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $list_id, $user_id);
    $check_stmt->execute();
    if ($check_stmt->get_result()->num_rows > 0) {
        $stmt = $db->prepare("INSERT INTO tasks (list_id, title, priority, status, created_at) 
                              VALUES (?, ?, ?, 'To Do', NOW())");
        $stmt->bind_param("iss", $list_id, $task_name, $priority);
        $stmt->execute();

        $_SESSION['list_id'] = $list_id;
    }
}

if (isset($_SESSION['list_id'])) {
    $list_id = (int)$_SESSION['list_id'];
} else {
    $lists_stmt = $db->prepare("SELECT id FROM lists WHERE user_id = ? LIMIT 1");
    $lists_stmt->bind_param("i", $user_id);
    $lists_stmt->execute();
    $first_list = $lists_stmt->get_result()->fetch_assoc();
    $list_id = $first_list ? $first_list['id'] : 0;
    $_SESSION['l    ist_id'] = $list_id;
}

$tasks = [];
if ($list_id > 0) {
    $tasks_stmt = $db->prepare("SELECT id, title, priority, status 
                                FROM tasks WHERE list_id = ?");
    $tasks_stmt->bind_param("i", $list_id);
    $tasks_stmt->execute();
    $tasks = $tasks_stmt->get_result();
}

$lists_stmt = $db->prepare("SELECT id, title FROM lists WHERE user_id = ?");
$lists_stmt->bind_param("i", $user_id);
$lists_stmt->execute();
$lists = $lists_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task List</title>
<style>
body { background-color: #353F4D; font-family: 'Nunito', sans-serif; color: aliceblue; margin: 0; padding: 30px; }
h1 { text-align: center; margin-bottom: 20px; }
.tasky { max-width: 700px; margin: 0 auto; }
.addForm { display: flex; justify-content: center; margin-bottom: 30px; }
.addForm input[type="text"], .addForm select { padding: 10px; margin-right: 10px; border-radius: 5px; border: none; background-color: #212A39; color: aliceblue; }
.addForm input[type="submit"] { background-color: #5C6B7D; color: aliceblue; border: none; border-radius: 5px; cursor: pointer; transition: 0.2s; }
.addForm input[type="submit"]:hover { background-color: aquamarine; color: #353F4D; }
.task { display: flex; justify-content: space-between; align-items: center; background-color: #212A39; padding: 15px 20px; margin-bottom: 10px; border-radius: 8px; box-shadow: inset 0 0 5px #000000; }
.task .name { flex: 2; }
.task .priority { flex: 1; font-weight: bold; text-align: center; }
.task .status { flex: 1; text-align: center; }
.task .actions { flex: 1; text-align: right; }
.task .priority.high { color: #FF6B6B; }
.task .priority.medium { color: #FFD700; }
.task .priority.low { color: #4CAF50; }
.task a { margin-left: 10px; color: aliceblue; text-decoration: none; }
.task a:hover { color: aquamarine; }
</style>
</head>
<body>

<h1>Task List</h1>

<div class="tasky">
    <form class="addForm" method="POST">
        <input type="text" name="task_name" placeholder="Task name" required>
        <select name="priority" required>
            <option value="High">High</option>
            <option value="Medium">Medium</option>
            <option value="Low">Low</option>
        </select>
        <select name="list_id">
            <?php while($list = $lists->fetch_assoc()): ?>
                <option value="<?php echo $list['id']; ?>" <?php echo ($list['id'] == $list_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($list['title']); ?>
                </option>
            <?php endwhile; ?>
        </select>
        <input type="submit" name="add_task" value="Add Task">
    </form>

    <?php while($task = $tasks->fetch_assoc()): ?>
    <div class="task">
        <div class="name"><?php echo htmlspecialchars($task['title']); ?></div>
        <div class="priority <?php echo strtolower($task['priority']); ?>"><?php echo $task['priority']; ?></div>
        <div class="status"><?php echo $task['status']; ?></div>
        <div class="actions">
            <a href="edit_task.php?id=<?php echo $task['id']; ?>">✏️</a>
            <a href="delete_task.php?id=<?php echo $task['id']; ?>">🗑️</a>
        </div>
    </div>
    <?php endwhile; ?>
</div>

</body>
</html>
