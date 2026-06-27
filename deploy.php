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

// Clean up development files that shouldn't be on production
@shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && rm -rf .git docker product-images vibe_images .kiro .agents alive.php alive.html debug-site.php setup-config.php 2>&1' );

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
