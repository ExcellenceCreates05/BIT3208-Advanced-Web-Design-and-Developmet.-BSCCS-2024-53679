<?php

session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

$error = '';
$book = null;

//  GET THE BOOK DATA (To fill the form)
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

//  PROCESS THE UPDATE (When the Admin clicks Save)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Hidden field
    $title = trim(strip_tags($_POST['title']));
    $author = trim(strip_tags($_POST['author']));
    $category = trim(strip_tags($_POST['category']));
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock_quantity'];

    if (empty($title) || empty($author) || $price <= 0) {
        $error = "Title, Author, and valid Price are required.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category = ?, price = ?, stock_quantity = ? WHERE id = ?");
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
<!DOCTYPE html>
<html lang="en">
<head><title>Edit Product - Decorum</title><link rel="stylesheet" href="css/style.css"></head>
<body>
  <div class="app-wrapper">
    <aside class="sidebar"><div class="sidebar-brand">Admin Portal</div>
      <nav class="sidebar-nav"><ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="dashboard.php" class="active">Products</a></li>
      </ul></nav>
    </aside>
    <main class="main-content"><div class="page-body">
      <div class="table-card" style="max-width: 600px; padding: 30px; margin: 0 auto;">
        <h2 style="color: var(--primary-blue); margin-bottom: 20px;">Edit Book: <?php echo htmlspecialchars($book['isbn']); ?></h2>
        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        
        <form method="POST" action="edit_product.php?id=<?php echo $book['id']; ?>">
          <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
          
          <div class="form-group"><label>Title *</label><input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($book['title']); ?>" required></div>
          <div class="form-group"><label>Author *</label><input type="text" name="author" class="form-control" value="<?php echo htmlspecialchars($book['author']); ?>" required></div>
          <div class="form-group"><label>Category</label><input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($book['category']); ?>"></div>
          
          <div style="display:flex; gap:15px;">
              <div class="form-group" style="flex:1;"><label>Price (Ksh) *</label><input type="number" step="0.01" name="price" class="form-control" value="<?php echo $book['price']; ?>" required></div>
              <div class="form-group" style="flex:1;"><label>Update Stock</label><input type="number" name="stock_quantity" class="form-control" value="<?php echo $book['stock_quantity']; ?>"></div>
          </div>
          <div style="display:flex; gap: 10px; margin-top: 20px;">
              <a href="dashboard.php" class="btn btn-outline">Cancel</a>
              <button type="submit" class="btn btn-primary">Update Database</button>
          </div>
        </form>
      </div>
    </div></main>
  </div>
</body>
</html>