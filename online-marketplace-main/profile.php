<?php
require_once 'includes/functions.php';
if (!isLoggedIn()) redirectTo('login.php');

$user = $_SESSION['user'];
$orders = loadData('orders.json');
$myOrders = array_filter($orders, fn($o) => ($o['user_id'] ?? null) === $user['id']);

include 'includes/header.php';
?>

<div class="container">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <h2>My Profile</h2>
    <a href="logout.php" class="btn btn-danger" style="padding: 8px 16px; font-size: 13px;">Logout</a>
  </div>

  <div class="card" style="padding: 25px; max-width: 550px;">
    <div style="display: flex; align-items: center; gap: 25px; flex-wrap: wrap;">
      
      <div style="width: 85px; height: 85px; border-radius: 50%; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 38px; border: 3px solid var(--primary); flex-shrink: 0;">
        👤
      </div>

      <div style="flex: 1; min-width: 200px;">
        <h3 style="font-size: 20px; margin-bottom: 6px;"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 4px;">✉️ <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 12px;">📞 <strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? '-') ?></p>
        
        <button type="button" class="btn" style="padding: 7px 16px; font-size: 13px;" onclick="alert('Profile editing will be available soon!')">Edit Profile</button>
      </div>

    </div>
  </div>
  <div style="margin-top: 40px;">
    <h3>My Orders</h3>

    <?php if (empty($myOrders)): ?>
      <div class="card" style="padding: 30px; text-align: center; margin-top: 15px; color: #64748b; max-width: 550px;">
        <div style="font-size: 40px; margin-bottom: 10px;">📦</div>
        <p>You have no orders yet.</p>
        <a href="catalog.php" class="btn" style="margin-top: 15px; font-size: 13px;">Browse Catalog</a>
      </div>
    <?php else: ?>
      <table>
        <tr>
          <th>Order ID</th>
          <th>Date</th>
          <th>Total</th>
          <th>Status</th>
        </tr>
        <?php foreach ($myOrders as $o): ?>
          <tr>
            <td><strong>#<?= $o['id'] ?></strong></td>
            <td><?= htmlspecialchars($o['date'] ?? '-') ?></td>
            <td><strong>$<?= htmlspecialchars($o['total'] ?? '0') ?></strong></td>
            <td>
              <span style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                <?= htmlspecialchars($o['status'] ?? 'Processing') ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

</div>

<?php include 'includes/footer.php'; ?>