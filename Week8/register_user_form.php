<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

// Only admins can access
requireLogin('admin');

$error_message = '';
$success_message = '';
$username_value = '';
$fullname_value = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(strip_tags($_POST['username'] ?? ''));
    $full_name = trim(strip_tags($_POST['full_name'] ?? ''));
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'manager';

    $username_value = htmlspecialchars($username, ENT_QUOTES);
    $fullname_value = htmlspecialchars($full_name, ENT_QUOTES);

    if (empty($username) || empty($full_name) || empty($password)) {
        $error_message = 'All fields are required.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif (!in_array($role, ['admin', 'manager'])) {
        $error_message = 'Invalid role selected.';
    } else {
        // Check username uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error_message = 'Username is already taken. Please choose another.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            try {
                $insert = $pdo->prepare("INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");
                $insert->execute([$username, $full_name, $hashed_password, $role]);
                $success_message = 'New ' . ucfirst($role) . ' account created successfully!';
                $username_value = '';
                $fullname_value = '';
            } catch (PDOException $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

$page_title = 'Add New User - Decorum Admin';
$page_heading = 'User Management: Add Account';
include 'includes/master_header.php';
?>

      <div class="table-card" style="max-width: 600px; margin: 0 auto; padding: 30px;">
        <h3 style="margin-bottom: 20px; color: var(--primary-blue);">Create System User</h3>

        <?php if ($error_message): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="register_user_form.php" method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo $fullname_value; ?>" required>
          </div>

          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username_value; ?>" required>
          </div>

          <div style="display: flex; gap: 15px;">
              <div class="form-group" style="flex: 1;">
                <label>Password (Min 6 chars)</label>
                <input type="password" name="password" class="form-control" required>
              </div>
      
              <div class="form-group" style="flex: 1;">
                <label>System Role</label>
                <select name="role" class="form-control" required>
                    <option value="manager">Branch Manager</option>
                    <option value="admin">Administrator</option>
                </select>
              </div>
          </div>

          <div style="display: flex; gap: 10px; margin-top: 20px;">
              <a href="dashboard.php" class="btn btn-outline">Cancel</a>
              <button type="submit" class="btn btn-primary">Create User Account</button>
          </div>
        </form>
      </div>

  </div> <!-- Close page-body -->
</main> <!-- Close main-content -->
</div> <!-- Close app-wrapper -->

<script src="js/app.js"></script>
</body>
</html>