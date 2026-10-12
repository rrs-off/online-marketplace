<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$orders = loadData('orders.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    foreach ($orders as &$o) {
        if ($o['id'] === $id) $o['status'] = $status;
    }
    saveData('orders.json', $orders);
    redirectTo('orders.php');
}

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
    <h2>Orders</h2>
    <table>
      <tr><th>No.</th><th>User ID</th><th>Total</th><th>Date</th><th>Status</th></tr>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td>#<?= $o['id'] ?></td>
          <td><?= $o['user_id'] ?></td>
          <td>$<?= $o['total'] ?></td>
          <td><?= $o['date'] ?></td>
          <td>
            <form method="post" style="display:flex; gap:8px;">
              <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
              <select name="status">
                <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
                <option value="confirmed" <?= $o['status']=='confirmed'?'selected':'' ?>>Confirmed</option>
                <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
              </select>
              <button type="submit" class="btn">OK</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>

<?php include '../includes/footer.php'; ?>