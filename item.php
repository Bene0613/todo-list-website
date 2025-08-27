<?php
session_start();
include_once './classes/Database.php';
$db = new Database("localhost", "root", "", "todo");

// Controle: gebruiker moet ingelogd zijn en er moet een taak-ID meegegeven zijn
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    exit("Taak niet gevonden of gebruiker niet ingelogd");
}

$task_id = $_GET['id']; // Haal taak-ID uit de URL

// Taakgegevens ophalen
$stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$taskResult = $stmt->get_result();
$task = $taskResult->fetch_assoc();

if (!$task) {
    exit("Taak niet gevonden in de database");
}


if (isset($_POST['upload_file'])) {
    $file = $_FILES['task_file']; 
    $file_tmp = $file['tmp_name']; 
    $file_name = basename($file['name']); 
    $file_folder = 'images/' . $file_name; 

    if (move_uploaded_file($file_tmp, $file_folder)) {

        $stmt = $db->prepare("UPDATE tasks SET files = ? WHERE id = ?");
        $stmt->bind_param("si", $file_folder, $task_id);
        $stmt->execute();

        $file_message = "Bestand succesvol geüpload.";


        $stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
        $task = $stmt->get_result()->fetch_assoc();
    } else {
        $file_message = "Fout bij uploaden bestand.";
    }
}


if (isset($_POST['btnComment'])) {
    $content = $_POST['comment'];
    if (!empty($content)) {
        $user_id = $_SESSION['user_id'];
        $stmt = $db->prepare("INSERT INTO comments (task_id, content, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $task_id, $content, $user_id);
        $stmt->execute();
    }
}

$stmt = $db->prepare("SELECT content, created_at FROM comments WHERE task_id = ? ORDER BY created_at DESC");
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

form textarea,
form input[type="file"] {
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    border: none;
    background-color: #353F4D;
    color: aliceblue;
    margin-bottom: 10px;
}

form button {
    background-color: #5C6B7D;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

form button:hover {
    background-color: aquamarine;
    color: #212A39;
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

.file-message {
    color: #00ff00;
    margin-bottom: 10px;
}
</style>
</head>
<body>

<h1><?php echo htmlspecialchars($task['title']); ?></h1>

<div class="taskBox">
    <h2><?php echo htmlspecialchars($task['title']); ?></h2>
    <p><strong>Priority:</strong> <?php echo htmlspecialchars($task['priority']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
    <p><strong>Made:</strong> <?php echo htmlspecialchars($task['created_at']); ?></p>

    <?php if (!empty($task['files'])): ?>
        <p><strong>Attached file:</strong> <a href="<?php echo htmlspecialchars($task['files']); ?>" target="_blank">View File</a></p>
    <?php endif; ?>

    <h2>Attach a file</h2>
    <?php if (isset($file_message)) echo "<p class='file-message'>$file_message</p>"; ?>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="task_file" required>
        <button type="submit" name="upload_file">Upload File</button>
    </form>

    <h2>Comments</h2>
    <form method="POST">
        <textarea name="comment" required></textarea><br>
        <button type="submit" name="btnComment">Add Comment</button>
    </form>

    <ul>
    <?php while ($c = $comments->fetch_assoc()): ?>
        <li>
            <?php echo htmlspecialchars($c['content']); ?> 
            (<?php echo $c['created_at']; ?>)
        </li>
    <?php endwhile; ?>
    </ul>

    <div class="back">
        <a href="taskIndex.php">← Back to Tasks</a>
    </div>
</div>

</body>
</html>
