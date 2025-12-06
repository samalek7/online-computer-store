<?php
include "header.php";

if (!isLoggedIn()) {
    echo '<div class="alert alert-warning">Please <a href="login.php">log in</a> to checkout.</div>';
    include "footer.php";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.stock
            FROM cart c
            JOIN products p ON p.id = c.product_id
            WHERE c.user_id = ?
        ");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $items = $stmt->get_result();

        if ($items->num_rows === 0) {
            throw new Exception("Cart is empty.");
        }

        $total = 0;
        $cartItems = [];
        while ($row = $items->fetch_assoc()) {
            if ($row['stock'] < $row['quantity']) {
                throw new Exception("Not enough stock for " . $row['name']);
            }
            $subtotal = $row['price'] * $row['quantity'];
            $total += $subtotal;
            $cartItems[] = $row;
        }

        // Create order
        $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmtOrder->bind_param("id", $user_id, $total);
        $stmtOrder->execute();
        $order_id = $stmtOrder->insert_id;

        // Insert order items + update stock
        $stmtItem  = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmtStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($cartItems as $item) {
            $stmtItem->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmtItem->execute();

            $stmtStock->bind_param("ii", $item['quantity'], $item['product_id']);
            $stmtStock->execute();
        }

        // Clear cart
        $stmtClear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmtClear->bind_param("i", $user_id);
        $stmtClear->execute();

        $conn->commit();
        echo '<div class="alert alert-success">Order placed successfully! Order ID #' . $order_id . '</div>';
        echo '<a href="orders.php" class="btn btn-primary">View My Orders</a>';
        include "footer.php";
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Show cart summary
$stmt = $conn->prepare("
    SELECT c.quantity, p.name, p.price
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$items = $stmt->get_result();

$total = 0;
?>

<h2>Checkout</h2>

<?php if ($items->num_rows === 0): ?>
  <p>Your cart is empty. <a href="products.php">Shop now</a>.</p>
<?php else: ?>
  <div class="table-responsive mb-4">
    <table class="table">
      <thead>
        <tr>
          <th>Product</th>
          <th style="width:120px;">Price</th>
          <th style="width:120px;">Qty</th>
          <th style="width:120px;">Subtotal</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $items->fetch_assoc()):
          $subtotal = $row['price'] * $row['quantity'];
          $total += $subtotal;
      ?>
        <tr>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td>$<?php echo number_format($row['price'], 2); ?></td>
          <td><?php echo (int)$row['quantity']; ?></td>
          <td>$<?php echo number_format($subtotal, 2); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total:</th>
          <th>$<?php echo number_format($total, 2); ?></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <form method="post">
    <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
    <a href="cart.php" class="btn btn-secondary ms-2">Back to Cart</a>
  </form>
<?php endif; ?>

<?php include "footer.php"; ?>
