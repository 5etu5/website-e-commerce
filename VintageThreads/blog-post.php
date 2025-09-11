<?php
require_once 'includes/header.php';

// Get blog post by slug
$post_slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND published = 1");
$stmt->execute([$post_slug]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: blog.php');
    exit;
}

$page_title = $post['title'];
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="blog.php">Blog</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($post['title']); ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <article class="blog-post">
                <header class="mb-4">
                    <h1 class="display-5 text-vintage-brown"><?php echo htmlspecialchars($post['title']); ?></h1>
                    <p class="text-muted">
                        <i class="fas fa-calendar me-2"></i>
                        <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </p>
                </header>
                
                <?php if ($post['image_url']): ?>
                    <div class="mb-4">
                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($post['title']); ?>"
                             class="img-fluid rounded">
                    </div>
                <?php endif; ?>
                
                <div class="blog-content">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
                
                <hr class="my-5">
                
                <div class="text-center">
                    <h5>Explore Our Vintage Collection</h5>
                    <p class="text-muted">Find unique pieces that tell their own stories</p>
                    <a href="index.php" class="btn btn-vintage me-2">Shop Now</a>
                    <a href="blog.php" class="btn btn-outline-primary">More Articles</a>
                </div>
            </article>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>