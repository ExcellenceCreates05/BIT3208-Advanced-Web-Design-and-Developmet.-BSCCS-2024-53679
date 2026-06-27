<?php
// edit_user.php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin('admin');

$error_message = '';
$user_id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$user_id) {
    header("Location: users.php");
    exit;
}

// --- HANDLE FORM SUBMISSION (UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(strip_tags($_POST['username']));
    $full_name = trim(strip_tags($_POST['full_name']));
    $role = $_POST['role'];
    $new_password = $_POST['password'] ?? '';

    if (empty($username) || empty($full_name)) {
        $error_message = 'Name and Username are required.';
    } else {
        try {
            // Check if they are resetting the password
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, role = ?, password = ? WHERE id = ?");
                $stmt->execute([$full_name, $username, $role, $hashed_password, $user_id]);
            } else {
                // Update without touching the password
                $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, role = ? WHERE id = ?");
                $stmt->execute([$full_name, $username, $role, $user_id]);
            }
            
            $_SESSION['flash_success'] = "User account updated successfully!";
            header("Location: users.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

// --- FETCH CURRENT DATA FOR THE FORM (READ) ---
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();

if (!$current_user) {
    header("Location: users.php");
    exit;
}

$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<?php
$page_title = 'Edit User - Decorum Admin';
$page_heading = 'User Management: Edit Account';
include 'includes/master_header.php';
?>

      <div class="table-card" style="max-width: 600px; margin: 0 auto; padding: 30px;">
        <h3 style="margin-bottom: 20px; color: var(--primary-blue);">Update System User</h3>
        
        <?php if ($error_message): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
          
        <form action="edit_user.php" method="POST">
          <input type="hidden" name="id" value="<?php echo $current_user['id']; ?>">
          
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required>
          </div>
          
          <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($current_user['username']); ?>" required>
          </div>
          
          <div style="display: flex; gap: 15px;">
              <div class="form-group" style="flex: 1;">
                <label>Reset Password (Leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
              </div>
              
              <div class="form-group" style="flex: 1;">
                <label>System Role</label>
                <select name="role" class="form-control" required>
                    <option value="manager" <?php echo $current_user['role'] === 'manager' ? 'selected' : ''; ?>>Branch Manager</option>
                    <option value="admin" <?php echo $current_user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                </select>
              </div>
          </div>
          
          <div style="display: flex; gap: 10px; margin-top: 20px;">
              <a href="users.php" class="btn btn-outline">Cancel</a>
              <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>

  </div> <!-- Close page-body -->
</main> <!-- Close main-content -->
</div> <!-- Close app-wrapper -->

<script src="js/app.js"></script>
</body>
</html>