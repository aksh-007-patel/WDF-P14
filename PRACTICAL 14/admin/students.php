<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_role(['admin','staff']); // dynamic menu: staff can see, student cannot
$pageTitle='Students'; $activeMenu='Students';

if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['name'])) {
    $stmt=$conn->prepare("INSERT INTO students (name,email,course) VALUES (?,?,?)");
    $stmt->bind_param("sss", $_POST['name'], $_POST['email'], $_POST['course']);
    $stmt->execute(); $stmt->close();
    audit_log($conn, $_SESSION['user']['id'], 'STUDENT_CREATE', 'Added student: '.$_POST['email']);
    header('Location: students.php'); exit;
}
if (isset($_GET['delete'])) {
    require_role(['admin']);
    $id=(int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id=$id");
    audit_log($conn, $_SESSION['user']['id'], 'STUDENT_DELETE', "Deleted student id=$id");
    header('Location: students.php'); exit;
}
$list=$conn->query("SELECT * FROM students ORDER BY id DESC");
include __DIR__.'/../includes/header.php';
?>
<h3>Students</h3>
<div class="row g-3">
<div class="col-lg-4"><div class="card shadow-sm"><div class="card-header"><b>Add Student</b></div><div class="card-body">
<form method="post"><div class="mb-2"><input name="name" class="form-control" placeholder="Name" required></div>
<div class="mb-2"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
<div class="mb-2"><input name="course" class="form-control" placeholder="Course" value="BCA"></div>
<button class="btn btn-success btn-sm">Add</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<tr><th>ID</th><th>Name</th><th>Email</th><th>Course</th><?php if($_SESSION['user']['role']==='admin'): ?><th></th><?php endif; ?></tr>
<?php while($s=$list->fetch_assoc()): ?><tr><td><?= $s['id'] ?></td><td><?= htmlspecialchars($s['name']) ?></td>
<td><?= htmlspecialchars($s['email']) ?></td><td><?= htmlspecialchars($s['course']) ?></td>
<?php if($_SESSION['user']['role']==='admin'): ?><td><a class="btn btn-sm btn-outline-danger" href="?delete=<?= $s['id'] ?>" onclick="return confirm('Delete?')">Delete</a></td><?php endif; ?></tr><?php endwhile; ?>
</table></div></div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
