<?php
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/audit.php';

$msg = $_GET['msg'] ?? '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id,name,email,password,role,status FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $u = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($u && password_verify($password, $u['password'])) {
        if ($u['status'] !== 'active') {
            $error = 'Account is inactive. Contact admin.';
        } else {
            $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
            audit_log($conn, $u['id'], 'LOGIN', 'User logged in: '.$u['email']);
            header('Location: /PRACTICAL 14/admin/dashboard.php');
            exit;
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
$pageTitle = 'Login';
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login | Practical 14</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-5">
<div class="card shadow"><div class="card-body p-4">
<h4 class="mb-1"><i class="bi bi-shield-lock me-2"></i>Admin Login</h4>
<p class="text-muted small">Practical 14 · Role-Based Access Control</p>
<?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($msg==='deactivated'): ?><div class="alert alert-warning">Account deactivated or session expired.</div><?php endif; ?>
<?php if ($msg==='loggedout'): ?><div class="alert alert-success">Logged out successfully.</div><?php endif; ?>
<form method="post">
<div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required value="admin@example.com"></div>
<div class="mb-3"><label class="form-label">Password</label><input name="password" type="password" class="form-control" required value="admin123"></div>
<button class="btn btn-primary w-100">Login</button>
</form>
<hr>
<div class="small text-muted">Demo accounts:<br>
<code>admin@example.com / admin123</code> (admin)<br>
<code>staff@example.com / staff123</code> (staff)<br>
<code>student@example.com / student123</code> (student)</div>
<a href="/PRACTICAL 14/setup.php" class="small">First run? Open setup.php to create DB + seed data</a>
</div></div></div></div></div>
</body></html>
