<?php
session_start();
require_once 'config/database.php';

// Get JSON POST data
$data = json_decode(file_get_contents('php://input'), true);

$product_id = intval($data['product_id'] ?? 0);
$size = $data['size'] ?? '';
$quantity = intval($data['quantity'] ?? 1);

if (!$product_id || !$size || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Fetch product from database
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Check stock for selected size
$stock_col = 'stock_' . strtoupper($size); // stock_S, stock_M, etc.
$available_stock = $product[$stock_col] ?? 0;

if ($quantity > $available_stock) {
    echo json_encode(['success' => false, 'message' => 'Not enough stock']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Use a unique key for product + size
$cart_key = $product_id . '_' . $size;

// Add or update quantity
if (isset($_SESSION['cart'][$cart_key])) {
    $_SESSION['cart'][$cart_key]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$cart_key] = [
        'product_id' => $product_id,
        'name' => $product['name'],
        'price' => $product['price'],
        'size' => $size,
        'quantity' => $quantity,
        'image_url' => $product['image_url']
    ];
}

// Optional: Decrease stock in database (if needed for real-time stock)
$stmt = $pdo->prepare("UPDATE products SET $stock_col = $stock_col - ? WHERE id = ?");
$stmt->execute([$quantity, $product_id]);

echo json_encode(['success' => true, 'cart_count' => count($_SESSION['cart'])]);
exit;
