<?php
/**
 * EMERGENCY FIX - gamtech-electronic.com
 * Upload this file to your server root and visit: https://gamtech-electronic.com/emergency-fix.php
 */

// Enable error display
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🚨 Emergency Recovery Tool</h1>";
echo "<style>body{background:#000;color:#0f0;font-family:monospace;padding:20px;}h1,h2{color:#0ff;}.btn{background:#0f0;color:#000;padding:10px 20px;text-decoration:none;display:inline-block;margin:5px;}</style>";

// Get action
$action = $_GET['action'] ?? '';

if (!$action) {
    echo "<h2>Choose a fix:</h2>";
    echo "<a href='?action=check' class='btn'>1. Check Errors</a><br>";
    echo "<a href='?action=disable_child_theme' class='btn'>2. Disable Child Theme (SAFE)</a><br>";
    echo "<a href='?action=disable_plugins' class='btn'>3. Disable All Plugins</a><br>";
    echo "<a href='?action=git_status' class='btn'>4. Check Git Status</a><br>";
    echo "<a href='?action=git_reset' class='btn'>5. Reset to GitHub (CAREFUL)</a><br>";
    echo "<a href='?action=test_wp' class='btn'>6. Test WordPress Load</a><br>";
    exit;
}

// Check errors
if ($action === 'check') {
    echo "<h2>Error Log (Last 50 lines)</h2>";
    $log = __DIR__ . '/error_log';
    if (file_exists($log)) {
        $lines = file($log);
        echo "<pre style='background:#222;padding:10px;'>";
        echo htmlspecialchars(implode('', array_slice($lines, -50)));
        echo "</pre>";
    } else {
        echo "No error_log found<br>";
    }
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Disable child theme (rename folder)
if ($action === 'disable_child_theme') {
    $child = __DIR__ . '/wp-content/themes/woodmart-child';
    $disabled = __DIR__ . '/wp-content/themes/woodmart-child-DISABLED-' . date('YmdHis');
    
    if (is_dir($child)) {
        if (rename($child, $disabled)) {
            echo "✅ Child theme DISABLED<br>";
            echo "Renamed: woodmart-child → " . basename($disabled) . "<br>";
            echo "<br>Now try visiting: <a href='/' style='color:#0ff;'>Your Homepage</a><br>";
            echo "<br>If site works now, the problem is in the child theme.<br>";
            echo "<br><a href='?action=enable_child_theme' class='btn'>Re-enable Child Theme</a>";
        } else {
            echo "❌ Failed to rename (check permissions)<br>";
        }
    } else {
        echo "⚠️ Child theme folder not found<br>";
        // Try to re-enable
        $disabled_dirs = glob(__DIR__ . '/wp-content/themes/woodmart-child-DISABLED-*');
        if ($disabled_dirs) {
            foreach ($disabled_dirs as $dir) {
                echo "<a href='?action=enable&dir=" . urlencode(basename($dir)) . "' class='btn'>Re-enable " . basename($dir) . "</a><br>";
            }
        }
    }
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Re-enable child theme
if ($action === 'enable_child_theme' || $action === 'enable') {
    $dir = $_GET['dir'] ?? 'woodmart-child-DISABLED-*';
    $disabled_dirs = glob(__DIR__ . '/wp-content/themes/' . $dir);
    if ($disabled_dirs) {
        $disabled = $disabled_dirs[0];
        $enabled = __DIR__ . '/wp-content/themes/woodmart-child';
        if (rename($disabled, $enabled)) {
            echo "✅ Child theme RE-ENABLED<br>";
            echo "<a href='/' style='color:#0ff;'>Test your site</a><br>";
        } else {
            echo "❌ Failed to rename back<br>";
        }
    }
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Disable plugins
if ($action === 'disable_plugins') {
    $plugins = __DIR__ . '/wp-content/plugins';
    $disabled = __DIR__ . '/wp-content/plugins-DISABLED-' . date('YmdHis');
    
    if (is_dir($plugins)) {
        if (rename($plugins, $disabled)) {
            echo "✅ All plugins DISABLED<br>";
            echo "Renamed: plugins → " . basename($disabled) . "<br>";
            echo "<br>Now try visiting: <a href='/' style='color:#0ff;'>Your Homepage</a><br>";
            echo "<br>If site works now, a plugin was crashing it.<br>";
            echo "<br><a href='?action=enable_plugins' class='btn'>Re-enable Plugins</a>";
        } else {
            echo "❌ Failed to rename (check permissions)<br>";
        }
    } else {
        echo "⚠️ Plugins folder not found or already disabled<br>";
        $disabled_dirs = glob(__DIR__ . '/wp-content/plugins-DISABLED-*');
        if ($disabled_dirs) {
            foreach ($disabled_dirs as $dir) {
                echo "<a href='?action=enable_plugins&dir=" . urlencode(basename($dir)) . "' class='btn'>Re-enable " . basename($dir) . "</a><br>";
            }
        }
    }
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Re-enable plugins
if ($action === 'enable_plugins') {
    $dir = $_GET['dir'] ?? 'plugins-DISABLED-*';
    $disabled_dirs = glob(__DIR__ . '/wp-content/' . $dir);
    if ($disabled_dirs) {
        $disabled = $disabled_dirs[0];
        $enabled = __DIR__ . '/wp-content/plugins';
        if (rename($disabled, $enabled)) {
            echo "✅ Plugins RE-ENABLED<br>";
            echo "<a href='/' style='color:#0ff;'>Test your site</a><br>";
        } else {
            echo "❌ Failed to rename back<br>";
        }
    }
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Git status
if ($action === 'git_status') {
    echo "<h2>Git Status</h2>";
    echo "<pre style='background:#222;padding:10px;'>";
    echo htmlspecialchars(shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git status 2>&1'));
    echo "</pre>";
    
    echo "<h2>Last 5 Commits</h2>";
    echo "<pre style='background:#222;padding:10px;'>";
    echo htmlspecialchars(shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git log --oneline -5 2>&1'));
    echo "</pre>";
    
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Git reset (CAREFUL!)
if ($action === 'git_reset') {
    if (!isset($_GET['confirm'])) {
        echo "<h2>⚠️ WARNING</h2>";
        echo "This will RESET all files to match GitHub (loses any local changes)<br><br>";
        echo "<a href='?action=git_reset&confirm=yes' class='btn' style='background:#f00;'>YES, RESET TO GITHUB</a><br>";
        echo "<a href='?' class='btn'>Cancel</a>";
        exit;
    }
    
    echo "<h2>Resetting to GitHub...</h2>";
    echo "<pre style='background:#222;padding:10px;'>";
    
    // Fetch and reset (NO git clean -fd — that deletes WordPress core files!)
    echo shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git fetch origin 2>&1');
    echo "\n";
    echo shell_exec('cd ' . escapeshellarg(__DIR__) . ' && git reset --hard origin/main 2>&1');
    echo "\n";
    // DO NOT run git clean -fd — it deletes wp-includes, wp-admin, and all uploads!
    echo "✅ Reset complete (WordPress core files preserved)\n";
    
    echo "</pre>";
    echo "✅ Reset complete<br>";
    echo "<a href='/' style='color:#0ff;'>Test your site now</a><br>";
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}

// Test WordPress load
if ($action === 'test_wp') {
    echo "<h2>Testing WordPress Load...</h2>";
    $wp_load = __DIR__ . '/wp-load.php';
    
    if (!file_exists($wp_load)) {
        echo "❌ wp-load.php not found!<br>";
        echo "<br><a href='?' class='btn'>Back</a>";
        exit;
    }
    
    try {
        require_once $wp_load;
        echo "✅ WordPress loaded successfully!<br>";
        echo "Site: " . get_bloginfo('url') . "<br>";
        echo "WP Version: " . get_bloginfo('version') . "<br>";
        echo "<br><strong>WordPress core is working. The problem is likely:</strong><br>";
        echo "- Child theme<br>";
        echo "- A plugin<br>";
        echo "- .htaccess<br>";
    } catch (Exception $e) {
        echo "❌ WordPress FAILED to load:<br>";
        echo "<pre style='background:#f00;color:#fff;padding:10px;'>";
        echo htmlspecialchars($e->getMessage());
        echo "</pre>";
    } catch (Error $e) {
        echo "❌ PHP Fatal Error:<br>";
        echo "<pre style='background:#f00;color:#fff;padding:10px;'>";
        echo htmlspecialchars($e->getMessage());
        echo "</pre>";
    }
    
    echo "<br><a href='?' class='btn'>Back</a>";
    exit;
}
?>
