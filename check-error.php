<?php
/**
 * Emergency Error Checker
 * Upload this to your server root and visit: https://gamtech-electronic.com/check-error.php
 * This will show you EXACTLY what's breaking
 */

// Show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>GamTech Error Diagnostic Tool</h1>";
echo "<hr>";

// Test 1: PHP works
echo "<h2>✅ Test 1: PHP is working</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "<br><br>";

// Test 2: WordPress path
echo "<h2>Test 2: WordPress Files</h2>";
$wp_load = __DIR__ . '/wp-load.php';
$wp_config = __DIR__ . '/wp-config.php';
$functions = __DIR__ . '/wp-content/themes/woodmart-child/functions.php';

echo "wp-load.php exists: " . (file_exists($wp_load) ? '✅ YES' : '❌ NO') . "<br>";
echo "wp-config.php exists: " . (file_exists($wp_config) ? '✅ YES' : '❌ NO') . "<br>";
echo "functions.php exists: " . (file_exists($functions) ? '✅ YES' : '❌ NO') . "<br><br>";

// Test 3: Read error log
echo "<h2>Test 3: Error Log (Last 30 lines)</h2>";
$error_log = __DIR__ . '/error_log';
if (file_exists($error_log)) {
    $lines = file($error_log);
    $last_30 = array_slice($lines, -30);
    echo "<pre style='background:#000;color:#0f0;padding:15px;overflow:auto;max-height:400px;'>";
    echo htmlspecialchars(implode('', $last_30));
    echo "</pre>";
} else {
    echo "❌ No error_log file found at: $error_log<br>";
}

// Test 4: Try loading WordPress
echo "<h2>Test 4: Try Loading WordPress</h2>";
if (file_exists($wp_load)) {
    try {
        require_once $wp_load;
        echo "✅ WordPress loaded successfully!<br>";
        echo "Site URL: " . get_bloginfo('url') . "<br>";
        echo "WP Version: " . get_bloginfo('version') . "<br>";
    } catch (Exception $e) {
        echo "❌ WordPress failed to load:<br>";
        echo "<pre style='background:#f00;color:#fff;padding:10px;'>";
        echo htmlspecialchars($e->getMessage());
        echo "</pre>";
    }
} else {
    echo "❌ Cannot test - wp-load.php not found<br>";
}

// Test 5: Check theme files syntax
echo "<h2>Test 5: PHP Syntax Check</h2>";
$files_to_check = [
    'wp-config.php',
    'wp-content/themes/woodmart-child/functions.php',
    'wp-content/themes/woodmart-child/front-page.php',
    'wp-content/themes/woodmart-child/header.php',
    'wp-content/themes/woodmart-child/inc/gamtech-core.php',
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $output = [];
        $return = 0;
        exec("php -l " . escapeshellarg($full_path) . " 2>&1", $output, $return);
        if ($return === 0) {
            echo "✅ $file - OK<br>";
        } else {
            echo "❌ $file - SYNTAX ERROR:<br>";
            echo "<pre style='background:#f00;color:#fff;padding:10px;'>";
            echo htmlspecialchars(implode("\n", $output));
            echo "</pre>";
        }
    } else {
        echo "⚠️ $file - NOT FOUND<br>";
    }
}

// Test 6: Git status
echo "<h2>Test 6: Git Status</h2>";
if (file_exists(__DIR__ . '/.git')) {
    echo "<pre style='background:#333;color:#fff;padding:10px;'>";
    $git_output = shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git status 2>&1');
    echo htmlspecialchars($git_output);
    echo "</pre>";
    
    echo "<h3>Last 5 commits:</h3>";
    echo "<pre style='background:#333;color:#fff;padding:10px;'>";
    $git_log = shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git log --oneline -5 2>&1');
    echo htmlspecialchars($git_log);
    echo "</pre>";
} else {
    echo "❌ Not a Git repository<br>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Copy ALL the output above</li>";
echo "<li>Send it to your developer</li>";
echo "<li>The error log section (Test 3) shows what's breaking</li>";
echo "</ol>";

// Self-destruct option
if (isset($_GET['delete'])) {
    unlink(__FILE__);
    die("✅ check-error.php deleted");
}

echo "<br><a href='?delete=1' style='color:red;'>Delete this file after use</a>";
?>
