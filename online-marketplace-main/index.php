<?php 
require_once 'includes/functions.php'; 
$products = loadData('products.json');
$featuredProducts = array_slice($products, 0, 3);

include 'includes/header.php'; 
?>

<section class="hero" style="padding: 50px 20px;">
  <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 30px; text-align: left; padding: 0;">
    
    <div style="flex: 1; min-width: 300px;">
      <h1 style="font-size: 38px; line-height: 1.2; margin-bottom: 15px;">Everything You Need, <br>Delivered to Your Door</h1>
      <p style="font-size: 18px; opacity: 0.9; margin-bottom: 25px;">Discover thousands of top quality products at unbeatable prices.</p>
      <a href="catalog.php" class="btn" style="background: white; color: var(--primary); font-weight: bold; padding: 12px 28px; font-size: 16px;">Start Shopping →</a>
    </div>

    <div style="flex: 1; min-width: 300px; text-align: center;">
      <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80" 
           alt="Shopping Online" 
           style="width: 100%; max-width: 480px; height: 300px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
    </div>

  </div>
</section>

<div class="container">
  <h2 style="text-align: center; margin-bottom: 25px;">Why Choose Us?</h2>
  <div class="grid">
    <div class="card" style="text-align: center; padding: 25px;">
      <div style="font-size: 45px; margin-bottom: 10px;"></div>
      <h3>Fast Delivery</h3>
      <p style="color: #6b7280; margin-top: 8px;">Lightning fast doorstep delivery across the whole country.</p>
    </div>
    
    <div class="card" style="text-align: center; padding: 25px;">
      <div style="font-size: 45px; margin-bottom: 10px;"></div>
      <h3>Secure Payment</h3>
      <p style="color: #6b7280; margin-top: 8px;">100% secure checkout with various payment methods.</p>
    </div>
    
    <div class="card" style="text-align: center; padding: 25px;">
      <div style="font-size: 45px; margin-bottom: 10px;"></div>
      <h3>Certified Quality</h3>
      <p style="color: #6b7280; margin-top: 8px;">Only verified vendors and authentic premium products.</p>
    </div>
  </div>

  <?php if (!empty($featuredProducts)): ?>
    <div style="margin-top: 60px;">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Popular Products</h2>
        <a href="catalog.php" style="color: var(--primary); text-decoration: none; font-weight: bold;">View All →</a>
      </div>

      <div class="grid">
        <?php foreach ($featuredProducts as $p): ?>
          <div class="card">
            <?php if (!empty($p['image'])): ?>
              <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
            <?php else: ?>
              <div class="no-image">No Image</div>
            <?php endif; ?>
            <div class="card-body">
              <h3><?= htmlspecialchars($p['name']) ?></h3>
              <p style="color: #6b7280;"><?= $p['category'] ?></p>
              <div class="price">$<?= $p['price'] ?></div>
              <a href="product.php?id=<?= $p['id'] ?>" class="btn" style="width: 100%; text-align: center;">View Details</a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>