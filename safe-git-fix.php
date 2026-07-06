<?php
/**
 * Safe Git Fix - restores main branch WITHOUT deleting untracked files
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);

echo "<h1>Safe Git Fix</h1>";
echo "<pre style='background:#111;color:#0f0;padding:15px;font-size:13px'>";

$dir = dirname(__FILE__);

echo "Step 1: Switching to main branch...\n";
echo shell_exec("cd " . escapeshellarg($dir) . " && git checkout main 2>&1");

echo "\nStep 2: Pulling latest...\n";
echo shell_exec("cd " . escapeshellarg($dir) . " && git pull origin main 2>&1");

echo "\nStep 3: Restoring WordPress core files (no clean)...\n";
$version = '6.8.1';
$wp_zip = tempnam(sys_get_temp_dir(), 'wp') . '.zip';
$url = "https://wordpress.org/wordpress-{$version}.zip";

echo "Downloading WordPress {$version}...\n";
$ch = curl_init($url);
$fp = fopen($wp_zip, 'w');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);

if ($http_code == 200 && filesize($wp_zip) > 1000000) {
    echo "Downloaded: " . number_format(filesize($wp_zip)) . " bytes\n";
    
    $zip = new ZipArchive();
    if ($zip->open($wp_zip) === true) {
        $extract_dir = sys_get_temp_dir() . '/wp_fix_' . uniqid();
        $zip->extractTo($extract_dir);
        $zip->close();
        
        $source = "{$extract_dir}/wordpress";
        
        foreach (array('wp-includes', 'wp-admin') as $dir_name) {
            if (is_dir("{$source}/{$dir_name}")) {
                $count = 0;
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator("{$source}/{$dir_name}", RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($it as $file) {
                    $rel = substr($file->getPathname(), strlen("{$source}/{$dir_name}"));
                    $dest = "{$dir}/{$dir_name}{$rel}";
                    if (!is_dir(dirname($dest))) @mkdir(dirname($dest), 0755, true);
                    if (copy($file->getPathname(), $dest)) $count++;
                }
                echo "Restored {$dir_name}/: {$count} files\n";
            }
        }
        
        @unlink($wp_zip);
    }
} else {
    echo "ERROR: Download failed (HTTP {$http_code})\n";
}

echo "\nStep 4: Creating .gitignore to protect WordPress files...\n";
$gitignore =<<<'EOF'
# WordPress core (not tracked in git)
wp-includes/
wp-admin/
wp-content/plugins/
wp-content/upgrade/
wp-content/upgrade-temp-backup/
wp-content/cache/
wp-content/backups/
wp-content/debug.log
error_log
wp-config.php
.htaccess
sitemap.xml
sitemap.xml.gz
robots.txt

# Keep these tracked
!wp-content/themes/
!wp-content/mu-plugins/
EOF;

file_put_contents("{$dir}/.gitignore", $gitignore);
echo ".gitignore created/updated\n";

echo "\nStep 5: Current git status...\n";
echo shell_exec("cd " . escapeshellarg($dir) . " && git status --short 2>&1 | head -5");

echo "\n=== FIX COMPLETE ===\n";
echo "Test: https://gamtech-electronic.com/\n";
echo "Admin: https://gamtech-electronic.com/admin?key=gamtech2026admin\n";
echo "</pre>";
echo "<br><a href='/' style='color:#0f0;font-size:18px'>Test Homepage</a>";
echo "<br><a href='/admin?key=gamtech2026admin' style='color:#0ff;font-size:18px'>Test Admin Panel</a>";
