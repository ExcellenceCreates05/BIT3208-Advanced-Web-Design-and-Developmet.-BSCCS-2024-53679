<?php
// Unified_Catalog.php
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin(); // Allows BOTH admins and managers

$role = $_SESSION['role'];

// Force fresh data
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$books = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();

// CHANGED: The $is_active navigation logic was removed from here because it is now handled centrally inside includes/header.php

// CHANGED: We now define the page titles here BEFORE calling the header, so the header knows what to display.
$page_title = "Book Catalog - Decorum";
$page_heading = "Products Catalog";

// CHANGED: Instead of writing the buttons in the middle of the HTML layout, we define them here. 
// The header.php will automatically inject them into the right side of the Topbar!
if ($role === 'admin') {
    $topbar_actions = '<a href="add_product.php" class="btn btn-primary">Add Product</a>';
} else {
    $topbar_actions = '<button type="submit" form="orderForm" class="btn btn-danger">Submit Order Cart</button>';
}

// CHANGED: This single line replaces your entire <!DOCTYPE html>, <head>, <aside> sidebar, and the opening <main> tags!
// NOTE: the centralized header file in Week8 is named "master_header.php" — include it.
include 'includes/master_header.php'; 
?>

<!-- The <div class="page-body"> is already opened by master_header.php, so we drop the content directly in -->

<?php if(isset($_SESSION['flash_success'])): ?>
  <div class="alert alert-success"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
<?php endif; ?>
<?php if(isset($_SESSION['flash_error'])): ?>
  <div class="alert alert-danger"><?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?></div>
<?php endif; ?>

  <!-- Restored: Global search bar (was removed in Week8 refactor) -->
  <div class="table-card" style="margin-bottom: 16px;">
    <div class="table-header">
      <div class="search-bar">
        <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
      </div>
      <div style="flex:1"></div>
    </div>
  </div>

<!-- Manager Form Wrapper: Only active if user is a manager -->
<?php if ($role === 'manager'): ?>
<form id="orderForm" action="process_requisition.php" method="POST">
<?php endif; ?>

<!-- CHANGED: The entire <div class="table-card"> and <table> have been completely removed.
     Replaced with the highly responsive CSS Grid Cards to match the Library Theme! -->
<div class="products-grid">
  <?php foreach ($books as $b): ?>
  <div class="product-card">
    
    <!-- CHANGED: Added the automatic image generator to fulfill the Week 8 "Responsive Image" requirement -->
    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($b['title']); ?>&background=random&color=fff&size=250" class="product-image" alt="Cover">
    
    <!-- CHANGED: Data is now formatted vertically with Theme Variables instead of horizontal table cells -->
    <h3 style="font-family: var(--font-heading); color: var(--bg-espresso); margin-bottom: 5px;">
        <?php echo htmlspecialchars($b['title']); ?>
    </h3>
    
    <p style="color: #64748B; font-size: 0.9rem; margin-bottom: 10px;">
        <?php echo htmlspecialchars($b['category']); ?> | <?php echo htmlspecialchars($b['publisher'] ?? 'N/A'); ?>
    </p>
    
    <div style="font-size: 1.4rem; font-weight: bold; color: var(--accent-gold); margin-bottom: 10px;">
        Ksh <?php echo number_format($b['price'], 2); ?>
    </div>
    
    <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 15px;">
        Stock: <strong><?php echo $b['stock_quantity']; ?> units</strong>
    </p>
    
    <!-- Action Buttons container (Pushed to the bottom of the card) -->
    <div style="margin-top: auto; display: flex; gap: 10px;">
      <?php if ($role === 'admin'): ?>
        <!-- Admin Controls -->
        <a href="edit_product.php?id=<?php echo $b['id']; ?>" class="btn btn-outline" style="flex: 1; text-align: center;">Edit</a>
        <form action="delete_product.php" method="POST" onsubmit="return confirm('Delete this book?');" style="margin:0; flex: 1;">
            <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
            <button type="submit" class="btn btn-danger-text" style="width: 100%; height: 100%; border: 1px solid #ef4444; border-radius: 8px;">Delete</button>
        </form>
      <?php else: ?>
        <!-- Manager Controls -->
        <input type="number" name="quantities[<?php echo $b['id']; ?>]" min="0" max="<?php echo $b['stock_quantity']; ?>" value="0" class="form-control" placeholder="Qty" style="width: 100%;">
      <?php endif; ?>
    </div>
  
  </div>
  <?php endforeach; ?>
</div>

<?php if ($role === 'manager'): ?>
</form>
<?php endif; ?>

<!-- CHANGED: We must close the structural tags that were opened inside includes/header.php -->
  </div> <!-- Close page-body -->
</main> <!-- Close main-content -->
</div> <!-- Close app-wrapper -->

<!-- CHANGED: Link the global javascript file here to activate the sliding sidebar menu -->
<script src="js/app.js"></script>
</body>
</html>