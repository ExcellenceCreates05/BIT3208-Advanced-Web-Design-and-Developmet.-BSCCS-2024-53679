<?php
// includes/master_header.php
$role = $_SESSION['role'] ?? 'manager';
$current_page = basename($_SERVER['PHP_SELF']);
$is_active = function($page) use ($current_page) {
    return $current_page === $page ? 'active' : '';
};
$page_title   = $page_title   ?? "Decorum Bookshop";
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

  <aside class="sidebar" id="mainSidebar" aria-label="Main navigation">
    <div class="sidebar-brand">
      Decorum
      <span><?php echo ucfirst($role); ?> Portal</span>
    </div>

    <nav class="sidebar-nav">
      <ul>
        <?php if ($role === 'admin'): ?>
          <li><a href="dashboard.php" class="<?php echo $is_active('dashboard.php'); ?>">
            <span class="nav-icon">&#9783;</span> Dashboard
          </a></li>
          <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">
            <span class="nav-icon"></span> Products
          </a></li>
          <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">
            <span class="nav-icon"></span> Orders
          </a></li>
          <li><a href="users.php" class="<?php echo $is_active('users.php'); ?>">
            <span class="nav-icon"></span> Users
          </a></li>
          <li><a href="register_user_form.php" class="<?php echo $is_active('register_user_form.php'); ?>">
            <span class="nav-icon"></span> Add User
          </a></li>
          <li><a href="add_product.php" class="<?php echo $is_active('add_product.php'); ?>">
            <span class="nav-icon"></span> Add Product
          </a></li>
        <?php else: ?>
          <li><a href="index.php" class="<?php echo $is_active('index.php'); ?>">
            <span class="nav-icon">&#127968;</span> Welcome Hub
          </a></li>
          <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">
            <span class="nav-icon"></span> Products
          </a></li>
          <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">
            <span class="nav-icon"></span> My Orders
          </a></li>
        <?php endif; ?>
      </ul>
    </nav>

    <div class="sidebar-footer">
      <a href="logout.php">
        <span class="nav-icon">&#8617;</span> Logout
      </a>
    </div>
  </aside>

  <!-- Backdrop overlay for mobile -->
  <div class="sidebar-overlay" id="sidebarOverlay" aria-hidden="true"></div>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <button id="sidebarToggle" class="hamburger-btn" aria-label="Toggle navigation" title="Toggle Sidebar">
          <span class="bars">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
          </span>
        </button>
        <h2><?php echo htmlspecialchars($page_heading); ?></h2>
      </div>
      <div class="topbar-actions">
        <?php if (isset($topbar_actions)) echo $topbar_actions; ?>
      </div>
    </div>

    <div class="page-body">
