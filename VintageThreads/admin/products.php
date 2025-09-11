<?php
require_once __DIR__ . '/includes/auth_check.php';
$page_title = 'Products';
require_once 'includes/header.php';
require_once '../includes/csrf.php';

$action = $_GET['action'] ?? 'list';
$product_id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('CSRF token verification failed');
    }
    
    if (isset($_POST['action'])) {
        $form_action = $_POST['action'];
        
        $name = sanitize_input($_POST['name']);
        $slug = generate_slug($name);
        $description = sanitize_input($_POST['description']);
        $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
        $category_id = filter_var($_POST['category_id'], FILTER_VALIDATE_INT);
        $condition_notes = sanitize_input($_POST['condition_notes']);
        $measurements = sanitize_input($_POST['measurements']);
        $image_url = sanitize_input($_POST['image_url']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        // Sizes columns
        $size_columns = ['stock_S','stock_M','stock_L','stock_XL']; // adjust if you have more
        $size_values = [];
        foreach ($size_columns as $col) {
            $size_values[$col] = filter_var($_POST[$col] ?? 0, FILTER_VALIDATE_INT);
        }

        if ($form_action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO products (name, slug, description, price, category_id, condition_notes, measurements, image_url, is_featured, stock_S, stock_M, stock_L, stock_XL) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $slug, $description, $price, $category_id, $condition_notes, $measurements, $image_url, $is_featured,
                $size_values['stock_S'], $size_values['stock_M'], $size_values['stock_L'], $size_values['stock_XL']
            ]);
            $_SESSION['success'] = 'Product added successfully!';
        } elseif ($form_action === 'edit') {
            $stmt = $pdo->prepare("UPDATE products SET name=?, slug=?, description=?, price=?, category_id=?, condition_notes=?, measurements=?, image_url=?, is_featured=?, stock_S=?, stock_M=?, stock_L=?, stock_XL=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
            $stmt->execute([
                $name, $slug, $description, $price, $category_id, $condition_notes, $measurements, $image_url, $is_featured,
                $size_values['stock_S'], $size_values['stock_M'], $size_values['stock_L'], $size_values['stock_XL'],
                $product_id
            ]);
            $_SESSION['success'] = 'Product updated successfully!';
        } elseif ($form_action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $_SESSION['success'] = 'Product deleted successfully!';
        }

        header('Location: products.php');
        exit;
    }
}

// Get categories for form
$categories = get_categories($pdo);

// Get product for editing
$product = null;
if ($action === 'edit' && $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    if (!$product) {
        header('Location: products.php');
        exit;
    }
}

// Get all products for listing
if ($action === 'list') {
    $products = get_products($pdo);
}

?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($action === 'list'): ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 text-vintage-brown">Products</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="products.php?action=add" class="btn btn-vintage">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-tshirt fa-3x text-muted mb-3"></i>
                    <h4>No products yet</h4>
                    <p class="text-muted">Start building your vintage collection!</p>
                    <a href="products.php?action=add" class="btn btn-vintage">Add Your First Product</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Featured</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <div style="width:50px;height:50px;background:var(--vintage-cream);border-radius:5px;display:flex;align-items:center;justify-content:center;">
                                            <?php if ($product['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     style="width:100%;height:100%;object-fit:cover;border-radius:5px;">
                                            <?php else: ?>
                                                <i class="fas fa-tshirt text-muted"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        <?php if ($product['condition_notes']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($product['condition_notes']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td>$<?php echo number_format($product['price'], 2); ?></td>
                                    <td>
                                        <?php
                                        $size_columns = ['stock_S','stock_M','stock_L','stock_XL'];
                                        foreach ($size_columns as $col):
                                            $stock = $product[$col];
                                            $size_name = strtoupper(str_replace('stock_', '', $col));
                                        ?>
                                            <span class="badge bg-<?php echo $stock <= 5 ? 'warning' : 'success'; ?>">
                                                <?php echo $size_name . ': ' . $stock; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['is_featured']): ?>
                                            <i class="fas fa-star text-warning"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="../product.php?slug=<?php echo $product['slug']; ?>" class="btn btn-outline-primary" target="_blank">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($action === 'add' || $action === 'edit'): ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 text-vintage-brown"><?php echo $action === 'add' ? 'Add New Product' : 'Edit Product'; ?></h1>
        <a href="products.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Products
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" class="admin-form">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" class="form-label">Category *</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo (isset($product) && $product['category_id']==$category['id'])?'selected':''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="price" class="form-label">Price *</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                       value="<?php echo $product['price'] ?? ''; ?>" required>
                            </div>

                            <!-- Stock per size -->
                            <?php
                            $size_columns = ['stock_S','stock_M','stock_L','stock_XL'];
                            foreach ($size_columns as $col):
                                $quantity = $product[$col] ?? 0;
                                $size_name = strtoupper(str_replace('stock_', '', $col));
                            ?>
                                <div class="col-md-2 mb-2">
                                    <label><?php echo $size_name; ?></label>
                                    <input type="number" name="<?php echo $col; ?>" class="form-control" min="0" value="<?php echo $quantity; ?>">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label for="condition_notes" class="form-label">Condition Notes</label>
                            <input type="text" class="form-control" id="condition_notes" name="condition_notes" 
                                   value="<?php echo htmlspecialchars($product['condition_notes'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="measurements" class="form-label">Measurements</label>
                            <textarea class="form-control" id="measurements" name="measurements" rows="3"><?php echo htmlspecialchars($product['measurements'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($product['image_url'] ?? ''); ?>">
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   <?php echo (isset($product) && $product['is_featured'])?'checked':''; ?>>
                            <label class="form-check-label" for="is_featured">Featured Product</label>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-vintage">
                                <i class="fas fa-save me-2"></i><?php echo $action==='add'?'Add Product':'Update Product'; ?>
                            </button>
                            <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <?php echo csrf_field(); ?>
        `;
        form.action = `products.php?id=${productId}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>
