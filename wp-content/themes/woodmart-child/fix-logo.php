<?php
/**
 * Fix logo — injects into ALL headers and clears all caches.
 * Run: php wp-content/themes/woodmart-child/fix-logo.php
 */
if ( php_sapi_name() !== 'cli' ) die('CLI only');

$_SERVER['HTTP_HOST']   = 'gamtech-electronic.com';
$_SERVER['REQUEST_URI'] = '/';

require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';

$attachment_id = 9388;
$logo_url      = wp_get_attachment_url( $attachment_id );

if ( ! $logo_url ) {
    die( "ERROR: Attachment ID 9388 not found in DB.\n" );
}

echo "Logo URL: {$logo_url}\n";

function gt_inject( &$elements, $id, $url ) {
    foreach ( $elements as &$el ) {
        if ( isset( $el['type'] ) && $el['type'] === 'logo' ) {
            $el['params']['image'] = array(
                'id'    => 'image',
                'value' => array( 'id' => $id, 'url' => $url ),
                'type'  => 'image',
            );
            $el['params']['sticky_image'] = array(
                'id'    => 'sticky_image',
                'value' => array( 'id' => $id, 'url' => $url ),
                'type'  => 'image',
            );
            echo "  -> Set logo in element [{$el['id']}]\n";
        }
        if ( isset( $el['content'] ) && is_array( $el['content'] ) ) {
            gt_inject( $el['content'], $id, $url );
        }
    }
}

// Get all saved headers and inject into every one
$saved = get_option( 'whb_saved_headers', array() );
echo "Found headers: " . implode( ', ', array_keys( $saved ) ) . "\n";

foreach ( array_keys( $saved ) as $header_id ) {
    $option_key  = 'whb_' . $header_id;
    $header_data = get_option( $option_key );

    if ( ! $header_data || ! isset( $header_data['structure']['content'] ) ) {
        echo "Skipping {$header_id} — no structure.\n";
        continue;
    }

    echo "Processing {$header_id} ({$header_data['name']})...\n";
    gt_inject( $header_data['structure']['content'], $attachment_id, $logo_url );
    update_option( $option_key, $header_data );
}

// Clear every possible cache
wp_cache_flush();
delete_option( 'woodmart_style_storage' );

// Delete all Woodmart CSS cache files
$upload_dir = wp_upload_dir();
$css_files  = glob( $upload_dir['basedir'] . '/*/xts-header_*.css' );
if ( $css_files ) {
    foreach ( $css_files as $f ) {
        unlink( $f );
        echo "Deleted cache: " . basename( $f ) . "\n";
    }
}

// Flush transients
global $wpdb;
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%woodmart%' AND option_name LIKE '%transient%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '%xts%' AND option_name LIKE '%cache%'" );

echo "\nAll done. Logo injected into all headers, all caches cleared.\n";

unlink( __FILE__ );
echo "Script deleted.\n";
