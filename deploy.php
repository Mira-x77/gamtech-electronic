<?php
/**
 * Auto-deploy webhook — triggered by GitHub on every push to main.
 * Secured with a secret token.
 *
 * Uses git fetch + reset --hard to guarantee the server exactly matches
 * the remote, restoring any missing core files.
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

// Deploy: git fetch + reset --hard to match remote exactly
$git_dir = REPO_PATH . '/.git';
if ( is_dir( $git_dir ) ) {
    shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git fetch origin ' . GIT_BRANCH . ' 2>&1' );
    $output = shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git reset --hard origin/' . GIT_BRANCH . ' 2>&1' );
} else {
    $wp_config_backup = '';
    if ( file_exists( REPO_PATH . '/wp-config.php' ) ) {
        $wp_config_backup = file_get_contents( REPO_PATH . '/wp-config.php' );
    }
    shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git clone --branch ' . GIT_BRANCH . ' ' . REPO_URL . ' . 2>&1' );
    $output = "Fresh clone (no .git found)";
    if ( $wp_config_backup ) {
        file_put_contents( REPO_PATH . '/wp-config.php', $wp_config_backup );
    }
}

// Clean up dev files that shouldn't be on production
@shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && rm -rf docker vibe_images .kiro .agents temp_repo deploy2.php deploy3.php deploy4.php 2>&1' );

// Purge cache
@shell_exec( 'curl -s -X PURGE http://localhost/ 2>&1' );
@shell_exec( 'curl -s -X PURGE http://127.0.0.1/ 2>&1' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/style.css' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/front-page.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/header.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/footer.php' );
if ( function_exists( 'wp_cache_flush' ) ) {
    wp_cache_flush();
}

// Clear PHP opcache so new code takes effect immediately
if ( function_exists( 'opcache_reset' ) ) {
    opcache_reset();
}

http_response_code( 200 );
echo "Deploy triggered:\n" . $output;
