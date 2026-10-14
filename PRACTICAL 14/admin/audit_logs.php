<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_role(['admin']); // Q4: audit logs visible to admin only
$pageTitle='Audit Logs'; $activeMenu='Audit Logs';
$logs=$conn->query("SELECT a.*, u.email FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id ORDER BY a.created_at DESC LIMIT 200");
include __DIR__.'/../includes/header.php';
?>
<h3>Audit Logs <span class="badge bg-secondary">Q4 traceability</span></h3>
<p class="text-muted">Important admin actions (login, status toggle, CRUD) are recorded here with user + IP + timestamp.</p>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0 small">
<tr><th>ID</th><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>At</th></tr>
<?php while($l=$logs->fetch_assoc()): ?><tr>
<td><?= $l['id'] ?></td><td><?= htmlspecialchars($l['email'] ?? ('#'.$l['user_id'])) ?></td>
<td><span class="badge bg-dark"><?= htmlspecialchars($l['action']) ?></span></td>
<td><?= htmlspecialchars($l['details']) ?></td><td><?= htmlspecialchars($l['ip_address']) ?></td><td><?= $l['created_at'] ?></td>
</tr><?php endwhile; ?>
</table></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
