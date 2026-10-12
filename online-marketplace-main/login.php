<?php
require_once 'includes/functions.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = "Барлық өрістерді толтырыңыз";
    } else {
        $users = loadData('users.json');
        $found = null;
        foreach ($users as $u) {
            if (strtolower($u['email']) === strtolower($email)) {
                $found = $u;
                break;
            }
        }
        if (!$found) {
            $errors[] = "Бұл email-мен пайдаланушы табылмады";
        } elseif (!password_verify($password, $found['password'])) {
            $errors[] = "Құпиясөз дұрыс емес";
        } elseif (isset($found['status']) && $found['status'] === 'blocked') {
            $errors[] = "Бұл есептік жазба бұғатталған. Әкімшіге хабарласыңыз.";
        } else {
            unset($found['password']);
            $_SESSION['user'] = $found;
            redirectTo('index.php');
        }
    }
}
include 'includes/header.php';
?>

<div class="form-box">
  <h2 style="margin-bottom:20px;">Аккаунтқа кіру</h2>

  <?php if (isset($_GET['registered'])): ?>
    <div class="success-box">Тіркеу сәтті аяқталды. Енді кіре аласыз.</div>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>Email</label>
      <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Құпиясөз</label>
      <input type="password" name="password">
    </div>
    <button type="submit" class="btn" style="width:100%;">Кіру</button>
  </form>
  <p style="margin-top:15px; text-align:center;">Аккаунтыңыз жоқ па? <a href="register.php">Тіркеу</a></p>
</div>

<?php include 'includes/footer.php'; ?>