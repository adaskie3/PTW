<?php
require 'db.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$game_id = (int)$_GET['id'];

$stmt = $pdo->prepare("
    SELECT g.*, 
        GROUP_CONCAT(DISTINCT t.name SEPARATOR ',') AS tags,
        (SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE game_id = g.id) AS avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE game_id = g.id) AS review_count
    FROM games g
    LEFT JOIN game_tags gt ON g.id = gt.game_id
    LEFT JOIN tags t ON gt.tag_id = t.id
    WHERE g.id = ?
    GROUP BY g.id
");
$stmt->execute([$game_id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$game) {
    header("Location: index.php");
    exit;
}

// Sprawdź czy gra jest w koszyku
$in_cart = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT 1 FROM user_carts WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $in_cart = (bool)$stmt->fetchColumn();
}

// Sprawdź, czy gra jest w ulubionych
$is_favorite = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $is_favorite = (bool)$stmt->fetchColumn();
}

// Pobierz recenzje
$stmt = $pdo->prepare("
    SELECT r.*, u.username, DATE_FORMAT(r.created_at, '%d.%m.%Y %H:%i') AS formatted_date
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.game_id = ?
    ORDER BY r.created_at DESC
");
$stmt->execute([$game_id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sprawdź recenzję użytkownika
$user_review = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ? AND game_id = ?");
    $stmt->execute([$_SESSION['user_id'], $game_id]);
    $user_review = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($game['title']) ?> - GameKeys</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Exo+2:wght@300;600&display=swap" rel="stylesheet" />
    <link href="css/game.css" rel="stylesheet" />
</head>
<body class="bg-space">
<nav class="navbar navbar-expand-lg navbar-dark bg-transparent py-3">
    <div class="container">
        <a class="navbar-brand gaming-logo" href="index.php">
            <img src="img/favicon.png" alt="GameKeys" height="50" class="me-2 floating" />
            <span class="gradient-text">GAME</span>KEYS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="ms-auto d-flex align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown ms-3">
                        <a href="#" class="btn btn-icon" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-user fa-lg"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item text-warning" href="admin.php"><i class="fas fa-toolbox me-2"></i>Panel Admina</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="dropdown ms-3">
                        <a href="#" class="btn btn-icon position-relative" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-shopping-cart fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge bg-danger" id="cart-badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                            <div id="cart-dropdown-content">
                                <div class="text-center">
                                    <div class="spinner-border text-secondary" role="status">
                                        <span class="visually-hidden">Ładowanie...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-icon ms-3">
                        <i class="fa-solid fa-right-from-bracket fa-lg"></i>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-blue ms-3">
                        <i class="fa-solid fa-sign-in-alt me-2"></i>Zaloguj się
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container py-5">
    <div class="row g-5">
        <div class="col-md-4">
            <img src="uploads/<?= htmlspecialchars($game['image']) ?>" class="img-fluid rounded-3 shadow" alt="<?= htmlspecialchars($game['title']) ?>">
            <div class="mt-4">
                <?php
                $tags = array_filter(array_map('trim', explode(',', $game['tags'] ?? '')));
                if ($tags):
                    foreach ($tags as $tag):
                ?>
                    <span class="tag-badge"><?= htmlspecialchars($tag) ?></span>
                <?php endforeach; else: ?>
                    <span class="tag-badge tag-badge-empty">Brak tagów</span>
                <?php endif; ?>
            </div>
            <div class="mt-3 d-flex align-items-center gap-2">
                <span class="rating-badge">
                    <i class="fa-solid fa-star text-warning"></i>
                    <?= number_format($game['avg_rating'], 1) ?> / 5
                </span>
                <span class="text-muted small">(<?= $game['review_count'] ?> recenzji)</span>
            </div>
        </div>
        <div class="col-md-8">
            <h1 class="gradient-text mb-2"><?= htmlspecialchars($game['title']) ?></h1>
            <div class="game-price-lg mb-2"><?= number_format($game['price'], 2) ?> PLN</div>
            <div class="mb-3"><?= nl2br(htmlspecialchars($game['description'])) ?></div>
            <div class="d-flex gap-3 mb-4 flex-wrap">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="button" 
                        class="btn <?= $in_cart ? 'btn-secondary in-cart' : 'btn-green' ?> add-to-cart" 
                        data-game-id="<?= $game_id ?>"
                        <?= $in_cart ? 'disabled' : '' ?>
                        <?= $in_cart ? 'data-in-cart="1"' : '' ?>>
                        <i class="fas <?= $in_cart ? 'fa-check' : 'fa-cart-plus' ?> me-2"></i>
                        <?= $in_cart ? 'W koszyku' : 'Dodaj do koszyka' ?>
                    </button>
                    <button type="button" class="btn <?= $is_favorite ? 'btn-neon' : 'btn-outline-neon' ?> favorite-toggle" data-game-id="<?= $game_id ?>">
                        <i class="fa<?= $is_favorite ? 's' : 'r' ?> fa-heart me-2"></i>
                        <?= $is_favorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych' ?>
                    </button>
                <?php endif; ?>
            </div>
            <a href="index.php" class="btn btn-outline-grey mt-2">
                ← Powrót do sklepu
            </a>
            <hr class="my-5">
            <div class="community-section">
                <h3 class="mb-4"><i class="fas fa-users me-2"></i>Społeczność</h3>
                <!-- Recenzje -->
                <div class="mb-5" id="reviews-block">
                    <h4>Recenzje (<span id="review-count"><?= $game['review_count'] ?></span>)</h4>
                    <div id="reviews-list">
                    <?php if ($reviews): ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="card review-card mb-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="ms-3">
                                            <strong><?= htmlspecialchars($review['username']) ?></strong>
                                            <div class="text-muted small"><?= $review['formatted_date'] ?></div>
                                        </div>
                                    </div>
                                    <div class="review-rating mb-2" style="font-size:1.3em;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $review['rating'] ? ' text-warning' : ' text-secondary' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="mb-0"><?= htmlspecialchars($review['content']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                            <p class="text-muted">Brak recenzji - bądź pierwszy!</p>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
                <!-- Formularz dodawania recenzji -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (!$user_review): ?>
                    <div class="card bg-dark border border-info mb-5">
                        <div class="card-body">
                            <h5 class="mb-3">Dodaj swoją recenzję</h5>
                            <form id="review-form" method="post" autocomplete="off">
                                <input type="hidden" name="game_id" value="<?= $game_id ?>">
                                <div class="mb-3">
                                    <label class="form-label">Ocena:</label>
                                    <div class="star-rating mb-2">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star star" data-value="<?= $i ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <input type="hidden" name="rating" id="star-rating-input" value="0" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Twoja recenzja:</label>
                                    <textarea name="review_text" class="form-control" rows="3" required 
                                              placeholder="Napisz szczegółową recenzję..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-orange">
                                    <span class="submit-text">Dodaj recenzję</span>
                                    <div class="spinner-border spinner-border-sm d-none" role="status"></div>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Kontener na toasty -->
<div id="toast-container"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lenis@1.3.4/dist/lenis.min.js"></script>
<script src="js/game.js"></script>
</body>
</html>
