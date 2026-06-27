<?php

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

// Determine active navigation link
$is_active = function($page) {
    return basename($_SERVER['PHP_SELF']) === $page ? 'active' : '';
};
?>
<?php
// Central header
$page_title = $role === 'admin' ? 'All Requisitions — Decorum' : 'My Requisitions — Decorum';
$page_heading = $role === 'admin' ? 'All Requisitions' : 'My Requisitions';
$topbar_actions = $role === 'manager' ? '<a href="index.php" class="btn btn-primary">➕ New Requisition</a>' : '';
include 'includes/master_header.php';
?>

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

</script>
<script src="js/validation.js"></script>
<script src="js/app.js"></script>
</body>
</html>
