<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Nieprawidłowe dane logowania!";
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png">
    <link href="css/login.css" rel="stylesheet">
</head>
<body class="login-bg">
<div class="login-panel-wrap">
    <div class="login-panel">
        <form method="post" autocomplete="off">
            <div class="login-panel-header">
                <span class="login-icon">🔑</span>
                <span class="login-title">Zaloguj się</span>
            </div>
            <?php if (isset($error)) echo "<div class='login-alert'>$error</div>"; ?>
            <div class="login-group">
                <label for="email">Adres email</label>
                <input type="email" id="email" name="email" required autocomplete="username">
            </div>
            <div class="login-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button class="login-btn" type="submit">Zaloguj się</button>
            <div class="login-links">
                <a href="register.php">Zarejestruj nowe konto</a>
                <a href="index.php">– Powrót do katalogu</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
