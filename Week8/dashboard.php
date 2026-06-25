<?php

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Fetch Stats for the Top Cards
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalStock = $pdo->query("SELECT COALESCE(SUM(stock_quantity),0) FROM books")->fetchColumn();
$ordersToday = $pdo->query("SELECT COUNT(*) FROM requisitions WHERE DATE(date_submitted) = CURDATE()")->fetchColumn();
// Assuming each requisition item has a quantity and price;
$estRevenue = $pdo->query("SELECT COALESCE(SUM(ri.quantity_requested * b.price), 0) FROM requisition_items ri JOIN books b ON ri.book_id = b.id")->fetchColumn();

// Removed the books fetch query since the table is no longer on this page

// Determine active navigation link
$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Decorum</title>
  <link rel="stylesheet" href="css/style.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar">
    <!-- Increased size and centered the Decorum Admin text -->
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
    <!-- Modified Topbar to include the Register User button -->
    <div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding-right: 40px;">
      <!-- Increased the main heading size -->
      <h2 style="font-size: 2.2rem; margin: 0;">Dashboard Overview</h2>
      <!-- New Quick Action Button -->
      <a href="users.php" class="btn btn-primary" style="padding: 12px 24px; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">+ Add System User</a>
    </div>

    <!-- Centered flexbox wrapper taking up most of the screen height -->
    <div class="page-body" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 75vh; padding: 40px;">
      
      <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" style="width: 100%; max-width: 1000px; font-size: 1.2rem; text-align: center; margin-bottom: 30px;"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>
      <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger" style="width: 100%; max-width: 1000px; font-size: 1.2rem; text-align: center; margin-bottom: 30px;"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>

      <!-- Massive 2x2 Grid for the Stat Cards -->
      <div class="stats-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; width: 100%; max-width: 1000px;">
        
        <div class="stat-card" style="padding: 50px 30px; text-align: center; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff;">
          <h3 style="font-size: 1.6rem; color: #64748B; margin-bottom: 15px;">Total Books</h3>
          <div class="value" style="font-size: 4rem; color: #1E3A8A; font-weight: 800;"><?php echo number_format($totalBooks); ?></div>
        </div>
        
        <div class="stat-card" style="padding: 50px 30px; text-align: center; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff;">
          <h3 style="font-size: 1.6rem; color: #64748B; margin-bottom: 15px;">Total Stock</h3>
          <div class="value" style="font-size: 4rem; color: #1E3A8A; font-weight: 800;"><?php echo number_format($totalStock); ?></div>
        </div>
        
        <div class="stat-card" style="padding: 50px 30px; text-align: center; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff;">
          <h3 style="font-size: 1.6rem; color: #64748B; margin-bottom: 15px;">Orders Today</h3>
          <div class="value" style="font-size: 4rem; color: #1E3A8A; font-weight: 800;"><?php echo number_format($ordersToday); ?></div>
        </div>
        
        <div class="stat-card red-accent" style="padding: 50px 30px; text-align: center; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: #fff; border-bottom: 6px solid #EF4444;">
          <h3 style="font-size: 1.6rem; color: #64748B; margin-bottom: 15px;">Est. Revenue</h3>
          <div class="value" style="font-size: 4rem; color: #EF4444; font-weight: 800;">Ksh <?php echo number_format($estRevenue); ?></div>
        </div>

      </div>

    </div>
  </main>
</div>
</body>
</html>