<?php
/**
 * Cleanup Non classé products
 * - DELETE: products with no image (non-tech)
 * - CATEGORIZE: products with image → assign to correct category
 *
 * Usage: visit https://gamtech-electronic.com/cleanup-nonclasse.php?key=gamtech2026prices
 */

require_once dirname(__FILE__) . '/wp-blog-header.php';

if ( ! isset( $_GET['key'] ) || $_GET['key'] !== 'gamtech2026prices' ) {
    die( 'Access denied.' );
}

$mode = isset( $_GET['mode'] ) ? $_GET['mode'] : 'preview';

// Find Non classé category
$nonclasse = get_term_by( 'name', 'Non classé', 'product_cat' );
if ( ! $nonclasse ) {
    $nonclasse = get_term_by( 'slug', 'non-classe', 'product_cat' );
}
if ( ! $nonclasse ) {
    die( 'Non classé category not found.' );
}

$products = wc_get_products( array(
    'category' => array( $nonclasse->slug ),
    'limit'    => -1,
    'status'   => 'publish',
) );

echo "<h2>Non classé Products: " . count( $products ) . " found</h2>";

// Category mapping by product name keywords
$category_map = array(
    // Cables & Converters
    'convertisseur'       => 'Cables & Converters',
    'cable'               => 'Cables & Converters',
    'hdmi'                => 'Cables & Converters',
    'usb'                 => 'Cables & Converters',
    'mini dp'             => 'Cables & Converters',
    // Laptop Accessories
    'souris'              => 'Laptop Accessories',
    'clavier'             => 'Laptop Accessories',
    'keyboard'            => 'Laptop Accessories',
    'mouse'               => 'Laptop Accessories',
    'ipod'                => 'Laptop Accessories',
    'dock'                => 'Laptop Accessories',
    'iphone dock'         => 'Laptop Accessories',
    'support laptop'      => 'Laptop Accessories',
    'hub'                 => 'Laptop Accessories',
    'ssd'                 => 'Computer Accessories',
    'cle usb'             => 'Computer Accessories',
    'flash drive'         => 'Computer Accessories',
    'disque'              => 'Computer Accessories',
    'disque dur'          => 'Computer Accessories',
    'hdd'                 => 'Computer Accessories',
    'ram'                 => 'Computer Accessories',
    'barrette'            => 'Computer Accessories',
    // Computer Accessories
    'montre'              => 'Computer Accessories',
    'watch'               => 'Computer Accessories',
    'montre connectee'    => 'Computer Accessories',
    'smart watch'         => 'Computer Accessories',
    // Printers
    'imprimante'          => 'Printers & Scanners',
    'scanner'             => 'Printers & Scanners',
    'printer'             => 'Printers & Scanners',
    // Audio
    'casque'              => 'Audio',
    'ecouteur'            => 'Audio',
    'enceinte'            => 'Audio',
    'speaker'             => 'Audio',
    'headset'             => 'Audio',
    'earphone'            => 'Audio',
    'micro'               => 'Audio',
    'microphone'          => 'Audio',
    // Gaming
    'manette'             => 'Gaming',
    'gaming'              => 'Gaming',
    'controller'          => 'Gaming',
    // Power
    'onduleur'            => 'Power Protection',
    'stabilisateur'       => 'Power Protection',
    'ups'                 => 'Power Protection',
    'batterie'            => 'Power Protection',
    // Networking
    'routeur'             => 'Networking',
    'switch'              => 'Networking',
    'wifi'                => 'Networking',
    'repeteur'            => 'Networking',
    'cpl'                 => 'Networking',
    // Storage
    'cle ssd'             => 'Storage',
    'ssd'                 => 'Storage',
    'disque dur'          => 'Storage',
    'stockage'            => 'Storage',
    // Computer Accessories (general)
    'support'             => 'Computer Accessories',
    'etui'                => 'Computer Accessories',
    'coque'               => 'Computer Accessories',
    'protection'          => 'Computer Accessories',
    'film'                => 'Computer Accessories',
    'verre'               => 'Computer Accessories',
    'ecran'               => 'Computer Accessories',
    'screen'              => 'Computer Accessories',
);

