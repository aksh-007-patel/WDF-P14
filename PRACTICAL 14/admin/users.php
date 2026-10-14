<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_role(['admin']);
$pageTitle='Users'; $activeMenu='Users';

// Intermediate extension: active/inactive status toggle + audit log
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $row = $conn->query("SELECT status,email FROM users WHERE id=$id")->fetch_assoc();
    if ($row && $id !== (int)$_SESSION['user']['id']) {
        $new = ($row['status']==='active') ? 'inactive' : 'active';
        $stmt = $conn->prepare("UPDATE users SET status=? WHERE id=?");
        $stmt->bind_param("si", $new, $id);
        $stmt->execute(); $stmt->close();
        audit_log($conn, $_SESSION['user']['id'], 'USER_STATUS_TOGGLE', "User $id ({$row['email']}) set to $new");
    }
    header('Location: users.php'); exit;
}

$users = $conn->query("SELECT id,name,email,role,status,created_at FROM users ORDER BY id");
include __DIR__ . '/../includes/header.php';
?>
<h3>Users <span class="badge bg-secondary">admin only</span></h3>
<p class="text-muted">Intermediate extension: click toggle to activate/deactivate. Action is audit-logged.</p>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Toggle</th></tr>
<?php while($u=$users->fetch_assoc()): ?>
<tr>
<td><?= $u['id'] ?></td><td><?= htmlspecialchars($u['name']) ?></td><td><?= htmlspecialchars($u['email']) ?></td>
<td><span class="badge bg-primary"><?= $u['role'] ?></span></td>
<td><span class="badge <?= $u['status']==='active'?'bg-success':'bg-danger' ?>"><?= $u['status'] ?></span></td>
<td><?php if($u['id']!=$_SESSION['user']['id']): ?>
<a class="btn btn-sm btn-outline-secondary" href="?toggle=<?= $u['id'] ?>" onclick="return confirm('Toggle status?')">Toggle</a>
<?php else: ?><span class="text-muted small">self</span><?php endif; ?></td>
</tr><?php endwhile; ?>
</table></div></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
