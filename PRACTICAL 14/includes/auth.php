<?php
// includes/auth.php - session + RBAC guards
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/audit.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}
function is_logged_in() {
    return isset($_SESSION['user']);
}
// Restrict to logged-in users; inactive users are forced out
function require_login() {
    global $conn;
    if (!is_logged_in()) {
        header('Location: /PRACTICAL 14/auth/login.php');
        exit;
    }
    // re-validate status from DB (for toggle test case)
    require_once __DIR__ . '/../config/db.php';
    $id = (int)$_SESSION['user']['id'];
    $stmt = $conn->prepare("SELECT status FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$res || $res['status'] !== 'active') {
        session_unset(); session_destroy();
        header('Location: /PRACTICAL 14/auth/login.php?msg=deactivated');
        exit;
    }
}
// Restrict page to given roles, e.g. require_role(['admin'])
function require_role($roles = ['admin']) {
    require_login();
    $role = $_SESSION['user']['role'] ?? '';
    if (!in_array($role, (array)$roles, true)) {
        http_response_code(403);
        header('Location: /PRACTICAL 14/admin/access_denied.php');
        exit;
    }
}
