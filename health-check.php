<?php
/**
 * Health Check Script
 * Visit: https://gamtech-electronic.com/health-check.php?key=gamtech2026health
 */

if (!isset($_GET['key']) || $_GET['key'] !== 'gamtech2026health') {
    http_response_code(403);
    die('Access denied');
}

header('Content-Type: text/plain');

echo "=== GAMTECH HEALTH CHECK ===\n\n";

// Check critical WordPress files
$critical_files = [
    'wp-includes/load.php',
    'wp-includes/functions.php',
    'wp-admin/admin.php',
    'wp-config.php',
    'index.php'
];

echo "Critical Files Status:\n";
foreach ($critical_files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    echo "  $status - $file\n";
    
    if (!$exists) {
        echo "    >>> CRITICAL: This file is missing!\n";
    }
}

// Check theme files
echo "\nTheme Files Status:\n";
$theme_files = [
    'wp-content/themes/woodmart-child/functions.php',
    'wp-content/themes/woodmart-child/header.php',
    'wp-content/themes/woodmart-child/footer.php',
    'wp-content/themes/woodmart-child/assets/gamtech-unified.js',
    'wp-content/themes/woodmart-child/assets/gamtech-unified.css'
];

foreach ($theme_files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    echo "  $status - $file\n";
}

// Check PHP version and memory
echo "\nPHP Configuration:\n";
echo "  PHP Version: " . PHP_VERSION . "\n";
echo "  Memory Limit: " . ini_get('memory_limit') . "\n";
echo "  Max Execution Time: " . ini_get('max_execution_time') . "s\n";

// Check if WooCommerce is loadable
echo "\nWordPress Status:\n";
if (file_exists(__DIR__ . '/wp-load.php')) {
    try {
        require_once __DIR__ . '/wp-load.php';
        echo "  ✓ WordPress loaded successfully\n";
        echo "  WordPress Version: " . get_bloginfo('version') . "\n";
        
        if (class_exists('WooCommerce')) {
            echo "  ✓ WooCommerce is active\n";
        } else {
            echo "  ✗ WooCommerce not found\n";
        }
    } catch (Exception $e) {
        echo "  ✗ WordPress load failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "  ✗ wp-load.php is missing\n";
}

// Check .git directory
echo "\nGit Status:\n";
if (is_dir(__DIR__ . '/.git')) {
    echo "  ✓ .git directory exists\n";
    
    // Check current branch and commit
    $head = @file_get_contents(__DIR__ . '/.git/HEAD');
    if ($head) {
        echo "  Current HEAD: " . trim($head) . "\n";
    }
} else {
    echo "  ✗ .git directory missing\n";
}

echo "\n=== END HEALTH CHECK ===\n";
echo "\nLast checked: " . date('Y-m-d H:i:s T') . "\n";
