<?php include "header.php"; ?>

<h2 class="mb-3">Products</h2>

<form class="row g-3 mb-4" method="get">
  <div class="col-md-4">
    <input type="text" name="category" class="form-control"
           placeholder="Filter by category (e.g. Laptops, Accessories)"
           value="<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>">
  </div>
  <div class="col-md-4">
    <input type="text" name="search" class="form-control"
           placeholder="Search by product name"
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
  </div>
  <div class="col-md-4">
    <button class="btn btn-primary" type="submit">Filter</button>
    <a href="products.php" class="btn btn-secondary ms-2">Reset</a>
  </div>
</form>

<div class="row">
<?php
$where = [];
$params = [];
$types  = "";

if (!empty($_GET['category'])) {
    $where[] = "category LIKE CONCAT('%', ?, '%')";
    $params[] = $_GET['category'];
    $types   .= "s";
}
if (!empty($_GET['search'])) {
    $where[] = "name LIKE CONCAT('%', ?, '%')";
    $params[] = $_GET['search'];
    $types   .= "s";
}

$sql = "SELECT * FROM products";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY name ASC";

if ($stmt = $conn->prepare($sql)) {
    if (!empty($types)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM products ORDER BY name ASC");
}

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
        <a href="product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary mb-2">Details</a>
        <form method="post" action="cart.php" class="mt-auto">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
          <button type="submit" class="btn btn-primary w-100">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>
<?php
  endwhile;
else:
  echo "<p>No products found.</p>";
endif;
?>
</div>

<?php include "footer.php"; ?>
