<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_role(['admin','staff']);
$pageTitle='Registrations'; $activeMenu='Registrations';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $sid=(int)$_POST['student_id']; $eid=(int)$_POST['event_id'];
    $stmt=$conn->prepare("INSERT IGNORE INTO registrations (student_id,event_id) VALUES (?,?)");
    $stmt->bind_param("ii",$sid,$eid); $stmt->execute(); $stmt->close();
    audit_log($conn, $_SESSION['user']['id'], 'REGISTRATION_CREATE', "student=$sid event=$eid");
    header('Location: registrations.php'); exit;
}
$list=$conn->query("SELECT r.id,s.name AS student,e.title AS event,r.registered_at FROM registrations r JOIN students s ON s.id=r.student_id JOIN events e ON e.id=r.event_id ORDER BY r.registered_at DESC LIMIT 100");
$students=$conn->query("SELECT id,name FROM students ORDER BY name");
$events=$conn->query("SELECT id,title FROM events ORDER BY title");
include __DIR__.'/../includes/header.php';
?>
<h3>Registrations</h3>
<div class="card shadow-sm mb-3"><div class="card-body"><form method="post" class="row g-2">
<div class="col-md-4"><select name="student_id" class="form-select"><?php while($s=$students->fetch_assoc()): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option><?php endwhile; ?></select></div>
<div class="col-md-4"><select name="event_id" class="form-select"><?php while($e=$events->fetch_assoc()): ?><option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['title']) ?></option><?php endwhile; ?></select></div>
<div class="col-md-4"><button class="btn btn-success">Register</button></div></form></div></div>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<tr><th>#</th><th>Student</th><th>Event</th><th>At</th></tr>
<?php while($r=$list->fetch_assoc()): ?><tr><td><?= $r['id'] ?></td><td><?= htmlspecialchars($r['student']) ?></td><td><?= htmlspecialchars($r['event']) ?></td><td class="small"><?= $r['registered_at'] ?></td></tr><?php endwhile; ?>
</table></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
