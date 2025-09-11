<?php
require_once __DIR__ . '/includes/auth_check.php';
$page_title = 'Blog Posts';
require_once 'includes/header.php';
require_once '../includes/csrf.php';

$action = $_GET['action'] ?? 'list';
$post_id = $_GET['id'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('CSRF token verification failed');
    }
    
    if (isset($_POST['action'])) {
        $form_action = $_POST['action'];
        
        if ($form_action === 'add' || $form_action === 'edit') {
            $title = sanitize_input($_POST['title']);
            $slug = generate_slug($title);
            $content = sanitize_input($_POST['content']);
            $excerpt = sanitize_input($_POST['excerpt']);
            $image_url = sanitize_input($_POST['image_url']);
            $published = isset($_POST['published']) ? 1 : 0;
            
            if ($form_action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, content, excerpt, image_url, published) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $excerpt, $image_url, $published]);
                $_SESSION['success'] = 'Blog post added successfully!';
            } else {
                $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, slug = ?, content = ?, excerpt = ?, image_url = ?, published = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$title, $slug, $content, $excerpt, $image_url, $published, $post_id]);
                $_SESSION['success'] = 'Blog post updated successfully!';
            }
            
            header('Location: blog.php');
            exit;
        } elseif ($form_action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $_SESSION['success'] = 'Blog post deleted successfully!';
            header('Location: blog.php');
            exit;
        }
    }
}

// Get blog post for editing
$post = null;
if ($action === 'edit' && $post_id) {
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    
    if (!$post) {
        header('Location: blog.php');
        exit;
    }
}

// Get all blog posts for listing
if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
    $blog_posts = $stmt->fetchAll();
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
        <h1 class="h2 text-vintage-brown">Blog Posts</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="blog.php?action=add" class="btn btn-vintage">
                <i class="fas fa-plus me-2"></i>Add New Post
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (empty($blog_posts)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <h4>No blog posts yet</h4>
                    <p class="text-muted">Start sharing vintage fashion insights!</p>
                    <a href="blog.php?action=add" class="btn btn-vintage">Write Your First Post</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($blog_posts as $blog_post): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($blog_post['title']); ?></strong>
                                        <?php if ($blog_post['excerpt']): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($blog_post['excerpt'], 0, 100)); ?>...</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $blog_post['published'] ? 'success' : 'secondary'; ?>">
                                            <?php echo $blog_post['published'] ? 'Published' : 'Draft'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($blog_post['created_at'])); ?></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <?php if ($blog_post['published']): ?>
                                                <a href="../blog-post.php?slug=<?php echo $blog_post['slug']; ?>" 
                                                   class="btn btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="blog.php?action=edit&id=<?php echo $blog_post['id']; ?>" 
                                               class="btn btn-outline-secondary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-outline-danger" 
                                                    onclick="deleteBlogPost(<?php echo $blog_post['id']; ?>)">
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
        <h1 class="h2 text-vintage-brown"><?php echo $action === 'add' ? 'Add New Blog Post' : 'Edit Blog Post'; ?></h1>
        <a href="blog.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Blog Posts
        </a>
    </div>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="" class="admin-form">
                        <input type="hidden" name="action" value="<?php echo $action; ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Post Title *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="excerpt" class="form-label">Excerpt</label>
                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3"
                                      placeholder="Brief description for the blog listing..."><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content *</label>
                            <textarea class="form-control" id="content" name="content" rows="12" required><?php echo htmlspecialchars($post['content'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image_url" class="form-label">Featured Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" 
                                   value="<?php echo htmlspecialchars($post['image_url'] ?? ''); ?>"
                                   placeholder="https://example.com/image.jpg">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="published" name="published" 
                                       <?php echo (isset($post) && $post['published']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="published">
                                    Publish Post
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-vintage">
                                <i class="fas fa-save me-2"></i><?php echo $action === 'add' ? 'Create Post' : 'Update Post'; ?>
                            </button>
                            <a href="blog.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6><i class="fas fa-lightbulb me-2"></i>Blog Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Write engaging headlines</li>
                        <li><i class="fas fa-check text-success me-2"></i>Include vintage fashion insights</li>
                        <li><i class="fas fa-check text-success me-2"></i>Add high-quality images</li>
                        <li><i class="fas fa-check text-success me-2"></i>Keep content relevant to your audience</li>
                        <li><i class="fas fa-check text-success me-2"></i>Use excerpts to entice readers</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function deleteBlogPost(postId) {
    if (confirm('Are you sure you want to delete this blog post? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="delete">
            <?php echo csrf_field(); ?>
        `;
        form.action = `blog.php?id=${postId}`;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>