<?php
// catalog.php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin(); // Allows BOTH admins and managers

$role = $_SESSION['role'];

// Force fresh data
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();

// Determine active navigation link
$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Catalog - Decorum</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="app-wrapper">
  
  <aside class="sidebar">
    <div class="sidebar-brand">Decorum <?php echo ucfirst($role); ?></div>
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
    <div class="topbar">
      <h2>Products Catalog</h2>
    </div>

    <div class="page-body">
      <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>
      <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>

      <div class="table-card">
        <div class="table-header">
          <div class="search-bar">
            <input type="text" class="form-control" placeholder="Search products...">
          </div>
          
          <?php if ($role === 'admin'): ?>
            <a href="add_product.php" class="btn btn-primary">Add Product</a>
          <?php else: ?>
            <!-- The submit button for managers is hooked to the form below -->
            <button type="submit" form="orderForm" class="btn btn-danger">Submit Order Cart</button>
          <?php endif; ?>
        </div>

        <!-- Manager Form Wrapper: Only active if user is a manager -->
        <?php if ($role === 'manager'): ?>
        <form id="orderForm" action="process_requisition.php" method="POST">
        <?php endif; ?>

        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Publisher</th>
              <th>Price</th>
              <th>Stock</th>
              <?php if ($role === 'admin'): ?>
                <th>Action</th>
              <?php else: ?>
                <th>Order Qty</th>
              <?php endif; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($books as $b): ?>
            <tr>
              <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
              <td><?php echo htmlspecialchars($b['category']); ?></td>
              <td><?php echo htmlspecialchars($b['publisher'] ?? 'N/A'); ?></td>
              <td>Ksh <?php echo number_format($b['price'], 2); ?></td>
              <td><?php echo $b['stock_quantity']; ?></td>
              
              <?php if ($role === 'admin'): ?>
                <!-- ADMIN VIEW: CRUD Buttons -->
                <td>
                  <div style="display: flex; gap: 5px;">
                      <a href="edit_product.php?id=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                      <form action="delete_product.php" method="POST" onsubmit="return confirm('Delete this book?');" style="margin:0;">
                          <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                          <button type="submit" class="btn btn-danger-text" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                      </form>
                  </div>
                </td>
              <?php else: ?>
                <!-- MANAGER VIEW: Order Inputs -->
                <td>
                    <input type="number" name="quantities[<?php echo $b['id']; ?>]" min="0" max="<?php echo $b['stock_quantity']; ?>" value="0" class="form-control" style="width: 80px; padding: 5px;">
                </td>
              <?php endif; ?>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <?php if ($role === 'manager'): ?>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </main>
</div>
</body>
</html>