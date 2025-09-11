<?php
// Common functions for the vintage clothing store

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generate_slug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
}

function format_price($price, $currency = 'USD') {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'CAD' => 'C$',
        'AUD' => 'A$'
    ];
    $symbol = $symbols[$currency] ?? '$';
    return $symbol . number_format($price, 2);
}

function get_categories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

function get_regions($pdo) {
    $stmt = $pdo->query("SELECT * FROM regions ORDER BY name");
    return $stmt->fetchAll();
}

function get_products($pdo, $category_id = null, $limit = null, $featured = false) {
    $sql = "SELECT p.*, c.name as category_name FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
    
    if ($category_id) {
        $sql .= " AND p.category_id = :category_id";
    }
    
    if ($featured) {
        $sql .= " AND p.is_featured = 1";
    }
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    
    if ($category_id) {
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_product_by_slug($pdo, $slug) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.slug = :slug");
    $stmt->bindParam(':slug', $slug);
    $stmt->execute();
    return $stmt->fetch();
}

function get_blog_posts($pdo, $published = true, $limit = null) {
    $sql = "SELECT * FROM blog_posts";
    if ($published) {
        $sql .= " WHERE published = 1";
    }
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Shopping cart functions
function add_to_cart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

function remove_from_cart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

function get_cart_items($pdo) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($product_ids);
    $products = $stmt->fetchAll();
    
    foreach ($products as &$product) {
        $product['quantity'] = $_SESSION['cart'][$product['id']];
        $product['subtotal'] = $product['price'] * $product['quantity'];
    }
    
    return $products;
}

function get_cart_total($pdo) {
    $items = get_cart_items($pdo);
    $total = 0;
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function clear_cart() {
    unset($_SESSION['cart']);
}

// Fetch user by email
function get_user_by_email(PDO $pdo, string $email) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetch();
}

// Create a new user (register)
function create_user(PDO $pdo, string $name, string $email, string $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);
    return $pdo->lastInsertId();
}

// Attempt login - returns user row if success, false otherwise
function authenticate_user(PDO $pdo, string $email, string $password) {
    $user = get_user_by_email($pdo, $email);
    if ($user && password_verify($password, $user['password_hash'])) {
        return $user;
    }
    return false;
}

// simple helper to require login on a page
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}
?>