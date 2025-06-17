<?php
require 'db.php';
session_start();
$tags = $pdo->query("SELECT * FROM tags")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>GameKeys - Sklep z grami</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Exo+2:wght@300;600&display=swap" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
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
            <form class="gaming-search" id="search-form" autocomplete="off">
                <input type="text" name="search" class="form-control" placeholder="Wyszukaj grę..." id="search" />
                <select class="form-select" name="tag" id="tag">
                    <option value="">Wszystkie kategorie</option>
                    <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag['id'] ?>"><?= htmlspecialchars($tag['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <div class="d-flex align-items-center">
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

<div class="container py-4">
    <div class="row" id="games-list"></div>
</div>

<button id="backToTop" title="Do góry" aria-label="Do góry">
    <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="12" fill="currentColor" opacity="0.15" />
        <path d="M12 17V7M12 7L7 12M12 7l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</button>

<div id="cart-popup" class="cart-popup" style="display:none;">
    <div class="cart-popup-content">
        <div class="d-flex align-items-center mb-3">
            <img id="cart-popup-img" src="" width="64" height="64" class="game-cover" />
            <div>
                <div id="cart-popup-title" class="fw-bold"></div>
                <div id="cart-popup-msg" class="small text-success"></div>
            </div>
        </div>
        <div class="text-end">
            <a href="cart.php" class="btn btn-warning">Idź do koszyka</a>
            <button id="cart-popup-close" class="btn btn-secondary ms-2">Kupuj dalej</button>
        </div>
    </div>
</div>

<!-- Kontener na toasty -->
<div id="toast-container"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/lenis@1.3.4/dist/lenis.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
