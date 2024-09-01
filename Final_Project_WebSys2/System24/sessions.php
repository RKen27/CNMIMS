<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in'])) {
    header("Location: login.php");
    exit();
}

// Set headers to prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies
?>
