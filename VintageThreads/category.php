<?php
require_once 'config/database.php'; // $pdo
require_once 'includes/functions.php';

// Get category by slug
$category_slug = $_GET['slug'] ?? '';

// Handle "all products"
if ($category_slug === 'all') {
    $category = ['id' => null, 'name' => 'All Products', 'description' => 'Browse all our vintage treasures'];
} else {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = :slug");
    $stmt->bindParam(':slug', $category_slug);
    $stmt->execute();
    $category = $stmt->fetch();

    if (!$category) {
        header('Location: index.php');
        exit;
    }
}

// Build filter query
$where = '';
$params = [];

if (!empty($category['id'])) {
    $where .= 'category_id = :category_id';
    $params[':category_id'] = $category['id'];
}

// Price filter
if (!empty($_GET['price_range'])) {
    $price_range = $_GET['price_range'];
    switch ($price_range) {
        case '0-25': $where .= ($where ? ' AND ' : '') . 'price BETWEEN 0 AND 25'; break;
        case '25-50': $where .= ($where ? ' AND ' : '') . 'price BETWEEN 25 AND 50'; break;
        case '50-100': $where .= ($where ? ' AND ' : '') . 'price BETWEEN 50 AND 100'; break;
        case '100-200': $where .= ($where ? ' AND ' : '') . 'price BETWEEN 100 AND 200'; break;
        case '200+': $where .= ($where ? ' AND ' : '') . 'price > 200'; break;
    }
}

// Condition filter
if (!empty($_GET['condition'])) {
    $conditions = $_GET['condition']; // array
    $placeholders = [];
    foreach ($conditions as $idx => $cond) {
        $key = ":cond$idx";
        $placeholders[] = $key;
        $params[$key] = $cond;
    }
    if ($placeholders) {
        $where .= ($where ? ' AND ' : '') . "condition_notes IN (" . implode(',', $placeholders) . ")";
    }
}

// Sorting
$order = 'created_at DESC'; // default
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_low': $order = 'price ASC'; break;
        case 'price_high': $order = 'price DESC'; break;
        case 'name': $order = 'name ASC'; break;
    }
}

// Final query
$sql = 'SELECT * FROM products';
if ($where) $sql .= ' WHERE ' . $where;
$sql .= ' ORDER BY ' . $order;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="category-header">
    <div class="container">
        <h1 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($category['description']); ?></p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-3 mb-4">
            <!-- Filters Sidebar -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="">
                        <input type="hidden" name="slug" value="<?php echo htmlspecialchars($category_slug); ?>">

                        <div class="mb-3">
                            <label class="form-label">Price Range</label>
                            <select name="price_range" class="form-select">
                                <option value="">All Prices</option>
                                <option value="0-25" <?php if($_GET['price_range']??''==='0-25') echo 'selected'; ?>>Under $25</option>
                                <option value="25-50" <?php if($_GET['price_range']??''==='25-50') echo 'selected'; ?>>$25 - $50</option>
                                <option value="50-100" <?php if($_GET['price_range']??''==='50-100') echo 'selected'; ?>>$50 - $100</option>
                                <option value="100-200" <?php if($_GET['price_range']??''==='100-200') echo 'selected'; ?>>$100 - $200</option>
                                <option value="200+" <?php if($_GET['price_range']??''==='200+') echo 'selected'; ?>>$200+</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Condition</label>
                            <?php 
                            $conds = $_GET['condition'] ?? [];
                            $cond_options = ['mint'=>'Mint Condition','excellent'=>'Excellent','good'=>'Good'];
                            foreach($cond_options as $key=>$label): 
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="condition[]" value="<?php echo $key; ?>" id="<?php echo $key; ?>" <?php if(in_array($key,$conds)) echo 'checked'; ?>>
                                    <label class="form-check-label" for="<?php echo $key; ?>"><?php echo $label; ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Sort By</label>
                            <select name="sort" class="form-select">
                                <option value="newest" <?php if(($_GET['sort']??'')==='newest') echo 'selected'; ?>>Newest First</option>
                                <option value="price_low" <?php if(($_GET['sort']??'')==='price_low') echo 'selected'; ?>>Price: Low to High</option>
                                <option value="price_high" <?php if(($_GET['sort']??'')==='price_high') echo 'selected'; ?>>Price: High to Low</option>
                                <option value="name" <?php if(($_GET['sort']??'')==='name') echo 'selected'; ?>>Name A-Z</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-vintage w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <!-- Products Grid -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <?php echo htmlspecialchars($category['name']); ?>
                    <span class="badge bg-secondary"><?php echo count($products); ?> items</span>
                </h2>

                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary active" id="grid-view">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="list-view">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h3>No products found</h3>
                    <p class="text-muted">Check back soon for new arrivals in this category!</p>
                    <a href="index.php" class="btn btn-vintage">Browse All Categories</a>
                </div>
            <?php else: ?>
                <div class="row g-4" id="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card product-card h-100">
                                <div class="product-image">
                                    <?php if ($product['image_url']): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid">
                                    <?php else: ?>
                                        <i class="fas fa-tshirt"></i>
                                    <?php endif; ?>

                                    <!-- Quick view overlay -->
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <a href="product.php?slug=<?php echo $product['slug']; ?>" 
                                           class="btn btn-sm btn-light rounded-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="product-info">
                                    <h5 class="product-title">
                                        <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($product['name']); ?>
                                        </a>
                                    </h5>
                                    <div class="product-price">
                                        <?php echo format_price($product['price']); ?>
                                    </div>
                                    <?php if ($product['condition_notes']): ?>
                                        <div class="product-condition">
                                            <?php echo htmlspecialchars($product['condition_notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex gap-2 mt-3">
                                        <button class="btn btn-primary flex-fill add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                        <button class="btn btn-outline-secondary wishlist-btn" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-heart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
