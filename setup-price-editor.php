<?php
/**
 * One-time setup: creates the Price Editor page.
 * DELETE this file after running it!
 *
 * Access: https://gamtech-electronic.com/setup-price-editor.php?key=gamtech2026setup
 */
define( 'ABSPATH', dirname( __FILE__ ) . '/' );
require_once ABSPATH . 'wp-load.php';

$key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
if ( $key !== 'gamtech2026setup' ) {
    http_response_code( 403 );
    die( 'Unauthorized' );
}

// Check if page already exists
$existing = get_page_by_path( 'price-editor' );
if ( $existing ) {
    echo 'Page already exists (ID: ' . $existing->ID . '). Visit: <a href="' . esc_url( home_url( '/price-editor/?key=gamtech2026prices' ) ) . '">Price Editor</a>';
    exit;
}

// Create the page
$page_id = wp_insert_post( array(
    'post_title'   => 'Price Editor',
    'post_name'    => 'price-editor',
    'post_content' => '',
    'post_status'  => 'publish',
    'post_type'    => 'page',
    'meta_input'   => array(
        '_wp_page_template' => 'page-price-editor.php',
    ),
) );

if ( $page_id && ! is_wp_error( $page_id ) ) {
    echo '<h2>Price Editor page created!</h2>';
    echo '<p>Page ID: ' . $page_id . '</p>';
    echo '<p><a href="' . esc_url( home_url( '/price-editor/?key=gamtech2026prices' ) ) . '" style="font-size:18px;padding:10px 20px;background:#7c3aed;color:#fff;border-radius:8px;text-decoration:none">Open Price Editor →</a></p>';
} else {
    echo 'Error creating page.';
}
