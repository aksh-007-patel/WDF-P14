<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$conn = null;
try {
    $tmp = new mysqli('localhost','root','', 'practical14_db');
    if (!$tmp->connect_error) { $conn = $tmp; $tmp->set_charset('utf8mb4'); }
} catch (Throwable $e) { $conn = null; }
if ($conn && isset($_SESSION['user'])) {
    require_once __DIR__ . '/../includes/audit.php';
    audit_log($conn, $_SESSION['user']['id'], 'LOGOUT', 'User logged out');
}
session_unset(); session_destroy();
header('Location: /PRACTICAL 14/auth/login.php?msg=loggedout');
exit;
