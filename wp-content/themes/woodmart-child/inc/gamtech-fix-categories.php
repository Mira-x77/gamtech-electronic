<?php
/**
 * GamTech Category Fix Script
 * Fixes miscategorized products — run once via:
 * https://gamtech-electronic.com/?run_gamtech_catfix=1&key=gamtech2026fix
 */

defined( 'ABSPATH' ) || exit;

function gamtech_run_category_fix() {
    if ( ! isset( $_GET['run_gamtech_catfix'] ) ) return;

    while ( ob_get_level() > 0 ) { ob_end_clean(); }

    if ( ( $_GET['key'] ?? '' ) !== 'gamtech2026fix' ) {
        header( 'HTTP/1.1 403 Forbidden' );
        echo 'Unauthorized';
        exit;
    }

    if ( ! function_exists( 'wc_get_product' ) ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo 'WooCommerce is not active.';
        exit;
    }

    @set_time_limit( 300 );
    @ini_set( 'memory_limit', '256M' );

    $log = array();

    // ── Correct category map: product name → [ parent category, subcategory or null ]
    $correct_map = array(
        // MOUSE — only actual mice
        'Logitech M220 Silent'        => array( 'Mouse', null ),
        'Logitech M90'                => array( 'Mouse', null ),
        'Logitech Lift Vertical'      => array( 'Mouse', null ),
        'Logitech MX Master 4'        => array( 'Mouse', null ),
        'Logitech Pro X Superlight 2' => array( 'Mouse', null ),
        'Logitech G502 X'             => array( 'Mouse', null ),
        'Logitech Signature M650L'    => array( 'Mouse', null ),
        'HP S1500'                    => array( 'Mouse', null ),
        'HP M10'                      => array( 'Mouse', null ),
        'HP DM10'                     => array( 'Mouse', null ),
        'Lenovo 150WL'                => array( 'Mouse', null ),

        // HEADPHONES & AUDIO
        'Logitech H390'               => array( 'Headphones & Audio', null ),
        'Logitech G435'               => array( 'Headphones & Audio', null ),
        'HP H231R'                    => array( 'Headphones & Audio', null ),
        'Tangbo C3U'                  => array( 'Headphones & Audio', null ),

        // KEYBOARDS
        'MX Keys Mini'                => array( 'Keyboards', null ),
        'MX Keys S'                   => array( 'Keyboards', null ),
        'BT Mini Keyboard with LED'   => array( 'Keyboards', null ),
        'Pebble 2 Combo'              => array( 'Keyboards', null ),
        'Foldable Keyboard with Touchpad' => array( 'Keyboards', null ),
        'Gaming Keyboard'             => array( 'Keyboards', null ),
        'JK100F'                      => array( 'Keyboards', null ),
        'HP CS500'                    => array( 'Keyboards', null ),
        'Mini Keyboard'               => array( 'Keyboards', null ),

        // STORAGE
        '500GB HDD'                   => array( 'Storage', 'HDD' ),
        '1TB HDD'                     => array( 'Storage', 'HDD' ),
        '2TB HDD'                     => array( 'Storage', 'HDD' ),
        'Portable HDD 1TB'            => array( 'Storage', 'HDD' ),
        'Portable HDD 2TB'            => array( 'Storage', 'HDD' ),
        '500GB SSD'                   => array( 'Storage', 'SSD' ),
        '1TB SSD'                     => array( 'Storage', 'SSD' ),
        'NVMe SSD'                    => array( 'Storage', 'SSD' ),
        'Lexar NM620 512GB'           => array( 'Storage', 'SSD' ),
        'Storage 25M3 2TB'            => array( 'Storage', 'External Storage' ),
        'ZNY 4GB'                     => array( 'Storage', 'USB Flash Drives' ),
        'ZNY 8GB'                     => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk 16GB'                => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk 64GB'                => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk 128GB'               => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk 256GB'               => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk 512GB'               => array( 'Storage', 'USB Flash Drives' ),
        'SanDisk MicroSD 16GB'        => array( 'Storage', 'Memory Cards' ),
        'SanDisk MicroSD 64GB'        => array( 'Storage', 'Memory Cards' ),
        'SanDisk MicroSD 128GB'       => array( 'Storage', 'Memory Cards' ),
        'SanDisk MicroSD 256GB'       => array( 'Storage', 'Memory Cards' ),
        'SanDisk MicroSD 512GB'       => array( 'Storage', 'Memory Cards' ),

        // RAM & MEMORY
        'Laptop RAM'                  => array( 'RAM & Memory', 'Laptop RAM' ),
        'Desktop RAM'                 => array( 'RAM & Memory', 'Desktop RAM' ),

        // NETWORKING
        'TP-Link Router'              => array( 'Networking', 'Routers' ),
        'Tenda Router'                => array( 'Networking', 'Routers' ),
        'Mercury Router'              => array( 'Networking', 'Routers' ),
        'NanoStation M5 Loco'         => array( 'Networking', 'Access Points' ),
        'BLGP2500M Adapter'           => array( 'Networking', 'Wi-Fi Adapters' ),
        'AC1200 Adapter'              => array( 'Networking', 'Wi-Fi Adapters' ),
        'AX900 Adapter'               => array( 'Networking', 'Wi-Fi Adapters' ),
        'G146 Adapter'                => array( 'Networking', 'Wi-Fi Adapters' ),
        'LAN Cable CAT6'              => array( 'Networking', 'Network Accessories' ),
        'Wireless Display Dongle'     => array( 'Networking', 'Network Accessories' ),

        // CABLES & CONVERTERS
        'HDMI Cable'                  => array( 'Cables & Converters', 'HDMI' ),
        'HDMI Switch'                 => array( 'Cables & Converters', 'HDMI' ),
        'VGA Cable'                   => array( 'Cables & Converters', 'VGA' ),
        'HDMI to VGA Converter'       => array( 'Cables & Converters', 'VGA' ),
        'HDMI to Type-C Converter'    => array( 'Cables & Converters', 'USB-C / Type-C' ),
        'Type-C to HDMI Converter'    => array( 'Cables & Converters', 'USB-C / Type-C' ),

        // LAPTOP ACCESSORIES
        'Laptop Stand'                => array( 'Laptop Accessories', 'Laptop Stands' ),
        'Laptop Stand with Cooler'    => array( 'Laptop Accessories', 'Laptop Stands' ),
        'Autoon Dock Station'         => array( 'Laptop Accessories', 'Docking Stations' ),
        'Internal Laptop Batteries'   => array( 'Laptop Accessories', 'Laptop Batteries' ),
        'Removable Laptop Batteries'  => array( 'Laptop Accessories', 'Laptop Batteries' ),
        'Dell Laptop Charger'         => array( 'Laptop Accessories', 'Chargers' ),
        'HP Laptop Charger'           => array( 'Laptop Accessories', 'Chargers' ),
        'Acer Laptop Charger'         => array( 'Laptop Accessories', 'Chargers' ),
        'Lenovo Laptop Charger'       => array( 'Laptop Accessories', 'Chargers' ),
        'Universal Laptop Charger'    => array( 'Laptop Accessories', 'Chargers' ),

        // COMPUTER ACCESSORIES — Mouse Pad GOES HERE, NOT in Mouse!
        'Mouse Pad'                   => array( 'Computer Accessories', null ),
        'Webcam HD3000'               => array( 'Computer Accessories', null ),
        'ThinkPlus KO1C'              => array( 'Computer Accessories', null ),
        'CMOS Batteries'              => array( 'Computer Accessories', null ),
        'HY880 Thermal Paste'         => array( 'Computer Accessories', null ),

        // TOOLS & REPAIR
        'RJ45 Crimping Tool'          => array( 'Tools & Repair', 'Networking Tools' ),
        'Cable Stripper'              => array( 'Tools & Repair', 'Networking Tools' ),
        'Network Tool Kit'            => array( 'Tools & Repair', 'Networking Tools' ),
        '6-in-1 Electric Screwdriver' => array( 'Tools & Repair', 'Electronics Tools' ),
        'Computer Repair Toolkit'     => array( 'Tools & Repair', 'Electronics Tools' ),

        // ADAPTERS & HUBS
        'USB Adapters'                => array( 'Adapters & Hubs', null ),
        'Network Adapters'            => array( 'Adapters & Hubs', null ),
        'HDMI Adapters'               => array( 'Adapters & Hubs', null ),
        'Type-C Adapters'             => array( 'Adapters & Hubs', null ),
        'Video Adapters'              => array( 'Adapters & Hubs', null ),
        'Multi-Port Adapters'         => array( 'Adapters & Hubs', null ),
    );

    // Helper: get term ID by name
    function gamtech_get_term_id( $name, $parent_id = 0 ) {
        $term = get_term_by( 'name', $name, 'product_cat' );
        if ( $term && ! is_wp_error( $term ) ) {
            return $term->term_id;
        }
        // Try slug
        $term = get_term_by( 'slug', sanitize_title( $name ), 'product_cat' );
        if ( $term && ! is_wp_error( $term ) ) {
            return $term->term_id;
        }
        return 0;
    }

    $fixed = 0;
    $notfound = 0;

    foreach ( $correct_map as $product_name => $cats ) {
        list( $parent_name, $sub_name ) = $cats;

        // Find product
        $posts = get_posts( array(
            'post_type'      => 'product',
            'title'          => $product_name,
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'post_status'    => 'any',
        ) );

        if ( empty( $posts ) ) {
            $log[] = "NOT FOUND: $product_name";
            $notfound++;
            continue;
        }

        $product_id = $posts[0];

        // Get parent cat ID
        $parent_id = gamtech_get_term_id( $parent_name );
        if ( ! $parent_id ) {
            $res = wp_insert_term( $parent_name, 'product_cat', array( 'slug' => sanitize_title( $parent_name ) ) );
            $parent_id = is_wp_error( $res ) ? 0 : $res['term_id'];
        }

        $assign = array( $parent_id );

        if ( $sub_name ) {
            $sub_id = gamtech_get_term_id( $sub_name );
            if ( ! $sub_id ) {
                $res = wp_insert_term( $sub_name, 'product_cat', array( 'slug' => sanitize_title( $sub_name ), 'parent' => $parent_id ) );
                $sub_id = is_wp_error( $res ) ? 0 : $res['term_id'];
            }
            if ( $sub_id ) $assign[] = $sub_id;
        }

        // Force-set categories (replace existing)
        $result = wp_set_object_terms( $product_id, $assign, 'product_cat', false );

        if ( is_wp_error( $result ) ) {
            $log[] = "ERROR fixing: $product_name — " . $result->get_error_message();
        } else {
            $cat_label = $sub_name ? "$parent_name > $sub_name" : $parent_name;
            $log[] = "FIXED: $product_name → $cat_label";
            $fixed++;
        }
    }

    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "====================================\n";
    echo "GamTech Category Fix Complete\n";
    echo "Fixed   : $fixed\n";
    echo "Missing : $notfound\n";
    echo "====================================\n\n";
    echo implode( "\n", $log );
    exit;
}
add_action( 'wp_loaded', 'gamtech_run_category_fix', 1 );
