<?php
/**
 * =============================================================
 * WEEK 4 — dashboard.php (Admin Dashboard)
 * Theme: Server-side components, backend development foundations
 *
 * Week 4 focus:
 *   - Sessions are properly working
 *   - Role-based access (admin only)
 *   - Dynamic data rendered from the DB (READ)
 *   - Add/Edit/Delete modals visible but actions are stubbed
 *     with clear "Week 5" labels — full CRUD arrives next week
 * =============================================================
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin('admin');

$flash_success = '';
$flash_error   = '';
if (isset($_SESSION['flash_success'])) { $flash_success = $_SESSION['flash_success']; unset($_SESSION['flash_success']); }
if (isset($_SESSION['flash_error']))   { $flash_error   = $_SESSION['flash_error'];   unset($_SESSION['flash_error']); }

// READ: Fetch all books from DB
$books      = $pdo->query("SELECT * FROM books ORDER BY title ASC")->fetchAll();
$totalBooks = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalStock = $pdo->query("SELECT COALESCE(SUM(stock_quantity),0) FROM books")->fetchColumn();
$lowStock   = $pdo->query("SELECT COUNT(*) FROM books WHERE stock_quantity < 10")->fetchColumn();
$pendingReqs= $pdo->query("SELECT COUNT(*) FROM requisitions WHERE status = 'pending'")->fetchColumn();

$fullName = sessionGet('full_name');
$initials = getUserInitials();
$today    = date('l, F j Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard — Decorum Bookshop</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="app-wrapper">

  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="brand-icon">📚</div>
      <h1>Decorum Bookshop</h1>
      <p>B2B Inventory Portal</p>
    </div>
    <nav class="sidebar-nav">
      <p class="nav-section-label">Main Menu</p>
      <ul>
        <li class="nav-item"><a href="index.php"><span class="nav-icon">📋</span> Book Catalog</a></li>
        <li class="nav-item"><a href="requisitions.php"><span class="nav-icon">📦</span> Requisitions</a></li>
      </ul>
      <p class="nav-section-label" style="margin-top:16px;">Administration</p>
      <ul>
        <li class="nav-item"><a href="dashboard.php" class="active"><span class="nav-icon">⚙️</span> Admin Dashboard</a></li>
      </ul>
    </nav>
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar"><?php echo $initials; ?></div>
        <div class="user-details">
          <div class="user-name"><?php echo htmlspecialchars($fullName); ?></div>
          <div class="user-role">Master Admin</div>
        </div>
      </div>
      <a href="logout.php" class="btn btn-logout btn-sm" style="width:100%;justify-content:center;">🚪 Logout</a>
    </div>
  </aside>

  <main class="main-content">

    <div class="topbar">
      <div class="topbar-left">
        <h2>Admin Dashboard</h2>
        <div class="breadcrumb">Home › Admin Dashboard</div>
      </div>
      <div class="topbar-right">
        <button class="btn btn-primary" onclick="openModal('addBookModal')">➕ Add New Book</button>
      </div>
    </div>

    <div class="page-body">

      <?php if ($flash_success): ?><div class="alert alert-success auto-dismiss">✅ <?php echo htmlspecialchars($flash_success); ?></div><?php endif; ?>
      <?php if ($flash_error):   ?><div class="alert alert-danger  auto-dismiss">⚠️ <?php echo htmlspecialchars($flash_error);   ?></div><?php endif; ?>

      <div class="alert alert-info">
        ℹ️ <strong>Week 4:</strong> Data is live from the database (READ working). Full CRUD (Add/Edit/Delete) is implemented in Week 5.
      </div>

      <!-- Stats from DB -->
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-icon blue">📚</div><div class="stat-info"><div class="stat-label">Total Titles</div><div class="stat-value"><?php echo number_format($totalBooks); ?></div><div class="stat-sub">From database</div></div></div>
        <div class="stat-card"><div class="stat-icon green">✅</div><div class="stat-info"><div class="stat-label">Total Stock</div><div class="stat-value"><?php echo number_format($totalStock); ?></div><div class="stat-sub">Book units</div></div></div>
        <div class="stat-card"><div class="stat-icon red">⚠️</div><div class="stat-info"><div class="stat-label">Low Stock</div><div class="stat-value"><?php echo $lowStock; ?></div><div class="stat-sub">Below 10 units</div></div></div>
        <div class="stat-card"><div class="stat-icon orange">📋</div><div class="stat-info"><div class="stat-label">Pending Requests</div><div class="stat-value"><?php echo $pendingReqs; ?></div><div class="stat-sub"><a href="requisitions.php" style="color:var(--orange);">View all →</a></div></div></div>
      </div>

      <!-- Books Table -->
      <div class="table-card">
        <div class="table-header">
          <div>
            <h3>Book Inventory</h3>
            <div class="table-subtitle">
              Showing <?php echo count($books); ?> titles from database · <?php echo $today; ?>
            </div>
          </div>
          <div class="table-actions">
            <div class="search-box">
              <span>🔍</span>
              <input type="text" placeholder="Search books..." id="adminSearch">
            </div>
          </div>
        </div>

        <table id="adminTable">
          <thead>
            <tr>
              <th>ID</th><th>ISBN</th><th>Title</th><th>Author</th><th>Category</th><th>Price (KES)</th><th>Stock</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($books)): ?>
              <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--grey-400);">No books found. Ensure Week3db.sql was imported.</td></tr>
            <?php else: ?>
              <?php foreach ($books as $b):
                $qty = (int)$b['stock_quantity'];
                $sc  = $qty >= 20 ? 'stock-high' : ($qty >= 10 ? 'stock-medium' : 'stock-low');
              ?>
              <tr>
                <td><?php echo $b['id']; ?></td>
                <td class="td-isbn"><?php echo htmlspecialchars($b['isbn']); ?></td>
                <td class="td-title"><?php echo htmlspecialchars($b['title']); ?></td>
                <td class="td-author"><?php echo htmlspecialchars($b['author']); ?></td>
                <td><?php echo htmlspecialchars($b['category'] ?? '—'); ?></td>
                <td class="td-price"><?php echo number_format($b['price'], 0); ?></td>
                <td><span class="stock-badge <?php echo $sc; ?>"><?php echo $qty; ?></span></td>
                <td>
                  <div class="action-btns">
                    <button type="button" class="btn btn-outline btn-sm btn-icon" title="Edit (Week 5)" onclick="openModal('editBookModal')">✏️</button>
                    <button type="button" class="btn btn-danger btn-sm btn-icon" title="Delete (Week 5)" onclick="confirmDelete(<?php echo $b['id']; ?>, '<?php echo addslashes($b['title']); ?>')">🗑️</button>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<!-- ADD BOOK MODAL (Week 4 — form shown, Week 5 wires the action) -->
<div class="modal-overlay" id="addBookModal">
  <div class="modal">
    <div class="modal-header">
      <h3>➕ Add New Book <small style="color:var(--grey-400);font-weight:400;">(Full save in Week 5)</small></h3>
      <button class="modal-close" onclick="closeModal('addBookModal')">✕</button>
    </div>
    <div class="modal-body">
      <form action="add_book_stub.php" method="POST" id="addBookForm" novalidate>
        <div class="form-row">
          <div class="form-group"><label>ISBN</label><input type="text" id="isbn" name="isbn" placeholder="9780000000000" required><span class="error-msg" id="isbnError">Valid ISBN required.</span></div>
          <div class="form-group"><label>Category</label><select name="category"><option value="">Select...</option><option>Fiction</option><option>Classic</option><option>African Lit</option><option>Dystopia</option><option>Non-Fiction</option><option>Academic</option><option>YA Fiction</option><option>Romance</option></select></div>
        </div>
        <div class="form-row single"><div class="form-group"><label>Book Title</label><input type="text" id="title" name="title" placeholder="Full book title" required><span class="error-msg" id="titleError">Title required.</span></div></div>
        <div class="form-row">
          <div class="form-group"><label>Author</label><input type="text" id="author" name="author" placeholder="Author full name" required><span class="error-msg" id="authorError">Author required.</span></div>
          <div class="form-group"><label>Publisher</label><input type="text" name="publisher" placeholder="Publisher name"></div>
        </div>
        <div class="form-row triple">
          <div class="form-group"><label>Price (KES)</label><input type="number" id="price" name="price" placeholder="1200" min="0" required><span class="error-msg" id="priceError">Valid price required.</span></div>
          <div class="form-group"><label>Initial Stock</label><input type="number" id="stock" name="stock_quantity" placeholder="50" min="0" required><span class="error-msg" id="stockError">Stock required.</span></div>
          <div class="form-group"><label>Year Published</label><input type="number" name="year_published" placeholder="2022"></div>
        </div>
        <div class="form-footer" style="margin:0 -22px -22px;border-radius:0 0 12px 12px;">
          <button type="button" class="btn btn-ghost" onclick="closeModal('addBookModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">💾 Save Book</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT BOOK MODAL -->
<div class="modal-overlay" id="editBookModal">
  <div class="modal">
    <div class="modal-header">
      <h3>✏️ Edit Book <small style="color:var(--grey-400);font-weight:400;">(Full DB update in Week 5)</small></h3>
      <button class="modal-close" onclick="closeModal('editBookModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="alert alert-info">Week 5 pre-fills this form from the selected database record.</div>
      <form action="#" method="POST">
        <div class="form-row single"><div class="form-group"><label>Book Title</label><input type="text" name="title" placeholder="Title" required></div></div>
        <div class="form-row"><div class="form-group"><label>Author</label><input type="text" name="author" placeholder="Author" required></div><div class="form-group"><label>Price (KES)</label><input type="number" name="price" placeholder="Price" required></div></div>
        <div class="form-row single"><div class="form-group"><label>Stock Quantity</label><input type="number" name="stock_quantity" placeholder="Stock" required></div></div>
        <div class="form-footer" style="margin:0 -22px -22px;border-radius:0 0 12px 12px;">
          <button type="button" class="btn btn-ghost" onclick="closeModal('editBookModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">💾 Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-overlay" id="deleteModal">
  <div class="modal" style="width:400px;">
    <div class="modal-header">
      <h3 style="color:var(--red-primary);">🗑️ Confirm Delete</h3>
      <button class="modal-close" onclick="closeModal('deleteModal')">✕</button>
    </div>
    <div class="modal-body">
      <div class="alert alert-danger">⚠️ Delete <strong id="deleteBookName"></strong>? (Full DB delete wired in Week 5)</div>
      <form action="#" method="POST" id="deleteForm">
        <input type="hidden" name="book_id" id="delete_book_id" value="">
        <div class="form-footer" style="margin:0 -22px -22px;border-radius:0 0 12px 12px;">
          <button type="button" class="btn btn-ghost" onclick="closeModal('deleteModal')">Cancel</button>
          <button type="submit" class="btn btn-danger">🗑️ Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="js/validation.js"></script>
</body>
</html>
