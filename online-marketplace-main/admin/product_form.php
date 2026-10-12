<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$products = loadData('products.json');
$editId = (int)($_GET['id'] ?? 0);
$product = null;
foreach ($products as $p) {
    if ($p['id'] === $editId) { $product = $p; break; }
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    if ($name === '' || $category === '' || $price === '') {
        $errors[] = "Міндетті өрістерді толтырыңыз";
    }
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Баға оң сан болуы керек";
    }
    if (!is_numeric($stock) || $stock < 0) {
        $errors[] = "Саны дұрыс емес";
    }

    if (empty($errors)) {
        if ($product) {
            foreach ($products as &$p) {
                if ($p['id'] === $editId) {
                    $p = ['id' => $editId, 'name' => $name, 'category' => $category, 'price' => (float)$price, 'stock' => (int)$stock, 'description' => $description, 'image' => $image];
                }
            }
        } else {
            $products[] = ['id' => getNextId($products), 'name' => $name, 'category' => $category, 'price' => (float)$price, 'stock' => (int)$stock, 'description' => $description, 'image' => $image];
        }
        saveData('products.json', $products);
        redirectTo('products.php');
    }
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
    <h2><?= $product ? 'Өнімді өңдеу' : 'Жаңа өнім қосу' ?></h2>
    <p class="admin-page-desc"><?= $product ? 'Өнім мәліметтерін жаңартыңыз' : 'Жаңа өнім туралы ақпарат енгізіңыз' ?></p>

    <?php if (!empty($errors)): ?>
      <div class="error-box"><?php foreach ($errors as $e) echo "<div>$e</div>"; ?></div>
    <?php endif; ?>

    <form method="post" style="max-width:600px; margin-top:20px;">
      <div class="form-group">
        <label>Атауы *</label>
        <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? $_POST['name'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Категория *</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category'] ?? $_POST['category'] ?? '') ?>">
      </div>
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
        <div class="form-group">
          <label>Бағасы ($) *</label>
          <input type="number" step="0.01" min="0" name="price" value="<?= htmlspecialchars($product['price'] ?? $_POST['price'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label>Қоймадағы саны</label>
          <input type="number" min="0" name="stock" value="<?= htmlspecialchars($product['stock'] ?? $_POST['stock'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label>Сурет URL</label>
        <input type="text" name="image" placeholder="images/items/9.jpg" value="<?= htmlspecialchars($product['image'] ?? $_POST['image'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Сипаттама</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? $_POST['description'] ?? '') ?></textarea>
      </div>
      <div style="display:flex; gap:12px; margin-top:10px;">
        <button type="submit" class="btn">Сақтау</button>
        <a href="products.php" class="btn" style="background:#e2e8f0; color:#475569; box-shadow:none;">Бас тарту</a>
      </div>
    </form>
  </div>
</div>

<?php include '../includes/footer.php'; ?>