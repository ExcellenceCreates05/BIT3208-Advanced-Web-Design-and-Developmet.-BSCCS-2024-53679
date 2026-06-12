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
// Assuming an estimated revenue calc based on pending items for the wireframe demo
$estRevenue = $pdo->query("SELECT COALESCE(SUM(ri.quantity_requested * b.price), 0) FROM requisition_items ri JOIN books b ON ri.book_id = b.id")->fetchColumn();

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();
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
    <div class="sidebar-brand">Decorum Admin</div>
    <nav class="sidebar-nav">
      <ul>
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="#">Products</a></li>
        <li><a href="#">Categories</a></li>
        <li><a href="requisitions.php">Orders</a></li>
        <li><a href="#">Users</a></li>
        <li><a href="#">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </aside>

  <main class="main-content">
    <div class="topbar">
      <h2>Dashboard Overview</h2>
    </div>

    <div class="page-body">
      <?php if(isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
      <?php endif; ?>
      <?php if(isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
      <?php endif; ?>

      <div class="stats-grid">
        <div class="stat-card">
          <h3>Total Books</h3>
          <div class="value"><?php echo number_format($totalBooks); ?></div>
        </div>
        <div class="stat-card">
          <h3>Total Stock</h3>
          <div class="value"><?php echo number_format($totalStock); ?></div>
        </div>
        <div class="stat-card">
          <h3>Orders Today</h3>
          <div class="value"><?php echo number_format($ordersToday); ?></div>
        </div>
        <div class="stat-card red-accent">
          <h3>Est. Revenue</h3>
          <div class="value">Ksh <?php echo number_format($estRevenue); ?></div>
        </div>
      </div>

      <div class="table-card">
        <div class="table-header">
          <div class="search-bar">
            <input type="text" class="form-control" placeholder="Search products...">
          </div>
          <a href="add_product.php" class="btn btn-primary">Add Product</a>
        </div>
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Category</th>
              <th>Publisher</th>
              <th>Price</th>
              <th>Stock</th>
              <th>Action</th>
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
              
              <td>
                <div style="display: flex; gap: 5px;">
                    <a href="edit_product.php?id=<?php echo $b['id']; ?>" class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    
                    <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this book?');">
                        <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
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