<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

$error_message = '';
$user_id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$user_id) { header("Location: users.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim(strip_tags($_POST['username']));
    $full_name    = trim(strip_tags($_POST['full_name']));
    $role         = $_POST['role'];
    $new_password = $_POST['password'] ?? '';

    if (empty($username) || empty($full_name)) {
        $error_message = 'Name and Username are required.';
    } else {
        try {
            if (!empty($new_password)) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, role=?, password=? WHERE id=?");
                $stmt->execute([$full_name, $username, $role, $hashed, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET full_name=?, username=?, role=? WHERE id=?");
                $stmt->execute([$full_name, $username, $role, $user_id]);
            }
            $_SESSION['flash_success'] = "User updated successfully!";
            header("Location: users.php");
            exit;
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$current_user = $stmt->fetch();
if (!$current_user) { header("Location: users.php"); exit; }
?>
<?php
$page_title   = 'Edit User — Decorum Admin';
$page_heading = 'Edit User Account';
include 'includes/master_header.php';
?>

<div class="table-card" style="max-width:620px; margin:0 auto; padding:28px;">
  <h3 style="margin-bottom:20px; color:var(--primary-blue);">Update System User</h3>

  <?php if ($error_message): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <form action="edit_user.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $current_user['id']; ?>">

    <div class="form-group">
      <label>Full Name</label>
      <input type="text" name="full_name" class="form-control"
             value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required>
    </div>
    <div class="form-group">
      <label>Username</label>
      <input type="text" name="username" class="form-control"
             value="<?php echo htmlspecialchars($current_user['username']); ?>" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>New Password <span style="font-weight:400;font-size:11px;">(leave blank to keep)</span></label>
        <input type="password" name="password" class="form-control" autocomplete="new-password">
      </div>
      <div class="form-group">
        <label>System Role</label>
        <select name="role" class="form-control" required>
          <option value="manager" <?php echo $current_user['role'] === 'manager' ? 'selected' : ''; ?>>Branch Manager</option>
          <option value="admin"   <?php echo $current_user['role'] === 'admin'   ? 'selected' : ''; ?>>Administrator</option>
        </select>
      </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;">
      <a href="users.php" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
  </form>
</div>

    </div><!-- /page-body -->
  </main>
</div>
<script src="js/app.js"></script>
</body>
</html>
