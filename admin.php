<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// Obsługa kodów gry
if (isset($_GET['show_codes'])) {
    $game_id = (int)$_GET['show_codes'];
    try {
        $stmt = $pdo->prepare("SELECT title FROM games WHERE id = ?");
        $stmt->execute([$game_id]);
        $game_title = $stmt->fetchColumn();

        if (isset($_GET['delete_code'])) {
            $delete_code_id = (int)$_GET['delete_code'];
            $stmt = $pdo->prepare("DELETE FROM game_codes WHERE id = ? AND assigned_to IS NULL");
            $stmt->execute([$delete_code_id]);
            header("Location: admin.php?show_codes=$game_id");
            exit;
        }

        if (isset($_POST['add_code'])) {
            $code = trim($_POST['code']);
            if (empty($code)) {
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                $code = implode('-', str_split(strtoupper(substr(str_shuffle($chars), 0, 12)), 3));
            }
            if (!preg_match('/^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}$/', $code)) {
                throw new Exception("Nieprawidłowy format kodu!");
            }
            $stmt = $pdo->prepare("INSERT INTO game_codes (game_id, code) VALUES (?, ?)");
            $stmt->execute([$game_id, $code]);
            header("Location: admin.php?show_codes=$game_id");
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT gc.*, u.username 
            FROM game_codes gc
            LEFT JOIN users u ON gc.assigned_to = u.id
            WHERE gc.game_id = ?
        ");
        $stmt->execute([$game_id]);
        $codes = $stmt->fetchAll();

    } catch (PDOException $e) {
        die("Błąd bazy danych: " . $e->getMessage());
    } catch (Exception $e) {
        die($e->getMessage());
    }
    ?>
    <!DOCTYPE html>
    <html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Kody gry</title>
        <link rel="icon" type="image/jpeg" href="img/favicon.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="css/admin.css" rel="stylesheet">
    </head>
    <body>
    <div class="admin-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kody dla: <?=htmlspecialchars($game_title)?></h2>
            <a href="admin.php" class="btn btn-secondary">← Powrót</a>
        </div>
        <form method="post" class="mb-4">
            <div class="d-flex align-items-center gap-2 w-100"> 
                <input type="text" name="code" class="form-control form-control-sm flex-fill" placeholder="Wygeneruj lub wprowadź kod (format: XXX-XXX-XXX-XXX)" pattern="[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}">
                <button type="submit" name="add_code" class="btn btn-success btn-sm">Dodaj kod</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Kod</th>
                        <th>Status</th>
                        <th>Przypisany do</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($codes as $code): ?>
                    <tr>
                        <td><code><?=htmlspecialchars($code['code'])?></code></td>
                        <td>
                            <?php if($code['assigned_to']): ?>
                                <span class="badge bg-danger">Użyty</span>
                            <?php else: ?>
                                <span class="badge bg-success">Dostępny</span>
                            <?php endif; ?>
                        </td>
                        <td><?=htmlspecialchars($code['username'] ?? '—')?></td>
                        <td>
                            <?php if(!$code['assigned_to']): ?>
                                <a href="admin.php?show_codes=<?=$game_id?>&delete_code=<?=$code['id']?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Na pewno usunąć ten kod?');">
                                    Usuń
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}

// Obsługa dodawania/edycji/tagów gier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_game'])) {
        $title = trim($_POST['title']);
        $desc = trim($_POST['description']);
        $price = (float)$_POST['price'];
        $img = '';

        if (!empty($_FILES['image']['tmp_name'])) {
            $img = uniqid() . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$img");
        }

        $stmt = $pdo->prepare("INSERT INTO games (title, description, image, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $img, $price]);
        $game_id = $pdo->lastInsertId();

        if (!empty($_POST['tags'])) {
            $tags = array_map('trim', explode(',', $_POST['tags']));
            foreach ($tags as $tag) {
                if ($tag === '') continue;
                $stmt = $pdo->prepare("INSERT IGNORE INTO tags (name) VALUES (?)");
                $stmt->execute([$tag]);
                $tag_id = $pdo->lastInsertId() ?: $pdo->query("SELECT id FROM tags WHERE name = " . $pdo->quote($tag))->fetchColumn();
                $pdo->prepare("INSERT IGNORE INTO game_tags (game_id, tag_id) VALUES (?, ?)")->execute([$game_id, $tag_id]);
            }
        }
    }

    if (isset($_POST['edit_price_game_id'])) {
        $game_id = (int)$_POST['edit_price_game_id'];
        $price = (float)$_POST['edit_price'];
        $pdo->prepare("UPDATE games SET price = ? WHERE id = ?")->execute([$price, $game_id]);
    }

    if (isset($_POST['edit_tags_game_id'])) {
        $game_id = (int)$_POST['edit_tags_game_id'];
        $tags = array_filter(array_map('trim', explode(',', $_POST['edit_tags'])));
        $pdo->prepare("DELETE FROM game_tags WHERE game_id = ?")->execute([$game_id]);
        foreach ($tags as $tag) {
            if ($tag === '') continue;
            $stmt = $pdo->prepare("INSERT IGNORE INTO tags (name) VALUES (?)");
            $stmt->execute([$tag]);
            $tag_id = $pdo->lastInsertId() ?: $pdo->query("SELECT id FROM tags WHERE name = " . $pdo->quote($tag))->fetchColumn();
            $pdo->prepare("INSERT IGNORE INTO game_tags (game_id, tag_id) VALUES (?, ?)")->execute([$game_id, $tag_id]);
        }
    }
}

