<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? htmlspecialchars($pageTitle).' | ' : '' ?>Practical 14 Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<style>body{background:#f4f6f9}.sidebar{position:sticky;top:0}</style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-3">
  <a class="navbar-brand" href="/PRACTICAL 14/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Practical 14 · Admin Panel</a>
  <div class="d-flex align-items-center gap-2">
    <?php if (isset($_SESSION['user'])): ?>
      <span class="badge bg-primary"><?= htmlspecialchars($_SESSION['user']['role']) ?></span>
      <span class="text-white-50 small"><?= htmlspecialchars($_SESSION['user']['name']) ?></span>
      <a href="/PRACTICAL 14/auth/logout.php" class="btn btn-sm btn-outline-light">Logout</a>
    <?php endif; ?>
  </div>
</nav>
<div class="container-fluid py-3">
<div class="row">
<?php if (isset($_SESSION['user']) && isset($conn)): ?>
<div class="col-md-3 col-lg-2 mb-3">
  <div class="list-group sidebar shadow-sm">
    <?= render_menu($conn, $activeMenu ?? '') ?>
  </div>
  <div class="card mt-3 shadow-sm"><div class="card-body small text-muted">
    Logged in as<br><b><?= htmlspecialchars($_SESSION['user']['email']) ?></b>
  </div></div>
</div>
<div class="col-md-9 col-lg-10">
<?php else: ?>
<div class="col-12">
<?php endif; ?>
