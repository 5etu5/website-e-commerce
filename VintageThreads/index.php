<?php
$page_title = 'Home';
require_once 'includes/header.php';

// Get featured products and new arrivals
$featured_products = get_products($pdo, null, 8, true);
$new_arrivals = get_products($pdo, null, 8);
$blog_posts = get_blog_posts($pdo, true, 3);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="fade-in">Vintage Threads</h1>
            <p class="fade-in">Discover authentic vintage clothing with stories to tell</p>
            <a href="category.php?slug=new-arrivals" class="btn btn-vintage btn-lg fade-in">
                Shop New Arrivals
            </a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 text-vintage-brown">Shop by Category</h2>
            <p class="lead text-muted">Find your perfect vintage piece</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="product-image">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="product-title"><?php echo htmlspecialchars($category['name']); ?></h5>
                            <p class="text-muted small"><?php echo htmlspecialchars($category['description']); ?></p>
                            <a href="category.php?slug=<?php echo $category['slug']; ?>" class="btn btn-outline-primary">
                                Browse <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<?php if (!empty($featured_products)): ?>
<section class="py-5 bg-vintage-cream">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 text-vintage-brown">Featured Items</h2>
            <p class="lead text-muted">Hand-picked vintage treasures</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="product-image">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="img-fluid">
                            <?php else: ?>
                                <i class="fas fa-tshirt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h5>
                            <div class="product-price">
                                <?php echo $product['price']; ?>
                            </div>
                            <?php if ($product['condition_notes']): ?>
                                <div class="product-condition">
                                    <?php echo htmlspecialchars($product['condition_notes']); ?>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="category.php?slug=all" class="btn btn-vintage">View All Products</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- New Arrivals -->
<?php if (!empty($new_arrivals)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 text-vintage-brown">New Arrivals</h2>
            <p class="lead text-muted">Fresh vintage finds just in</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($new_arrivals as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="product-image">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                     class="img-fluid">
                            <?php else: ?>
                                <i class="fas fa-tshirt"></i>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">
                                <a href="product.php?slug=<?php echo $product['slug']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h5>
                            <div class="product-price">
                                <?php echo $product['price']; ?>
                            </div>
                            <?php if ($product['condition_notes']): ?>
                                <div class="product-condition">
                                    <?php echo htmlspecialchars($product['condition_notes']); ?>
                                </div>
                            <?php endif; ?>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product['id']; ?>">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Preview -->
<?php if (!empty($blog_posts)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 text-vintage-brown">Latest from the Blog</h2>
            <p class="lead text-muted">Vintage fashion insights and style guides</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($blog_posts as $post): ?>
                <div class="col-lg-4">
                    <div class="card blog-card h-100">
                        <div class="blog-image">
                            <?php if ($post['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     class="img-fluid">
                            <?php else: ?>
                                <i class="fas fa-newspaper"></i>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars($post['excerpt']); ?>
                            </p>
                            <small class="text-muted">
                                <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="blog.php" class="btn btn-outline-primary">Read More Articles</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Newsletter Section -->
<section class="py-5 bg-vintage-cream">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h3 class="text-vintage-brown">Stay in the Loop</h3>
                <p class="text-muted">Get notified about new vintage arrivals and exclusive offers</p>
            </div>
            <div class="col-lg-6">
                <form class="d-flex">
                    <input type="email" class="form-control me-2" placeholder="Enter your email">
                    <button type="submit" class="btn btn-vintage">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
