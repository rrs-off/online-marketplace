<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$users = loadData('users.json');
$products = loadData('products.json');
$orders = loadData('orders.json');

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
    <h2>Dashboard</h2>
    <div class="stats">
      <div class="stat-box"><h2><?= count($users) ?></h2><p>Users</p></div>
      <div class="stat-box"><h2><?= count($products) ?></h2><p>Products</p></div>
      <div class="stat-box"><h2><?= count($orders) ?></h2><p>Orders</p></div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>