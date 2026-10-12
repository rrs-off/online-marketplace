<?php
$basePath = '../';
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$users = loadData('users.json');
$orders = loadData('orders.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $userId = (int)($_POST['user_id'] ?? 0);

    if ($action === 'delete') {
        $users = array_filter($users, fn($u) => $u['id'] !== $userId);
        saveData('users.json', array_values($users));
        redirectTo('users.php');
    } elseif ($action === 'block' || $action === 'unblock') {
        foreach ($users as &$u) {
            if ($u['id'] === $userId) {
                $u['status'] = ($action === 'block') ? 'blocked' : 'active';
                break;
            }
        }
        saveData('users.json', $users);
        redirectTo('users.php');
    }
}

$search = trim($_GET['search'] ?? '');
$filteredUsers = $users;
if ($search !== '') {
    $filteredUsers = array_filter($filteredUsers, fn($u) =>
        stripos($u['first_name'] . ' ' . $u['last_name'], $search) !== false ||
        stripos($u['email'], $search) !== false
    );
}

$orderCountByUser = [];
foreach ($orders as $o) {
    $uid = $o['user_id'];
    if (!isset($orderCountByUser[$uid])) $orderCountByUser[$uid] = 0;
    $orderCountByUser[$uid]++;
}

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
    <h2>Пайдаланушылар</h2>
    <p class="admin-page-desc">Пайдаланушыларды басқарыңыз — блоктау, жою</p>

    <div class="admin-toolbar">
      <form method="get" class="admin-toolbar" style="margin:0; flex:1; gap:12px;">
        <input type="text" name="search" class="search-input" placeholder="Аты немесе email бойынша іздеу..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-sm">Іздеу</button>
        <?php if ($search !== ''): ?>
          <a href="users.php" class="btn btn-sm" style="background:#e2e8f0; color:#475569; box-shadow:none;">Тазалау</a>
        <?php endif; ?>
      </form>
    </div>

    <?php if (!empty($filteredUsers)): ?>
      <table>
        <tr>
          <th>ID</th>
          <th>Аты</th>
          <th>Email</th>
          <th>Телефон</th>
          <th>Тапсырыстар</th>
          <th>Рөлі</th>
          <th>Статус</th>
          <th>Әрекеттер</th>
        </tr>
        <?php foreach ($filteredUsers as $u): ?>
          <?php $isBlocked = isset($u['status']) && $u['status'] === 'blocked'; ?>
          <tr>
            <td>#<?= $u['id'] ?></td>
            <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['phone'] ?? '—') ?></td>
            <td><?= $orderCountByUser[$u['id']] ?? 0 ?></td>
            <td>
              <?php if ($u['role'] === 'admin'): ?>
                <span class="badge badge-confirmed"><span class="badge-dot"></span>Админ</span>
              <?php else: ?>
                <span class="badge badge-active"><span class="badge-dot"></span>Қолданушы</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($isBlocked): ?>
                <span class="badge badge-blocked"><span class="badge-dot"></span>Бұғатталған</span>
              <?php else: ?>
                <span class="badge badge-active"><span class="badge-dot"></span>Белсенді</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($u['role'] !== 'admin'): ?>
                <div class="admin-actions">
                  <?php if ($isBlocked): ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="action" value="unblock">
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <button type="submit" class="action-unblock" style="border:none; cursor:pointer; font-size:13px; font-weight:500; padding:4px 10px; border-radius:6px;">Қайта қосу</button>
                    </form>
                  <?php else: ?>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="action" value="block">
                      <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                      <button type="submit" class="action-block" style="border:none; cursor:pointer; font-size:13px; font-weight:500; padding:4px 10px; border-radius:6px;">Блоктау</button>
                    </form>
                  <?php endif; ?>
                  <form method="post" style="display:inline;" onsubmit="return confirm('Бұл пайдаланушыны жойғыңыз келе ме?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button type="submit" class="action-delete" style="border:none; cursor:pointer; font-size:13px; font-weight:500; padding:4px 10px; border-radius:6px;">Жою</button>
                  </form>
                </div>
              <?php else: ?>
                <span style="color:var(--text-muted); font-size:13px;">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <div class="empty-icon">&#128100;</div>
        <p>Пайдаланушылар табылмады</p>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php include '../includes/footer.php'; ?>