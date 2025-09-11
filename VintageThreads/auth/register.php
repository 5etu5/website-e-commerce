<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/csrf.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('CSRF check failed');

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$name || !$email || !$password) {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } elseif (get_user_by_email($pdo, $email)) {
        $error = 'Email already registered.';
    } else {
        $user_id = create_user($pdo, $name, $email, $password);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $name;
        header('Location: ../index.php');
        exit;
    }
}

$page_title = 'Register';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="container py-5">
  <h2>Register</h2>
  <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <form method="post">
    <?= csrf_field(); ?>
    <div class="mb-3">
      <label>Name</label>
      <input class="form-control" name="name" required value="<?=htmlspecialchars($_POST['name'] ?? '')?>">
    </div>
    <div class="mb-3">
      <label>Email</label>
      <input class="form-control" type="email" name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
    </div>
    <div class="mb-3">
      <label>Password</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <div class="mb-3">
      <label>Confirm password</label>
      <input class="form-control" type="password" name="password2" required>
    </div>
    <button class="btn btn-primary">Create account</button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
