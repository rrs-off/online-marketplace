<?php
require_once 'includes/functions.php';
$products = loadData('products.json');
$cart = $_SESSION['cart'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove'])) {
        unset($_SESSION['cart'][$_POST['remove']]);
        redirectTo('cart.php');
    }
}

$cartItems = [];
$total = 0;
foreach ($cart as $pid => $qty) {
    foreach ($products as $p) {
        if ($p['id'] == $pid) {
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            $cartItems[] = array_merge($p, ['qty' => $qty, 'subtotal' => $subtotal]);
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
  <h2>My Cart</h2>

  <?php if (empty($cartItems)): ?>
    <p>Your cart is empty. <a href="catalog.php">Go to Catalog</a></p>
  <?php else: ?>
    <table>
      <tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th></th></tr>
      <?php foreach ($cartItems as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td>$<?= $item['price'] ?></td>
          <td><?= $item['qty'] ?></td>
          <td>$<?= $item['subtotal'] ?></td>
          <td>
            <form method="post">
              <button type="submit" name="remove" value="<?= $item['id'] ?>" class="btn btn-danger">Remove</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
    <h3 style="margin-top:20px;">Total: $<?= $total ?></h3>
    <a href="checkout.php" class="btn" style="margin-top:15px;">Proceed to Checkout</a>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>