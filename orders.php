<?php
include "header.php";

if (!isLoggedIn()) {
    echo '<div class="alert alert-warning">Please <a href="login.php">log in</a> to view orders.</div>';
    include "footer.php";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>

<h2>My Orders</h2>

<?php if ($orders->num_rows === 0): ?>
  <p>You have not placed any orders yet.</p>
<?php else: ?>
  <?php while ($order = $orders->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-header">
        <strong>Order #<?php echo $order['id']; ?></strong>
        <span class="text-muted ms-2"><?php echo $order['created_at']; ?></span>
        <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($order['status']); ?></span>
      </div>
      <div class="card-body">
        <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>

        <?php
        $stmtItems = $conn->prepare("
            SELECT oi.*, p.name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
        ");
        $stmtItems->bind_param("i", $order['id']);
        $stmtItems->execute();
        $items = $stmtItems->get_result();
        ?>

        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Product</th>
                <th style="width:120px;">Price</th>
                <th style="width:80px;">Qty</th>
                <th style="width:120px;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
            <?php while ($item = $items->fetch_assoc()):
                $sub = $item['price'] * $item['quantity'];
            ?>
              <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>$<?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo (int)$item['quantity']; ?></td>
                <td>$<?php echo number_format($sub, 2); ?></td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

<?php include "footer.php"; ?>