// Track what we're doing
$to_delete = array();
$to_categorize = array();
$skipped = array();

foreach ( $products as $product ) {
    $pid = $product->get_id();
    $name = $product->get_name();
    $name_lower = strtolower( $name );
    $image_id = $product->get_image_id();
    $has_image = ! empty( $image_id );

    if ( ! $has_image ) {
        // No image = non-tech = delete
        $to_delete[] = array( 'id' => $pid, 'name' => $name );
    } else {
        // Has image = try to categorize
        $assigned = false;
        foreach ( $category_map as $keyword => $cat_name ) {
            if ( strpos( $name_lower, $keyword ) !== false ) {
                $to_categorize[] = array( 'id' => $pid, 'name' => $name, 'category' => $cat_name );
                $assigned = true;
                break;
            }
        }
        if ( ! $assigned ) {
            $skipped[] = array( 'id' => $pid, 'name' => $name );
        }
    }
}

echo "<h3>Preview (dry run):</h3>";

echo "<h4>To DELETE (no image, non-tech): " . count( $to_delete ) . "</h4>";
echo "<ul>";
foreach ( $to_delete as $item ) {
    echo "<li>ID {$item['id']}: {$item['name']}</li>";
}
echo "</ul>";

echo "<h4>To CATEGORIZE (has image): " . count( $to_categorize ) . "</h4>";
echo "<ul>";
foreach ( $to_categorize as $item ) {
    echo "<li>ID {$item['id']}: {$item['name']} → {$item['category']}</li>";
}
echo "</ul>";

echo "<h4>Skipped (has image, no match): " . count( $skipped ) . "</h4>";
echo "<ul>";
foreach ( $skipped as $item ) {
    echo "<li>ID {$item['id']}: {$item['name']}</li>";
}
echo "</ul>";

if ( $mode === 'execute' ) {
    echo "<hr><h3>EXECUTING:</h3>";

    // Delete products
    $deleted = 0;
    foreach ( $to_delete as $item ) {
        $result = wp_delete_post( $item['id'], true );
        if ( $result ) {
            echo "<p style='color:green'>Deleted: {$item['name']} (ID {$item['id']})</p>";
            $deleted++;
        } else {
            echo "<p style='color:red'>Failed to delete: {$item['name']}</p>";
        }
    }

    // Categorize products
    $categorized = 0;
    foreach ( $to_categorize as $item ) {
        $cat = get_term_by( 'name', $item['category'], 'product_cat' );
        if ( $cat ) {
            wp_set_object_terms( $item['id'], array( (int) $cat->term_id ), 'product_cat' );
            echo "<p style='color:blue'>Categorized: {$item['name']} → {$item['category']}</p>";
            $categorized++;
        } else {
            echo "<p style='color:red'>Category not found: {$item['category']} for {$item['name']}</p>";
        }
    }

    // Also remove from Non classé for categorized products
    foreach ( $to_categorize as $item ) {
        $current_terms = wp_get_object_terms( $item['id'], 'product_cat', array( 'fields' => 'ids' ) );
        if ( ! is_wp_error( $current_terms ) ) {
            $new_terms = array_diff( $current_terms, array( $nonclasse->term_id ) );
            wp_set_object_terms( $item['id'], $new_terms, 'product_cat' );
        }
    }

    echo "<hr><h3>Results: Deleted $deleted, Categorized $categorized</h3>";
    echo "<p><a href='/price-editor/?key=gamtech2026prices'>← Back to Price Editor</a></p>";
} else {
    echo "<hr><p><a href='?key=gamtech2026prices&mode=execute'>CLICK TO EXECUTE (delete + categorize)</a></p>";
}
