<?php
// includes/menu.php - dynamic role-based menu loaded from `menus` table
function render_menu($conn, $active = '') {
    $role = $_SESSION['user']['role'] ?? 'student';
    $out = '';
    $res = $conn->query("SELECT label, url, icon, allowed_roles FROM menus WHERE is_active=1 ORDER BY sort_order");
    if (!$res) return $out;
    while ($m = $res->fetch_assoc()) {
        $allowed = array_map('trim', explode(',', $m['allowed_roles']));
        if (!in_array($role, $allowed, true)) continue;
        $isActive = ($active === $m['label']) ? 'active' : '';
        $label = htmlspecialchars($m['label']);
        $url   = htmlspecialchars($m['url']);
        $icon  = htmlspecialchars($m['icon']);
        $out .= "<a href=\"{$url}\" class=\"list-group-item list-group-item-action {$isActive}\"><i class=\"bi {$icon} me-2\"></i>{$label}</a>";
    }
    return $out;
}
