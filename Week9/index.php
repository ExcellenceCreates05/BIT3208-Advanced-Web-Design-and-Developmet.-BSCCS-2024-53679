<?php

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('manager');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$flash = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);
$error = $_SESSION['flash_error'] ?? '';
unset($_SESSION['flash_error']);

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();

// Determine active navigation link
$current_page = basename($_SERVER['PHP_SELF']);
$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};

$page_title = "Products Catalog - Decorum";
$page_heading = "Products Catalog";
$topbar_actions = '<button type="submit" form="orderForm" class="btn btn-primary">Submit Order Cart</button>';
include 'includes/master_header.php';
?>
<?php if ($flash): ?><div class="alert alert-success"><?php echo htmlspecialchars($flash); ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

      <form id="orderForm" action="process_requisition.php" method="POST">
        <div class="table-card">
          <div class="table-header">
             <div class="search-bar">
               <input type="text" class="form-control" placeholder="Search Inventory">
               <select class="form-control" style="width: auto;">
                 <option>Search Category</option>
                 <option>Fiction</option>
                 <option>Classic</option>
                 <option>Science</option>
               </select>
             </div>
             <button type="submit" class="btn btn-primary">Submit Order Cart</button>
          </div>
          
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Order Qty</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($books as $b): ?>
              <tr>
                <td><?php echo $b['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($b['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($b['author']); ?></td>
                <td>Ksh <?php echo number_format($b['price'], 2); ?></td>
                <td><?php echo $b['stock_quantity']; ?></td>
                <td>
                  <?php if ($b['stock_quantity'] > 0): ?>
                    <input type="number" name="qty[<?php echo $b['id']; ?>]" class="form-control" min="1" max="<?php echo $b['stock_quantity']; ?>" placeholder="0" style="width: 80px;">
                  <?php else: ?>
                    <span style="color: var(--accent-red); font-weight: bold;">Out of Stock</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </form>

  </div> <!-- Close page-body -->
</main> <!-- Close main-content -->
</div> <!-- Close app-wrapper -->

<script src="js/app.js"></script>
</body>
</html>