<?php
require_once __DIR__ . '/includes/auth_check.php';

$page_title = 'Dashboard';
require_once 'includes/header.php';

// Get stats
$stats = [];

// Total products
$stmt = $pdo->query("SELECT COUNT(*) as total FROM products");
$stats['products'] = $stmt->fetch()['total'];

// Total orders
$stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
$stats['orders'] = $stmt->fetch()['total'];

// Total revenue
$stmt = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status != 'cancelled'");
$stats['revenue'] = $stmt->fetch()['total'];

// Recent orders
$stmt = $pdo->prepare("SELECT o.*, COUNT(oi.id) as item_count 
                      FROM orders o 
                      LEFT JOIN order_items oi ON o.id = oi.order_id 
                      GROUP BY o.id 
                      ORDER BY o.created_at DESC LIMIT 5");
$stmt->execute();
$recent_orders = $stmt->fetchAll();

// Low stock products
$stmt = $pdo->query("SELECT * FROM products WHERE stock_quantity <= 5 ORDER BY stock_quantity ASC LIMIT 5");
$low_stock = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2 text-vintage-brown">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-download me-1"></i>Export
            </button>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo number_format($stats['products']); ?></h4>
                        <p class="card-text">Total Products</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tshirt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo number_format($stats['orders']); ?></h4>
                        <p class="card-text">Total Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">$<?php echo number_format($stats['revenue'], 2); ?></h4>
                        <p class="card-text">Total Revenue</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title"><?php echo count($low_stock); ?></h4>
                        <p class="card-text">Low Stock Items</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h5>
                <a href="orders.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_orders)): ?>
                    <p class="text-muted">No orders yet.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td><?php echo $order['item_count']; ?> items</td>
                                        <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $order['status'] == 'completed' ? 'success' : ($order['status'] == 'pending' ? 'warning' : 'secondary'); ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Low Stock Products -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-exclamation-triangle me-2"></i>Low Stock Alert</h5>
                <a href="products.php" class="btn btn-sm btn-outline-warning">Manage</a>
            </div>
            <div class="card-body">
                <?php if (empty($low_stock)): ?>
                    <p class="text-muted">All products are well stocked!</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($low_stock as $product): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h6>
                                    <small class="text-muted">Stock: <?php echo $product['stock_quantity']; ?></small>
                                </div>
                                <span class="badge bg-warning rounded-pill"><?php echo $product['stock_quantity']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="products.php?action=add" class="btn btn-vintage w-100">
                            <i class="fas fa-plus me-2"></i>Add New Product
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="blog.php?action=add" class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-2"></i>Write Blog Post
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="orders.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-eye me-2"></i>View Orders
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="../index.php" target="_blank" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-external-link-alt me-2"></i>View Store
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>