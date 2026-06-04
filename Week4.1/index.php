<?php
/**
 * =============================================================
 * index.php — Book Catalog / Requisition Page (Week 4)
 * =============================================================
 * Accessible by: ALL logged-in users (admin + manager)
 * Managers: view books + submit requisitions
 * Admins:   view books (full CRUD is on dashboard.php)
 * =============================================================
 */

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin(); // Any logged-in user can view this page

// --- Flash message (set by process_requisition.php on success) ---
$flash = '';
if (isset($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']); // Show once only
}

// --- Fetch all books from the database ---
$stmt  = $pdo->query("SELECT * FROM books ORDER BY title ASC");
$books = $stmt->fetchAll();

// --- Get user session data ---
$fullName  = sessionGet('full_name');
$role      = sessionGet('role');
$initials  = getUserInitials();
$today     = date('l, F j Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Catalog — Decorum Bookshop B2B Portal</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app-wrapper">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">📚</div>
      <h1>Decorum Bookshop</h1>
      <p>B2B Inventory Portal</p>
    </div>
    <nav class="sidebar-nav">
      <p class="nav-section-label">Main Menu</p>
      <ul>
        <li class="nav-item"><a href="index.php" class="active"><span class="nav-icon">📋</span> Book Catalog</a></li>
        <li class="nav-item"><a href="my_requisitions.php"><span class="nav-icon">📦</span> My Requisitions</a></li>
      </ul>
      <?php if ($role === 'admin'): ?>
      <p class="nav-section-label" style="margin-top:16px;">Administration</p>
      <ul>
        <li class="nav-item"><a href="dashboard.php"><span class="nav-icon">⚙️</span> Admin Dashboard</a></li>
      </ul>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar"><?php echo $initials; ?></div>
        <div class="user-details">
          <div class="user-name"><?php echo htmlspecialchars($fullName); ?></div>
          <div class="user-role"><?php echo ucfirst($role); ?></div>
        </div>
      </div>
      <a href="logout.php" class="btn btn-logout btn-sm" style="width:100%;justify-content:center;">🚪 Logout</a>
    </div>
  </aside>

  <!-- MAIN CONTENT -->
  <main class="main-content">

    <div class="topbar">
      <div class="topbar-left">
        <h2>Book Catalog</h2>
        <div class="breadcrumb">Home › Book Catalog</div>
      </div>
      <div class="topbar-right">
        <span style="font-size:0.78rem;color:var(--grey-400);"><?php echo $today; ?></span>
      </div>
    </div>

    <div class="page-body">

      <?php if ($flash): ?>
        <div class="alert alert-success auto-dismiss">✅ <?php echo htmlspecialchars($flash); ?></div>
      <?php endif; ?>

      <div class="alert alert-info">
        ℹ️ Enter required quantities in the <strong>Qty Needed</strong> column, then click <strong>Submit Requisition</strong>.
      </div>

      <!--
        FORM: POST to process_requisition.php
        Wraps the entire table so all qty inputs are submitted together
      -->
      <form action="process_requisition.php" method="POST" id="requisitionForm">

        <div class="table-card">

          <div class="table-header">
            <div>
              <h3>Master Book Inventory</h3>
              <div class="table-subtitle">
                <?php echo count($books); ?> titles available · <?php echo $today; ?>
              </div>
            </div>
            <div class="table-actions">
              <div class="search-box">
                <span>🔍</span>
                <input type="text" placeholder="Search title, author, ISBN..." id="searchInput">
              </div>
              <?php if ($role === 'manager'): ?>
              <button type="submit" class="btn btn-primary">📤 Submit Requisition</button>
              <?php endif; ?>
            </div>
          </div>

          <table id="catalogTable">
            <thead>
              <tr>
                <th>#</th>
                <th>ISBN</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Unit Price</th>
                <th>In Stock</th>
                <?php if ($role === 'manager'): ?>
                <th>Qty Needed</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>

              <?php if (empty($books)): ?>
                <tr>
                  <td colspan="8" style="text-align:center;padding:40px;color:var(--grey-400);">
                    No books in inventory yet. Admin can add books via the dashboard.
                  </td>
                </tr>

              <?php else: ?>
                <?php foreach ($books as $i => $book): ?>

                  <?php
                    // Determine stock badge class
                    $qty = (int)$book['stock_quantity'];
                    if ($qty >= 20)       $stockClass = 'stock-high';
                    elseif ($qty >= 10)   $stockClass = 'stock-medium';
                    else                  $stockClass = 'stock-low';
                  ?>

                  <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td class="td-isbn"><?php echo htmlspecialchars($book['isbn']); ?></td>
                    <td class="td-title"><?php echo htmlspecialchars($book['title']); ?></td>
                    <td class="td-author"><?php echo htmlspecialchars($book['author']); ?></td>
                    <td><?php echo htmlspecialchars($book['category'] ?? '—'); ?></td>
                    <td class="td-price">KES <?php echo number_format($book['price'], 0); ?></td>
                    <td>
                      <span class="stock-badge <?php echo $stockClass; ?>">
                        <?php echo $qty; ?> units
                      </span>
                    </td>
                    <?php if ($role === 'manager'): ?>
                    <td>
                      <?php if ($qty > 0): ?>
                        <input
                          type="number"
                          class="qty-input"
                          name="qty[<?php echo $book['id']; ?>]"
                          min="1"
                          max="<?php echo $qty; ?>"
                          placeholder="0"
                        >
                      <?php else: ?>
                        <span style="font-size:0.75rem;color:var(--red-primary);font-weight:600;">Out of Stock</span>
                      <?php endif; ?>
                    </td>
                    <?php endif; ?>
                  </tr>

                <?php endforeach; ?>
              <?php endif; ?>

            </tbody>
          </table>

        </div><!-- /table-card -->

      </form>

    </div><!-- /page-body -->

  </main>

</div>

<script src="js/validation.js"></script>
</body>
</html>
