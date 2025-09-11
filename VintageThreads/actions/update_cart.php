<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

if (!$product_id || $quantity < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

if ($quantity == 0) {
    remove_from_cart($product_id);
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

echo json_encode(['success' => true, 'message' => 'Cart updated']);
?>