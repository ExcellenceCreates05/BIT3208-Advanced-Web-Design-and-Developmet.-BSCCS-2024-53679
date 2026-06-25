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
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar">
    <div class="sidebar-brand" style="font-size: 24px; padding: 25px 20px;">Decorum Admin</div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php" class="<?php echo $is_active('dashboard.php'); ?>">Dashboard</a></li>
        <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
        <li><a href="#">Categories</a></li>
        <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">Orders</a></li>
        <li><a href="#">Users</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <div class="topbar" style="padding: 25px 30px;">
      <h2 style="font-size: 28px;">Dashboard Overview</h2>
    </div>

    <div class="page-body" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; padding: 40px;">
      
      <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success" style="width: 100%; max-width: 1100px; box-sizing: border-box;"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>
      <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger" style="width: 100%; max-width: 1100px; box-sizing: border-box;"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>

      <div class="stats-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; width: 100%; max-width: 1100px; margin: 0 auto;">
        
        <div class="stat-card" style="padding: 40px; text-align: center; min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <h3 style="font-size: 20px; margin-bottom: 15px; color: #64748b;">Total Books</h3>
          <div class="value" style="font-size: 48px; font-weight: bold; color: #1e3a8a;"><?php echo number_format($totalBooks); ?></div>
        </div>
        
        <div class="stat-card" style="padding: 40px; text-align: center; min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <h3 style="font-size: 20px; margin-bottom: 15px; color: #64748b;">Total Stock</h3>
          <div class="value" style="font-size: 48px; font-weight: bold; color: #1e3a8a;"><?php echo number_format($totalStock); ?></div>
        </div>
        
        <div class="stat-card" style="padding: 40px; text-align: center; min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
          <h3 style="font-size: 20px; margin-bottom: 15px; color: #64748b;">Orders Today</h3>
          <div class="value" style="font-size: 48px; font-weight: bold; color: #1e3a8a;"><?php echo number_format($ordersToday); ?></div>
        </div>
        
        <div class="stat-card red-accent" style="padding: 40px; text-align: center; min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center; border-top: 5px solid #ef4444;">
          <h3 style="font-size: 20px; margin-bottom: 15px; color: #64748b;">Est. Revenue</h3>
          <div class="value" style="font-size: 48px; font-weight: bold; color: #ef4444;">Ksh <?php echo number_format($estRevenue); ?></div>
        </div>

      </div>

    </div>
  </main>
</div>
</body>
</html>