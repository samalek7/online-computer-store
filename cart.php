<?php
include "header.php";

if (!isLoggedIn()) {
    echo '<div class="alert alert-warning">Please <a href="login.php">log in</a> to use the cart.</div>';
    include "footer.php";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $cart_id    = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $qty        = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    if ($action === 'add' && $product_id > 0) {
        // Check if in cart already
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $newQty = $existing['quantity'] + $qty;
            $stmt2 = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt2->bind_param("ii", $newQty, $existing['id']);
            $stmt2->execute();
        } else {
            $stmt2 = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt2->bind_param("iii", $user_id, $product_id, $qty);
            $stmt2->execute();
        }
        echo '<div class="alert alert-success">Product added to cart.</div>';
    }

    if ($action === 'update' && $cart_id > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $qty, $cart_id, $user_id);
        $stmt->execute();
    }

    if ($action === 'remove' && $cart_id > 0) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    }
}

// Fetch cart items
$stmt = $conn->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$items = $stmt->get_result();

$total = 0;
?>

<h2>My Cart</h2>

<?php if ($items->num_rows === 0): ?>
  <p>Your cart is empty. <a href="products.php">Shop now</a>.</p>
<?php else: ?>
  <div class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Product</th>
          <th style="width:120px;">Price</th>
          <th style="width:150px;">Quantity</th>
          <th style="width:120px;">Subtotal</th>
          <th></th>
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
          <td>
            <form method="post" class="d-flex">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
              <input type="number" name="quantity" min="1"
                     value="<?php echo $row['quantity']; ?>"
                     class="form-control form-control-sm me-2">
              <button class="btn btn-sm btn-outline-primary">Update</button>
            </form>
          </td>
          <td>$<?php echo number_format($subtotal, 2); ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="cart_id" value="<?php echo $row['cart_id']; ?>">
              <button class="btn btn-sm btn-outline-danger">Remove</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="3" class="text-end">Total:</th>
          <th>$<?php echo number_format($total, 2); ?></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
<?php endif; ?>

<?php include "footer.php"; ?>
