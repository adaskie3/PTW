<?php
require 'db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0]);
    exit;
}
$stmt = $pdo->prepare("SELECT COUNT(*) FROM user_carts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$count = $stmt->fetchColumn();
echo json_encode(['count' => $count]);
?>
