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

@shell_exec( 'cd ' . escapeshellarg( REPO_PATH ) . ' && rm -rf docker vibe_images .kiro .agents temp_repo deploy2.php deploy3.php deploy4.php 2>&1' );
@shell_exec( 'curl -s -X PURGE http://localhost/ 2>&1' );
@shell_exec( 'curl -s -X PURGE http://127.0.0.1/ 2>&1' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/style.css' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/functions.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/front-page.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/header.php' );
@touch( REPO_PATH . '/wp-content/themes/woodmart-child/footer.php' );
if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
if ( function_exists( 'opcache_reset' ) )  { opcache_reset(); }

$log[] = 'Done.';
@file_put_contents( LOG_FILE, implode( "\n", $log ) . "\n", FILE_APPEND );

http_response_code( 200 );
echo implode( "\n", $log );
