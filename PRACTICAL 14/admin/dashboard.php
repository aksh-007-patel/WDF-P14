<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/menu.php';
// Q2: access restricted to admin users only
require_role(['admin']);

$pageTitle = 'Dashboard'; $activeMenu = 'Dashboard';

// Q1: dashboard values loaded dynamically via SQL aggregation
$totalUsers   = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$activeUsers  = $conn->query("SELECT COUNT(*) c FROM users WHERE status='active'")->fetch_assoc()['c'];
$studentCount = $conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c'];
$eventCount   = $conn->query("SELECT COUNT(*) c FROM events")->fetch_assoc()['c'];
$regCount     = $conn->query("SELECT COUNT(*) c FROM registrations")->fetch_assoc()['c'];

$latestRegs = $conn->query("SELECT r.id, s.name AS student, e.title AS event, r.registered_at
  FROM registrations r JOIN students s ON s.id=r.student_id JOIN events e ON e.id=r.event_id
  ORDER BY r.registered_at DESC LIMIT 8");

// Advanced extension: registrations by month (last 6 months) for Chart.js
$chartLabels = []; $chartData = [];
$q = $conn->query("SELECT DATE_FORMAT(registered_at,'%b %Y') m, DATE_FORMAT(registered_at,'%Y-%m') k, COUNT(*) c
  FROM registrations GROUP BY k ORDER BY k DESC LIMIT 6");
$rows = $q ? $q->fetch_all(MYSQLI_ASSOC) : [];
$rows = array_reverse($rows);
foreach ($rows as $r) { $chartLabels[] = $r['m']; $chartData[] = (int)$r['c']; }

include __DIR__ . '/../includes/header.php';
?>
<h3>Admin Dashboard</h3>
<p class="text-muted">Q1–Q4 mapping: dynamic SQL counts · admin-only RBAC · dynamic menu · audit logging</p>
<div class="row g-3 mb-3">
  <div class="col-md-3"><div class="card text-bg-primary shadow-sm"><div class="card-body"><div class="small">Total Users</div><h3><?= $totalUsers ?></h3><div class="small"><?= $activeUsers ?> active</div></div></div></div>
  <div class="col-md-3"><div class="card text-bg-success shadow-sm"><div class="card-body"><div class="small">Students</div><h3><?= $studentCount ?></h3><a href="students.php" class="text-white small">Manage →</a></div></div></div>
  <div class="col-md-3"><div class="card text-bg-warning shadow-sm"><div class="card-body"><div class="small">Events</div><h3><?= $eventCount ?></h3><a href="events.php" class="text-dark small">Manage →</a></div></div></div>
  <div class="col-md-3"><div class="card text-bg-info shadow-sm"><div class="card-body"><div class="small">Registrations</div><h3><?= $regCount ?></h3><a href="registrations.php" class="text-white small">View →</a></div></div></div>
</div>
<div class="row g-3">
  <div class="col-lg-7">
    <div class="card shadow-sm"><div class="card-header"><b>Latest Registrations</b></div>
    <div class="table-responsive"><table class="table table-striped mb-0">
      <tr><th>#</th><th>Student</th><th>Event</th><th>Registered</th></tr>
      <?php while ($r = $latestRegs->fetch_assoc()): ?>
      <tr><td><?= $r['id'] ?></td><td><?= htmlspecialchars($r['student']) ?></td><td><?= htmlspecialchars($r['event']) ?></td><td class="small"><?= $r['registered_at'] ?></td></tr>
      <?php endwhile; ?>
    </table></div></div>
  </div>
  <div class="col-lg-5">
    <div class="card shadow-sm"><div class="card-header"><b>Analytics (Advanced): Registrations by Month</b></div>
    <div class="card-body"><canvas id="regChart"></canvas></div></div>
  </div>
</div>
<script>
new Chart(document.getElementById('regChart'), {type:'bar',
 data:{labels: <?= json_encode($chartLabels) ?>, datasets:[{label:'Registrations', data: <?= json_encode($chartData) ?>}]},
 options:{plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true, precision:0}}}});
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
