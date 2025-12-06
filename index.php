<?php include "header.php"; ?>

<div class="p-5 mb-4 bg-light rounded-3">
  <div class="container-fluid py-5">
    <h1 class="display-5 fw-bold">Welcome to the Online Computer Store</h1>
    <p class="col-md-8 fs-4">
      Browse laptops, PCs, monitors, and accessories. Create an account, add items to your cart, and place orders securely.
    </p>
    <a href="products.php" class="btn btn-primary btn-lg">Start Shopping</a>
  </div>
</div>

<h2 class="mb-3">Featured Products</h2>
<div class="row">
<?php
$result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
if ($result && $result->num_rows > 0):
  while ($p = $result->fetch_assoc()):
?>
  <div class="col-md-3 mb-4">
    <div class="card h-100">
      <?php if (!empty($p['image'])): ?>
        <img src="<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['name']); ?>">
      <?php else: ?>
        <div class="placeholder-image card-img-top d-flex align-items-center justify-content-center">
          <span>No Image</span>
        </div>
      <?php endif; ?>
      <div class="card-body d-flex flex-column">
        <h5 class="card-title"><?php echo htmlspecialchars($p['name']); ?></h5>
        <p class="card-text text-muted"><?php echo htmlspecialchars($p['category']); ?></p>
        <p class="card-text fw-bold mb-3">$<?php echo number_format($p['price'], 2); ?></p>
        <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary mt-auto">View Details</a>
      </div>
    </div>
  </div>
<?php
  endwhile;
else:
  echo "<p>No products yet.</p>";
endif;
?>
</div>

<?php include "footer.php"; ?>
