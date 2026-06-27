<?php
/**
 * Auto-deploy webhook — triggered by GitHub on every push to main.
 * Secured with a secret token.
 * 
 * Handles both git pull (normal) and fresh clone (if .git was deleted).
 */

define( 'DEPLOY_SECRET', 'gamtech2026deploy' );
define( 'REPO_URL',      'https://github.com/Mira-x77/gamtech-electronic.git' );
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

// Deploy: git pull or fresh clone if .git is missing
$git_dir = REPO_PATH . '/.git';
if ( is_dir( $git_dir ) ) {
    $output = shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git pull origin ' . GIT_BRANCH . ' 2>&1' );
} else {
    // .git missing — back up wp-config.php, clone fresh, restore wp-config
    $wp_config_backup = '';
    if ( file_exists( REPO_PATH . '/wp-config.php' ) ) {
        $wp_config_backup = file_get_contents( REPO_PATH . '/wp-config.php' );
    }
    shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git clone --branch ' . GIT_BRANCH . ' ' . REPO_URL . ' . 2>&1' );
    $output = "Fresh clone (no .git found)";
    // Restore wp-config.php if it existed
    if ( $wp_config_backup ) {
        file_put_contents( REPO_PATH . '/wp-config.php', $wp_config_backup );
    }
}

// Purge cache
shell_exec( 'curl -s -X PURGE http://localhost/ 2>&1' );
shell_exec( 'curl -s -X PURGE http://127.0.0.1/ 2>&1' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/style.css' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/front-page.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/header.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/footer.php' );
if ( function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}

http_response_code( 200 );
echo "Deploy triggered:\n" . $output;
