<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(120);
echo "<pre style='background:#111;color:#0f0;padding:15px'>";
$dir = dirname(__FILE__);
echo "Step 1: Stash local changes...\n";
echo shell_exec("cd " . escapeshellarg($dir) . " && git stash 2>&1");
echo "\nStep 2: Pull latest...\n";
echo shell_exec("cd " . escapeshellarg($dir) . " && git pull origin main 2>&1");
echo "\nStep 3: Restore WordPress core (no clean)...\n";
$version = '6.8.1';
$wp_zip = tempnam(sys_get_temp_dir(), 'wp') . '.zip';
$ch = curl_init("https://wordpress.org/wordpress-{$version}.zip");
$fp = fopen($wp_zip, 'w');
curl_setopt($ch, CURLOPT_FILE, $fp);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_exec($ch);
curl_close($ch);
fclose($fp);
if (filesize($wp_zip) > 1000000) {
    $zip = new ZipArchive();
    if ($zip->open($wp_zip) === true) {
        $extract_dir = sys_get_temp_dir() . '/wp_pull_' . uniqid();
        $zip->extractTo($extract_dir);
        $zip->close();
        $source = "{$extract_dir}/wordpress";
        foreach (array('wp-includes', 'wp-admin') as $dn) {
            if (is_dir("{$source}/{$dn}")) {
                $count = 0;
                $it = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator("{$source}/{$dn}", RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($it as $file) {
                    $rel = substr($file->getPathname(), strlen("{$source}/{$dn}"));
                    $dest = "{$dir}/{$dn}{$rel}";
                    if (!is_dir(dirname($dest))) @mkdir(dirname($dest), 0755, true);
                    copy($file->getPathname(), $dest); $count++;
                }
                echo "  Restored {$dn}/: {$count} files\n";
            }
        }
    }
}
@unlink($wp_zip);
echo "\nDone! Admin: /admin?key=gamtech2026admin\n";
echo "</pre>";
echo "<a href='/admin?key=gamtech2026admin' style='color:#0ff;font-size:18px'>Test Admin</a>";
