<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$users = loadData('users.json');
$products = loadData('products.json');
$orders = loadData('orders.json');

$totalRevenue = 0;
$pendingCount = 0;
$confirmedCount = 0;
$cancelledCount = 0;
foreach ($orders as $o) {
    if ($o['status'] === 'confirmed' || $o['status'] === 'pending') {
        $totalRevenue += $o['total'];
    }
    if ($o['status'] === 'pending') $pendingCount++;
    if ($o['status'] === 'confirmed') $confirmedCount++;
    if ($o['status'] === 'cancelled') $cancelledCount++;
}

$blockedUsers = 0;
foreach ($users as $u) {
    if (isset($u['status']) && $u['status'] === 'blocked') $blockedUsers++;
}

$lowStock = 0;
foreach ($products as $p) {
    if ($p['stock'] <= 5) $lowStock++;
}

$salesByMonth = [];
foreach ($orders as $o) {
    if ($o['status'] === 'cancelled') continue;
    $month = substr($o['date'], 3, 7); // MM.YYYY
    if (!isset($salesByMonth[$month])) $salesByMonth[$month] = 0;
    $salesByMonth[$month] += $o['total'];
}
krsort($salesByMonth);
$chartData = array_slice($salesByMonth, -6, 6, true);
$chartData = array_reverse($chartData);
$maxSale = !empty($chartData) ? max($chartData) : 1;
if ($maxSale == 0) $maxSale = 1;

$productSales = [];
foreach ($orders as $o) {
    if ($o['status'] === 'cancelled') continue;
    foreach ($o['items'] as $item) {
        $pid = $item['product_id'];
        if (!isset($productSales[$pid])) {
            $productSales[$pid] = ['name' => $item['name'], 'qty' => 0, 'revenue' => 0];
        }
        $productSales[$pid]['qty'] += $item['qty'];
        $productSales[$pid]['revenue'] += $item['price'] * $item['qty'];
    }
}
uasort($productSales, fn($a, $b) => $b['qty'] <=> $a['qty']);
$topProducts = array_slice($productSales, 0, 5, true);

$recentOrders = array_slice(array_reverse($orders), 0, 5);

$monthLabels = [
    '01' => 'Қаң', '02' => 'Ақп', '03' => 'Нау', '04' => 'Сәу',
    '05' => 'Мам', '06' => 'Мау', '07' => 'Шіл', '08' => 'Там',
    '09' => 'Қыр', '10' => 'Қаз', '11' => 'Қар', '12' => 'Жел'
];

function setActive($page) {
    $current = basename($_SERVER['PHP_SELF']);
    return $current === $page ? 'active' : '';
}
?>
<?php include '../includes/header.php'; ?>

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
    <h2>Басқару панелі</h2>
    <p class="admin-page-desc">Жүйенің жалпы көрінісі және статистикасы</p>

    <div class="stats">
      <div class="stat-box">
        <div class="stat-icon blue">&#128176;</div>
        <h2>$<?= number_format($totalRevenue, 0, '.', ',') ?></h2>
        <p>Жалпы кіріс</p>
        <div class="stat-trend up">+<?= $confirmedCount ?> расталған</div>
      </div>
      <div class="stat-box">
        <div class="stat-icon green">&#128100;</div>
        <h2><?= count($users) ?></h2>
        <p>Пайдаланушылар</p>
        <div class="stat-trend <?= $blockedUsers > 0 ? 'down' : 'up' ?>"><?= $blockedUsers > 0 ? $blockedUsers . ' бұғатталған' : 'Бәрі белсенді' ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-icon orange">&#128230;</div>
        <h2><?= count($products) ?></h2>
        <p>Өнімдер</p>
        <div class="stat-trend <?= $lowStock > 0 ? 'down' : 'up' ?>"><?= $lowStock > 0 ? $lowStock . ' тапшы' : 'Бәрі жеткілікті' ?></div>
      </div>
      <div class="stat-box">
        <div class="stat-icon red">&#128722;</div>
        <h2><?= count($orders) ?></h2>
        <p>Тапсырыстар</p>
        <div class="stat-trend"><?= $pendingCount ?> күтуде</div>
      </div>
    </div>

    <div class="admin-chart-wrap">
      <h3>Сату кірісі (айлық)</h3>
      <?php if (!empty($chartData)): ?>
        <div class="chart-container">
          <div class="chart-bars">
            <?php foreach ($chartData as $month => $amount): ?>
              <?php
                $monthNum = substr($month, 0, 2);
                $label = $monthLabels[$monthNum] ?? $monthNum;
                $heightPercent = ($amount / $maxSale) * 100;
              ?>
              <div class="chart-bar-group">
                <div class="chart-bar" style="height: <?= max($heightPercent, 2) ?>%">
                  <span class="chart-bar-value">$<?= number_format($amount, 0, '.', ',') ?></span>
                </div>
                <span class="chart-bar-label"><?= $label ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="empty-state">
          <div class="empty-icon">&#128202;</div>
          <p>Әлі сату деректері жоқ</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="admin-row">
      <div class="recent-orders">
        <h3>Соңғы тапсырыстар</h3>
        <?php if (!empty($recentOrders)): ?>
          <?php foreach ($recentOrders as $o): ?>
            <div class="recent-order-item">
              <div class="order-info">
                <span class="order-num">#<?= $o['id'] ?> — <?= htmlspecialchars($o['phone'] ?? 'Қонақ') ?></span>
                <span class="order-date"><?= $o['date'] ?></span>
              </div>
              <div style="display:flex; align-items:center; gap:12px;">
                <span class="badge badge-<?= $o['status'] ?>"><span class="badge-dot"></span><?= ucfirst($o['status']) ?></span>
                <span class="order-amount">$<?= number_format($o['total'], 0, '.', ',') ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state" style="padding:30px 10px;">
            <p>Тапсырыстар жоқ</p>
          </div>
        <?php endif; ?>
      </div>

      <div class="top-products">
        <h3>Танымал өнімдер</h3>
        <?php if (!empty($topProducts)): ?>
          <?php $rank = 1; foreach ($topProducts as $pid => $data): ?>
            <div class="top-product-item">
              <div class="rank"><?= $rank++ ?></div>
              <div class="prod-name"><?= htmlspecialchars($data['name']) ?></div>
              <div class="prod-sales"><?= $data['qty'] ?> дана</div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state" style="padding:30px 10px;">
            <p>Сату деректері жоқ</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include '../includes/footer.php'; ?>