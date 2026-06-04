<?php
/**
 * =============================================================
 * DECORUM BOOKSHOP — B2B INVENTORY SYSTEM
 * login.php — Authentication Gatekeeper (Week 4)
 * =============================================================
 *
 * WHAT THIS FILE DOES (the full request-response cycle):
 *   GET  request → Show the login form (HTML below)
 *   POST request → Process the form submission:
 *                  1. Sanitize input
 *                  2. Validate input (server-side)
 *                  3. Query the database
 *                  4. Verify password
 *                  5. Start session + set session variables
 *                  6. Redirect based on role
 *
 * WHY SERVER-SIDE VALIDATION MATTERS:
 *   JavaScript validation can be bypassed by disabling JS
 *   or using tools like Postman. PHP validation is the real gate.
 * =============================================================
 */

session_start();  // Must be the FIRST thing — before ANY output

require_once __DIR__ . '/includes/db_connect.php';

// --- If already logged in, send to the right page ---
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// --- Initialize variables ---
$error_message = '';  // Will hold any login error to display
$username_value = ''; // Preserve username input on error (good UX)

// =============================================================
// PROCESS THE LOGIN FORM (POST request)
// =============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /**
     * STEP 1: Sanitize input
     * strip_tags() removes HTML tags
     * trim() removes whitespace
     * This prevents stored XSS (Cross-Site Scripting)
     */
    $username = trim(strip_tags($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    // Preserve username so user doesn't have to retype on error
    $username_value = htmlspecialchars($username);

    /**
     * STEP 2: Server-side validation
     * Never trust the client. Even if JS validated it, check again here.
     */
    if (empty($username) || empty($password)) {
        $error_message = 'Both username and password are required.';

    } elseif (strlen($username) > 50) {
        $error_message = 'Invalid credentials.'; // Don't leak schema info

    } else {

        /**
         * STEP 3: Query the database using a Prepared Statement
         *
         * WHAT IS A PREPARED STATEMENT?
         *   Instead of: "SELECT * FROM users WHERE username = '$username'"
         *   We use:     "SELECT * FROM users WHERE username = :username"
         *
         *   PDO separates the SQL structure from the data.
         *   This COMPLETELY prevents SQL Injection attacks.
         *   Example attack this blocks: username = "' OR 1=1 --"
         */
        $stmt = $pdo->prepare(
            "SELECT id, username, password, role, full_name
             FROM users
             WHERE username = :username
             LIMIT 1"
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(); // Returns associative array or false

        /**
         * STEP 4: Verify the password
         *
         * In Week 3 we stored plain text passwords as seed data.
         * In production (and for full marks), use password_hash() to store
         * and password_verify() to check.
         *
         * We handle BOTH here for compatibility with the seed data.
         */
        if ($user) {

            // Check if password is hashed (starts with $2y$ = bcrypt)
            $password_valid = false;

            if (password_verify($password, $user['password'])) {
                // Modern hashed password — correct approach
                $password_valid = true;
            } elseif ($password === $user['password']) {
                // Plain text (seed data only) — works during development
                // TODO: Hash all passwords in production
                $password_valid = true;
            }

            if ($password_valid) {

                /**
                 * STEP 5: Start session — establish the user's identity
                 *
                 * session_regenerate_id() creates a new session ID.
                 * This prevents Session Fixation attacks.
                 */
                session_regenerate_id(true);

                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                /**
                 * STEP 6: Redirect based on role (Role-Based Access Control)
                 *
                 * Admins go to the full control dashboard.
                 * Managers go to the book catalog / requisition page.
                 */
                if ($user['role'] === 'admin') {
                    header('Location: dashboard.php');
                } else {
                    header('Location: index.php');
                }
                exit; // ALWAYS exit after header redirect

            } else {
                // Password wrong — use a vague message (don't say "wrong password" specifically)
                $error_message = 'Invalid username or password. Please try again.';
            }

        } else {
            // Username not found — same vague message (security best practice)
            $error_message = 'Invalid username or password. Please try again.';
        }
    }
}
// If we're still here, it's a GET request OR a failed POST — show the form
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login — Decorum Bookshop B2B Portal</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <div class="login-page">

    <div class="login-left">
      <div class="login-branding">
        <div class="brand-logo">📚</div>
        <h1>Decorum Bookshop<br>B2B Portal</h1>
        <p>The internal procurement system for branch managers and administrators. Manage inventory, submit requisitions, and track stock levels — all in one place.</p>
        <div class="login-features">
          <div class="login-feature">
            <div class="feat-icon">📦</div>
            Real-time inventory visibility across all branches
          </div>
          <div class="login-feature">
            <div class="feat-icon">📋</div>
            Submit and track book requisitions instantly
          </div>
          <div class="login-feature">
            <div class="feat-icon">🔐</div>
            Role-based access for Admins and Branch Managers
          </div>
        </div>
      </div>
    </div>

    <div class="login-right">
      <div class="login-form-container">

        <h2>Welcome back</h2>
        <p class="login-subtitle">Sign in to access the inventory portal</p>

        <?php if (!empty($error_message)): ?>
          <!-- PHP echo the error into an alert div -->
          <div class="alert alert-danger">
            ⚠️ <?php echo htmlspecialchars($error_message); ?>
          </div>
        <?php endif; ?>

        <!--
          METHOD="POST": Form data sent in the request body (not the URL)
          ACTION="login.php": This same file processes the submission
          NOVALIDATE: We handle validation ourselves (JS + PHP)
        -->
        <form class="login-form" action="login.php" method="POST" id="loginForm" novalidate>

          <div class="form-group">
            <label for="username">Username</label>
            <input
              type="text"
              id="username"
              name="username"
              placeholder="Enter your username"
              value="<?php echo $username_value; ?>"
              autocomplete="username"
              required
            >
            <span class="error-msg" id="usernameError">Username is required.</span>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="Enter your password"
              autocomplete="current-password"
              required
            >
            <span class="error-msg" id="passwordError">Password is required.</span>
          </div>

          <button type="submit" class="btn btn-login btn-primary">
            🔑 Sign In
          </button>

        </form>

        <div class="login-demo-creds">
          <strong>🧪 Demo Credentials</strong>
          Admin &nbsp;&nbsp;&nbsp;→ <code>admin</code> / <code>admin123</code><br>
          Manager → <code>manager1</code> / <code>pass123</code>
        </div>

      </div>
    </div>

  </div>

  <script src="js/validation.js"></script>
</body>
</html>
