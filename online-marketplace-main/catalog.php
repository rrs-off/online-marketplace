<?php
require_once 'includes/functions.php';
$products = loadData('products.json');

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? 'all';

$filtered = array_filter($products, function($p) use ($search, $category) {
    $matchSearch = $search === '' || stripos($p['name'], $search) !== false;
    $matchCat = $category === 'all' || $p['category'] === $category;
    return $matchSearch && $matchCat;
});

$categories = array_unique(array_column($products, 'category'));

include 'includes/header.php';
?>

<div class="container">
  <h2>Product Catalog</h2>

  <form method="get" style="display:flex; gap:10px; margin:20px 0; flex-wrap:wrap;">
    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="flex:1; padding:10px; border-radius:6px; border:1px solid #ccc;">
    <select name="category" style="padding:10px; border-radius:6px;">
      <option value="all">All Categories</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn">Search</button>
  </form>

  <div class="grid">
    <?php if (empty($filtered)): ?>
      <p>No products found.</p>
    <?php endif; ?>
    <?php foreach ($filtered as $p): ?>
      <div class="card">
        <?php if (!empty($p['image'])): ?>
          <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <?php else: ?>
          <div class="no-image">No Image</div>
        <?php endif; ?>
        <div class="card-body">
          <h3><?= htmlspecialchars($p['name']) ?></h3>
          <p><?= $p['category'] ?></p>
          <div class="price">$<?= $p['price'] ?></div>
          <a href="product.php?id=<?= $p['id'] ?>" class="btn">View Details</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>