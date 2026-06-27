<?php

session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn      = trim(strip_tags($_POST['isbn']));
    $title     = trim(strip_tags($_POST['title']));
    $author    = trim(strip_tags($_POST['author']));
    $publisher = trim(strip_tags($_POST['publisher']));
    $category  = trim(strip_tags($_POST['category']));
    $price     = (float)$_POST['price'];
    $stock     = (int)$_POST['stock_quantity'];
    $year      = (int)$_POST['year_published'];

    if (empty($isbn) || empty($title) || empty($author) || $price <= 0) {
        $error = "ISBN, Title, Author, and a valid Price are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO books (isbn, title, author, publisher, category, price, stock_quantity, year_published) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$isbn, $title, $author, $publisher, $category, $price, $stock, $year]);
            $_SESSION['flash_success'] = "Book added successfully!";
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() == 23000
                ? "A book with this ISBN already exists."
                : "Database error: " . $e->getMessage();
        }
    }
}
?>
<?php
$page_title   = 'Add Product — Decorum';
$page_heading = 'Add New Book';
include 'includes/master_header.php';
?>

<div class="table-card" style="max-width:620px; padding:28px; margin:0 auto;">
  <h2 style="color:var(--primary-blue); margin-bottom:20px;">New Book Details</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="form-group">
      <label>ISBN *</label>
      <input type="text" name="isbn" class="form-control" required placeholder="e.g. 978-3-16-148410-0">
    </div>
    <div class="form-group">
      <label>Title *</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Author *</label>
      <input type="text" name="author" class="form-control" required>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Category</label>
        <input type="text" name="category" class="form-control" placeholder="Fiction, Science…">
      </div>
      <div class="form-group">
        <label>Publisher</label>
        <input type="text" name="publisher" class="form-control">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Price (Ksh) *</label>
        <input type="number" step="0.01" name="price" class="form-control" required placeholder="0.00">
      </div>
      <div class="form-group">
        <label>Initial Stock</label>
        <input type="number" name="stock_quantity" class="form-control" value="0" min="0">
      </div>
      <div class="form-group">
        <label>Year</label>
        <input type="number" name="year_published" class="form-control" placeholder="2024">
      </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;">
      <a href="dashboard.php" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Save to Database</button>
    </div>
  </form>
</div>

    </div><!-- /page-body -->
  </main>
</div>

<script src="js/app.js"></script>
</body>
</html>