// Pobierz gry z opcjonalnym filtrem wyszukiwania
$where = '';
$params = [];
if (!empty($_GET['search'])) {
    $where = "WHERE g.title LIKE :search";
    $params[':search'] = '%' . $_GET['search'] . '%';
}
$sql = "
    SELECT g.*, GROUP_CONCAT(t.name SEPARATOR ', ') AS tags 
    FROM games g
    LEFT JOIN game_tags gt ON g.id = gt.game_id
    LEFT JOIN tags t ON gt.tag_id = t.id
    $where
    GROUP BY g.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

// AJAX: tylko lista gier
if ($isAjax) { ?>
    <div class="row g-4">
        <?php foreach ($games as $game): ?>
        <div class="col-md-6 col-xl-4">
            <div class="game-card">
                <div class="d-flex align-items-center mb-2">
                    <small class="text-muted me-2">ID: <?= $game['id'] ?></small>
                </div>
                <?php if ($game['image']): ?>
                    <img src="uploads/<?= htmlspecialchars($game['image']) ?>" class="game-image">
                <?php else: ?>
                    <div class="game-image bg-light d-flex align-items-center justify-content-center">
                        <span class="text-muted">Brak obrazu</span>
                    </div>
                <?php endif; ?>
                <h4 class="h6 mb-2"><?= htmlspecialchars($game['title']) ?></h4>
                <p class="small text-secondary mb-2">
                    <?= htmlspecialchars(mb_strimwidth($game['description'], 0, 90, '...')) ?>
                </p>
                <div class="d-flex align-items-center mb-3">
                    <span class="game-price"><?= number_format($game['price'], 2) ?> PLN</span>
                </div>
                <!-- AJAX edycja ceny -->
                <form class="d-flex gap-2 mb-2 ajax-edit-price" data-game-id="<?= $game['id'] ?>">
                    <input type="number" name="price" value="<?= $game['price'] ?>" 
                        class="form-control form-control-sm" step="0.01">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-floppy-o"></i>
                    </button>
                </form>
                <!-- AJAX edycja tagów -->
                <form class="d-flex gap-2 mb-3 ajax-edit-tags" data-game-id="<?= $game['id'] ?>">
                    <input type="text" name="tags" value="<?= htmlspecialchars($game['tags']) ?>" 
                        class="form-control form-control-sm">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-floppy-o"></i>
                    </button>
                </form>
                <div class="d-flex justify-content-end align-items-center">
                    <a href="admin.php?show_codes=<?= $game['id'] ?>" class="btn btn-sm btn-kody">Kody</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($games)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-gamepad fa-3x mb-3"></i>
                    <h4>Brak gier w katalogu</h4>
                    <p class="mb-0">Dostosuj kryteria wyszukiwania</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php exit; } ?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Admina</title>
    <link rel="icon" type="image/jpeg" href="img/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/admin.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/admin.js"></script>
