<?php
session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    if ($stmt->execute([$_POST['id']])) {
        $_SESSION['flash_success'] = "Book successfully deleted from inventory.";
    } else {
        $_SESSION['flash_error'] = "Failed to delete book.";
    }
}
header('Location: dashboard.php');
exit;
?>