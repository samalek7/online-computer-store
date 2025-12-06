<?php
include "admin_header.php";

// Fetch all orders with user info
$sql = "
  SELECT o.*, u.name AS user_name, u.email AS user_email
  FROM orders o
  JOIN users u ON u.id = o.user_id
  ORDER BY o.created_at DESC
";
$orders = $conn->query($sql);
?>

<h1 class="mb-4">All Orders</h1>

<?php if (!$orders || $orders->num_rows === 0): ?>
  <p>No orders yet.</p>
<?php else: ?>
  <?php while ($order = $orders->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-header">
        <strong>Order #<?php echo $order['id']; ?></strong>
        <span class="text-muted ms-2"><?php echo $order['created_at']; ?></span>
        <span class="badge bg-secondary ms-2"><?php echo htmlspecialchars($order['status']); ?></span>
      </div>
      <div class="card-body">
        <p>
          <strong>Customer:</strong>
          <?php echo htmlspecialchars($order['user_name']); ?>
          (<?php echo htmlspecialchars($order['user_email']); ?>)
        </p>
        <p><strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>

        <?php
        $stmt = $conn->prepare("
            SELECT oi.*, p.name
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order['id']);
        $stmt->execute();
        $items = $stmt->get_result();
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

<?php include "admin_footer.php"; ?>
