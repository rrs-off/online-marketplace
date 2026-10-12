<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$users = loadData('users.json');
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
    <h2>Users</h2>
    <table>
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['role'] ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>