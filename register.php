<?php
require_once __DIR__ . "/config.php";

$error = "";
$success = "";
$name = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === "" || $email === "" || $password === "" || $confirm === "") {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();

        if ($exists) {
            $error = "An account with this email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmtIns = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmtIns->bind_param("sss", $name, $email, $hash);
            if ($stmtIns->execute()) {
                $success = "Registration successful. You can now log in.";
                $name = $email = "";
            } else {
                $error = "Error creating account.";
            }
        }
    }
}

include "header.php";
?>

<h2>Register</h2>

<?php if ($error): ?>
  <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post" class="col-md-6 col-lg-4">
  <div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" name="name" class="form-control" required
           value="<?php echo htmlspecialchars($name); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" required
           value="<?php echo htmlspecialchars($email); ?>">
  </div>
  <div class="mb-3">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" name="confirm_password" class="form-control" required>
  </div>
  <button class="btn btn-primary" type="submit">Register</button>
</form>

<?php include "footer.php"; ?>
