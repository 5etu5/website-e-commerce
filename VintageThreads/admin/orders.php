<?php
require_once __DIR__ . '/includes/auth_check.php';

require_once 'includes/header.php';
require_once '../includes/csrf.php';

$action = $_GET['action'] ?? 'list';
$order_id = $_GET['id'] ?? null;

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('CSRF token verification failed');
    }
    
    $new_status = sanitize_input($_POST['status']);
    $order_id = filter_var($_POST['order_id'], FILTER_VALIDATE_INT);
    
    if ($order_id && in_array($new_status, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['success'] = 'Order status updated successfully!';
    }
    
    header('Location: orders.php');
    exit;
}

// Get order details for viewing
$order = null;
$order_items = [];
if ($action === 'view' && $order_id) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();
    
    if ($order) {
        $stmt = $pdo->prepare("SELECT oi.*, p.name as product_name, p.image_url 
                              FROM order_items oi 
                              LEFT JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?");
        $stmt->execute([$order_id]);
        $order_items = $stmt->fetchAll();
    }
}

// Get all orders for listing
if ($action === 'list') {
    $status_filter = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT o.*, 
                   COUNT(oi.id) as item_count,
                   SUM(oi.quantity) as total_items
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id";
    
    $conditions = [];
    $params = [];
    
    if ($status_filter) {
        $conditions[] = "o.status = ?";
        $params[] = $status_filter;
    }
    
    if ($search) {
        $conditions[] = "(o.customer_name LIKE ? OR o.customer_email LIKE ? OR o.id = ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search;
    }
    
    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " GROUP BY o.id ORDER BY o.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
}
?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart me-2"></i>Orders</h2>
    </div>
    
    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" class="d-flex">
                <input type="text" class="form-control me-2" name="search" 
                       placeholder="Search by customer name, email, or order ID..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <select class="form-select me-2" name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>Processing</option>
                    <option value="shipped" <?php echo $status_filter === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                    <h5>No orders found</h5>
                    <p class="text-muted">No orders match your current filters.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo $order['total_items'] ?? 0; ?> items</td>
                                    <td><strong><?php echo format_price($order['total_amount']); ?></strong></td>
                                    <td>
                                        <?php
                                        $badge_class = [
                                            'pending' => 'warning',
                                            'processing' => 'primary',
                                            'shipped' => 'info',
                                            'delivered' => 'success',
                                            'cancelled' => 'danger'
                                        ];
                                        $class = $badge_class[$order['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $class; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="orders.php?action=view&id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="View Order">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($action === 'view' && $order): ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-receipt me-2"></i>Order #<?php echo $order['id']; ?></h2>
        <a href="orders.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Orders
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
           <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-box me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($order_items)): ?>
                        <p class="text-muted">No items found for this order.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td><?php echo $item['product_id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($item['image_url']): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                            alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                            class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td><?php echo format_price($item['price']); ?></td>
                                            <td><strong><?php echo format_price($item['price'] * $item['quantity']); ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        
        <div class="col-lg-4">
            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Order Details</h5>
                </div>
                <div class="card-body">
                    <p><strong>Order Date:</strong><br><?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                    <p><strong>Total Amount:</strong><br><?php echo format_price($order['total_amount']); ?></p>
                    <p><strong>Current Status:</strong><br>
                        <?php
                        $badge_class = [
                            'pending' => 'warning',
                            'processing' => 'primary', 
                            'shipped' => 'info',
                            'delivered' => 'success',
                            'cancelled' => 'danger'
                        ];
                        $class = $badge_class[$order['status']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?php echo $class; ?> fs-6">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong><br><?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Number:</strong><br><?php echo htmlspecialchars($order['customer_number']); ?></p>
                    <p><strong>Email:</strong><br><?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>City:</strong><br><?php echo htmlspecialchars($order['customer_city']); ?></p>
                    <p><strong>Postal Code:</strong><br><?php echo htmlspecialchars($order['customer_postal_code']); ?></p>
                    <p><strong>Shipping Address:</strong><br><?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                </div>
            </div>
            
            <!-- Update Status -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit me-2"></i>Update Status</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="mb-3">
                            <label for="status" class="form-label">New Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="text-center py-5">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h5>Order Not Found</h5>
        <p class="text-muted">The requested order could not be found.</p>
        <a href="orders.php" class="btn btn-primary">Back to Orders</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
