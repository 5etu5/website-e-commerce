<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin - Vintage Threads</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header class="admin-header">
        <div class="container-fluid">
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="dashboard.php">
                    <i class="fas fa-tshirt me-2"></i>Vintage Threads Admin
                </a>
                
                <div class="navbar-nav ms-auto">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="adminDropdown">
                            <li><a class="dropdown-item" href="../index.php">
                                <i class="fas fa-external-link-alt me-2"></i>View Store
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>
    
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 admin-sidebar">
                <div class="position-sticky">
                    <h6 class="text-muted text-uppercase fw-bold mb-3">Navigation</h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                                <i class="fas fa-chart-bar me-2"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? 'active' : ''; ?>" href="products.php">
                                <i class="fas fa-tshirt me-2"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? 'active' : ''; ?>" href="orders.php">
                                <i class="fas fa-shopping-cart me-2"></i>Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="admin-nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>" href="blog.php">
                                <i class="fas fa-newspaper me-2"></i>Blog Posts
                            </a>
                        </li>

                    </ul>
                </div>
            </nav>
            
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="py-4">