<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $password]);
        header('Location: login.php');
    } catch (Exception $e) {
        $error = "Użytkownik lub email już istnieje!";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Nowe konto</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png">
    <link href="css/login.css" rel="stylesheet">
</head>
<body class="login-bg">
<div class="login-panel-wrap">
    <div class="login-panel">
        <form method="post" autocomplete="off">
            <div class="login-panel-header">
                <span class="login-icon">🎨</span>
                <span class="login-title">Nowe konto</span>
            </div>
            <?php if (isset($error)) echo "<div class='login-alert'>$error</div>"; ?>
            <div class="login-group">
                <label for="username">Nick</label>
                <input type="text" id="username" name="username" required autocomplete="nickname">
            </div>
            <div class="login-group">
                <label for="email">Adres email</label>
                <input type="email" id="email" name="email" required autocomplete="email">
            </div>
            <div class="login-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="login-group">
                <label for="password2">Potwierdź hasło</label>
                <input type="password" id="password2" name="password2" required autocomplete="new-password">
            </div>
            <button class="login-btn" type="submit">Zarejestruj się</button>
            <div class="login-links">
                <span>Masz już konto? <a href="login.php">Zaloguj się</a></span>
                <a href="index.php">Powrót do katalogu</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
