<?php
require 'db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Nie jesteś zalogowany']);
    exit;
}

$gameId = (int)$_POST['game_id'];
$userId = $_SESSION['user_id'];

try {
    // Usuń grę z koszyka
    $stmt = $pdo->prepare("DELETE FROM user_carts WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$userId, $gameId]);
    
    // Zwróć aktualny stan koszyka
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_carts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $count = $stmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'count' => $count,
        'game_id' => $gameId
    ]);
} catch (PDOException $e) {
    error_log("Błąd usuwania z koszyka: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych']);
}
?>
