<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: log.php");
    exit();
}


include_once 'Database.php';
$db = new Database("localhost", "root", "", "todo");

$user_id = $_SESSION['user_id']; 

if (isset($_POST['add_list'])) {
    $title = $_POST['title']; 
    if (!empty($title)) {
        $stmt = $db->prepare("INSERT INTO lists (user_id, title) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $title);
        $stmt->execute();
    }
}

$stmt = $db->prepare("SELECT id, title FROM lists WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lists = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Lists</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
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

.around {
    max-width: 600px;
    margin: 0 auto;
}

.list {
    background-color: #212A39;
    padding: 15px;
    margin: 10px 0;
    border-radius: 8px;
    box-shadow: inset 0 0 5px #000;
}

.list a {
    text-decoration: none;
    color: aquamarine;
    font-weight: bold;
}

.list a:hover {
    text-decoration: underline;
}

form {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

form input[type="text"] {
    padding: 10px;
    border-radius: 5px;
    border: none;
    background: #212A39;
    color: aliceblue;
    margin-right: 10px;
}

form input[type="submit"] {
    background-color: #5C6B7D;
    border: none;
    border-radius: 5px;
    padding: 10px 15px;
    cursor: pointer;
    color: aliceblue;
}

form input[type="submit"]:hover {
    background-color: aquamarine;
    color: #353F4D;
}

.logout {
    text-align: center;
    margin-top: 30px;
}

.logout a {
    color: pink;
    text-decoration: none;
}

.logout a:hover {
    text-decoration: underline;
}
</style>
</head>
<body>
<h1>My Lists</h1>
<div class="around">
    <?php while($list = $lists->fetch_assoc()): ?>
        <div class="list">
            <a href="taskIndex.php?list_id=<?= $list['id'] ?>">
                <?= htmlspecialchars($list['title']) ?>
            </a>
        </div>
    <?php endwhile; ?>

    <form method="POST">
        <input type="text" name="title" placeholder="New list name" required>
        <input type="submit" name="add_list" value="Add List">
    </form>

    <div class="logout">
        <a href="logout.php">Log out</a>
    </div>
</div>
</body>
</html>
