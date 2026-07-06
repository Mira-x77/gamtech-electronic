<?php
/**
 * WordPress Core Recovery Script
 * Downloads and restores wp-includes and wp-admin
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

echo "<h1>WordPress Core Recovery</h1>";
echo "<pre style='background:#111;color:#0f0;padding:15px;font-size:13px'>";

$wp_dir = dirname(__FILE__);
$wp_zip = tempnam(sys_get_temp_dir(), 'wp') . '.zip';
$version = '6.8.1';

echo "WordPress version to restore: {$version}\n";
echo "Target directory: {$wp_dir}\n\n";

// Check what's missing
$missing = array();
if (!is_dir("{$wp_dir}/wp-includes") || !file_exists("{$wp_dir}/wp-includes/load.php")) {
    $missing[] = 'wp-includes';
}
if (!is_dir("{$wp_dir}/wp-admin") || !file_exists("{$wp_dir}/wp-admin/index.php")) {
    $missing[] = 'wp-admin';
}

if (empty($missing)) {
    echo "Both wp-includes and wp-admin exist. Checking integrity...\n";
    // Still might need to restore individual files
}

echo "Missing: " . implode(', ', $missing) . "\n\n";

// Download WordPress
echo "Downloading WordPress {$version}...\n";
$url = "https://wordpress.org/wordpress-{$version}.zip";
$ctx = stream_context_create(array('http' => array('timeout' => 120)));

$result = @file_get_contents($url, false, $ctx);
if ($result === false) {
    // Try with curl
    echo "file_get_contents failed, trying curl...\n";
    $ch = curl_init($url);
    $fp = fopen($wp_zip, 'w');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    if ($code != 200 || filesize($wp_zip) < 1000000) {
        echo "ERROR: Download failed (HTTP {$code}). File size: " . filesize($wp_zip) . "\n";
        echo "Try manual download from: https://wordpress.org/download/\n";
        exit;
    }
    echo "Downloaded via curl: " . number_format(filesize($wp_zip)) . " bytes\n";
} else {
    file_put_contents($wp_zip, $result);
    echo "Downloaded: " . number_format(filesize($wp_zip)) . " bytes\n";
}

// Extract
echo "\nExtracting...\n";
$zip = new ZipArchive();
if ($zip->open($wp_zip) === true) {
    $extract_dir = sys_get_temp_dir() . '/wp_extract_' . uniqid();
    $zip->extractTo($extract_dir);
    $zip->close();
    echo "Extracted to: {$extract_dir}\n";

    // Copy only what's needed
    $source = "{$extract_dir}/wordpress";

    foreach (array('wp-includes', 'wp-admin') as $dir) {
        if (is_dir("{$source}/{$dir}")) {
            echo "\nRestoring {$dir}...\n";

            // Remove existing broken dir if any
            if (is_dir("{$wp_dir}/{$dir}")) {
                // Don't remove - just overwrite
            }

            // Recursive copy
            $count = 0;
            $it = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator("{$source}/{$dir}", RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($it as $file) {
                $rel = substr($file->getPathname(), strlen("{$source}/{$dir}"));
                $dest = "{$wp_dir}/{$dir}{$rel}";

                if (!is_dir(dirname($dest))) {
                    @mkdir(dirname($dest), 0755, true);
                }

                if (copy($file->getPathname(), $dest)) {
                    $count++;
                } else {
                    echo "  FAILED: {$rel}\n";
                }
            }
            echo "  Copied {$count} files to {$dir}/\n";
        } else {
            echo "ERROR: {$dir} not found in downloaded archive\n";
        }
    }

    // Cleanup
    @unlink($wp_zip);
    @unlink($zip_file);

    echo "\n=== RECOVERY COMPLETE ===\n";
    echo "Test: https://gamtech-electronic.com/\n";
} else {
    echo "ERROR: Could not open ZIP file\n";
    echo "File size: " . filesize($wp_zip) . " bytes\n";
}

echo "</pre>";
echo "<br><a href='/' style='color:#0f0;font-size:18px'>Test Homepage</a>";
echo "<br><a href='/admin?key=gamtech2026admin' style='color:#0ff;font-size:18px'>Test Admin Panel</a>";
