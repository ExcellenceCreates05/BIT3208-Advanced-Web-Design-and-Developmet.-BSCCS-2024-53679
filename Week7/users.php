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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management - Decorum Admin</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar">
    <div class="sidebar-brand" style="font-size: 1.8rem; padding: 30px 20px; text-align: center; line-height: 1.3;">Decorum Admin</div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php" class="<?php echo $is_active('dashboard.php'); ?>">Dashboard</a></li>
        <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
        <li><a href="#">Categories</a></li>
        <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">Orders</a></li>
        <li><a href="users.php" class="<?php echo $is_active('users.php'); ?>">Users</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding-right: 40px;">
      <h2 style="font-size: 2.2rem; margin: 0;">System Users</h2>
      <!-- Here is the Add User button connecting to our form! -->
      <a href="register.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">+ Add New User</a>
    </div>

    <div class="page-body">
      
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
              <td><strong><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
              <td><?php echo htmlspecialchars($u['username']); ?></td>
              <td>
                <!-- Visual Role Badges -->
                <?php if($u['role'] === 'admin'): ?>
                    <span style="background: #DBEAFE; color: #1E3A8A; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Administrator</span>
                <?php else: ?>
                    <span style="background: #E0E7FF; color: #4338CA; padding: 4px 8px; border-radius: 4px; font-size: 12px;">Branch Manager</span>
                <?php endif; ?>
              </td>
              <td>
                <div style="display: flex; gap: 5px;">
                    <!-- Fixed routing for Edit -->
                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <!-- Fixed routing for Delete -->
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
</body>
</html>