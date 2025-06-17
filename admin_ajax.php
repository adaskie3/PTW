<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Brak uprawnień']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    $gameId = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

    // Edycja ceny gry
    if ($action === 'edit_price' && $gameId > 0 && isset($_POST['price'])) {
        $price = (float)$_POST['price'];
        $stmt = $pdo->prepare("UPDATE games SET price = ? WHERE id = ?");
        $stmt->execute([$price, $gameId]);
        echo json_encode(['success' => true, 'new_price' => number_format($price, 2)]);
        exit;
    }

    // Edycja tagów gry
    if ($action === 'edit_tags' && $gameId > 0 && isset($_POST['tags'])) {
        // Usuń stare tagi
        $pdo->prepare("DELETE FROM game_tags WHERE game_id = ?")->execute([$gameId]);
        $tags = array_filter(array_map('trim', explode(',', $_POST['tags'])));
        $tagArr = [];
        foreach ($tags as $tag) {
            if ($tag === '') continue;
            // Wstaw tag jeśli nie istnieje
            $stmt = $pdo->prepare("INSERT IGNORE INTO tags (name) VALUES (?)");
            $stmt->execute([$tag]);
            $tagId = $pdo->lastInsertId() ?: $pdo->query("SELECT id FROM tags WHERE name = " . $pdo->quote($tag))->fetchColumn();
            $pdo->prepare("INSERT IGNORE INTO game_tags (game_id, tag_id) VALUES (?, ?)")->execute([$gameId, $tagId]);
            $tagArr[] = $tag;
        }
        echo json_encode(['success' => true, 'new_tags' => implode(', ', $tagArr)]);
        exit;
    }

    // Usuwanie nieprzypisanego kodu gry (opcjonalnie)
    if ($action === 'delete_code' && isset($_POST['code_id'])) {
        $codeId = (int)$_POST['code_id'];
        $stmt = $pdo->prepare("DELETE FROM game_codes WHERE id = ? AND assigned_to IS NULL");
        $stmt->execute([$codeId]);
        echo json_encode(['success' => true]);
        exit;
    }

    // Jeśli nie rozpoznano akcji
    http_response_code(400);
    echo json_encode(['error' => 'Nieznana lub nieprawidłowa akcja']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Błąd serwera: ' . $e->getMessage()]);
}
