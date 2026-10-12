<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Marketplace</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= isset($basePath) ? $basePath : '' ?>css/style.css">
</head>
<body>
<header class="navbar">
  <a href="<?= isset($basePath) ? $basePath : '' ?>index.php" class="logo">Marketplace</a>
  <nav>
    <a href="<?= isset($basePath) ? $basePath : '' ?>index.php">Home</a>
    <a href="<?= isset($basePath) ? $basePath : '' ?>catalog.php">Catalog</a>
    <a href="<?= isset($basePath) ? $basePath : '' ?>cart.php">Cart</a>
    <?php if (isLoggedIn()): ?>
      <a href="<?= isset($basePath) ? $basePath : '' ?>profile.php">Profile</a>
      <?php if (isAdmin()): ?>
        <a href="<?= isset($basePath) ? $basePath : '' ?>admin/index.php">Admin Panel</a>
      <?php endif; ?>
      <a href="<?= isset($basePath) ? $basePath : '' ?>logout.php">Logout</a>
    <?php else: ?>
      <a href="<?= isset($basePath) ? $basePath : '' ?>login.php">Login</a>
      <a href="<?= isset($basePath) ? $basePath : '' ?>register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>