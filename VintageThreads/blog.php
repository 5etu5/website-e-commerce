<?php
$page_title = 'Blog';
require_once 'includes/header.php';

$blog_posts = get_blog_posts($pdo, true);
?>

<div class="category-header">
    <div class="container">
        <h1 class="category-title">Vintage Fashion Blog</h1>
        <p class="lead">Stories, style guides, and vintage fashion insights</p>
    </div>
</div>

<div class="container py-5">
    <?php if (empty($blog_posts)): ?>
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-5x text-muted mb-4"></i>
            <h3>No blog posts yet</h3>
            <p class="text-muted">Check back soon for vintage fashion insights and style guides!</p>
            <a href="index.php" class="btn btn-vintage">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($blog_posts as $post): ?>
                <div class="col-lg-4 col-md-6">
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
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                                </small>
                                <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" class="btn btn-outline-primary btn-sm">
                                    Read More
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>