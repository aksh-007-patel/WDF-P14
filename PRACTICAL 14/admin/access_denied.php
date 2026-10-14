<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_login();
$pageTitle='Access Denied'; $activeMenu='';
include __DIR__.'/../includes/header.php';
?>
<div class="alert alert-danger mt-3"><h4>403 · Access Denied</h4>
<p>Your role (<b><?= htmlspecialchars($_SESSION['user']['role']) ?></b>) is not allowed to view that page. This is RBAC working as required (Q2/Q3).</p>
<a href="dashboard.php" class="btn btn-sm btn-secondary">Back</a></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
