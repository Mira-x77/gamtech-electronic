<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GamTech Error Diagnostic Tool</h1><hr>";

echo "<h2>Test 1: PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? '?') . "<br><br>";

echo "<h2>Test 2: Critical File Check</h2>";
$files = [
    'wp-load.php',
    'wp-config.php',
    'wp-includes/load.php',
    'wp-includes/version.php',
    'wp-settings.php',
    'wp-content/themes/woodmart-child/functions.php',
    'wp-content/themes/woodmart-child/inc/gamtech-core.php',
    'wp-content/themes/woodmart-child/bulk-import-from-gam.php',
    'wp-content/themes/woodmart-child/inc/gamtech-import-chargers.php',
    '.htaccess',
    'deploy.php',
];
foreach ($files as $f) {
    $p = __DIR__ . '/' . $f;
    echo ($f . ': ' . (file_exists($p) ? '✅ YES' : '❌ MISSING')) . "<br>";
}

echo "<br><h2>Test 3: Error Log (last 30 lines)</h2>";
$el = __DIR__ . '/error_log';
if (file_exists($el)) {
    $lines = file($el);
    echo "<pre style='background:#000;color:#0f0;padding:15px;overflow:auto;max-height:400px;'>";
    echo htmlspecialchars(implode('', array_slice($lines, -30)));
    echo "</pre>";
} else {
    echo "❌ No error_log found<br>";
}

echo "<h2>Test 4: Git Status</h2>";
if (is_dir(__DIR__ . '/.git')) {
    echo "<pre style='background:#333;color:#fff;padding:10px;'>";
    echo htmlspecialchars(shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git status 2>&1') ?: '');
    echo "</pre>";
    echo "<h3>Last 10 commits:</h3><pre style='background:#333;color:#fff;padding:10px;'>";
    echo htmlspecialchars(shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git log --oneline -10 2>&1') ?: '');
    echo "</pre>";
    echo "<h3>Check if load.php is tracked:</h3><pre style='background:#333;color:#fff;padding:10px;'>";
    echo htmlspecialchars(shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git ls-files wp-includes/load.php 2>&1') ?: '');
    echo "</pre>";
} else {
    echo "❌ Not a Git repo<br>";
}

echo "<h2>Test 5: PHP Syntax Check</h2>";
foreach ($files as $f) {
    if (strpos($f, '.php') === false) continue;
    $p = __DIR__ . '/' . $f;
    if (!file_exists($p)) { echo "⚠️ $f - NOT FOUND<br>"; continue; }
    $out = []; $rv = 0;
    exec("php -l " . escapeshellarg($p) . " 2>&1", $out, $rv);
    echo ($rv === 0 ? "✅ " : "❌ ") . "$f - " . ($rv === 0 ? "OK" : "SYNTAX ERROR") . "<br>";
    if ($rv !== 0) echo "<pre style='background:#f00;color:#fff;padding:10px;'>" . htmlspecialchars(implode("\n", $out)) . "</pre>";
}

echo "<hr><p>To delete this file: <a href='?delete=1'>Delete</a></p>";
if (isset($_GET['delete'])) { unlink(__FILE__); die("✅ Deleted"); }
