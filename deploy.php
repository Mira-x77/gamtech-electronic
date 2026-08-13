<?php
define( 'DEPLOY_SECRET', 'gamtech2026deploy' );
define( 'REPO_URL',      'https://github.com/Mira-x77/gamtech-electronic.git' );
define( 'REPO_PATH',     '/home/c2423708c/public_html' );
define( 'GIT_BRANCH',    'main' );
define( 'LOG_FILE',      __DIR__ . '/deploy-log.txt' );

$log = [ '--- Deploy ' . date( 'Y-m-d H:i:s' ) . ' ---' ];

$manual_key = $_GET['key'] ?? '';
if ( $manual_key === 'fixitnow2026' ) {
    $log[] = 'Manual trigger via key';
} else {
    $payload   = file_get_contents( 'php://input' );
    $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    $expected  = 'sha256=' . hash_hmac( 'sha256', $payload, DEPLOY_SECRET );
    if ( ! hash_equals( $expected, $signature ) ) {
        http_response_code( 403 );
        die( 'Unauthorized' );
    }
    $data = json_decode( $payload, true );
    if ( isset( $data['ref'] ) && $data['ref'] !== 'refs/heads/' . GIT_BRANCH ) {
        http_response_code( 200 );
        die( 'Not main branch, skipping.' );
    }
}

$git_dir = REPO_PATH . '/.git';
if ( is_dir( $git_dir ) ) {
    foreach ( [ 'git fetch origin ' . GIT_BRANCH, 'git reset --hard origin/' . GIT_BRANCH, 'git checkout -- .' ] as $cmd ) {
        $out = []; $rv = 0;
        exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && ' . $cmd . ' 2>&1', $out, $rv );
        $log[] = $cmd . ' (exit ' . $rv . ')';
        $log   = array_merge( $log, $out );
    }
} else {
    $wp_config_backup = '';
    if ( file_exists( REPO_PATH . '/wp-config.php' ) ) {
        $wp_config_backup = file_get_contents( REPO_PATH . '/wp-config.php' );
    }
    $out = []; $rv = 0;
    exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && git clone --branch ' . GIT_BRANCH . ' ' . REPO_URL . ' . 2>&1', $out, $rv );
    $log[] = 'Fresh clone (exit ' . $rv . ')';
    $log = array_merge( $log, $out );
    if ( $wp_config_backup ) {
        file_put_contents( REPO_PATH . '/wp-config.php', $wp_config_backup );
    }
}

require_once __DIR__ . '/inc/deploy-helpers.php';

@shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && rm -rf docker vibe_images .kiro .agents temp_repo deploy2.php deploy3.php deploy4.php 2>&1' );

// Rotate bloated error_log (a 256MB+ log causes 500 errors on its own)
$log[] = gamtech_rotate_error_log();

// Verify critical files survived the deploy
$log[] = 'Critical file check:';
$log   = array_merge( $log, gamtech_verify_critical_files() );

// PHP syntax check on child theme
$syntax_out = []; $syntax_rv = 0;
exec(
    'php -l ' . escapeshellarg( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' ) . ' 2>&1',
    $syntax_out,
    $syntax_rv
);
$log[] = 'Syntax check functions.php: ' . ( $syntax_rv === 0 ? 'OK' : implode( ' ', $syntax_out ) );

// Bust opcache + reload PHP-FPM (without this, old broken code keeps running)
$log[] = 'Cache clear:';
$log   = array_merge( $log, gamtech_clear_php_cache() );

@touch( REPO_PATH . '/wp-content/themes/woodmart-child/style.css' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/front-page.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/header.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/footer.php' );

$http_code = gamtech_http_self_check();
$log[]     = 'Post-deploy HTTP check: ' . $http_code;
$log[]     = $http_code === '200' ? 'Deploy successful' : 'WARNING: site may still be down';
@file_put_contents( LOG_FILE, implode( "\n", $log ) . "\n", FILE_APPEND );

http_response_code( 200 );
echo implode( "\n", $log );
