<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$products = loadData('products.json');
include '../includes/header.php';
?>

<div class="admin-wrap">
  <div class="admin-sidebar">
    <a href="index.php">Dashboard</a>
    <a href="users.php">Users</a>
    <a href="products.php">Products</a>
    <a href="orders.php">Orders</a>
    <a href="../logout.php">Logout</a>
  </div>
  <div class="admin-content">
    <h2>Products</h2>
    <a href="product_form.php" class="btn" style="margin:15px 0; display:inline-block;">Add Product</a>
    <table>
      <tr><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr>
      <?php foreach ($products as $p): ?>
        <tr>
          <td><?= htmlspecialchars($p['name']) ?></td>
          <td><?= $p['category'] ?></td>
          <td>$<?= $p['price'] ?></td>
          <td><?= $p['stock'] ?></td>
          <td>
            <a href="product_form.php?id=<?= $p['id'] ?>">Edit</a> |
            <a href="product_delete.php?id=<?= $p['id'] ?>">Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>