<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
require_role(['admin','staff']);
$pageTitle='Events'; $activeMenu='Events';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['title'])) {
    $stmt=$conn->prepare("INSERT INTO events (title,event_date,location) VALUES (?,?,?)");
    $stmt->bind_param("sss", $_POST['title'], $_POST['event_date'], $_POST['location']);
    $stmt->execute(); $stmt->close();
    audit_log($conn, $_SESSION['user']['id'], 'EVENT_CREATE', 'Added event: '.$_POST['title']);
    header('Location: events.php'); exit;
}
if (isset($_GET['delete'])) {
    require_role(['admin']);
    $id=(int)$_GET['delete'];
    $conn->query("DELETE FROM events WHERE id=$id");
    audit_log($conn, $_SESSION['user']['id'], 'EVENT_DELETE', "Deleted event id=$id");
    header('Location: events.php'); exit;
}
$list=$conn->query("SELECT e.*, (SELECT COUNT(*) FROM registrations r WHERE r.event_id=e.id) AS regs FROM events e ORDER BY event_date DESC");
include __DIR__.'/../includes/header.php';
?>
<h3>Events</h3>
<div class="row g-3">
<div class="col-lg-4"><div class="card shadow-sm"><div class="card-header"><b>Add Event</b></div><div class="card-body">
<form method="post">
<div class="mb-2"><input name="title" class="form-control" placeholder="Title" required></div>
<div class="mb-2"><input name="event_date" type="date" class="form-control" required></div>
<div class="mb-2"><input name="location" class="form-control" placeholder="Location" value="Main Hall"></div>
<button class="btn btn-success btn-sm">Add</button></form></div></div></div>
<div class="col-lg-8"><div class="card shadow-sm"><div class="table-responsive"><table class="table table-striped mb-0">
<tr><th>ID</th><th>Title</th><th>Date</th><th>Location</th><th>Regs</th><?php if($_SESSION['user']['role']==='admin'): ?><th></th><?php endif; ?></tr>
<?php while($e=$list->fetch_assoc()): ?><tr><td><?= $e['id'] ?></td><td><?= htmlspecialchars($e['title']) ?></td>
<td><?= $e['event_date'] ?></td><td><?= htmlspecialchars($e['location']) ?></td><td><?= $e['regs'] ?></td>
<?php if($_SESSION['user']['role']==='admin'): ?><td><a class="btn btn-sm btn-outline-danger" href="?delete=<?= $e['id'] ?>" onclick="return confirm('Delete?')">Delete</a></td><?php endif; ?></tr><?php endwhile; ?>
</table></div></div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
