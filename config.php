<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$db   = "online_computer_store";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

function isAdmin() {
    return !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}
?>
