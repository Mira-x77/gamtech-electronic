<?php
/**
 * Auto-deploy webhook — triggered by GitHub on every push to main.
 * Secured with a secret token.
 */

define( 'DEPLOY_SECRET', 'gamtech2026deploy' );
define( 'REPO_PATH',     '/home/c2423708c/public_html' );
define( 'GIT_BRANCH',    'main' );

// Verify GitHub signature
$payload   = file_get_contents( 'php://input' );
$signature = isset( $_SERVER['HTTP_X_HUB_SIGNATURE_256'] )
    ? $_SERVER['HTTP_X_HUB_SIGNATURE_256']
    : '';

$expected = 'sha256=' . hash_hmac( 'sha256', $payload, DEPLOY_SECRET );

if ( ! hash_equals( $expected, $signature ) ) {
    http_response_code( 403 );
    die( 'Unauthorized' );
}

// Only deploy on push to main
$data = json_decode( $payload, true );
if ( isset( $data['ref'] ) && $data['ref'] !== 'refs/heads/' . GIT_BRANCH ) {
    http_response_code( 200 );
    die( 'Not main branch, skipping.' );
}

// Run git pull
$output = shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git pull origin ' . GIT_BRANCH . ' 2>&1' );

// Auto-recover wp-config.php if missing (prevents 500 error)
$wp_config = REPO_PATH . '/wp-config.php';
if ( ! file_exists( $wp_config ) && file_exists( REPO_PATH . '/setup-config.php' ) ) {
    // Minimal production wp-config.php
    $config = '<?php
define( \'DB_NAME\',     \'c2423708c_gamtech\' );
define( \'DB_USER\',     \'c2423708c_gamtech\' );
define( \'DB_PASSWORD\', \'CHANGE_ME\' );
define( \'DB_HOST\',     \'localhost\' );
define( \'DB_CHARSET\',  \'utf8mb4\' );
define( \'DB_COLLATE\',  \'\' );
define( \'AUTH_KEY\',         \'gmT!ch@2026#k1\' );
define( \'SECURE_AUTH_KEY\',  \'gmT!ch@2026#k2\' );
define( \'LOGGED_IN_KEY\',    \'gmT!ch@2026#k3\' );
define( \'NONCE_KEY\',        \'gmT!ch@2026#k4\' );
define( \'AUTH_SALT\',        \'gmT!ch@2026#s1\' );
define( \'SECURE_AUTH_SALT\', \'gmT!ch@2026#s2\' );
define( \'LOGGED_IN_SALT\',   \'gmT!ch@2026#s3\' );
define( \'NONCE_SALT\',       \'gmT!ch@2026#s4\' );
$table_prefix = \'wp_\';
define( \'WP_DEBUG\', false );
define( \'WP_MEMORY_LIMIT\', \'256M\' );
define( \'AUTOMATIC_UPDATER_DISABLED\', true );
if ( ! defined( \'ABSPATH\' ) ) { define( \'ABSPATH\', __DIR__ . \'/\' ); }
require_once ABSPATH . \'wp-settings.php\';
';
    file_put_contents( $wp_config, $config );
}

// Purge Varnish / edge cache via localhost
shell_exec( 'curl -s -X PURGE http://localhost/ 2>&1' );
shell_exec( 'curl -s -X PURGE http://127.0.0.1/ 2>&1' );
// Also try banning all cached objects
shell_exec( 'curl -s -X PURGE http://localhost/wp-content/ 2>&1' );
// Touch key files to force WordPress cache invalidation
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/style.css' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/front-page.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/header.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/footer.php' );
// Clear any object caches
if ( function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}

http_response_code( 200 );
echo "Deploy triggered:\n" . $output;
