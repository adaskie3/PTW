<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Pobierz dane użytkownika
$user_stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

// Pobierz kupione gry i klucze
$mygames_stmt = $pdo->prepare("SELECT games.title, game_codes.code, games.image FROM orders
    JOIN games ON orders.game_id=games.id
    JOIN game_codes ON orders.code_id=game_codes.id
    WHERE orders.user_id=?");
$mygames_stmt->execute([$user_id]);
$mygames = $mygames_stmt->fetchAll();

// Pobierz ulubione gry
$favorites_stmt = $pdo->prepare("SELECT g.id, g.title, g.price, g.image, f.created_at as favorited_at FROM favorites f JOIN games g ON f.game_id = g.id WHERE f.user_id = ? ORDER BY f.created_at DESC");
$favorites_stmt->execute([$user_id]);
$favorites = $favorites_stmt->fetchAll();

// Pobierz recenzje użytkownika
$reviews_stmt = $pdo->prepare("SELECT r.*, g.title as game_title, g.image as game_image FROM reviews r JOIN games g ON r.game_id = g.id WHERE r.user_id = ? ORDER BY r.created_at DESC");
$reviews_stmt->execute([$user_id]);
$reviews = $reviews_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Mój profil</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cart_profile.css" rel="stylesheet">
</head>
<body>
<div class="profile-container">
    <h2 class="mb-4">Witaj, <?=htmlspecialchars($user['username'])?>!</h2>

    <!-- Sekcja: Kupione gry i klucze -->
    <h4 class="mb-3">Twoje kupione gry i klucze</h4>
    <?php if (count($mygames) > 0): ?>
        <table class="table table-dark table-hover profile-table">
            <thead>
                <tr>
                    <th>Okładka</th>
                    <th>Tytuł gry</th>
                    <th>Klucz</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mygames as $g): ?>
                    <tr>
                        <td><img src="uploads/<?=htmlspecialchars($g['image'])?>" width="60" height="60"></td>
                        <td><?=htmlspecialchars($g['title'])?></td>
                        <td>
                            <div class="key-cell">
                                <span class="badge bg-success"><?=htmlspecialchars($g['code'])?></span>
                                <button class="copy-btn" onclick="navigator.clipboard.writeText('<?=htmlspecialchars($g['code'])?>')">Kopiuj</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="empty-message">Nie masz jeszcze żadnych kupionych gier.</div>
    <?php endif; ?>

    <!-- Zakładka: Ulubione gry -->
    <h4 class="mt-5 mb-3">Ulubione gry</h4>
    <?php if (count($favorites) > 0): ?>
        <div class="row g-3">
            <?php foreach ($favorites as $fav): ?>
                <div class="col-md-4">
                    <div class="card bg-dark text-light h-100">
                        <img src="uploads/<?=htmlspecialchars($fav['image'])?>" class="card-img-top" alt="">
                        <div class="card-body">
                            <h5 class="card-title"><?=htmlspecialchars($fav['title'])?></h5>
                            <p class="card-text"><?=number_format($fav['price'],2)?> PLN</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-message">Nie masz jeszcze ulubionych gier.</div>
    <?php endif; ?>

    <!-- Zakładka: Twoje recenzje -->
    <h4 class="mt-5 mb-3">Twoje recenzje</h4>
    <?php if (count($reviews) > 0): ?>
        <div class="row g-3">
            <?php foreach ($reviews as $rev): ?>
                <div class="col-md-6">
                    <div class="card bg-dark text-light h-100">
                        <div class="row g-0">
                            <div class="col-4">
                                <img src="uploads/<?=htmlspecialchars($rev['game_image'])?>" class="img-fluid rounded-start" alt="">
                            </div>
                            <div class="col-8">
                                <div class="card-body">
                                    <h5 class="card-title"><?=htmlspecialchars($rev['game_title'])?></h5>
                                    <p class="card-text"><?=htmlspecialchars($rev['content'])?></p>
                                    <p class="card-text"><small class="text-muted">Ocena: <?=$rev['rating']?>/5</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-message">Nie napisałeś jeszcze żadnych recenzji.</div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-outline-secondary mt-5">← Powrót do sklepu</a>
</div>
</body>
</html>