</head>
<body>
<div class="admin-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="panel-header">Panel Administracyjny</h2>
        <div>
            <a href="index.php" class="btn btn-back">Sklep</a>
            <a href="logout.php" class="btn btn-danger ms-2">Wyloguj</a>
        </div>
    </div>
    <!-- Formularz dodawania gry -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h5 mb-4">Dodaj nową grę</h2>
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Tytuł" required>
                </div>
                <div class="col-md-6">
                    <input type="number" name="price" step="0.01" class="form-control" placeholder="Cena PLN" required>
                </div>
                <div class="col-12">
                    <textarea name="description" class="form-control" placeholder="Opis" rows="2" required></textarea>
                </div>
                <div class="col-md-6">
                    <label class="input-file-label" for="image">Wybierz plik</label>
                    <input type="file" name="image" id="image" class="input-file" accept="image/*">
                    <span id="file-name" style="margin-left: 1em; color: #b0b8c9;">Nie wybrano pliku</span>
                </div>
                <div class="col-md-6">
                    <input type="text" name="tags" class="form-control" placeholder="Tagi (oddziel przecinkami)">
                </div>
                <div class="col-12">
                    <button type="submit" name="add_game" class="btn btn-success">Dodaj grę</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista gier -->
    <h2 class="lista-gier-header">Lista gier</h2>
    <!-- Wyszukiwarka gier -->
    <form id="admin-search-form" class="mb-4" style="max-width: 400px;">
        <div class="input-group">
            <input type="text" name="search" id="admin-search" class="form-control" 
                placeholder="Szukaj gry po tytule..." autocomplete="off" 
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        </div>
    </form>
    <div id="admin-games-list">
        <div class="row g-4">
            <?php foreach ($games as $game): ?>
            <div class="col-md-6 col-xl-4">
                <div class="game-card">
                    <div class="d-flex align-items-center mb-2">
                        <small class="text-muted me-2">ID: <?= $game['id'] ?></small>
                    </div>
                    <?php if ($game['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($game['image']) ?>" class="game-image">
                    <?php else: ?>
                        <div class="game-image bg-light d-flex align-items-center justify-content-center">
                            <span class="text-muted">Brak obrazu</span>
                        </div>
                    <?php endif; ?>
                    <h4 class="h6 mb-2"><?= htmlspecialchars($game['title']) ?></h4>
                    <p class="small text-secondary mb-2">
                        <?= htmlspecialchars(mb_strimwidth($game['description'], 0, 90, '...')) ?>
                    </p>
                    <div class="d-flex align-items-center mb-3">
                        <span class="game-price"><?= number_format($game['price'], 2) ?> PLN</span>
                    </div>
                    <!-- AJAX edycja ceny -->
                    <form class="d-flex gap-2 mb-2 ajax-edit-price" data-game-id="<?= $game['id'] ?>">
                        <input type="number" name="price" value="<?= $game['price'] ?>" 
                            class="form-control form-control-sm" step="0.01">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-floppy-o"></i>
                        </button>
                    </form>
                    <!-- AJAX edycja tagów -->
                    <form class="d-flex gap-2 mb-3 ajax-edit-tags" data-game-id="<?= $game['id'] ?>">
                        <input type="text" name="tags" value="<?= htmlspecialchars($game['tags']) ?>" 
                            class="form-control form-control-sm">
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-floppy-o"></i>
                        </button>
                    </form>
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="admin.php?show_codes=<?= $game['id'] ?>" class="btn btn-sm btn-kody">Kody</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<script>
document.getElementById('image').addEventListener('change', function(e) {
  document.getElementById('file-name').textContent = e.target.files[0]?.name || 'Nie wybrano pliku';
});
</script>
</body>
</html>
