<?php
/**
 * =============================================================
 * WEEK 4 — requisitions.php (My Requisitions — Manager view)
 * Shows the logged-in manager's submitted requisitions from the DB
 * =============================================================
 */
require_once __DIR__ . '/includes/auth_guard.php';
require_once __DIR__ . '/includes/db_connect.php';

requireLogin();

$userId   = sessionGet('user_id');
$role     = sessionGet('role');
$fullName = sessionGet('full_name');
$initials = getUserInitials();

// Managers see only their own; admins see all
if ($role === 'admin') {
    $stmt = $pdo->query(
        "SELECT r.*, u.full_name AS manager_name,
                COUNT(ri.id) AS item_count,
                COALESCE(SUM(ri.quantity_requested),0) AS total_qty
         FROM requisitions r
         JOIN users u ON r.manager_id = u.id
         LEFT JOIN requisition_items ri ON ri.requisition_id = r.id
         GROUP BY r.id ORDER BY r.date_submitted DESC"
    );
} else {
    $stmt = $pdo->prepare(
        "SELECT r.*, u.full_name AS manager_name,
                COUNT(ri.id) AS item_count,
                COALESCE(SUM(ri.quantity_requested),0) AS total_qty
         FROM requisitions r
         JOIN users u ON r.manager_id = u.id
         LEFT JOIN requisition_items ri ON ri.requisition_id = r.id
         WHERE r.manager_id = :uid
         GROUP BY r.id ORDER BY r.date_submitted DESC"
    );
    $stmt->execute(['uid' => $userId]);
}
$requisitions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Requisitions — Decorum Bookshop (Week 4)</title>
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
        <li class="nav-item"><a href="requisitions.php" class="active"><span class="nav-icon">📦</span>
          <?php echo $role === 'admin' ? 'All Requisitions' : 'My Requisitions'; ?>
        </a></li>
      </ul>
      <?php if ($role === 'admin'): ?>
      <p class="nav-section-label" style="margin-top:16px;">Administration</p>
      <ul>
        <li class="nav-item"><a href="dashboard.php"><span class="nav-icon">⚙️</span> Admin Dashboard</a></li>
      </ul>
      <?php endif; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar"><?php echo $initials; ?></div>
        <div class="user-details">
          <div class="user-name"><?php echo htmlspecialchars($fullName); ?></div>
          <div class="user-role"><?php echo ucfirst($role); ?></div>
        </div>
      </div>
      <a href="logout.php" class="btn btn-logout btn-sm" style="width:100%;justify-content:center;">🚪 Logout</a>
    </div>
  </aside>

  <main class="main-content">
    <div class="topbar">
      <div class="topbar-left">
        <h2><?php echo $role === 'admin' ? 'All Requisitions' : 'My Requisitions'; ?></h2>
        <div class="breadcrumb">Home › Requisitions</div>
      </div>
      <div class="topbar-right">
        <?php if ($role === 'manager'): ?>
          <a href="index.php" class="btn btn-primary">➕ New Requisition</a>
        <?php endif; ?>
      </div>
    </div>

    <div class="page-body">

      <div class="table-card">
        <div class="table-header">
          <div>
            <h3><?php echo $role === 'admin' ? 'All Requisitions' : 'My Submitted Requisitions'; ?></h3>
            <div class="table-subtitle"><?php echo count($requisitions); ?> records</div>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              <th>Req #</th>
              <?php if ($role === 'admin'): ?><th>Manager</th><?php endif; ?>
              <th>Date Submitted</th>
              <th>Items</th>
              <th>Total Qty</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($requisitions)): ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:40px;color:var(--grey-400);">
                  No requisitions found. <a href="index.php" style="color:var(--blue-medium);">Submit your first one →</a>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($requisitions as $req): ?>
              <tr>
                <td style="font-weight:700;">#<?php echo $req['id']; ?></td>
                <?php if ($role === 'admin'): ?><td><?php echo htmlspecialchars($req['manager_name']); ?></td><?php endif; ?>
                <td style="font-size:0.82rem;"><?php echo date('d M Y, H:i', strtotime($req['date_submitted'])); ?></td>
                <td><?php echo $req['item_count']; ?> titles</td>
                <td><?php echo number_format($req['total_qty']); ?> units</td>
                <td><span class="status-badge status-<?php echo $req['status']; ?>"><?php echo ucfirst($req['status']); ?></span></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script src="js/validation.js"></script>
</body>
</html>
