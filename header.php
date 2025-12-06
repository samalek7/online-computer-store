<?php
require_once __DIR__ . "/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Computer Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="index.php">Computer Store</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
        <?php if (isLoggedIn()): ?>
            <li class="nav-item"><a class="nav-link" href="orders.php">My Orders</a></li>
        <?php endif; ?>
        <?php if (isAdmin()): ?>
            <li class="nav-item"><a class="nav-link" href="admin/index.php">Admin</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
            <li class="nav-item">
                <span class="navbar-text me-2">
                    Hello, <?php echo htmlspecialchars(currentUserName()); ?>!
                </span>
            </li>
            <li class="nav-item"><a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li class="nav-item me-2"><a class="btn btn-outline-light btn-sm" href="login.php">Login</a></li>
            <li class="nav-item"><a class="btn btn-warning btn-sm" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mb-5">
