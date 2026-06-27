<?php

session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

$error = '';
$book  = null;

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $book = $stmt->fetch();
    if (!$book) {
        $_SESSION['flash_error'] = "Book not found.";
        header('Location: dashboard.php');
        exit;
    }
} else {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'];
    $title    = trim(strip_tags($_POST['title']));
    $author   = trim(strip_tags($_POST['author']));
    $category = trim(strip_tags($_POST['category']));
    $price    = (float)$_POST['price'];
    $stock    = (int)$_POST['stock_quantity'];

    if (empty($title) || empty($author) || $price <= 0) {
        $error = "Title, Author, and valid Price are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, price=?, stock_quantity=? WHERE id=?");
            $stmt->execute([$title, $author, $category, $price, $stock, $id]);
            $_SESSION['flash_success'] = "Book updated successfully!";
            header('Location: dashboard.php');
            exit;
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<?php
$page_title   = 'Edit Product — Decorum';
$page_heading = 'Edit Book';
include 'includes/master_header.php';
?>

<div class="table-card" style="max-width:620px; padding:28px; margin:0 auto;">
  <h2 style="color:var(--primary-blue); margin-bottom:20px;">
    Editing: <?php echo htmlspecialchars($book['isbn']); ?>
  </h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST" action="edit_product.php?id=<?php echo $book['id']; ?>">
    <input type="hidden" name="id" value="<?php echo $book['id']; ?>">

    <div class="form-group">
      <label>Title *</label>
      <input type="text" name="title" class="form-control"
             value="<?php echo htmlspecialchars($book['title']); ?>" required>
    </div>
    <div class="form-group">
      <label>Author *</label>
      <input type="text" name="author" class="form-control"
             value="<?php echo htmlspecialchars($book['author']); ?>" required>
    </div>
    <div class="form-group">
      <label>Category</label>
      <input type="text" name="category" class="form-control"
             value="<?php echo htmlspecialchars($book['category']); ?>">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Price (Ksh) *</label>
        <input type="number" step="0.01" name="price" class="form-control"
               value="<?php echo $book['price']; ?>" required>
      </div>
      <div class="form-group">
        <label>Update Stock</label>
        <input type="number" name="stock_quantity" class="form-control"
               value="<?php echo $book['stock_quantity']; ?>" min="0">
      </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:24px; flex-wrap:wrap;">
      <a href="dashboard.php" class="btn btn-outline">Cancel</a>
      <button type="submit" class="btn btn-primary">Update Database</button>
    </div>
  </form>
</div>

    </div><!-- /page-body -->
  </main>
</div>

<script src="js/app.js"></script>
</body>
</html>
