<?php
include "admin_header.php";

$message = "";
$editProduct = null;

// DELETE product
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Product deleted successfully.";
    } else {
        $message = "Error deleting product.";
    }
}

// LOAD product for editing if ?edit=id
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $editProduct = $res->fetch_assoc();
    if (!$editProduct) {
        $message = "Product not found for editing.";
    }
}

// HANDLE add / edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name        = trim($_POST['name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $image       = trim($_POST['image'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($name === "" || $category === "") {
        $message = "Name and category are required.";
    } else {
        if ($id > 0) {
            // UPDATE
            $stmt = $conn->prepare("
                UPDATE products
                SET name = ?, category = ?, price = ?, stock = ?, image = ?, description = ?
                WHERE id = ?
            ");
            $stmt->bind_param("ssdissi", $name, $category, $price, $stock, $image, $description, $id);
            if ($stmt->execute()) {
                $message = "Product updated successfully.";
            } else {
                $message = "Error updating product.";
            }
        } else {
            // INSERT
            $stmt = $conn->prepare("
                INSERT INTO products (name, category, price, stock, image, description)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssdiss", $name, $category, $price, $stock, $image, $description);
            if ($stmt->execute()) {
                $message = "Product added successfully.";
            } else {
                $message = "Error adding product.";
            }
        }

        if ($id > 0) {
            $editProduct = [
                'id'          => $id,
                'name'        => $name,
                'category'    => $category,
                'price'       => $price,
                'stock'       => $stock,
                'image'       => $image,
                'description' => $description
            ];
        } else {
            $editProduct = null;
        }
    }
}

// Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<h1 class="mb-4">Manage Products</h1>

<?php if ($message): ?>
  <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="row">
  <div class="col-md-5">
    <h4><?php echo $editProduct ? "Edit Product" : "Add New Product"; ?></h4>
    <form method="post">
      <input type="hidden" name="id" value="<?php echo $editProduct['id'] ?? 0; ?>">

      <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required
               value="<?php echo htmlspecialchars($editProduct['name'] ?? ""); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control" required
               value="<?php echo htmlspecialchars($editProduct['category'] ?? ""); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" name="price" class="form-control" required
               value="<?php echo isset($editProduct['price']) ? htmlspecialchars($editProduct['price']) : ""; ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" required
               value="<?php echo isset($editProduct['stock']) ? (int)$editProduct['stock'] : 0; ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Image Path (relative)</label>
        <input type="text" name="image" class="form-control"
               placeholder="e.g. assets/img/laptop1.jpg"
               value="<?php echo htmlspecialchars($editProduct['image'] ?? ""); ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control"><?php
            echo htmlspecialchars($editProduct['description'] ?? "");
        ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary">
        <?php echo $editProduct ? "Update Product" : "Add Product"; ?>
      </button>
      <?php if ($editProduct): ?>
        <a href="products.php" class="btn btn-secondary ms-2">Cancel</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="col-md-7">
    <h4>Product List</h4>
    <div class="table-responsive">
      <table class="table table-striped table-sm align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th style="width:140px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td><?php echo htmlspecialchars($row['category']); ?></td>
              <td>$<?php echo number_format($row['price'], 2); ?></td>
              <td><?php echo (int)$row['stock']; ?></td>
              <td>
                <a href="products.php?edit=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="products.php?delete=<?php echo $row['id']; ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this product?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include "admin_footer.php"; ?>
