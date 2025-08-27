<?php
session_start();
$error_message = ""; // Variabele voor foutmeldingen


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['username']; // Ingevoerde gebruikersnaam
    $password = $_POST['password']; // Ingevoerd wachtwoord

    if (!empty($uname) && !empty($password)) {
        // Verbinden met de database
        $conn = new mysqli("localhost", "root", "", "todo");
        if ($conn->connect_error) {
            die("Verbinding mislukt: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        if ($stmt) {
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id']; // Sessie starten
                    header("Location: index.php"); // Doorsturen naar lijstenpagina
                    exit();
                } else {
                    $error_message = "Wachtwoord onjuist.";
                }
            } else {
                $error_message = "Geen gebruiker gevonden met die naam.";
            }

            $stmt->close();
        }
        $conn->close();
    } else {
        $error_message = "Vul zowel gebruikersnaam als wachtwoord in.";
    }
}
?>

<!DOCTYPE html>
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

.more {
    font-size: smaller;
    text-align: center;
    margin-top: 15px;
}

.more a {
    color: aquamarine;
    text-decoration: none;
}

.alert {
    color: #FF6B6B;
    font-style: italic;
    text-align: center;
    margin-top: 10px;
}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="euhm">
            <form action="" method="POST">
                <h2>Log in</h2>
                <div class="text">
                    <input type="text" name="username" placeholder="Username">
                </div>
                <div class="text">
                    <input type="password" name="password" placeholder="Password">
                </div>
                <button type="submit">Log in</button>
                <?php if (!empty($error_message)): ?>
                    <div class="alert"><?= $error_message ?></div>
                <?php endif; ?>
                <div class="more">
                    <p>Don't have an account? <a href="sign.php">Sign Up </a></p>
                </div>
            </form>
        </div>
    </div>
    <footer>©2024 all rights reserved</footer>
</body>
</html>
