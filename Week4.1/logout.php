<?php
/**
 * logout.php — End the user's session
 * Called when the user clicks the "Logout" button
 */
require_once __DIR__ . '/includes/auth_guard.php';
logoutUser(); // Destroys session and redirects to login.php
?>
