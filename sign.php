<?php
    if(!empty($_POST)){
       $uname = $_POST['username'];
       $email = $_POST['email'];
       $password = $_POST['password'];
       $options = [
        'cost' => 14,
    ];
       $hash = password_hash($password, PASSWORD_DEFAULT, $options);
       $conn = new mysqli("localhost","root","","todo");
       $statement = $conn->prepare('INSERT INTO users (username, email, password) VALUES (?,?,?)');
       $statement-> bind_param('sss', $uname, $email, $hash);
       $stmt->execute();

    $new_user_id = $stmt->insert_id;

    // Create a default list (chat gpt)
    $stmt2 = $db->prepare("INSERT INTO lists (user_id, title) VALUES (?, 'My First List')");
    $stmt2->bind_param("i", $new_user_id);
    $stmt2->execute();

    header("Location: log.php");
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
    <style>
    body {
    background-color: #353F4D;
    font-family: 'Nunito', sans-serif;
    color: aliceblue;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.wrap {
    background-color: #212A39;
    padding: 30px 20px;
    border-radius: 10px;
    width: 350px;
    box-shadow: inset 0 0 10px #000000;
    display: flex;
    flex-direction: column;
    align-items: center;
}

h2 {
    margin-bottom: 20px;
    color: aliceblue;
    text-align: center;
}

.text input {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    border-radius: 5px;
    background-color: #353F4D;
    color: aliceblue;
}

.text input::placeholder {
    color: #b0b0b0;
}

button {
    width: 100%;
    height: 40px;
    border: none;
    border-radius: 5px;
    background-color: #5C6B7D;
    color: aliceblue;
    cursor: pointer;
    transition: background-color 0.2s;
}

button:hover {
    background-color: aquamarine;
    color: #353F4D;
}

.alert.hidden {
    display: none;
}

.alert {
    color: #FF6B6B;
    font-style: italic;
    margin-top: 10px;
    text-align: center;
}

.more {
    font-size: smaller;
    text-align: center;
    margin-top: 15px;
}

.more a {
    color: pink;
    text-decoration: none;
}
    </style>
</head>
<body>
    <div class="wrap">
        <div class ="euhm">
            <form action="" method="POST">
            <h2>Welcome</h2>
                <div class= "text">
                    <input type ="text" name="username" placeholder= "Username">
                </div>
                <div class= "text">
                    <input type ="text" name="email" placeholder= "Email">
                </div>
                <div class= "text">
                    <input type ="password" name="password" placeholder= "Password">
                </div>
                <button type = "submit">Sign up</button>
                <div class="alert <?php echo $error ? '': 'hidden'; ?>">
                There is already an account linked to this email. Let’s try again!
                </div>
                <div class="more">
                    <p>Already have an account? <a href="log.php">Log in </a></p>
                </div>
            </form>
        </div>
</div>
<footer>©2024 all rights reserved</footer>
</body>
</html>