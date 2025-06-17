<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT g.*, f.created_at as favorited_at
    FROM favorites f
    JOIN games g ON f.game_id = g.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Ulubione gry</title>
    <link rel="stylesheet" href="css/cart_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<div class="profile-container">
    <h2>Twoje ulubione gry</h2>
    <?php if (empty($favorites)): ?>
        <div class="empty-message">
            <i class="fas fa-heart-broken me-2"></i> Nie masz jeszcze żadnych ulubionych gier.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($favorites as $game): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="game-card h-100">
                        <?php if ($game['image']): ?>
                            <img src="uploads/<?= htmlspecialchars($game['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($game['title']) ?>">
                        <?php else: ?>
                            <div class="no-image-placeholder bg-dark d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-gamepad fa-3x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($game['title']) ?></h5>
                            <p class="card-text text-muted"><?= htmlspecialchars(mb_strimwidth($game['description'], 0, 80, '...')) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="game-price"><?= number_format($game['price'], 2) ?> PLN</span>
                                <a href="game.php?id=<?= $game['id'] ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrow-right"></i> Szczegóły
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
