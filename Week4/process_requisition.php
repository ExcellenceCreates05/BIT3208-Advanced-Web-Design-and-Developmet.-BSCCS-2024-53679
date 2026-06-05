<?php
/**
 * =============================================================
 * process_requisition.php — Handle Requisition Submission
 * =============================================================
 * Receives POST data from index.php form
 * Validates, inserts into DB, redirects back
 * =============================================================
 */

require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin('manager'); // Only managers can submit requisitions

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$qtyData = $_POST['qty'] ?? []; // ['book_id' => 'quantity', ...]

// Filter: keep only entries where quantity > 0
$validItems = [];
foreach ($qtyData as $bookId => $qty) {
    $qty    = (int)$qty;
    $bookId = (int)$bookId;
    if ($qty > 0 && $bookId > 0) {
        $validItems[$bookId] = $qty;
    }
}

if (empty($validItems)) {
    $_SESSION['flash_error'] = 'Please enter a quantity for at least one book.';
    header('Location: index.php');
    exit;
}

// --- Validate quantities against actual stock ---
foreach ($validItems as $bookId => $qty) {
    $stmt = $pdo->prepare("SELECT stock_quantity FROM books WHERE id = :id");
    $stmt->execute(['id' => $bookId]);
    $book = $stmt->fetch();
    if (!$book || $qty > $book['stock_quantity']) {
        $_SESSION['flash_error'] = 'One or more requested quantities exceed available stock.';
        header('Location: index.php');
        exit;
    }
}

// --- Insert requisition header ---
$stmt = $pdo->prepare(
    "INSERT INTO requisitions (manager_id, status) VALUES (:manager_id, 'pending')"
);
$stmt->execute(['manager_id' => $_SESSION['user_id']]);
$requisitionId = $pdo->lastInsertId(); // Get the ID of the new requisition row

// --- Insert each line item ---
$itemStmt = $pdo->prepare(
    "INSERT INTO requisition_items (requisition_id, book_id, quantity_requested)
     VALUES (:req_id, :book_id, :qty)"
);

foreach ($validItems as $bookId => $qty) {
    $itemStmt->execute([
        'req_id'  => $requisitionId,
        'book_id' => $bookId,
        'qty'     => $qty
    ]);
}

// --- Success! Set flash message and redirect ---
$_SESSION['flash_success'] = 'Requisition #' . $requisitionId . ' submitted successfully! It is now pending admin approval.';
header('Location: index.php');
exit;
?>
