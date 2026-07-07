<?php
defined( 'ABSPATH' ) || exit;

function gamtech_run_charger_import() {
    if ( ! isset( $_GET['run_gamtech_charger_import'] ) ) return;

    while ( ob_get_level() > 0 ) { ob_end_clean(); }

    if ( ( $_GET['key'] ?? '' ) !== 'gamtech2026charger' ) {
        header( 'HTTP/1.1 403 Forbidden' );
        echo 'Unauthorized';
        exit;
    }

    if ( get_option( 'gamtech_charger_import_done' ) === 'yes' ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo 'Charger import already completed. Delete wp_option gamtech_charger_import_done to re-run.';
        exit;
    }

    if ( ! function_exists( 'wc_get_product' ) ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo 'WooCommerce is not active.';
        exit;
    }

    @set_time_limit( 300 );
    @ini_set( 'memory_limit', '256M' );

    $uploads_dir = wp_upload_dir();
    $img_dir     = $uploads_dir['basedir'] . '/product-images/chargers/';
    $img_url     = $uploads_dir['baseurl'] . '/product-images/chargers/';

    // Get or create "Laptop Accessories > Chargers" category
    function gamtech_charger_get_or_create_cat( $name, $parent_id = 0, &$cache = array() ) {
        $key = $name . '_' . $parent_id;
        if ( isset( $cache[ $key ] ) ) return $cache[ $key ];

        $existing = get_term_by( 'name', $name, 'product_cat' );
        if ( $existing && ( $parent_id === 0 || (int) $existing->parent === $parent_id ) ) {
            $cache[ $key ] = $existing->term_id;
            return $existing->term_id;
        }

        $args = array( 'slug' => sanitize_title( $name ) );
        if ( $parent_id ) $args['parent'] = $parent_id;

        $result = wp_insert_term( $name, 'product_cat', $args );
        if ( is_wp_error( $result ) ) {
            $by_slug = get_term_by( 'slug', sanitize_title( $name ), 'product_cat' );
            if ( $by_slug ) {
                $cache[ $key ] = $by_slug->term_id;
                return $by_slug->term_id;
            }
            return 0;
        }
        $cache[ $key ] = $result['term_id'];
        return $result['term_id'];
    }

    $cat_cache = array();
    $parent_id = gamtech_charger_get_or_create_cat( 'Laptop Accessories', 0, $cat_cache );
    $sub_id    = gamtech_charger_get_or_create_cat( 'Chargers', $parent_id, $cat_cache );
    $assign_cats = array( $parent_id );
    if ( $sub_id ) $assign_cats[] = $sub_id;

    // Register image as WP attachment
    function gamtech_charger_attach_image( $filename, $img_dir, $img_url ) {
        $filepath = $img_dir . $filename;
        if ( ! file_exists( $filepath ) ) return 0;

        $existing = get_posts( array(
            'post_type'      => 'attachment',
            'meta_key'       => '_gamtech_charger_img',
            'meta_value'     => $filename,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ) );
        if ( ! empty( $existing ) ) return $existing[0];

        $wp_filetype = wp_check_filetype( $filename );
        $attachment  = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name( pathinfo( $filename, PATHINFO_FILENAME ) ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );
        $attach_id = wp_insert_attachment( $attachment, $filepath );
        if ( is_wp_error( $attach_id ) || ! $attach_id ) return 0;

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filepath );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        update_post_meta( $attach_id, '_gamtech_charger_img', $filename );

        return $attach_id;
    }

    // Scan all image files in the chargers directory
    $allowed_exts = array( 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' );
    $image_files  = array();

    if ( is_dir( $img_dir ) ) {
        $files = scandir( $img_dir );
        foreach ( $files as $file ) {
            if ( $file === '.' || $file === '..' ) continue;
            $ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
            if ( in_array( $ext, $allowed_exts, true ) ) {
                $image_files[] = $file;
            }
        }
    }
    sort( $image_files );

    $log     = array();
    $created = 0;
    $skipped = 0;

    foreach ( $image_files as $img_file ) {
        $name = pathinfo( $img_file, PATHINFO_FILENAME );

        // Check if product already exists
        $existing = get_posts( array(
            'post_type'      => 'product',
            'title'          => $name,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'post_status'    => 'any',
        ) );
        if ( ! empty( $existing ) ) {
            $log[] = "SKIP (exists): $name";
            $skipped++;
            continue;
        }

        $product = new WC_Product_Simple();
        $product->set_name( $name );
        $product->set_status( 'publish' );
        $product->set_catalog_visibility( 'visible' );
        $product->set_description( '' );
        $product->set_short_description( '' );
        $product->set_regular_price( '' );
        $product->set_category_ids( $assign_cats );
        $product->set_manage_stock( false );
        $product->set_stock_status( 'instock' );
        $product_id = $product->save();

        if ( ! $product_id || is_wp_error( $product_id ) ) {
            $log[] = "ERROR creating: $name";
            continue;
        }

        $attach_id = gamtech_charger_attach_image( $img_file, $img_dir, $img_url );
        if ( $attach_id ) {
            set_post_thumbnail( $product_id, $attach_id );
        }

        $log[] = "CREATED: $name (ID $product_id)" . ( $attach_id ? " + image" : " [no image]" );
        $created++;
    }

    update_option( 'gamtech_charger_import_done', 'yes' );

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "====================================\n";
    echo "GamTech Charger Import Complete\n";
    echo "Created : $created\n";
    echo "Skipped : $skipped\n";
    echo "====================================\n\n";
    echo implode( "\n", $log );
    exit;
}
add_action( 'wp_loaded', 'gamtech_run_charger_import', 1 );
