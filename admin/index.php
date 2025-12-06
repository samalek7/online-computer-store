<?php
include "admin_header.php";

// Simple stats
$countUsers    = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()['c'] ?? 0;
$countProducts = $conn->query("SELECT COUNT(*) AS c FROM products")->fetch_assoc()['c'] ?? 0;
$countOrders   = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'] ?? 0;
?>

<h1 class="mb-4">Dashboard</h1>

<div class="row">
  <div class="col-md-4 mb-3">
    <div class="card text-bg-primary">
      <div class="card-body">
        <h5 class="card-title">Users</h5>
        <p class="display-6 mb-0"><?php echo $countUsers; ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card text-bg-success">
      <div class="card-body">
        <h5 class="card-title">Products</h5>
        <p class="display-6 mb-0"><?php echo $countProducts; ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card text-bg-warning">
      <div class="card-body">
        <h5 class="card-title">Orders</h5>
        <p class="display-6 mb-0"><?php echo $countOrders; ?></p>
      </div>
    </div>
  </div>
</div>

<?php include "admin_footer.php"; ?>
