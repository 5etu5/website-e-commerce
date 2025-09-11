<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$region_id = filter_input(INPUT_GET, 'region_id', FILTER_VALIDATE_INT) ?: 
             filter_input(INPUT_POST, 'region_id', FILTER_VALIDATE_INT);

if ($region_id && isset($pdo)) {
    // Get region from database
    $stmt = $pdo->prepare("SELECT * FROM regions WHERE id = :id");
    $stmt->bindParam(':id', $region_id, PDO::PARAM_INT);
    $stmt->execute();
    $region = $stmt->fetch();
    
    if ($region) {
        $_SESSION['region'] = $region;
    }
}

// Redirect back or return JSON
if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
}
exit;
?>