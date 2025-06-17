<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo '<div class="empty-message">Musisz być zalogowany</div>';
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, g.title as game_title, g.image as game_image
    FROM reviews r
    JOIN games g ON r.game_id = g.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$user_id]);
$reviews = $stmt->fetchAll();

if (empty($reviews)) {
    echo '<div class="empty-message">
        <i class="fas fa-comment-slash me-2"></i> 
        Nie napisałeś jeszcze żadnych recenzji.
    </div>';
} else {
    echo '<div class="review-list">';
    foreach ($reviews as $review) {
        echo '<div class="review-card mb-4 p-4">
            <div class="d-flex align-items-start gap-4">';
        
        if ($review['game_image']) {
            echo '<img src="uploads/' . htmlspecialchars($review['game_image']) . '" class="review-game-image" alt="' . htmlspecialchars($review['game_title']) . '">';
        }
        
        echo '<div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="neon-text">
                        <a href="game.php?id=' . $review['game_id'] . '" class="text-decoration-none">' . htmlspecialchars($review['game_title']) . '</a>
                    </h5>
                    <small class="text-muted">' . date('d.m.Y H:i', strtotime($review['created_at'])) . '</small>
                </div>
                <div class="star-rating mb-2">';
        
        for ($i = 1; $i <= 5; $i++) {
            echo '<i class="fas fa-star ' . ($i <= $review['rating'] ? 'text-warning' : 'text-muted') . '"></i>';
        }
        
        echo '</div>';
        
        if (!empty($review['review_text'])) {
            echo '<div class="review-content">' . nl2br(htmlspecialchars($review['review_text'])) . '</div>';
        }
        
        echo '</div>
            </div>
        </div>';
    }
    echo '</div>';
}
?>
