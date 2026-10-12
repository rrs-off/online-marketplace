<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirectTo('login.php');
}

$products = loadData('products.json');
$cart = $_SESSION['cart'] ?? [];
$errors = [];

if (empty($cart)) {
    redirectTo('cart.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment = $_POST['payment'] ?? '';

    if ($address === '' || $phone === '' || $payment === '') {
        $errors[] = "All fields are required";
    }
    if ($phone !== '' && !preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
        $errors[] = "Please enter a valid phone number";
    }

    if (empty($errors)) {
        $orders = loadData('orders.json');
        $items = [];
        $total = 0;
        foreach ($cart as $pid => $qty) {
            foreach ($products as $p) {
                if ($p['id'] == $pid) {
                    $items[] = ['product_id' => $p['id'], 'name' => $p['name'], 'price' => $p['price'], 'qty' => $qty];
                    $total += $p['price'] * $qty;
                }
            }
        }
        $newOrder = [
            'id' => getNextId($orders),
            'user_id' => $_SESSION['user']['id'],
            'items' => $items,
            'total' => $total,
            'address' => $address,
            'phone' => $phone,
            'payment' => $payment,
            'status' => 'pending',
            'date' => date('d.m.Y H:i')
        ];
        $orders[] = $newOrder;
        saveData('orders.json', $orders);
        $_SESSION['cart'] = [];
        redirectTo('confirmation.php?order_id=' . $newOrder['id']);
    }
}

include 'includes/header.php';
?>

<div class="form-box">
  <h2>Checkout</h2>

  <?php if (!empty($errors)): ?>
    <div class="error-box">
      <?php foreach ($errors as $e) echo "<div>$e</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="form-group">
      <label>Delivery Address</label>
      <input type="text" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Phone</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label>Payment Method</label>
      <select name="payment">
        <option value="card">Bank Card</option>
        <option value="cash">Cash on Delivery</option>
      </select>
    </div>
    <button type="submit" class="btn" style="width:100%;">Confirm Order</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>