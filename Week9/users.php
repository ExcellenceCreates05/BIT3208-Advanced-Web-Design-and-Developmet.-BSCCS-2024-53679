<?php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

// STRICT SECURITY: Only logged-in Admins can view the user list
requireLogin('admin');

// Fetch all users from the database, sorted by Role and then Name
$users = $pdo->query("SELECT id, username, full_name, role FROM users ORDER BY role ASC, full_name ASC")->fetchAll();

$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<?php
// Central header
$page_title = 'User Management - Decorum Admin';
$page_heading = 'System Users';
$topbar_actions = '<a href="register_user_form.php" class="btn btn-primary">+ Add New User</a>';
include 'includes/master_header.php';
?>

<?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>

      <div class="table-card">
        <div class="table-header">
          <div class="search-bar">
            <input type="text" class="form-control" placeholder="Search users by name or branch...">
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Full Name</th>
              <th>Username (Branch)</th>
              <th>System Role</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <!-- STEP 2: The data-label attributes are added here! -->
              <td data-label="Full Name"><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
              <td data-label="Username (Branch)"><?php echo htmlspecialchars($u['username']); ?></td>
              <td data-label="System Role">
                <!-- Visual Role Badges -->
                <?php if($u['role'] === 'admin'): ?>
                    <span style="background: #DBEAFE; color: #1E3A8A; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Administrator</span>
                <?php else: ?>
                    <span style="background: #E0E7FF; color: #4338CA; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Branch Manager</span>
                <?php endif; ?>
              </td>
              <td data-label="Action">
                <div style="display: flex; gap: 5px; justify-content: flex-end;">
                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Delete this user?');" style="margin:0;">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <button type="submit" class="btn btn-danger-text" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                    </form>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<!-- Link the global JavaScript to make the Hamburger menu work -->
 
<script src="js/app.js"></script>
</body>
</html>