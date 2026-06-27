<?php
/**
 * Temporary debug file — check site PHP errors
 * DELETE after fixing the issue!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h2>GamTech Site Debug</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check if WP loaded
echo "<p><strong>ABSPATH defined:</strong> " . (defined('ABSPATH') ? 'yes' : 'no') . "</p>";

// Check wp-config.php
$wp_config = dirname(__FILE__) . '/wp-config.php';
echo "<p><strong>wp-config.php:</strong> " . (file_exists($wp_config) ? 'exists (' . filesize($wp_config) . ' bytes)' : 'MISSING') . "</p>";

// Check theme
$theme_dir = dirname(__FILE__) . '/wp-content/themes/woodmart-child';
echo "<p><strong>Child theme dir:</strong> " . (is_dir($theme_dir) ? 'exists' : 'MISSING') . "</p>";

// Check key files
$files = array(
    'functions.php' => $theme_dir . '/functions.php',
    'header.php' => $theme_dir . '/header.php',
    'footer.php' => $theme_dir . '/footer.php',
    'front-page.php' => $theme_dir . '/front-page.php',
    'gamtech-core.php' => $theme_dir . '/inc/gamtech-core.php',
    'gamtech-unified.css' => $theme_dir . '/assets/gamtech-unified.css',
    'gamtech-unified.js' => $theme_dir . '/assets/gamtech-unified.js',
);
foreach ($files as $name => $path) {
    echo "<p><strong>$name:</strong> " . (file_exists($path) ? 'OK (' . filesize($path) . 'b)' : 'MISSING') . "</p>";
}

// Check woodmart parent theme
$parent_dir = dirname(__FILE__) . '/wp-content/themes/woodmart';
echo "<p><strong>Parent theme (woodmart):</strong> " . (is_dir($parent_dir) ? 'exists' : 'MISSING') . "</p>";

// Check WooCommerce
$woo_dir = dirname(__FILE__) . '/wp-content/plugins/woocommerce';
echo "<p><strong>WooCommerce plugin:</strong> " . (is_dir($woo_dir) ? 'exists' : 'MISSING') . "</p>";

// Check error log
$log_file = dirname(__FILE__) . '/wp-content/debug.log';
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $lines = explode("\n", $log_content);
    $recent = array_slice($lines, -30);
    echo "<h3>Last 30 lines of debug.log:</h3>";
    echo "<pre>" . htmlspecialchars(implode("\n", $recent)) . "</pre>";
} else {
    echo "<p><strong>debug.log:</strong> not found</p>";
}

// Try loading WP
echo "<h3>Attempting WordPress load:</h3>";
echo "<pre>";
flush();
ob_flush();
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "ERROR [$errno]: $errstr in $errfile:$errline\n";
});
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error) {
        echo "\nFATAL: " . $error['message'] . " in " . $error['file'] . ":" . $error['line'] . "\n";
    }
});

try {
    require_once dirname(__FILE__) . '/wp-blog-header.php';
    echo "WordPress loaded successfully!\n";
    echo "Site URL: " . home_url() . "\n";
} catch (\Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack:\n" . $e->getTraceAsString() . "\n";
}
echo "</pre>";
