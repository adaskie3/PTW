<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Musisz być zalogowany']);
    exit;
}

$errors = [];
$gameId = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;
$rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
$content = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

if ($gameId < 1) {
    $errors[] = 'Nieprawidłowe ID gry';
}

if ($rating < 1 || $rating > 5) {
    $errors[] = 'Ocena musi być w zakresie 1-5';
}

if (mb_strlen($content) < 10) {
    $errors[] = 'Recenzja musi mieć minimum 10 znaków';
}

if (mb_strlen($content) > 2000) {
    $errors[] = 'Recenzja może mieć maksymalnie 2000 znaków';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    // Sprawdź czy gra istnieje
    $stmt = $pdo->prepare("SELECT id FROM games WHERE id = ?");
    $stmt->execute([$gameId]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Gra nie została znaleziona']);
        exit;
    }

    // Sprawdź czy użytkownik już dodał recenzję
    $stmt = $pdo->prepare("SELECT id FROM reviews WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$_SESSION['user_id'], $gameId]);
    if ($stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Już dodałeś recenzję dla tej gry']);
        exit;
    }

    // Dodaj recenzję
    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, game_id, rating, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $result = $stmt->execute([$_SESSION['user_id'], $gameId, $rating, $content]);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Recenzja została dodana pomyślnie']);
    } else {
        throw new Exception('Nie udało się zapisać recenzji');
    }

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych']);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Wystąpił błąd podczas dodawania recenzji']);
}
?>
