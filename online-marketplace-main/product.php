<?php
require_once 'includes/functions.php';
$products = loadData('products.json');
$id = (int)($_GET['id'] ?? 0);
$product = null;
foreach ($products as $p) {
    if ($p['id'] === $id) { $product = $p; break; }
}

if (!$product) {
    redirectTo('catalog.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $qty = max(1, (int)$_POST['quantity']);
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $qty;
    } else {
        $_SESSION['cart'][$id] = $qty;
    }
    redirectTo('cart.php');
}

include 'includes/header.php';
?>

<div class="container">
  <div class="card" style="max-width:900px; margin:0 auto;">
    <?php if (!empty($product['image'])): ?>
      <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%; height:350px; object-fit:contain; background:#f3f4f6;">
    <?php else: ?>
      <div class="no-image" style="height:350px;">No Image</div>
    <?php endif; ?>
    <div class="card-body">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p><?= htmlspecialchars($product['description']) ?></p>
      <p><strong>Category:</strong> <?= $product['category'] ?></p>
      <p><strong>In Stock:</strong> <?= $product['stock'] ?> units</p>
      <div class="price">Price: $<?= $product['price'] ?></div>

      <form method="post" style="margin-top:15px; display:flex; gap:10px; align-items:center;">
        <label>Quantity:</label>
        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" style="width:70px; padding:8px;">
        <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
      </form>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>