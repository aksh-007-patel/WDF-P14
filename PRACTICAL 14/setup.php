<?php
// setup.php - one-click DB creation + seeding (fixes password hashes too). Delete after use or keep.
$host='localhost'; $user='root'; $pass='';
$adminPass = password_hash('admin123', PASSWORD_DEFAULT);
$staffPass = password_hash('staff123', PASSWORD_DEFAULT);
$studPass  = password_hash('student123', PASSWORD_DEFAULT);

$m = new mysqli($host,$user,$pass);
if ($m->connect_error) die('MySQL connect failed: '.$m->connect_error.' — start MySQL in XAMPP.');
$m->query("CREATE DATABASE IF NOT EXISTS practical14_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$m->close();

require __DIR__.'/config/db.php';
// run schema
$sql = file_get_contents(__DIR__.'/database.sql');
// strip seed password placeholder lines by executing multi-query in parts
if (!$conn->multi_query($sql)) { echo 'Import error: '.$conn->error; exit; }
// flush multi results properly (exception-safe for re-runs)
mysqli_report(MYSQLI_REPORT_OFF);
do { if ($res = $conn->store_result()) $res->free(); } while ($conn->more_results() && $conn->next_result());
// fix password hashes deterministically (prepared -> no truncation issues)
$stmt = $conn->prepare("UPDATE users SET password=? WHERE email=?");
$email='admin@example.com'; $stmt->bind_param("ss", $adminPass, $email); $stmt->execute();
$email='staff@example.com'; $stmt->bind_param("ss", $staffPass, $email); $stmt->execute();
$email='student@example.com'; $stmt->bind_param("ss", $studPass, $email); $stmt->execute();
$stmt->close();
echo "<!-- hash len: ".strlen($adminPass)." -->\n";
echo "<h3>Setup done ✔</h3><p>DB <b>practical14_db</b> created & seeded.</p>";
echo "<p>Login: <a href='/PRACTICAL 14/auth/login.php'>/PRACTICAL 14/auth/login.php</a> — admin@example.com / admin123</p>";
