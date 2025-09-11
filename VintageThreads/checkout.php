<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/csrf.php';

$page_title = 'Checkout';
require_once 'includes/header.php';

$cart_items = get_cart_items($pdo);
$cart_total = get_cart_total($pdo);

if (empty($cart_items)) {
    echo '<div class="container py-5 text-center">
            <h3>Your cart is empty.</h3>
            <a href="index.php" class="btn btn-vintage mt-3">Return to Store</a>
          </div>';
    require_once 'includes/footer.php';
    exit;
}

$order_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('CSRF token verification failed');
    }

    $customer_name = sanitize_input($_POST['customer_name']);
    $customer_number = sanitize_input($_POST['customer_number']);
    $customer_email = sanitize_input($_POST['customer_email']);
    $customer_address = sanitize_input($_POST['customer_address']);
    $customer_city = sanitize_input($_POST['customer_city']);
    $customer_postal_code = sanitize_input($_POST['customer_postal_code']);

    try {
        $pdo->beginTransaction();

        // Check stock and update inventory
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("SELECT stock_quantity FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$item['id']]);
            $product = $stmt->fetch();

            if (!$product || $product['stock_quantity'] < $item['quantity']) {
                throw new Exception("Insufficient stock for " . $item['name']);
            }

            $stmt = $pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        $order_token = bin2hex(random_bytes(16));

        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders 
            (customer_name, customer_email, customer_number, customer_address, customer_city, customer_postal_code, total_amount, status, order_token)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?)");
        $stmt->execute([
            $customer_name,
            $customer_email,
            $customer_number,
            $customer_address,
            $customer_city,
            $customer_postal_code,
            $cart_total,
            $order_token
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price']]);
        }

        clear_cart();
        $pdo->commit();

        $order_success = true;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'There was an error processing your order. Please try again.';
    }
}
?>

<div class="container py-5">
    <?php if ($order_success): ?>
        <div class="alert alert-success text-center py-5">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h3>Thank you for your order, <?=htmlspecialchars($customer_name)?>!</h3>
            <p>Your order has been placed successfully.</p>
            <a href="index.php" class="btn btn-vintage mt-3">Return to Store</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-credit-card me-2"></i>Checkout</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <?php echo csrf_field(); ?>
                            <h5 class="mb-3">Shipping Information</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_number" class="form-label">Phone Number *</label>
                                    <input type="text" class="form-control" id="customer_number" name="customer_number" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="customer_email" name="customer_email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="customer_city" name="customer_city" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_postal_code" class="form-label">Postal Code *</label>
                                    <input type="text" class="form-control" id="customer_postal_code" name="customer_postal_code" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="customer_address" class="form-label">Shipping Address *</label>
                                <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required></textarea>
                            </div>

                            <hr>

                            <h5 class="mb-3">Payment Information</h5>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                This is a demo checkout. In a real store, this would integrate with a payment processor.
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" disabled>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="expiry" class="form-label">Expiry</label>
                                    <input type="text" class="form-control" id="expiry" name="expiry" placeholder="MM/YY" disabled>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" disabled>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-vintage btn-lg">
                                    <i class="fas fa-lock me-2"></i>Place Order
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                <span><?php echo format_price($item['subtotal']); ?></span>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong><?php echo format_price($cart_total); ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body text-center">
                        <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                        <h6>Secure Checkout</h6>
                        <small class="text-muted">Your information is protected with SSL encryption</small>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
