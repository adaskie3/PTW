<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="empty-message">Musisz być zalogowany</div>';
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT g.id, g.title, g.price, g.image, f.created_at as favorited_at
    FROM favorites f
    JOIN games g ON f.game_id = g.id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

if (empty($favorites)) {
    echo '<div class="empty-message">
        <i class="fas fa-heart-broken me-2"></i> 
        Nie masz jeszcze żadnych ulubionych gier.
    </div>';
} else {
    echo '<div class="row g-4">';
    foreach ($favorites as $game) {
        echo '<div class="col-md-6 col-lg-4">
            <div class="game-card h-100">
                <div class="game-image-container">';
        
        if ($game['image']) {
            echo '<img src="uploads/' . htmlspecialchars($game['image']) . '" class="card-img-top" alt="' . htmlspecialchars($game['title']) . '">';
        } else {
            echo '<div class="no-image-placeholder bg-dark d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-gamepad fa-3x text-muted"></i>
                </div>';
        }
        
        echo '</div>
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($game['title']) . '</h5>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="game-price">' . number_format($game['price'], 2) . ' PLN</span>
                        <a href="game.php?id=' . $game['id'] . '" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-right"></i> Szczegóły
                        </a>
                    </div>
                </div>
            </div>
        </div>';
    }
    echo '</div>';
}
?>
