<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_POST['game_id']) || !ctype_digit((string)$_POST['game_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Nieprawidłowe ID gry']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Wymagane logowanie']);
    exit;
}

try {
    $gameId = (int)$_POST['game_id'];
    $userId = $_SESSION['user_id'];

    // Sprawdź dostępność kluczy (jeśli nie admin)
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM game_codes WHERE game_id = ? AND assigned_to IS NULL");
        $stmt->execute([$gameId]);
        if ($stmt->fetchColumn() == 0) {
            http_response_code(409);
            echo json_encode(['status' => 'error', 'message' => 'Brak kluczy dla tej gry']);
            exit;
        }
    }

    // Czy już jest w koszyku?
    $stmt = $pdo->prepare("SELECT 1 FROM user_carts WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$userId, $gameId]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO user_carts (user_id, game_id) VALUES (?, ?)")->execute([$userId, $gameId]);
    }

    // Liczba gier w koszyku
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_carts WHERE user_id = ?");
    $stmt->execute([$userId]);
    $cartCount = $stmt->fetchColumn();
    $_SESSION['cart_count'] = $cartCount;

    echo json_encode([
        'status' => 'success',
        'count' => $cartCount,
        'message' => 'Gra dodana do koszyka'
    ]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Błąd systemu, spróbuj ponownie']);
}
?>
