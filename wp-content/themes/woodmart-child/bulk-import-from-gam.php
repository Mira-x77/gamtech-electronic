<?php
/**
 * Bulk Import Products from Gam Folder
 * 
 * Creates WooCommerce products from C:\Users\Skydrake\Documents\gam
 * Products are created as DRAFT with no name/price - you set them in admin panel
 * 
 * Usage: https://gamtech-electronic.com/?bulk_import_gam=1&key=gamtech2026import
 */

defined( 'ABSPATH' ) || exit;

function gamtech_bulk_import_from_gam() {
    if ( ! isset( $_GET['bulk_import_gam'] ) ) {
        return;
    }

    // Clean output buffer
    while ( ob_get_level() > 0 ) {
        ob_end_clean();
    }

    // Security check
    if ( ( $_GET['key'] ?? '' ) !== 'gamtech2026import' ) {
        header( 'HTTP/1.1 403 Forbidden' );
        echo 'Unauthorized';
        exit;
    }

    // Check if already run
    if ( get_option( 'gamtech_gam_import_done' ) === 'yes' ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo "Import already completed.\n";
        echo "Delete wp_option 'gamtech_gam_import_done' to re-run.\n";
        exit;
    }

    if ( ! function_exists( 'wc_get_product' ) ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo 'WooCommerce is not active.';
        exit;
    }

    @set_time_limit( 300 );
    @ini_set( 'memory_limit', '256M' );

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "========================================\n";
    echo "GamTech Bulk Product Import\n";
    echo "========================================\n\n";

    // Source folder (local path on server)
    $source_folder = 'C:\\Users\\Skydrake\\Documents\\gam';
    
    // On Linux server, try this path instead
    if ( ! is_dir( $source_folder ) ) {
        $source_folder = '/home/c2423708c/gam';
    }
    
    if ( ! is_dir( $source_folder ) ) {
        echo "ERROR: Source folder not found: $source_folder\n";
        echo "Please upload images to the server first.\n";
        exit;
    }

    echo "Source: $source_folder\n\n";

    // Get all JPG files
    $images = glob( $source_folder . '/*.jpg' );
    
    if ( empty( $images ) ) {
        echo "ERROR: No JPG images found in $source_folder\n";
        exit;
    }

    echo "Found " . count( $images ) . " images\n\n";

    // WordPress uploads directory
    $uploads_dir = wp_upload_dir();
    $target_dir = $uploads_dir['basedir'] . '/gam-products';
    
    if ( ! is_dir( $target_dir ) ) {
        wp_mkdir_p( $target_dir );
    }

    $log = array();
    $created = 0;
    $skipped = 0;

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    foreach ( $images as $image_path ) {
        $filename = basename( $image_path );
        
        // Skip text files
        if ( strpos( $filename, '.txt' ) !== false ) {
            continue;
        }

        // Generate SKU from filename
        $sku = str_replace( '.jpg', '', $filename );
        
        // Check if product with this SKU already exists
        $existing = wc_get_products( array(
            'sku'    => $sku,
            'limit'  => 1,
            'return' => 'ids',
        ) );

        if ( ! empty( $existing ) ) {
            $log[] = "SKIP (exists): $filename (SKU: $sku)";
            $skipped++;
            continue;
        }

        // Copy image to WordPress uploads
        $target_path = $target_dir . '/' . $filename;
        
        if ( ! file_exists( $target_path ) ) {
            copy( $image_path, $target_path );
        }

        // Create attachment
        $filetype = wp_check_filetype( $filename );
        $attachment = array(
            'guid'           => $uploads_dir['baseurl'] . '/gam-products/' . $filename,
            'post_mime_type' => $filetype['type'],
            'post_title'     => $sku,
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attach_id = wp_insert_attachment( $attachment, $target_path );
        
        if ( is_wp_error( $attach_id ) || ! $attach_id ) {
            $log[] = "ERROR: Failed to create attachment for $filename";
            $skipped++;
            continue;
        }

        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata( $attach_id, $target_path );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        // Create WooCommerce product (PUBLISHED)
        $product = new WC_Product_Simple();
        $product->set_name( $sku ); // Temporary name (user will change)
        $product->set_status( 'publish' ); // PUBLISHED - visible immediately
        $product->set_catalog_visibility( 'visible' );
        $product->set_description( 'Product description - edit in admin panel' );
        $product->set_short_description( '' );
        $product->set_sku( $sku );
        $product->set_regular_price( '0' ); // Price $0 - update in admin
        $product->set_manage_stock( false );
        $product->set_stock_status( 'instock' );
        $product->set_image_id( $attach_id );

        $product_id = $product->save();

        if ( ! $product_id || is_wp_error( $product_id ) ) {
            $log[] = "ERROR creating product: $filename";
            $skipped++;
            continue;
        }

        $log[] = "CREATED: $filename → Product ID $product_id (Published, SKU: $sku)";
        $created++;
    }

    // Mark as done
    update_option( 'gamtech_gam_import_done', 'yes' );

    echo "====================================\n";
    echo "Import Complete\n";
    echo "Created : $created products\n";
    echo "Skipped : $skipped products\n";
    echo "====================================\n\n";
    echo "All products are PUBLISHED (live on site).\n";
    echo "Go to https://gamtech-electronic.com/admin to edit names and prices.\n\n";
    echo "Details:\n";
    echo "--------\n";
    echo implode( "\n", $log );
    exit;
}
add_action( 'wp_loaded', 'gamtech_bulk_import_from_gam', 1 );
