<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$products = loadData('products.json');

$search = trim($_GET['search'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');

$categories = array_unique(array_column($products, 'category'));
sort($categories);

$filteredProducts = $products;
if ($search !== '') {
    $filteredProducts = array_filter($filteredProducts, fn($p) =>
        stripos($p['name'], $search) !== false || stripos($p['description'], $search) !== false
    );
}
if ($categoryFilter !== '') {
    $filteredProducts = array_filter($filteredProducts, fn($p) => $p['category'] === $categoryFilter);
}

function setActive($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'active' : '';
}

include '../includes/header.php';
?>

<div class="admin-wrap">
  <div class="admin-sidebar">
    <div class="sidebar-section">Басқару</div>
    <a href="index.php" class="<?= setActive('index.php') ?>"><span class="sidebar-icon">&#9632;</span> Басты бет</a>
    <a href="users.php" class="<?= setActive('users.php') ?>"><span class="sidebar-icon">&#9632;</span> Пайдаланушылар</a>
    <a href="products.php" class="<?= setActive('products.php') ?>"><span class="sidebar-icon">&#9632;</span> Өнімдер</a>
    <a href="orders.php" class="<?= setActive('orders.php') ?>"><span class="sidebar-icon">&#9632;</span> Тапсырыстар</a>
    <div class="sidebar-section">Жүйе</div>
    <a href="../logout.php"><span class="sidebar-icon">&#9632;</span> Шығу</a>
  </div>
  <div class="admin-content">
    <h2>Өнімдер</h2>
    <p class="admin-page-desc">Барлық өнімдерді басқарыңыз — қосу, өңдеу, жою</p>

    <div class="admin-toolbar">
      <form method="get" class="admin-toolbar" style="margin:0; flex:1; gap:12px;">
        <input type="text" name="search" class="search-input" placeholder="Өнім атауы бойынша іздеу..." value="<?= htmlspecialchars($search) ?>">
        <select name="category">
          <option value="">Барлық категориялар</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $categoryFilter === $cat ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-sm">Сүзгілеу</button>
        <?php if ($search !== '' || $categoryFilter !== ''): ?>
          <a href="products.php" class="btn btn-sm" style="background:#e2e8f0; color:#475569; box-shadow:none;">Тазалау</a>
        <?php endif; ?>
      </form>
      <a href="product_form.php" class="btn">+ Жаңа өнім</a>
    </div>

    <?php if (!empty($filteredProducts)): ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Атауы</th>
          <th>Категория</th>
          <th>Бағасы</th>
          <th>Қойма</th>
          <th>Әрекеттер</th>
        </tr>
        <?php foreach ($filteredProducts as $p): ?>
          <tr>
            <td>#<?= $p['id'] ?></td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <?php if (!empty($p['image'])): ?>
                  <img src="<?= isset($basePath) ? $basePath : '' ?><?= htmlspecialchars($p['image']) ?>" alt="" style="width:40px; height:40px; border-radius:8px; object-fit:cover;">
                <?php endif; ?>
                <span><?= htmlspecialchars($p['name']) ?></span>
              </div>
            </td>
            <td><?= htmlspecialchars($p['category']) ?></td>
            <td>$<?= number_format($p['price'], 2, '.', ',') ?></td>
            <td>
              <?php if ($p['stock'] == 0): ?>
                <span class="stock-out">Жоқ</span>
              <?php elseif ($p['stock'] <= 5): ?>
                <span class="stock-low"><?= $p['stock'] ?> (тапшы)</span>
              <?php else: ?>
                <span class="stock-ok"><?= $p['stock'] ?></span>
              <?php endif; ?>
            </td>
            <td>
              <div class="admin-actions">
                <a href="product_form.php?id=<?= $p['id'] ?>" class="action-edit">Өңдеу</a>
                <a href="product_delete.php?id=<?= $p['id'] ?>" class="action-delete" onclick="return confirm('Бұл өнімді жойғыңыз келе ме?')">Жою</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">&#128230;</div>
        <p>Өнімдер табылмады</p>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php include '../includes/footer.php'; ?>