<?php
// includes/header.php
// Centralized layout so we never copy-paste the sidebar again!
$role = $_SESSION['role'] ?? 'manager';
$current_page = basename($_SERVER['PHP_SELF']);

$is_active = function($page) use ($current_page) {
    return $current_page === $page ? 'active' : '';
};

// Fallback titles if the page doesn't set them
$page_title = $page_title ?? "Decorum Bookshop";
$page_heading = $page_heading ?? "Dashboard";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title); ?></title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar" id="mainSidebar">
    <div class="sidebar-brand">Decorum <?php echo ucfirst($role); ?></div>
    <nav class="sidebar-nav">
      <ul>
        <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php" class="<?php echo $is_active('dashboard.php'); ?>">Dashboard</a></li>
            <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">Orders</a></li>
            <li><a href="users.php" class="<?php echo $is_active('users.php'); ?>">Users</a></li>
            <li><a href="register_user_form.php" class="<?php echo $is_active('register_user_form.php'); ?>">Add User</a></li>
        <?php else: ?>
            <li><a href="index.php" class="<?php echo $is_active('index.php'); ?>">Welcome Hub</a></li>
            <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
            <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">My Orders</a></li>
        <?php endif; ?>
        <li><a href="logout.php" style="color: #FCA5A5; margin-top: 20px;">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <!-- Dark overlay for when the mobile menu is open -->
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <main class="main-content">
    <div class="topbar">
      <div style="display: flex; align-items: center; gap: 15px;">
        <button id="sidebarToggle" class="hamburger-btn" title="Toggle Sidebar">☰</button>
        <h2><?php echo htmlspecialchars($page_heading); ?></h2>
      </div>
      <!-- Injects any buttons (like "+ Add Product") defined on the specific page -->
      <div class="topbar-actions">
        <?php if(isset($topbar_actions)) echo $topbar_actions; ?>
      </div>
    </div>
    
    <div class="page-body">