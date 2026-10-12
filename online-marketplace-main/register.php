<?php
require_once 'includes/functions.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name  = trim($_POST['last_name'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '' || $confirm === '') {
        $errors[] = "All required fields must be filled in";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    if (strlen($password) < 6) {
        $errors[] = "The password must contain at least 6 characters";
    }
    if ($password !== $confirm) {
        $errors[] = "Passwords do not match";
    }

    $users = loadData('users.json');
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower($email)) {
            $errors[] = "A user with this email already exists";
            break;
        }
    }

    if (empty($errors)) {
        $newUser = [
            'id' => getNextId($users),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => trim($_POST['phone'] ?? ''),
            'role' => 'user'
        ];
        $users[] = $newUser;
        saveData('users.json', $users);
        redirectTo('login.php?registered=1');
    }
}
include 'includes/header.php';
?>

<div class="form-box">
  <h2 style="margin-bottom:20px;">Create an Account</h2>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>First Name</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Last Name</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Email</label>
      <input type="text" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" name="password">
    </div>
    <div class="form-group">
      <label>Confirm Password</label>
      <input type="password" name="confirm_password">
    </div>
    <button type="submit" class="btn" style="width:100%;">Register</button>
  </form>
  <p style="margin-top:15px; text-align:center;">Already have an account? <a href="login.php">Log In</a></p>
</div>

<?php include 'includes/footer.php'; ?>