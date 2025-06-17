<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
    exit;
}

if (!isset($_POST['game_id']) || !ctype_digit($_POST['game_id'])) {
    echo json_encode(['success' => false, 'message' => 'Błędne ID gry']);
    exit;
}

try {
    $gameId = (int)$_POST['game_id'];
    $userId = $_SESSION['user_id'];

    // Sprawdź czy jest w ulubionych
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$userId, $gameId]);
    $is_favorite = (bool)$stmt->fetch();

    if ($is_favorite) {
        $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND game_id = ?")
            ->execute([$userId, $gameId]);
    } else {
        $pdo->prepare("INSERT INTO favorites (user_id, game_id) VALUES (?, ?)")
            ->execute([$userId, $gameId]);
    }

    echo json_encode(['success' => true, 'is_favorite' => !$is_favorite]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych']);
}
