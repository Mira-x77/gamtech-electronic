<?php
/**
 * GamTech Product Importer
 * Creates WooCommerce categories and products from the product-images folder.
 * Runs once via ?run_gamtech_import=1 and records completion in wp_options.
 *
 * Access: yourdomain.com/?run_gamtech_import=1&key=gamtech2026import
 */

defined( 'ABSPATH' ) || exit;

function gamtech_run_product_import() {
    if ( ! isset( $_GET['run_gamtech_import'] ) ) return;

    // Clean any buffered output so we can send our own headers/content
    while ( ob_get_level() > 0 ) { ob_end_clean(); }

    if ( ( $_GET['key'] ?? '' ) !== 'gamtech2026import' ) {
        header( 'HTTP/1.1 403 Forbidden' );
        echo 'Unauthorized';
        exit;
    }

    // Prevent re-running if already done
    if ( get_option( 'gamtech_import_done' ) === 'yes' ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo 'Import already completed. Delete wp_option gamtech_import_done to re-run.';
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
    $img_base    = $uploads_dir['basedir'] . '/product-images/';
    $img_url     = $uploads_dir['baseurl'] . '/product-images/';

    // ────────────────────────────────────────────────
    // PRODUCT DATA: [ 'Product Name', 'image-file.ext', 'Category', 'Subcategory or null' ]
    // ────────────────────────────────────────────────
    $catalog = array(

        // ── MOUSE ──────────────────────────────────
        array( 'Logitech M220 Silent',          'Logitech-M220-Silent.png',      'Mouse', null ),
        array( 'Logitech M90',                  'Logitech-M90.jpg',              'Mouse', null ),
        array( 'Logitech Lift Vertical',        'Logitech-Lift-Vertical.jpg',    'Mouse', null ),
        array( 'Logitech MX Master 4',          'Logitech-MX-Master-4.png',      'Mouse', null ),
        array( 'Logitech Pro X Superlight 2',   'Logitech-Pro-X-Superlight-2.png','Mouse', null ),
        array( 'Logitech G502 X',               'Logitech-G502-X.png',           'Mouse', null ),
        array( 'Logitech Signature M650L',      'Logitech-Signature-M650L.jpg',  'Mouse', null ),
        array( 'HP S1500',                      'HP-S1500-Mouse.jpg',            'Mouse', null ),
        array( 'HP M10',                        'HP-M10-Mouse.jpg',              'Mouse', null ),
        array( 'HP DM10',                       'HP-DM10-Mouse.jpg',             'Mouse', null ),
        array( 'Lenovo 150WL',                  'Lenovo-150WL.jpg',              'Mouse', null ),

        // ── HEADPHONES & AUDIO ─────────────────────
        array( 'Logitech H390',                 'Logitech-H390.jpg',             'Headphones & Audio', null ),
        array( 'Logitech G435',                 'Logitech-G435.webp',            'Headphones & Audio', null ),
        array( 'HP H231R',                      'HP-H231R.png',                  'Headphones & Audio', null ),
        array( 'Tangbo C3U',                    'Tangbo-C3U.jpg',                'Headphones & Audio', null ),

        // ── KEYBOARDS ──────────────────────────────
        array( 'MX Keys Mini',                  'Logitech-MX-Keys-Mini.png',     'Keyboards', null ),
        array( 'MX Keys S',                     'Logitech-MX-Keys-S.jpg',        'Keyboards', null ),
        array( 'BT Mini Keyboard with LED',     'BT-Mini-Keyboard-LED.jpg',      'Keyboards', null ),
        array( 'Pebble 2 Combo',                'Logitech-Pebble-2-Combo.png',   'Keyboards', null ),
        array( 'Foldable Keyboard with Touchpad','Foldable-Keyboard-Touchpad.jpg','Keyboards', null ),
        array( 'Gaming Keyboard',               'Logitech-Gaming-Keyboard.png',  'Keyboards', null ),
        array( 'JK100F',                        'JK100F-Keyboard.jpg',           'Keyboards', null ),
        array( 'HP CS500',                      'HP-CS500-Keyboard.jpg',         'Keyboards', null ),
        array( 'Mini Keyboard',                 'Mini-Keyboard-Wireless.png',    'Keyboards', null ),

        // ── STORAGE: HDD ───────────────────────────
        array( '500GB HDD',                     'HDD-500GB.png',                 'Storage', 'HDD' ),
        array( '1TB HDD',                       'HDD-1TB.png',                   'Storage', 'HDD' ),
        array( '2TB HDD',                       'HDD-2TB.jpg',                   'Storage', 'HDD' ),
        array( 'Portable HDD 1TB',              'Portable-HDD-1TB.jpg',          'Storage', 'HDD' ),
        array( 'Portable HDD 2TB',              'Portable-HDD-2TB.jpg',          'Storage', 'HDD' ),

        // ── STORAGE: SSD ───────────────────────────
        array( '500GB SSD',                     'SSD-500GB.jpg',                 'Storage', 'SSD' ),
        array( '1TB SSD',                       'SSD-1TB.jpg',                   'Storage', 'SSD' ),
        array( 'NVMe SSD',                      'NVMe-SSD.jpg',                  'Storage', 'SSD' ),
        array( 'Lexar NM620 512GB',             'Lexar-NM620-512GB.jpg',         'Storage', 'SSD' ),

        // ── STORAGE: External ──────────────────────
        array( 'Storage 25M3 2TB',              'Storage-25M3-2TB.jpg',          'Storage', 'External Storage' ),

        // ── STORAGE: USB Flash Drives ──────────────
        array( 'ZNY 4GB',                       'ZNY-4GB-USB.png',               'Storage', 'USB Flash Drives' ),
        array( 'ZNY 8GB',                       'ZNY-8GB-USB.png',               'Storage', 'USB Flash Drives' ),
        array( 'SanDisk 16GB',                  'SanDisk-16GB-USB.jpg',          'Storage', 'USB Flash Drives' ),
        array( 'SanDisk 64GB',                  'SanDisk-64GB-USB.jpg',          'Storage', 'USB Flash Drives' ),
        array( 'SanDisk 128GB',                 'SanDisk-128GB-USB.jpg',         'Storage', 'USB Flash Drives' ),
        array( 'SanDisk 256GB',                 'SanDisk-256GB-USB.jpg',         'Storage', 'USB Flash Drives' ),
        array( 'SanDisk 512GB',                 'SanDisk-512GB-USB.png',         'Storage', 'USB Flash Drives' ),

        // ── STORAGE: Memory Cards ──────────────────
        array( 'SanDisk MicroSD 16GB',          'SanDisk-MicroSD-16GB.jpg',      'Storage', 'Memory Cards' ),
        array( 'SanDisk MicroSD 64GB',          'SanDisk-MicroSD-64GB.jpg',      'Storage', 'Memory Cards' ),
        array( 'SanDisk MicroSD 128GB',         'SanDisk-MicroSD-128GB.jpg',     'Storage', 'Memory Cards' ),
        array( 'SanDisk MicroSD 256GB',         'SanDisk-MicroSD-256GB.jpg',     'Storage', 'Memory Cards' ),
        array( 'SanDisk MicroSD 512GB',         'SanDisk-MicroSD-512GB.png',     'Storage', 'Memory Cards' ),

        // ── RAM & MEMORY ───────────────────────────
        array( 'Laptop RAM',                    'Laptop-RAM.jpg',                'RAM & Memory', 'Laptop RAM' ),
        array( 'Desktop RAM',                   'Desktop-RAM.jpg',               'RAM & Memory', 'Desktop RAM' ),

        // ── NETWORKING: Routers ────────────────────
        array( 'TP-Link Router',                'TP-Link-Router.jpg',            'Networking', 'Routers' ),
        array( 'Tenda Router',                  'Tenda-Router.jpg',              'Networking', 'Routers' ),
        array( 'Mercury Router',                'Mercury-Router.jpg',            'Networking', 'Routers' ),

        // ── NETWORKING: Access Points ──────────────
        array( 'NanoStation M5 Loco',           'NanoStation-M5-Loco.jpg',       'Networking', 'Access Points' ),

        // ── NETWORKING: Wi-Fi Adapters ─────────────
        array( 'BLGP2500M Adapter',             'BLGP2500M-WiFi-Adapter.jpg',    'Networking', 'Wi-Fi Adapters' ),
        array( 'AC1200 Adapter',                'AC1200-WiFi-Adapter.jpg',       'Networking', 'Wi-Fi Adapters' ),
        array( 'AX900 Adapter',                 'AX900-WiFi-Adapter.jpg',        'Networking', 'Wi-Fi Adapters' ),
        array( 'G146 Adapter',                  'G146-WiFi-Adapter.jpg',         'Networking', 'Wi-Fi Adapters' ),

        // ── NETWORKING: Accessories ────────────────
        array( 'LAN Cable CAT6',                'LAN-Cable-CAT6.jpg',            'Networking', 'Network Accessories' ),
        array( 'Wireless Display Dongle',       'Wireless-Display-Dongle.jpg',   'Networking', 'Network Accessories' ),

        // ── CABLES & CONVERTERS: HDMI ──────────────
        array( 'HDMI Cable',                    'HDMI-Cable.jpg',                'Cables & Converters', 'HDMI' ),
        array( 'HDMI Switch',                   'HDMI-Switch.jpg',               'Cables & Converters', 'HDMI' ),

        // ── CABLES & CONVERTERS: VGA ───────────────
        array( 'VGA Cable',                     'VGA-Cable.jpg',                 'Cables & Converters', 'VGA' ),
        array( 'HDMI to VGA Converter',         'HDMI-to-VGA-Converter.jpg',     'Cables & Converters', 'VGA' ),

        // ── CABLES & CONVERTERS: USB-C / Type-C ───
        array( 'HDMI to Type-C Converter',      'HDMI-to-TypeC-Converter.png',   'Cables & Converters', 'USB-C / Type-C' ),
        array( 'Type-C to HDMI Converter',      'TypeC-to-HDMI-Converter.jpg',   'Cables & Converters', 'USB-C / Type-C' ),

        // ── LAPTOP ACCESSORIES ─────────────────────
        array( 'Laptop Stand',                  'Laptop-Stand.jpg',              'Laptop Accessories', 'Laptop Stands' ),
        array( 'Laptop Stand with Cooler',      'Laptop-Stand-Cooler.jpg',       'Laptop Accessories', 'Laptop Stands' ),
        array( 'Autoon Dock Station',           'Dock-Station.jpg',              'Laptop Accessories', 'Docking Stations' ),
        array( 'Internal Laptop Batteries',     'Laptop-Battery-Internal.webp',  'Laptop Accessories', 'Laptop Batteries' ),
        array( 'Removable Laptop Batteries',    'Laptop-Battery-Removable.jpg',  'Laptop Accessories', 'Laptop Batteries' ),
        array( 'Dell Laptop Charger',           'Dell-Laptop-Charger.jpg',       'Laptop Accessories', 'Chargers' ),
        array( 'HP Laptop Charger',             'HP-Laptop-Charger.jpg',         'Laptop Accessories', 'Chargers' ),
        array( 'Acer Laptop Charger',           'Acer-Laptop-Charger.jpg',       'Laptop Accessories', 'Chargers' ),
        array( 'Lenovo Laptop Charger',         'Lenovo-Laptop-Charger.jpg',     'Laptop Accessories', 'Chargers' ),
        array( 'Universal Laptop Charger',      'Universal-Laptop-Charger.jpg',  'Laptop Accessories', 'Chargers' ),

        // ── COMPUTER ACCESSORIES ───────────────────
        array( 'Mouse Pad',                     'Mouse-Pad.jpg',                 'Computer Accessories', null ),
        array( 'Webcam HD3000',                 'Webcam-HD3000.jpg',             'Computer Accessories', null ),
        array( 'ThinkPlus KO1C',                'ThinkPlus-KO1C.jpg',            'Computer Accessories', null ),
        array( 'CMOS Batteries',                'CMOS-Battery.jpg',              'Computer Accessories', null ),
        array( 'HY880 Thermal Paste',           'HY880-Thermal-Paste.jpg',       'Computer Accessories', null ),

        // ── TOOLS & REPAIR ─────────────────────────
        array( 'RJ45 Crimping Tool',            'RJ45-Crimping-Tool.jpg',        'Tools & Repair', 'Networking Tools' ),
        array( 'Cable Stripper',                'Cable-Stripper.jpg',            'Tools & Repair', 'Networking Tools' ),
        array( 'Network Tool Kit',              'Network-Tool-Kit.jpg',          'Tools & Repair', 'Networking Tools' ),
        array( '6-in-1 Electric Screwdriver',   'Electric-Screwdriver.jpg',      'Tools & Repair', 'Electronics Tools' ),
        array( 'Computer Repair Toolkit',       'Computer-Repair-Toolkit.jpg',   'Tools & Repair', 'Electronics Tools' ),

        // ── ADAPTERS & HUBS ────────────────────────
        array( 'USB Adapters',                  'USB-Adapter-Hub.jpg',           'Adapters & Hubs', null ),
        array( 'Network Adapters',              'Network-Adapter-USB.jpg',       'Adapters & Hubs', null ),
        array( 'HDMI Adapters',                 'HDMI-Adapter.jpg',              'Adapters & Hubs', null ),
        array( 'Type-C Adapters',               'TypeC-Adapter-Hub.jpg',         'Adapters & Hubs', null ),
        array( 'Video Adapters',                'Video-Adapter-VGA-HDMI.jpg',    'Adapters & Hubs', null ),
        array( 'Multi-Port Adapters',           'Multi-Port-USB-Hub.jpg',        'Adapters & Hubs', null ),
    );

    // ────────────────────────────────────────────────
    // HELPER: get or create product_cat term
    // ────────────────────────────────────────────────
    $cat_cache = array();

    function gamtech_get_or_create_cat( $name, $parent_id = 0, &$cache = array() ) {
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
            // Might already exist under different slug
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

    // ────────────────────────────────────────────────
    // HELPER: register image as WP attachment
    // ────────────────────────────────────────────────
    function gamtech_attach_image( $filename, $img_base, $img_url ) {
        $filepath = $img_base . $filename;
        if ( ! file_exists( $filepath ) ) return 0;

        // Check if already attached
        $existing = get_posts( array(
            'post_type'      => 'attachment',
            'meta_key'       => '_gamtech_img_file',
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
        update_post_meta( $attach_id, '_gamtech_img_file', $filename );

        return $attach_id;
    }

    // ────────────────────────────────────────────────
    // RUN THE IMPORT
    // ────────────────────────────────────────────────
    $log     = array();
    $created = 0;
    $skipped = 0;

    foreach ( $catalog as $entry ) {
        list( $name, $img_file, $cat_name, $subcat_name ) = $entry;

        // Get/create parent category
        $parent_id = gamtech_get_or_create_cat( $cat_name, 0, $cat_cache );

        // Get/create subcategory
        $assign_cats = array( $parent_id );
        if ( $subcat_name ) {
            $sub_id = gamtech_get_or_create_cat( $subcat_name, $parent_id, $cat_cache );
            if ( $sub_id ) $assign_cats[] = $sub_id;
        }

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

        // Create the product
        $product = new WC_Product_Simple();
        $product->set_name( $name );
        $product->set_status( 'publish' );
        $product->set_catalog_visibility( 'visible' );
        $product->set_description( '' );
        $product->set_short_description( '' );
        $product->set_regular_price( '' ); // No price yet
        $product->set_category_ids( $assign_cats );
        $product->set_manage_stock( false );
        $product->set_stock_status( 'instock' );
        $product_id = $product->save();

        if ( ! $product_id || is_wp_error( $product_id ) ) {
            $log[] = "ERROR creating: $name";
            continue;
        }

        // Attach image
        $attach_id = gamtech_attach_image( $img_file, $img_base, $img_url );
        if ( $attach_id ) {
            set_post_thumbnail( $product_id, $attach_id );
        }

        $log[] = "CREATED: $name (ID $product_id)" . ( $attach_id ? " + image" : " [no image]" );
        $created++;
    }

    // Mark as done
    update_option( 'gamtech_import_done', 'yes' );

    // Output result
    header( 'Content-Type: text/plain; charset=utf-8' );
    echo "====================================\n";
    echo "GamTech Product Import Complete\n";
    echo "Created : $created\n";
    echo "Skipped : $skipped\n";
    echo "====================================\n\n";
    echo implode( "\n", $log );
    exit;
}
add_action( 'wp_loaded', 'gamtech_run_product_import', 1 );
