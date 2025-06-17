<?php
require 'db.php';
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");

$search = $_GET['search'] ?? '';
$tag = $_GET['tag'] ?? '';

// Zapytanie SQL z możliwością wyszukiwania
$sql = "SELECT g.*, GROUP_CONCAT(t.name SEPARATOR ',') AS tags,
        (SELECT COUNT(*) FROM game_codes WHERE game_id = g.id AND assigned_to IS NULL) as available_keys
        FROM games g
        LEFT JOIN game_tags gt ON g.id = gt.game_id
        LEFT JOIN tags t ON gt.tag_id = t.id
        WHERE (g.title LIKE ? OR g.description LIKE ?)";

$params = ["%$search%", "%$search%"];

if ($tag !== '' && ctype_digit($tag)) {
    $sql .= " AND g.id IN (SELECT game_id FROM game_tags WHERE tag_id = ?)";
    $params[] = $tag;
}

$sql .= " GROUP BY g.id ORDER BY g.title ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$games = $stmt->fetchAll();

// Pobierz aktualny stan koszyka z bazy
$in_cart = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT game_id FROM user_carts WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $in_cart = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'game_id');
}
?>

<?php foreach ($games as $game): ?>
  <div class="col-12 col-md-6 col-lg-4 mb-4 d-flex align-items-stretch">
    <a href="game.php?id=<?= $game['id'] ?>" class="game-card-link w-100" style="text-decoration:none;color:inherit;">
      <div class="card game-card h-100 shadow w-100">
        <?php if ($game['image']): ?>
          <img src="uploads/<?=htmlspecialchars($game['image'])?>" class="card-img-top" alt="<?=htmlspecialchars($game['title'])?>">
        <?php else: ?>
          <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 220px; background: #1a1f2b;">
            <i class="fas fa-gamepad fa-3x text-muted"></i>
          </div>
        <?php endif; ?>
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?=htmlspecialchars($game['title'])?></h5>
          <p class="card-text"><?=htmlspecialchars(mb_strimwidth($game['description'],0,90,'...'))?></p>
          <div class="mb-3 d-flex flex-wrap">
            <?php if (!empty($game['tags'])): ?>
              <?php foreach (explode(',', $game['tags']) as $tagName): ?>
                <?php $tagName = trim($tagName); if ($tagName !== ''): ?>
                  <span class="tag-badge"><i class="fa fa-tag"></i><?=htmlspecialchars($tagName)?></span>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <span class="tag-badge tag-badge-empty"><i class="fa fa-tag"></i>Brak tagów</span>
            <?php endif; ?>
          </div>
          <div class="fw-bold mb-2 game-price"><?=number_format($game['price'],2)?> PLN</div>
          <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($game['available_keys'] > 0): ?>
              <?php $isInCart = in_array($game['id'], $in_cart); ?>
              <button class="btn add-to-cart mt-auto w-100 <?= $isInCart ? 'btn-secondary in-cart' : 'btn-success' ?>"
                data-game-id="<?= $game['id'] ?>"
                data-in-cart="<?= $isInCart ? '1' : '0' ?>"
                <?= $isInCart ? 'disabled' : '' ?>>
                <i class="fas <?= $isInCart ? 'fa-check' : 'fa-cart-plus' ?> me-2"></i>
                <?= $isInCart ? 'W koszyku' : 'Dodaj do koszyka' ?>
              </button>
            <?php else: ?>
              <button class="btn btn-secondary mt-auto w-100" disabled>
                <i class="fas fa-times-circle me-2"></i>Brak dostępnych kluczy!
              </button>
            <?php endif; ?>
          <?php else: ?>
            <button onclick="window.location.href='login.php'" class="btn login-btn mt-auto w-100" type="button">
                <i class="fas fa-sign-in-alt me-2"></i>Zaloguj się, by kupić
            </button>
          <?php endif; ?>
        </div>
      </div>
    </a>
  </div>
<?php endforeach; ?>

<?php if (empty($games)): ?>
  <div class="col-12">
    <div class="alert alert-info text-center py-4">
      <i class="fas fa-gamepad fa-3x mb-3"></i>
      <h4>Brak gier w katalogu</h4>
      <p class="mb-0">Spróbuj zmienić kryteria wyszukiwania</p>
    </div>
  </div>
<?php endif; ?>
