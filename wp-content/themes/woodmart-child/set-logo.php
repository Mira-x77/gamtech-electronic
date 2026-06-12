<?php
/**
 * One-time logo setter — runs via CLI only.
 * Usage: php wp-content/themes/woodmart-child/set-logo.php
 */

// Only allow CLI execution
if ( php_sapi_name() !== 'cli' ) {
    die( 'Run from CLI only: php wp-content/themes/woodmart-child/set-logo.php' );
}

// Bootstrap WordPress without HTTP
$_SERVER['HTTP_HOST']   = 'gamtech-electronic.com';
$_SERVER['REQUEST_URI'] = '/';

$wp_root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
require_once $wp_root . '/wp-load.php';

$logo_file     = WP_CONTENT_DIR . '/uploads/2024/09/gamtech_logo_transparent.png';
$logo_rel_path = '2024/09/gamtech_logo_transparent.png';

if ( ! file_exists( $logo_file ) ) {
    die( "ERROR: Logo file not found at {$logo_file}\n" );
}

echo "Logo file found.\n";

// --- Step 1: Register in Media Library ---
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$existing = get_posts( array(
    'post_type'   => 'attachment',
    'meta_key'    => '_wp_attached_file',
    'meta_value'  => $logo_rel_path,
    'numberposts' => 1,
) );

if ( $existing ) {
    $attachment_id = $existing[0]->ID;
    echo "Already in Media Library. Attachment ID: {$attachment_id}\n";
} else {
    $filetype   = wp_check_filetype( basename( $logo_file ), null );
    $upload_dir = wp_upload_dir();

    $attachment = array(
        'guid'           => $upload_dir['baseurl'] . '/2024/09/gamtech_logo_transparent.png',
        'post_mime_type' => $filetype['type'],
        'post_title'     => 'Gamtech Logo',
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    $attachment_id = wp_insert_attachment( $attachment, $logo_file );
    wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $logo_file ) );

    echo "Registered in Media Library. Attachment ID: {$attachment_id}\n";
}

// --- Step 2: Inject into Woodmart Header Builder ---
$logo_url    = wp_get_attachment_url( $attachment_id );
$header_data = get_option( 'whb_default_header' );

if ( ! $header_data || ! isset( $header_data['structure'] ) ) {
    die( "ERROR: whb_default_header option not found. The Header Builder may not have been saved yet.\n" );
}

function gamtech_inject_logo( &$elements, $attachment_id, $logo_url ) {
    foreach ( $elements as &$element ) {
        if ( isset( $element['type'] ) && $element['type'] === 'logo' ) {
            $element['params']['image'] = array(
                'id'    => 'image',
                'value' => array( 'id' => $attachment_id, 'url' => $logo_url ),
                'type'  => 'image',
            );
            $element['params']['sticky_image'] = array(
                'id'    => 'sticky_image',
                'value' => array( 'id' => $attachment_id, 'url' => $logo_url ),
                'type'  => 'image',
            );
            echo "Injected logo into element: {$element['id']}\n";
        }
        if ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
            gamtech_inject_logo( $element['content'], $attachment_id, $logo_url );
        }
    }
}

gamtech_inject_logo( $header_data['structure']['content'], $attachment_id, $logo_url );
update_option( 'whb_default_header', $header_data );

delete_transient( 'woodmart_style_storage' );
wp_cache_flush();

echo "Header builder updated.\n";
echo "Done! Logo is now live on the site.\n";

// Self-delete
unlink( __FILE__ );
echo "Script deleted.\n";
