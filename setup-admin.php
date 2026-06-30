<?php
/**
 * Run once to create the Admin page
 * Visit: https://gamtech-electronic.com/setup-admin.php?key=gamtech2026admin
 */
require_once dirname(__FILE__) . '/wp-blog-header.php';

if ( ! isset( $_GET['key'] ) || $_GET['key'] !== 'gamtech2026admin' ) {
    die( 'Access denied.' );
}

// Check if Admin page already exists
$existing = get_page_by_path( 'admin' );
if ( $existing ) {
    echo "<p>Admin page already exists. ID: {$existing->ID}</p>";
    echo "<p>Template: " . get_page_template_slug( $existing->ID ) . "</p>";
    echo "<p><a href='/admin?key=gamtech2026admin'>Open Admin Panel →</a></p>";
    exit;
}

$page_id = wp_insert_post( array(
    'post_title'   => 'Admin',
    'post_name'    => 'admin',
    'post_type'    => 'page',
    'post_status'  => 'publish',
    'post_content' => '',
) );

if ( $page_id && ! is_wp_error( $page_id ) ) {
    update_post_meta( $page_id, '_wp_page_template', 'admin-panel.php' );
    echo "<p style='color:green;font-size:18px'>Admin page created! ID: {$page_id}</p>";
    echo "<p><a href='/admin?key=gamtech2026admin' style='font-size:16px'>Open Admin Panel →</a></p>";
} else {
    echo "<p style='color:red'>Failed to create page.</p>";
}
