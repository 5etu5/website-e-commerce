<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database/functions if not already included
if (!isset($pdo)) {
    require_once __DIR__ . '/../config/database.php';
}
require_once __DIR__ . '/functions.php';

// Fetch categories safely
$categories = $categories ?? get_categories($pdo);

// Determine correct dashboard/account link based on role
$user_link = '/VintageThreads/auth/account.php';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $user_link = '/VintageThreads/admin/dashboard.php';
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Vintage Threads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/VintageThreads/assets/css/style.css" rel="stylesheet">
</head>
<body>
<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold fs-3" href="/VintageThreads/index.php">
                <i class="fas fa-tshirt me-2"></i>Vintage Threads
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" 
                           data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="categoriesDropdown">
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <a class="dropdown-item" href="/VintageThreads/category.php?slug=<?php echo $category['slug']; ?>">
                                        <?php echo $category['name']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="/VintageThreads/blog.php">Blog</a></li>
                </ul>

                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a class="btn btn-outline-primary me-3" href="<?= $user_link ?>">
                            <?= htmlspecialchars($_SESSION['user_name']) ?>
                        </a>
                        <a class="btn btn-outline-danger me-3" href="/VintageThreads/auth/logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-outline-primary me-3" href="/VintageThreads/auth/login.php">Login</a>
                        <a class="btn btn-primary me-3" href="/VintageThreads/auth/register.php">Register</a>
                    <?php endif; ?>

                    <!-- Cart -->
                    <a href="/VintageThreads/cart.php" class="btn btn-outline-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                            <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="py-4">
