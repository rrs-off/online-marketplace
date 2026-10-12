<?php
require_once '../includes/functions.php';
if (!isAdmin()) redirectTo('../login.php');

$id = (int)($_GET['id'] ?? 0);
$products = loadData('products.json');
$products = array_filter($products, fn($p) => $p['id'] !== $id);
saveData('products.json', array_values($products));
redirectTo('products.php');