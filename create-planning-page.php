<?php
/**
 * One-time script to create the Planning page
 * Visit: yourdomain.com/create-planning-page.php?key=gamtech2026create
 */

// Security key
if (($_GET['key'] ?? '') !== 'gamtech2026create') {
    die('Unauthorized');
}

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Check if page already exists
$existing = get_page_by_path('planning');

if ($existing) {
    echo '✅ Planning page already exists!<br>';
    echo 'URL: <a href="' . get_permalink($existing->ID) . '">' . get_permalink($existing->ID) . '</a><br>';
    echo '<br><a href="' . home_url() . '">Go to Homepage</a>';
    exit;
}

// Create the page
$page_data = array(
    'post_title'    => 'Order Planning',
    'post_name'     => 'planning',
    'post_content'  => '<!-- This page uses the Order Planning Page template -->',
    'post_status'   => 'publish',
    'post_type'     => 'page',
    'post_author'   => 1,
    'page_template' => 'page-planning.php'
);

$page_id = wp_insert_post($page_data);

if ($page_id && !is_wp_error($page_id)) {
    // Set the template
    update_post_meta($page_id, '_wp_page_template', 'page-planning.php');
    
    echo '✅ Planning page created successfully!<br>';
    echo 'Page ID: ' . $page_id . '<br>';
    echo 'URL: <a href="' . get_permalink($page_id) . '">' . get_permalink($page_id) . '</a><br>';
    echo '<br>You can now delete this file (create-planning-page.php) from your server.<br>';
    echo '<br><a href="' . home_url() . '">Go to Homepage</a>';
} else {
    echo '❌ Failed to create page<br>';
    if (is_wp_error($page_id)) {
        echo 'Error: ' . $page_id->get_error_message();
    }
}
?>
