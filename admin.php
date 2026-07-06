<?php
/**
 * Admin entry point — /admin
 * Cookie-based auth
 */
session_start();

$already_authed = !empty($_SESSION['gs_admin']) || !empty($_COOKIE['gs_admin']);

if ($already_authed) {
    $_SESSION['gs_admin'] = true;
    require_once dirname(__FILE__) . '/admin-panel.php';
    exit;
}

if (isset($_GET['key']) && $_GET['key'] === 'gamtech2026admin') {
    $_SESSION['gs_admin'] = true;
    setcookie('gs_admin', '1', time() + 86400 * 30, '/');
    require_once dirname(__FILE__) . '/admin-panel.php';
    exit;
}

http_response_code(403);
echo '<!DOCTYPE html><html><head><title>Access Denied</title></head><body style="background:#0f0f13;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0"><div style="text-align:center"><h1>Access Denied</h1><p>You need to log in first.</p><a href="/admin.php?key=gamtech2026admin" style="color:#7c3aed">Login to Admin Panel</a></div></body></html>';
