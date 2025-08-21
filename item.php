<?php
session_start();
include_once './classes/Database.php';

$db = new Database("localhost", "root", "", "todo");

if (!isset($_GET['id'])) {
    exit("Task not found");
}

$task_id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$taskResult = $stmt->get_result();
$task = $taskResult->fetch_assoc();

if (!$task) {
    exit("Task not found in database");
}

if (isset($_POST['btnComment'])) {
    $content = $_POST['content'];

    if (!empty($content)) {
        $stmt = $db->prepare("INSERT INTO comments (task_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $task_id, $content);
        $stmt->execute();
    }
}

$stmt = $db->prepare("SELECT * FROM comments WHERE task_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task Details</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        background-color: #353F4D;
        font-family: 'Nunito', sans-serif;
        color: aliceblue;
        margin: 0;
        padding: 20px;
    }

    h1 {
        text-align: center;
        color: aquamarine;
        margin-bottom: 20px;
    }

    .taskBox {
        background-color: #212A39;
        max-width: 600px;
        margin: auto;
        padding: 20px;
        border-radius: 12px;
        box-shadow: inset 0 0 10px #000;
    }

    .taskBox h2 {
        color: aquamarine;
        margin-bottom: 10px;
    }

    .taskBox p {
        margin: 5px 0;
    }

    .comments {
        margin-top: 20px;
    }

    .comment {
        background-color: #353F4D;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 8px;
    }

    .comment small {
        display: block;
        font-size: 12px;
        color: gray;
        margin-top: 5px;
    }

    form textarea {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: none;
        background-color: #353F4D;
        color: aliceblue;
        margin-bottom: 10px;
        resize: none;
    }

    form button {
        background-color: aquamarine;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        color: #212A39;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }

    form button:hover {
        background-color: #5C6B7D;
        color: aliceblue;
    }

    .back {
        display: block;
        text-align: center;
        margin-top: 20px;
    }

    .back a {
        color: aquamarine;
        text-decoration: none;
    }

    .back a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<h1>Task Details</h1>

<div class="taskBox">
    <h2><?php echo htmlspecialchars($task['title']); ?></h2>
    <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>

    <div class="comments">
        <h2>Comments</h2>
        <?php if ($comments->num_rows > 0): ?>
            <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="comment">
                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                    <small><?php echo $comment['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No comments yet.</p>
        <?php endif; ?>
    </div>

    <h2>Add a Comment</h2>
    <form method="POST">
        <textarea name="content" rows="3" placeholder="Write a comment..." required></textarea>
        <button type="submit" name="btnComment">Add Comment</button>
    </form>
</div>

<div class="back">
    <a href="taskIndex.php">← Back to Tasks</a>
</div>

</body>
</html>
