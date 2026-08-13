<?php
/**
 * One-time error_log cleanup — visit with key when diagnostics crash from log size.
 * https://gamtech-electronic.com/clear-log.php?key=gamtech2026health
 */
if ( ! isset( $_GET['key'] ) || $_GET['key'] !== 'gamtech2026health' ) {
    http_response_code( 403 );
    die( 'Access denied' );
}

header( 'Content-Type: text/plain' );

require_once __DIR__ . '/inc/deploy-helpers.php';

echo "=== GAMTECH ERROR LOG CLEANUP ===\n\n";
echo "Before:\n" . gamtech_tail_error_log( 5 ) . "\n\n";
echo gamtech_rotate_error_log() . "\n\n";
echo "After:\n" . gamtech_tail_error_log( 5 ) . "\n\n";
echo gamtech_clear_php_cache()[0] . "\n";
echo "\nDone at " . date( 'Y-m-d H:i:s T' ) . "\n";
