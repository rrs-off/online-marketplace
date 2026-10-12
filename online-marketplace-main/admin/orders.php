<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$orders = loadData('orders.json');
$users = loadData('users.json');

$userNames = [];
foreach ($users as $u) {
    $userNames[$u['id']] = $u['first_name'] . ' ' . $u['last_name'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed = ['pending', 'confirmed', 'cancelled'];
    if (in_array($status, $allowed)) {
        foreach ($orders as &$o) {
            if ($o['id'] === $id) $o['status'] = $status;
        }
        saveData('orders.json', $orders);
    }
    redirectTo('orders.php');
}

$statusFilter = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$filteredOrders = $orders;
if ($statusFilter !== '') {
    $filteredOrders = array_filter($filteredOrders, fn($o) => $o['status'] === $statusFilter);
}
if ($search !== '') {
    $filteredOrders = array_filter($filteredOrders, function($o) use ($search, $userNames) {
        $name = $userNames[$o['user_id']] ?? '';
        return stripos($name, $search) !== false || stripos((string)$o['id'], $search) !== false;
    });
}
$filteredOrders = array_reverse($filteredOrders);

$statusLabels = [
    'pending' => 'Күтуде',
    'confirmed' => 'Расталды',
    'cancelled' => 'Болдырмаған'
];

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
    <h2>Тапсырыстар</h2>
    <p class="admin-page-desc">Тапсырыстарды басқарыңыз — статусты өзгерту, мәліметтерді көру</p>

    <div class="admin-toolbar">
      <form method="get" class="admin-toolbar" style="margin:0; flex:1; gap:12px;">
        <input type="text" name="search" class="search-input" placeholder="Тапсырыс № немесе пайдаланушы аты..." value="<?= htmlspecialchars($search) ?>">
        <select name="status">
          <option value="">Барлық статустар</option>
          <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Күтуде</option>
          <option value="confirmed" <?= $statusFilter === 'confirmed' ? 'selected' : '' ?>>Расталды</option>
          <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Болдырмаған</option>
        </select>
        <button type="submit" class="btn btn-sm">Сүзгілеу</button>
        <?php if ($search !== '' || $statusFilter !== ''): ?>
          <a href="orders.php" class="btn btn-sm" style="background:#e2e8f0; color:#475569; box-shadow:none;">Тазалау</a>
        <?php endif; ?>
      </form>
    </div>

    <?php if (!empty($filteredOrders)): ?>
      <table>
        <tr>
          <th>№</th>
          <th>Тапсырыс беруші</th>
          <th>Өнімдер</th>
          <th>Жалпы</th>
          <th>Күні</th>
          <th>Статус</th>
          <th>Мәлімет</th>
        </tr>
        <?php foreach ($filteredOrders as $o): ?>
          <tr>
            <td>#<?= $o['id'] ?></td>
            <td><?= htmlspecialchars($userNames[$o['user_id']] ?? 'Қолданушы #' . $o['user_id']) ?></td>
            <td><?= count($o['items'] ?? []) ?> дана</td>
            <td><strong>$<?= number_format($o['total'], 2, '.', ',') ?></strong></td>
            <td style="font-size:13px; color:var(--text-muted);"><?= $o['date'] ?></td>
            <td>
              <form method="post" style="display:flex; gap:8px; align-items:center;">
                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                <select name="status" onchange="this.form.submit()" style="padding:5px 10px; border-radius:6px; border:1.5px solid var(--border); font-size:13px; cursor:pointer;">
                  <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Күтуде</option>
                  <option value="confirmed" <?= $o['status']=='confirmed'?'selected':'' ?>>Расталды</option>
                  <option value="cancelled" <?= $o['status']=='cancelled'?'selected':'' ?>>Болдырмаған</option>
                </select>
              </form>
            </td>
            <td>
              <button class="action-edit" style="border:none; cursor:pointer; font-size:13px; font-weight:500; padding:4px 10px; border-radius:6px;" onclick="showOrderDetails(<?= $o['id'] ?>)">Көру</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">&#128722;</div>
        <p>Тапсырыстар табылмады</p>
      </div>
    <?php endif; ?>

  </div>
</div>

<div class="order-detail-modal" id="orderModal">
  <div class="order-detail-content">
    <h3>
      <span>Тапсырыс мәліметтері</span>
      <button class="modal-close" onclick="closeOrderModal()">&times;</button>
    </h3>
    <div id="orderDetailBody"></div>
  </div>
</div>

<script>
const ordersData = <?= json_encode(array_values($filteredOrders)) ?>;
const userNames = <?= json_encode($userNames) ?>;

function showOrderDetails(orderId) {
  const order = ordersData.find(o => o.id === orderId);
  if (!order) return;

  const statusLabels = {
    'pending': 'Күтуде',
    'confirmed': 'Расталды',
    'cancelled': 'Болдырмаған'
  };

  let html = '';
  html += '<div class="detail-row"><span class="detail-label">Тапсырыс №</span><span class="detail-value">#' + order.id + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Тапсырыс беруші</span><span class="detail-value">' + (userNames[order.user_id] || 'Қолданушы #' + order.user_id) + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Күні</span><span class="detail-value">' + order.date + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Телефон</span><span class="detail-value">' + (order.phone || '—') + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Жеткізу адресі</span><span class="detail-value">' + (order.address || '—') + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Төлем түрі</span><span class="detail-value">' + (order.payment === 'card' ? 'Банк картасы' : 'Қолма-қол') + '</span></div>';
  html += '<div class="detail-row"><span class="detail-label">Статус</span><span class="detail-value"><span class="badge badge-' + order.status + '"><span class="badge-dot"></span>' + statusLabels[order.status] + '</span></span></div>';

  if (order.items && order.items.length > 0) {
    html += '<h4 style="margin:20px 0 10px; font-size:15px; font-weight:600;">Өнімдер</h4>';
    html += '<div class="detail-items">';
    order.items.forEach(function(item) {
      html += '<div class="detail-item"><span>' + item.name + ' × ' + item.qty + '</span><span>$' + (item.price * item.qty).toFixed(2) + '</span></div>';
    });
    html += '</div>';
  }

  html += '<div class="detail-row" style="border-top:2px solid var(--border); margin-top:10px; padding-top:14px;"><span class="detail-label" style="font-weight:600; font-size:16px;">Жалпы сомасы</span><span class="detail-value" style="font-size:18px; color:var(--primary);">$' + parseFloat(order.total).toFixed(2) + '</span></div>';

  document.getElementById('orderDetailBody').innerHTML = html;
  document.getElementById('orderModal').classList.add('show');
}

function closeOrderModal() {
  document.getElementById('orderModal').classList.remove('show');
}

document.getElementById('orderModal').addEventListener('click', function(e) {
  if (e.target === this) closeOrderModal();
});
</script>

<?php include '../includes/footer.php'; ?>