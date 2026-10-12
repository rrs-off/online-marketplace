<?php
require_once 'includes/functions.php';
$orderId = (int)($_GET['order_id'] ?? 0);
$orders = loadData('orders.json');
$order = null;
foreach ($orders as $o) {
    if ($o['id'] === $orderId) { $order = $o; break; }
}
if (!$order) redirectTo('index.php');

include 'includes/header.php';
?>

<div class="form-box" style="text-align:center;">
  <h1 style="color:var(--success);">Order Confirmed!</h1>
  <p style="margin:15px 0;">Thank you for your purchase.</p>
  <p><strong>Order Number:</strong> #<?= $order['id'] ?></p>
  <p><strong>Date:</strong> <?= $order['date'] ?></p>
  <p><strong>Total:</strong> $<?= $order['total'] ?></p>
  <a href="profile.php" class="btn" style="margin-top:20px;">My Orders</a>
</div>

<?php include 'includes/footer.php'; ?>