<?php

session_start();
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $user_id = $_POST['id'];

    // Security Check: Prevent the admin from deleting themselves!
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['flash_error'] = "Action Denied: You cannot delete your own active account.";
        header("Location: users.php");
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $_SESSION['flash_success'] = "User account permanently deleted.";
    } catch (PDOException $e) {
        $_SESSION['flash_error'] = "Database error: " . $e->getMessage();
    }
}

// Always redirect back to the user list
header("Location: users.php");
exit;