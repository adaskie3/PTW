<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

$game_id = isset($_GET['game_id']) ? (int)$_GET['game_id'] : 0;

// Średnia i liczba recenzji
$stmt = $pdo->prepare("SELECT COALESCE(AVG(rating),0) AS avg, COUNT(*) AS count FROM reviews WHERE game_id = ?");
$stmt->execute([$game_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Lista recenzji
$stmt = $pdo->prepare("
    SELECT r.*, u.username, DATE_FORMAT(r.created_at, '%d.%m.%Y %H:%i') AS formatted_date
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.game_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$game_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$reviews_html = '';
if ($reviews) {
    foreach ($reviews as $review) {
        $reviews_html .= '<div class="card review-card mb-3"><div class="card-body">';
        $reviews_html .= '<div class="d-flex align-items-center mb-3">';
        $reviews_html .= '<div class="review-author-avatar">'.strtoupper(substr($review['username'],0,1)).'</div>';
        $reviews_html .= '<div class="ms-3"><strong>'.htmlspecialchars($review['username']).'</strong>';
        $reviews_html .= '<div class="text-muted small">'.$review['formatted_date'].'</div></div></div>';
        $reviews_html .= '<div class="review-rating mb-2" style="font-size:1.3em;">';
        for ($i=1;$i<=5;$i++) {
            $reviews_html .= '<i class="fas fa-star'.($i<=$review['rating']?' text-warning':' text-secondary').'"></i>';
        }
        $reviews_html .= '</div>';
        $reviews_html .= '<p class="mb-0">'.htmlspecialchars($review['content']).'</p>';
        $reviews_html .= '</div></div>';
    }
} else {
    $reviews_html = '<div class="text-center py-4"><i class="fas fa-comment-slash fa-2x text-muted mb-3"></i><p class="text-muted">Brak recenzji - bądź pierwszy!</p></div>';
}

echo json_encode([
    'reviews' => $reviews_html,
    'count' => (int)$row['count'],
    'avg' => number_format($row['avg'], 1)
]);
?>
