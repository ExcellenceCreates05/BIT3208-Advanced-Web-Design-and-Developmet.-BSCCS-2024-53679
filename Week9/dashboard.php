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

<?php
$page_title = 'Admin Dashboard - Decorum';
$page_heading = 'Dashboard Overview';
$topbar_actions = '<a href="users.php" class="btn btn-primary">+ Add System User</a>';
include 'includes/master_header.php';
?>

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
    <script src="js/app.js"></script>
    </html>