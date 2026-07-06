<?php
/**
 * Fix .htaccess for admin panel access
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Fix .htaccess</h1>";
echo "<pre style='background:#111;color:#0f0;padding:15px'>";

$htaccess = dirname(__FILE__) . '/.htaccess';

echo "Current .htaccess:\n";
echo htmlspecialchars(file_get_contents($htaccess)) . "\n\n";

// Write correct .htaccess
$content = <<< 'HTACCESS'
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

# GamTech Admin Panel
RewriteRule ^admin/?$ /admin.php?key=gamtech2026admin [L,QSA]

RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress
HTACCESS;

if (file_put_contents($htaccess, $content)) {
    echo "\n.htaccess FIXED!\n\n";
    echo "New .htaccess:\n";
    echo htmlspecialchars(file_get_contents($htaccess)) . "\n\n";
} else {
    echo "\nERROR: Could not write .htaccess\n";
}

echo "</pre>";
echo "<br><a href='/admin?key=gamtech2026admin' style='color:#0ff;font-size:18px'>Test Admin Panel</a>";
