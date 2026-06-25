<?php
/**
 * =============================================================
 * includes/auth_guard.php — Session Authentication Guard
 * =============================================================
 *
 * PURPOSE:
 *   Every protected page (index.php, dashboard.php) must include
 *   this file at the very top. It checks whether the user is
 *   logged in and has the right role.
 *
 * USAGE:
 *   Protect any page (all roles allowed):
 *     require_once __DIR__ . '/includes/auth_guard.php';
 *     requireLogin();
 *
 *   Protect admin-only pages:
 *     require_once __DIR__ . '/includes/auth_guard.php';
 *     requireLogin('admin');
 *
 * HOW SESSIONS WORK:
 *   When a user logs in (login.php), PHP stores their data in
 *   $_SESSION on the server. A session cookie (PHPSESSID) is
 *   sent to the browser. On every subsequent request, the browser
 *   sends that cookie back, and PHP loads the right session.
 * =============================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Require the user to be logged in.
 * Optionally enforce a specific role.
 *
 * @param string|null $requiredRole  'admin' | 'manager' | null (any role)
 */
function requireLogin($requiredRole = null) {

    // Check if session contains a user
    if (!isset($_SESSION['user_id'])) {
        // Not logged in — redirect to login page
        header('Location: login.php');
        exit;
    }

    // If a specific role is required, enforce it
    if ($requiredRole !== null && $_SESSION['role'] !== $requiredRole) {
        // Logged in but wrong role — show access denied
        http_response_code(403);
        echo '<!DOCTYPE html><html><head><title>Access Denied</title>
              <link rel="stylesheet" href="css/style.css"></head><body>
              <div style="display:flex;align-items:center;justify-content:center;min-height:100vh;flex-direction:column;gap:16px;">
              <div style="font-size:3rem;">🚫</div>
              <h2>Access Denied</h2>
              <p style="color:var(--grey-400)">You do not have permission to view this page.</p>
              <a href="index.php" class="btn btn-primary">← Go Back</a>
              </div></body></html>';
        exit;
    }
}

/**
 * Destroy the session and log the user out.
 * Called by logout.php
 */
function logoutUser() {
    $_SESSION = [];                        // Clear all session variables
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();                     // Destroy the session on the server
    header('Location: login.php');
    exit;
}

/**
 * Get a session value safely.
 * Avoids undefined index notices.
 *
 * @param  string $key
 * @param  mixed  $default
 * @return mixed
 */
function sessionGet($key, $default = '') {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
}

/**
 * Get the initials of the logged-in user for the avatar.
 * "Jane Mwangi" → "JM"
 */
function getUserInitials() {
    $name  = sessionGet('full_name', 'User');
    $parts = explode(' ', $name);
    $initials = '';
    foreach ($parts as $part) {
        $initials .= strtoupper(substr($part, 0, 1));
    }
    return substr($initials, 0, 2);
}
?>
