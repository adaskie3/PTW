<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo '<div class="text-center text-muted py-2">Zaloguj się, aby korzystać z koszyka.</div>';
    exit;
}

$stmt = $pdo->prepare("SELECT games.* FROM user_carts JOIN games ON user_carts.game_id = games.id WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$games = $stmt->fetchAll();

if (count($games) === 0) {
    echo '<div class="text-center text-muted py-2">Koszyk jest pusty.</div>';
} else {
    foreach ($games as $game) {
        echo '<div class="d-flex align-items-center mb-2">';
        if ($game['image']) {
            echo '<img src="uploads/' . htmlspecialchars($game['image']) . '" width="48" height="48" style="object-fit:cover;border-radius:8px;margin-right:12px;">';
        } else {
            echo '<div style="width:48px;height:48px;background:#23272b;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-right:12px;"><i class="fa fa-gamepad text-muted"></i></div>';
        }
        echo '<div class="flex-grow-1">';
        echo '<div class="fw-bold" style="font-size:1em;">' . htmlspecialchars($game['title']) . '</div>';
        echo '<div class="text-secondary small">' . number_format($game['price'],2) . ' PLN</div>';
        echo '</div>';
        echo '<button class="btn btn-sm btn-danger ms-2 remove-from-cart" data-game-id="'.$game['id'].'"><i class="fa fa-trash"></i></button>';
        echo '</div>';
    }
    echo '<div class="mt-3 text-end"><a href="cart.php" class="btn btn-sm btn-success w-100">Przejdź do koszyka</a></div>';
}
?>
