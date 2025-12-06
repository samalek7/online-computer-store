<?php
include "header.php";

// Validate product id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product.</p>";
    include "footer.php";
    exit;
}

$product_id = (int)$_GET['id'];

// If review submitted
$review_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isLoggedIn()) {
        $review_message = "You must be logged in to leave a review.";
    } else {
        $user_id = $_SESSION['user_id'];
        $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $review_message = "Rating must be between 1 and 5 stars.";
        } else {
            // Check if this user already reviewed this product
            $stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();

            if ($existing) {
                // Update existing review
                $review_id = $existing['id'];
                $stmt2 = $conn->prepare("UPDATE reviews SET rating = ?, comment = ? WHERE id = ?");
                $stmt2->bind_param("isi", $rating, $comment, $review_id);
                if ($stmt2->execute()) {
                    $review_message = "Your review has been updated.";
                } else {
                    $review_message = "Error updating your review.";
                }
            } else {
                // Insert new review
                $stmt2 = $conn->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("iiis", $user_id, $product_id, $rating, $comment);
                if ($stmt2->execute()) {
                    $review_message = "Thank you! Your review has been added.";
                } else {
                    $review_message = "Error saving your review.";
                }
            }
        }
    }
}

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result  = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<p>Product not found.</p>";
    include "footer.php";
    exit;
}

// Fetch average rating + total reviews
$stmtAvg = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?");
$stmtAvg->bind_param("i", $product_id);
$stmtAvg->execute();
$ratingData = $stmtAvg->get_result()->fetch_assoc();
$avg_rating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;
$total_reviews = (int)($ratingData['total_reviews'] ?? 0);

// Fetch individual reviews
$stmtReviews = $conn->prepare("
    SELECT r.*, u.name AS user_name
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$stmtReviews->bind_param("i", $product_id);
$stmtReviews->execute();
$reviews = $stmtReviews->get_result();

// If logged in, fetch current user's review (to prefill form)
$userReview = null;
if (isLoggedIn()) {
    $uid = $_SESSION['user_id'];
    $stmtUR = $conn->prepare("SELECT * FROM reviews WHERE user_id = ? AND product_id = ?");
    $stmtUR->bind_param("ii", $uid, $product_id);
    $stmtUR->execute();
    $userReview = $stmtUR->get_result()->fetch_assoc();
}

// Helper to render star icons
function render_stars($rating) {
    $rating = (int)$rating;
    $stars = "";
    for ($i = 1; $i <= 5; $i++) {
        $stars .= $i <= $rating ? "★" : "☆";
    }
    return $stars;
}
?>

<div class="row">
  <div class="col-md-5">
    <?php if (!empty($product['image'])): ?>
      <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <?php else: ?>
      <div class="placeholder-image-large d-flex align-items-center justify-content-center">
        <span>No Image</span>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-md-7">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p class="text-muted">Category: <?php echo htmlspecialchars($product['category']); ?></p>
    <h3 class="text-primary">$<?php echo number_format($product['price'], 2); ?></h3>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <p>In stock: <?php echo (int)$product['stock']; ?></p>

    <!-- Average rating -->
    <p class="mt-2">
        <strong>Rating:</strong>
        <?php if ($total_reviews > 0): ?>
            <span class="text-warning"><?php echo render_stars(round($avg_rating)); ?></span>
            <span class="ms-2">
              <?php echo $avg_rating; ?>/5
              (<?php echo $total_reviews; ?> review<?php echo $total_reviews > 1 ? 's' : ''; ?>)
            </span>
        <?php else: ?>
            <span class="text-muted">No reviews yet.</span>
        <?php endif; ?>
    </p>

    <!-- Add to cart form -->
    <form method="post" action="cart.php" class="mt-3">
      <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
      <input type="hidden" name="action" value="add">
      <div class="mb-3" style="max-width: 150px;">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" min="1" value="1">
      </div>
      <button class="btn btn-primary" type="submit">Add to Cart</button>
    </form>
  </div>
</div>

<hr class="my-4">

<!-- Review section -->
<div class="row">
  <div class="col-md-6 mb-4">
    <h4><?php echo $userReview ? "Edit Your Review" : "Write a Review"; ?></h4>

    <?php if ($review_message): ?>
      <div class="alert alert-info"><?php echo htmlspecialchars($review_message); ?></div>
    <?php endif; ?>

    <?php if (!isLoggedIn()): ?>
      <p>You must <a href="login.php">log in</a> to leave a review.</p>
    <?php else: ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Rating (1–5)</label>
          <select name="rating" class="form-select" required>
            <?php
              $currentRating = $userReview['rating'] ?? 5;
              for ($i = 5; $i >= 1; $i--):
            ?>
              <option value="<?php echo $i; ?>" <?php echo (int)$currentRating === $i ? 'selected' : ''; ?>>
                <?php echo $i; ?> - <?php echo render_stars($i); ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Comment (optional)</label>
          <textarea name="comment" rows="4" class="form-control"><?php
            echo htmlspecialchars($userReview['comment'] ?? "");
          ?></textarea>
        </div>

        <button type="submit" name="submit_review" class="btn btn-success">
          <?php echo $userReview ? "Update Review" : "Submit Review"; ?>
        </button>
      </form>
    <?php endif; ?>
  </div>

  <div class="col-md-6">
    <h4>Customer Reviews</h4>

    <?php if ($total_reviews === 0): ?>
      <p class="text-muted">No reviews yet. Be the first to review this product!</p>
    <?php else: ?>
      <?php while ($rev = $reviews->fetch_assoc()): ?>
        <div class="border rounded p-2 mb-2">
          <div class="d-flex justify-content-between">
            <strong><?php echo htmlspecialchars($rev['user_name']); ?></strong>
            <small class="text-muted"><?php echo $rev['created_at']; ?></small>
          </div>
          <div class="text-warning">
            <?php echo render_stars($rev['rating']); ?>
            <span class="ms-2 text-dark"><?php echo (int)$rev['rating']; ?>/5</span>
          </div>
          <?php if (trim($rev['comment']) !== ""): ?>
            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>
</div>

<?php include "footer.php"; ?>
