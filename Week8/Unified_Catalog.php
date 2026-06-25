<?php

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin(); // Allows BOTH admins and managers

$role = $_SESSION['role'];

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();

$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Catalog - Decorum</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar">
    <div class="sidebar-brand" style="font-size: 1.8rem; padding: 30px 20px; text-align: center; line-height: 1.3;">Decorum <?php echo ucfirst($role); ?></div>
    <nav class="sidebar-nav">
      <ul>
        <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php" class="<?php echo $is_active('dashboard.php'); ?>">Dashboard</a></li>
            <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
            <li><a href="#">Categories</a></li>
            <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">Orders</a></li>
            <li><a href="users.php" class="<?php echo $is_active('users.php'); ?>">Users</a></li>
            <li><a href="#">Profile</a></li>
        <?php else: ?>
            <li><a href="index.php" class="<?php echo $is_active('index.php'); ?>">Welcome Hub</a></li>
            <li><a href="Unified_Catalog.php" class="<?php echo $is_active('Unified_Catalog.php'); ?>">Products</a></li>
            <li><a href="requisitions.php" class="<?php echo $is_active('requisitions.php'); ?>">My Orders</a></li>
            <li><a href="#">Profile</a></li>
            <li><a href="#">Settings</a></li>
        <?php endif; ?>
        <li><a href="logout.php" style="color: #FCA5A5; margin-top: 20px;">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <div class="topbar" style="display: flex; justify-content: space-between; align-items: center; padding-right: 40px; flex-wrap: wrap;">
      <h2 style="font-size: 2.2rem; margin: 0;">Products Catalog</h2>
      <?php if ($role === 'admin'): ?>
        <a href="add_product.php" class="btn btn-primary" style="padding: 10px 20px;">+ Add Product</a>
      <?php else: ?>
        <button type="submit" form="orderForm" class="btn btn-danger" style="padding: 10px 20px;">Submit Order Cart</button>
      <?php endif; ?>
    </div>

    <div class="page-body">
      <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>
      <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>

      <!-- Manager Form Wrapper -->
      <?php if ($role === 'manager'): ?>
      <form id="orderForm" action="process_requisition.php" method="POST">
      <?php endif; ?>

      <!-- THE NEW CSS GRID LAYOUT -->
      <div class="products-grid">
        <?php foreach ($books as $b): ?>
        <div class="product-card">
          <!-- Responsive Image Placeholder -->
          <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($b['title']); ?>&background=random&color=fff&size=250&font-size=0.3" alt="<?php echo htmlspecialchars($b['title']); ?>" class="product-image">
          
          <h3 class="product-title"><?php echo htmlspecialchars($b['title']); ?></h3>
          <p class="product-category"><?php echo htmlspecialchars($b['category']); ?> | <?php echo htmlspecialchars($b['publisher'] ?? 'Standard Pub.'); ?></p>
          <div class="product-price">Ksh <?php echo number_format($b['price'], 2); ?></div>
          
          <p style="font-size: 0.9rem; color: #64748B; margin-bottom: 15px;">In Stock: <strong><?php echo $b['stock_quantity']; ?> units</strong></p>
          
          <div class="product-actions">
            <?php if ($role === 'admin'): ?>
              <!-- Admin Controls -->
              <a href="edit_product.php?id=<?php echo $b['id']; ?>" class="btn btn-outline" style="flex: 1; text-align: center;">Edit</a>
              <form action="delete_product.php" method="POST" onsubmit="return confirm('Delete this book?');" style="margin:0; flex: 1;">
                  <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                  <button type="submit" class="btn btn-danger-text" style="width: 100%; border: 1px solid #ef4444;">Delete</button>
              </form>
            <?php else: ?>
              <!-- Manager Controls -->
              <div style="width: 100%;">
                <label style="font-size: 0.8rem; color: #64748B; display: block; margin-bottom: 5px;">Order Quantity:</label>
                <input type="number" name="quantities[<?php echo $b['id']; ?>]" min="0" max="<?php echo $b['stock_quantity']; ?>" value="0" class="form-control" style="width: 100%;">
              </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php if ($role === 'manager'): ?>
      </form>
      <?php endif; ?>

    </div>
  </main>
</div>
</body>
</html>