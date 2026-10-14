<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_login();
$pageTitle='My Profile'; $activeMenu='My Profile';
$u = $_SESSION['user'];
include __DIR__.'/../includes/header.php';
?>
<h3>My Profile</h3>
<div class="card shadow-sm"><div class="card-body">
<p><b>Name:</b> <?= htmlspecialchars($u['name']) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($u['email']) ?></p>
<p><b>Role:</b> <span class="badge bg-primary"><?= htmlspecialchars($u['role']) ?></span></p>
<p class="text-muted small">Menu on the left is dynamic — loaded from <code>menus</code> table filtered by your role.</p>
</div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
