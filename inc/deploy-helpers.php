<?php
/**
 * Shared deploy / diagnostics helpers (included by deploy.php, check-error.php, etc.)
 */

if ( ! defined( 'GAMTECH_SITE_PATH' ) ) {
    define( 'GAMTECH_SITE_PATH', '/home/c2423708c/public_html' );
}

if ( ! defined( 'GAMTECH_ERROR_LOG_MAX_BYTES' ) ) {
    define( 'GAMTECH_ERROR_LOG_MAX_BYTES', 10 * 1024 * 1024 ); // 10 MB
}

/**
 * Read last N lines of error_log without loading the whole file into memory.
 */
function gamtech_tail_error_log( $lines = 30 ) {
    $log = GAMTECH_SITE_PATH . '/error_log';
    if ( ! file_exists( $log ) ) {
        return 'No error_log found';
    }

    $size = filesize( $log );
    $size_mb = round( $size / 1024 / 1024, 1 );

    $out = shell_exec(
        'tail -n ' . (int) $lines . ' ' . escapeshellarg( $log ) . ' 2>&1'
    );

    if ( ! $out ) {
        return "error_log exists ({$size_mb} MB) but tail failed — truncate it via cPanel or SSH";
    }

    return "=== error_log ({$size_mb} MB, last {$lines} lines) ===\n" . $out;
}

/**
 * Truncate error_log when it grows too large (prevents memory exhaustion / disk full).
 */
function gamtech_rotate_error_log() {
    $log = GAMTECH_SITE_PATH . '/error_log';
    if ( ! file_exists( $log ) ) {
        return 'No error_log to rotate';
    }

    $size = filesize( $log );
    if ( $size <= GAMTECH_ERROR_LOG_MAX_BYTES ) {
        return 'error_log OK (' . round( $size / 1024 / 1024, 1 ) . ' MB)';
    }

    $backup = $log . '.' . date( 'Ymd-His' ) . '.bak';
    // Keep only last 500 lines in backup, then truncate main log
    shell_exec(
        'tail -n 500 ' . escapeshellarg( $log ) . ' > ' . escapeshellarg( $backup ) . ' 2>&1'
    );
    shell_exec( ': > ' . escapeshellarg( $log ) . ' 2>&1' );

    return 'Rotated error_log (' . round( $size / 1024 / 1024, 1 ) . ' MB → truncated, backup: ' . basename( $backup ) . ')';
}

/**
 * Verify WordPress critical files exist after deploy.
 */
function gamtech_verify_critical_files() {
    $files = [
        'wp-includes/load.php',
        'wp-includes/functions.php',
        'wp-config.php',
        'index.php',
        'wp-content/themes/woodmart-child/functions.php',
    ];

    $results = [];
    foreach ( $files as $file ) {
        $path = GAMTECH_SITE_PATH . '/' . $file;
        $ok   = file_exists( $path );
        $results[] = ( $ok ? 'OK' : 'MISSING' ) . ' - ' . $file;
        if ( ! $ok ) {
            $results[] = '  >>> Attempting git restore...';
            shell_exec(
                'cd ' . escapeshellarg( GAMTECH_SITE_PATH ) .
                ' && git checkout HEAD -- ' . escapeshellarg( $file ) . ' 2>&1'
            );
        }
    }
    return $results;
}

/**
 * Clear PHP opcache and restart PHP-FPM so deployed code is actually used.
 */
function gamtech_clear_php_cache() {
    $results = [];

    if ( function_exists( 'opcache_reset' ) ) {
        opcache_reset();
        $results[] = 'opcache_reset() called';
    }

    $fpm = shell_exec( 'killall -USR2 php-fpm 2>&1' );
    $results[] = 'PHP-FPM reload: ' . trim( $fpm ?: 'signal sent' );

    return $results;
}

/**
 * Quick HTTP self-check after deploy.
 */
function gamtech_http_self_check() {
    $code = shell_exec(
        'curl -s -o /dev/null -w "%{http_code}" --max-time 15 https://gamtech-electronic.com/ 2>/dev/null'
    );
    return trim( $code ?: '000' );
}
