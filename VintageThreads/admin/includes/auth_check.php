<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database/functions if needed
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Check if user role is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not an admin → redirect to main store page
    header('Location: ../index.php');
    exit;
}

// Optional: admin username for display
$_SESSION['admin_username'] = $_SESSION['user_name'] ?? 'Admin';
