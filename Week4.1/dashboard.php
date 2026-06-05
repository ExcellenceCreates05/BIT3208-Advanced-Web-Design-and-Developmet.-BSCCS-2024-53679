<?php
// dashboard.php (Admin)
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

// Fetch Stats for the Top Cards
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalStock = $pdo->query("SELECT COALESCE(SUM(stock_quantity),0) FROM books")->fetchColumn();
$ordersToday = $pdo->query("SELECT COUNT(*) FROM requisitions WHERE DATE(date_submitted) = CURDATE()")->fetchColumn();
// Assuming an estimated revenue calc based on pending items for the wireframe demo
$estRevenue = $pdo->query("SELECT COALESCE(SUM(ri.quantity_requested * b.price), 0) FROM requisition_items ri JOIN books b ON ri.book_id = b.id")->fetchColumn();

// Fetch Products for the Table
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
          <button class="btn btn-primary">Add Product</button>
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
                <button class="btn btn-outline" style="padding: 5px 10px; font-size: 12px;">Edit</button>
                <button class="btn btn-danger-text" style="padding: 5px 10px; font-size: 12px; margin-left: 5px;">Delete</button>
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