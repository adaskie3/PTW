<?php
require 'db.php';
session_start();

// Pobierz koszyk
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT games.* FROM user_carts JOIN games ON user_carts.game_id = games.id WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $games = $stmt->fetchAll();
} else {
    header("Location: login.php");
    exit;
}

// Obsługa usuwania
if (isset($_POST['remove'])) {
    $removeId = intval($_POST['remove']);
    $pdo->prepare("DELETE FROM user_carts WHERE user_id = ? AND game_id = ?")
        ->execute([$_SESSION['user_id'], $removeId]);
    header("Location: cart.php");
    exit;
}

// Finalizacja zakupu
if (isset($_POST['checkout']) && count($games) > 0) {
    try {
        $pdo->beginTransaction();
        $error = null;
        
        foreach ($games as $game) {
            $stmt = $pdo->prepare("SELECT * FROM game_codes WHERE game_id = ? AND assigned_to IS NULL LIMIT 1");
            $stmt->execute([$game['id']]);
            $code = $stmt->fetch();
            
            if ($code) {
                $pdo->prepare("UPDATE game_codes SET assigned_to = ? WHERE id = ?")
                    ->execute([$_SESSION['user_id'], $code['id']]);
                
                $pdo->prepare("INSERT INTO orders (user_id, game_id, code_id, purchase_date) VALUES (?, ?, ?, NOW())")
                    ->execute([$_SESSION['user_id'], $game['id'], $code['id']]);
                
                // Usuń z koszyka
                $pdo->prepare("DELETE FROM user_carts WHERE user_id = ? AND game_id = ?")
                    ->execute([$_SESSION['user_id'], $game['id']]);
            } else {
                $error = "Brak dostępnych kodów dla gry: {$game['title']}";
                break;
            }
        }
        
        if ($error) {
            $pdo->rollback();
            $checkout_error = $error;
        } else {
            $pdo->commit();
            $checkout_success = "Zakup zakończony pomyślnie! Kody dostępu znajdziesz w swoim profilu.";
        }
        
    } catch (PDOException $e) {
        $pdo->rollback();
        $checkout_error = "Błąd bazy danych: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Koszyk</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cart_profile.css" rel="stylesheet">
</head>
<body>
<div class="cart-container">
    <h2 class="mb-4">Twój koszyk</h2>
    
    <?php if (isset($checkout_success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($checkout_success) ?></div>
    <?php elseif (isset($checkout_error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($checkout_error) ?></div>
    <?php endif; ?>

    <?php if (count($games) > 0): ?>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Cena</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($games as $game): 
                        $total += $game['price'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($game['title']) ?></td>
                            <td><?= number_format($game['price'], 2) ?> PLN</td>
                            <td>
                                <form method="post" class="d-inline">
                                    <button type="submit" name="remove" value="<?= $game['id'] ?>"
                                        class="btn btn-remove" title="Usuń z koszyka">
                                        <span class="trash-icon">
                                            <svg width="36" height="36" viewBox="0 0 36 36" fill="none">
                                            <!-- Klapa kosza -->
                                            <rect class="trash-lid" x="9" y="8" width="18" height="4" rx="2" fill="#fff" />
                                            <!-- Korpus kosza -->
                                            <rect x="11" y="14" width="14" height="14" rx="3" fill="#ff3c3c"/>
                                            <!-- Linie na korpusie -->
                                            <rect x="14" y="17" width="2" height="8" rx="1" fill="#fff"/>
                                            <rect x="20" y="17" width="2" height="8" rx="1" fill="#fff"/>
                                            </svg>
                                        </span>
                                    </button>

                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="text-end fw-bold" colspan="2">Suma całkowita:</td>
                        <td class="total-price"><?= number_format($total, 2) ?> PLN</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-outline-secondary">
                ← Kontynuuj zakupy
            </a>
            <form method="post">
                <button type="submit" name="checkout" class="btn btn-success btn-lg">
                    🛍️ Finalizuj zakup
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h4 class="text-muted mb-4">Twój koszyk jest pusty</h4>
            <a href="index.php" class="btn btn-primary btn-lg">
                Przejdź do sklepu
            </a>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
