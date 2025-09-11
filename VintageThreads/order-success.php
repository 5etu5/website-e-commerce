<?php
$page_title = 'Order Confirmation';
require_once 'includes/header.php';

$token = $_GET['token'] ?? null;

if (!$token) {
    header('Location: index.php');
    exit;
}

// Get order details using secure token
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_token = ?");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

// Provide defaults for currency (since regions are removed)
$currency = '$'; // or 'USD', adjust as needed
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                <h1 class="text-success">Order Confirmed!</h1>
                <p class="lead">Thank you for your purchase. Your order has been received and is being processed.</p>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4>Order #<?php echo $order['id']; ?></h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p>
                                <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                <?php echo htmlspecialchars($order['customer_email']); ?><br>
                                <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Order Details</h6>
                            <p>
                                <strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?><br>
                                <strong>Total:</strong> <?php echo format_price($order['total_amount'], $currency); ?><br>
                                <strong>Status:</strong> <span class="badge bg-warning">Processing</span>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>What's Next?</h6>
                    <ul>
                        <li>You'll receive an email confirmation shortly</li>
                        <li>We'll prepare your vintage items with care</li>
                        <li>Your order will ship within 1-2 business days</li>
                        <li>Tracking information will be sent via email</li>
                    </ul>
                    
                    <div class="text-center mt-4">
                        <a href="index.php" class="btn btn-vintage me-2">Continue Shopping</a>
                        <a href="category.php?slug=new-arrivals" class="btn btn-outline-primary">Browse New Arrivals</a>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <h5>Follow Your Style Journey</h5>
                <p class="text-muted">Stay connected for vintage fashion inspiration</p>
                <div class="social-links">
                    <a href="#" class="btn btn-outline-secondary me-2">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                    <a href="#" class="btn btn-outline-secondary me-2">
                        <i class="fab fa-facebook"></i> Facebook
                    </a>
                    <a href="blog.php" class="btn btn-outline-secondary">
                        <i class="fas fa-newspaper"></i> Read Our Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
