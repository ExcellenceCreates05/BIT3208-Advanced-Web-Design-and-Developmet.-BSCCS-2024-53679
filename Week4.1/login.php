<?php
// login.php
session_start();
require_once __DIR__ . '/includes/db_connect.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'dashboard.php' : 'index.php'));
    exit;
}

if (!isset($_SESSION['login_attempts'])) { $_SESSION['login_attempts'] = 0; }
$error_message = '';
$username_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['login_attempts'] >= 5) { die("Account locked."); }

    $username = trim(strip_tags($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');
    $username_value = htmlspecialchars($username);

    if (empty($username) || empty($password)) {
        $error_message = 'Both fields are required.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password, role, full_name FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        $password_valid = false;
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $password_valid = true;
        }

        if ($password_valid) {
            session_regenerate_id(true);
            $_SESSION['login_attempts'] = 0;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            header('Location: ' . ($user['role'] === 'admin' ? 'dashboard.php' : 'index.php'));
            exit;
        } else {
            $_SESSION['login_attempts']++;
            $error_message = 'Invalid credentials. Attempts remaining: ' . (5 - $_SESSION['login_attempts']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Decorum Bookshop - Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-bg">
  <div class="login-card">
    <h1>Decorum Bookshop</h1>
    <?php if ($error_message): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $username_value; ?>" required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Login to System</button>
    </form>
  </div>
</body>
</html>