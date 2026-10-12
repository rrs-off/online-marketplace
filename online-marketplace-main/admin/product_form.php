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
        $errors[] = "Please fill in all required fields";
    }
    if (!is_numeric($price) || $price <= 0) {
        $errors[] = "Price must be a positive number";
    }
    if (!is_numeric($stock) || $stock < 0) {
        $errors[] = "Invalid quantity";
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

include '../includes/header.php';
?>

<div class="container">
  <h2><?= $product ? 'Edit Product' : 'Add Product' ?></h2>

  <?php if (!empty($errors)): ?>
    <div class="error-box"><?php foreach ($errors as $e) echo "<div>$e</div>"; ?></div>
  <?php endif; ?>

  <form method="post" class="form-box">
    <div class="form-group">
      <label>Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? $_POST['name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Category</label>
      <input type="text" name="category" value="<?= htmlspecialchars($product['category'] ?? $_POST['category'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Price</label>
      <input type="text" name="price" value="<?= htmlspecialchars($product['price'] ?? $_POST['price'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Stock</label>
      <input type="text" name="stock" value="<?= htmlspecialchars($product['stock'] ?? $_POST['stock'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Image URL</label>
      <input type="text" name="image" placeholder="images/items/9.jpg" value="<?= htmlspecialchars($product['image'] ?? $_POST['image'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Description</label>
      <textarea name="description" rows="4"><?= htmlspecialchars($product['description'] ?? $_POST['description'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn">Save</button>
  </form>
</div>

<?php include '../includes/footer.php'; ?>