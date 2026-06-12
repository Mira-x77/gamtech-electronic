<?php
/**
 * One-time logo setter for Gamtech.
 *
 * Visit: https://gamtech-electronic.com/wp-content/themes/woodmart-child/set-logo.php
 *
 * What it does:
 *  1. Registers gamtech_logo_transparent.png in the WordPress Media Library
 *  2. Injects the attachment ID into the Woodmart header builder option (whb_default_header)
 *     for both desktop and mobile logo elements
 *  3. Deletes itself when done
 */

// Bootstrap WordPress
$wp_root = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
require_once $wp_root . '/wp-load.php';

// Safety check — must be logged in as admin
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Unauthorized. Please log in as an admin first.' );
}

$logo_file = WP_CONTENT_DIR . '/uploads/2024/09/gamtech_logo_transparent.png';

if ( ! file_exists( $logo_file ) ) {
    wp_die( 'Logo file not found at: ' . $logo_file );
}

// --- Step 1: Register in Media Library ---
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

// Check if already registered to avoid duplicates
$existing = get_posts( array(
    'post_type'  => 'attachment',
    'meta_key'   => '_wp_attached_file',
    'meta_value' => '2024/09/gamtech_logo_transparent.png',
    'numberposts' => 1,
) );

if ( $existing ) {
    $attachment_id = $existing[0]->ID;
    echo "<p>Logo already in Media Library. Attachment ID: <strong>{$attachment_id}</strong></p>";
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

    echo "<p>Logo registered in Media Library. Attachment ID: <strong>{$attachment_id}</strong></p>";
}

// --- Step 2: Inject into Woodmart Header Builder ---
$logo_url    = wp_get_attachment_url( $attachment_id );
$header_data = get_option( 'whb_default_header' );

if ( ! $header_data || ! isset( $header_data['structure'] ) ) {
    wp_die( 'Could not load Woodmart header data (whb_default_header option not found). Has the Header Builder been saved at least once in WP Admin?' );
}

// Walk the structure and update all logo elements
function gamtech_inject_logo( &$elements, $attachment_id, $logo_url ) {
    foreach ( $elements as &$element ) {
        if ( isset( $element['type'] ) && $element['type'] === 'logo' ) {
            $element['params']['image'] = array(
                'id'    => 'image',
                'value' => array(
                    'id'  => $attachment_id,
                    'url' => $logo_url,
                ),
                'type'  => 'image',
            );
            $element['params']['sticky_image'] = array(
                'id'    => 'sticky_image',
                'value' => array(
                    'id'  => $attachment_id,
                    'url' => $logo_url,
                ),
                'type'  => 'image',
            );
        }
        if ( isset( $element['content'] ) && is_array( $element['content'] ) ) {
            gamtech_inject_logo( $element['content'], $attachment_id, $logo_url );
        }
    }
}

gamtech_inject_logo( $header_data['structure']['content'], $attachment_id, $logo_url );
update_option( 'whb_default_header', $header_data );

// Clear Woodmart/caching transients
delete_transient( 'woodmart_style_storage' );
wp_cache_flush();

echo "<p>Logo injected into header builder for all logo elements (desktop + mobile + sticky).</p>";
echo "<p><strong>Done! The logo is now live on the site.</strong></p>";

// --- Step 3: Self-delete ---
unlink( __FILE__ );
echo "<p><em>This file has been deleted.</em></p>";
