<?php

session_start();
session_destroy(); //reset all session data, including login attempts and lockout timers, Change Before deployment 
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
  // 1. Initialize attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    // Calculate minutes left
    $minutes_left = ceil(($_SESSION['lockout_time'] - time()) / 60);
    die("<div style='text-align:center; padding: 50px; font-family: Arial;'><h2>Account Locked</h2><p>Too many failed attempts. Please try again in $minutes_left minute(s).</p></div>");
}

$error_message = '';
$username_value = '';


    
    // 3. NEW: If they hit 5 attempts, set a 5-minute penalty timer
    if ($_SESSION['login_attempts'] >= 5) {
        $_SESSION['lockout_time'] = time() + (2 * 60); // Current time + 2 minutes
        die("<div style='text-align:center; padding: 50px; font-family: Arial;'><h2>Account Locked</h2><p>Too many failed attempts. Please try again in 2 minutes.</p></div>");
    }

    // 2. Sanitize and validate input
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
    }}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Decorum Bookshop - Login</title>
  <link rel="stylesheet" href="css/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

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