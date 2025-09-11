<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

// Initialize variables
$email = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF verification failed');

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = authenticate_user($pdo, $email, $password);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        $redirect = $_GET['redirect'] ?? '/VintageThreads/index.php';
        header('Location: ' . $redirect);
        exit;
    } else {
        $error = 'Invalid email or password';
    }
}

$page_title = 'Login';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid vh-100 d-flex align-items-center justify-content-center bg-vintage-cream">
    <div class="row w-100">
        <div class="col-lg-4 col-md-6 mx-auto">
            <div class="form-vintage p-4 shadow-sm rounded">
                <div class="text-center mb-4">
                    <h2 class="text-vintage-brown fw-bold">
                        <i class="fas fa-tshirt me-2"></i>Vintage Threads
                    </h2>
                    <h4>User Login</h4>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <?= csrf_field(); ?>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($email) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-vintage btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </div>

                    <div class="text-center">
                        <a href="/VintageThreads/auth/register.php">Create an account</a> | 
                        <a href="/VintageThreads/index.php">Back to Store</a>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <small class="text-muted">Demo account: user@example.com / password</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
