<?php
/**
 * Re-run product import — creates all 90 GamTech products.
 *
 * Access: https://gamtech-electronic.com/reimport-products.php?key=gamtech2026reimport
 *
 * SAFE TO RUN MULTIPLE TIMES — skips products that already exist.
 * DELETE this file after running!
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

define('ABSPATH', dirname(__FILE__) . '/');
require_once ABSPATH . 'wp-load.php';

$key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
if ($key !== 'gamtech2026reimport') {
    http_response_code(403);
    die('Unauthorized');
}

if (!function_exists('wc_get_product')) {
    die('WooCommerce not active.');
}

// Reset the import flag so the theme's importer can re-run if needed
delete_option('gamtech_import_done');

// Copy product images from repo folder to WordPress uploads
$repo_img_dir = ABSPATH . 'product-images/';
$uploads_dir  = wp_upload_dir();
$wp_img_dir   = $uploads_dir['basedir'] . '/product-images/';

if (!is_dir($wp_img_dir)) {
    wp_mkdir_p($wp_img_dir);
}

if (is_dir($repo_img_dir)) {
    $copied = 0;
    foreach (scandir($repo_img_dir) as $file) {
        if ($file === '.' || $file === '..') continue;
        $src = $repo_img_dir . $file;
        $dst = $wp_img_dir . $file;
        if (!file_exists($dst) && is_file($src)) {
            copy($src, $dst);
            $copied++;
        }
    }
    echo "Copied $copied new images to uploads.\n";
}

// Product catalog (same as gamtech-import-products.php)
$catalog = array(
    array('Logitech M220 Silent', 'Mouse', null),
    array('Logitech M90', 'Mouse', null),
    array('Logitech Lift Vertical', 'Mouse', null),
    array('Logitech MX Master 4', 'Mouse', null),
    array('Logitech Pro X Superlight 2', 'Mouse', null),
    array('Logitech G502 X', 'Mouse', null),
    array('Logitech Signature M650L', 'Mouse', null),
    array('HP S1500', 'Mouse', null),
    array('HP M10', 'Mouse', null),
    array('HP DM10', 'Mouse', null),
    array('Lenovo 150WL', 'Mouse', null),
    array('Logitech H390', 'Headphones & Audio', null),
    array('Logitech G435', 'Headphones & Audio', null),
    array('HP H231R', 'Headphones & Audio', null),
    array('Tangbo C3U', 'Headphones & Audio', null),
    array('MX Keys Mini', 'Keyboards', null),
    array('MX Keys S', 'Keyboards', null),
    array('BT Mini Keyboard with LED', 'Keyboards', null),
    array('Pebble 2 Combo', 'Keyboards', null),
    array('Foldable Keyboard with Touchpad', 'Keyboards', null),
    array('Gaming Keyboard', 'Keyboards', null),
    array('JK100F', 'Keyboards', null),
    array('HP CS500', 'Keyboards', null),
    array('Mini Keyboard', 'Keyboards', null),
    array('500GB HDD', 'Storage', 'HDD'),
    array('1TB HDD', 'Storage', 'HDD'),
    array('2TB HDD', 'Storage', 'HDD'),
    array('Portable HDD 1TB', 'Storage', 'HDD'),
    array('Portable HDD 2TB', 'Storage', 'HDD'),
    array('500GB SSD', 'Storage', 'SSD'),
    array('1TB SSD', 'Storage', 'SSD'),
    array('NVMe SSD', 'Storage', 'SSD'),
    array('Lexar NM620 512GB', 'Storage', 'SSD'),
    array('Storage 25M3 2TB', 'Storage', 'External Storage'),
    array('ZNY 4GB', 'Storage', 'USB Flash Drives'),
    array('ZNY 8GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk 16GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk 64GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk 128GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk 256GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk 512GB', 'Storage', 'USB Flash Drives'),
    array('SanDisk MicroSD 16GB', 'Storage', 'Memory Cards'),
    array('SanDisk MicroSD 64GB', 'Storage', 'Memory Cards'),
    array('SanDisk MicroSD 128GB', 'Storage', 'Memory Cards'),
    array('SanDisk MicroSD 256GB', 'Storage', 'Memory Cards'),
    array('SanDisk MicroSD 512GB', 'Storage', 'Memory Cards'),
    array('Laptop RAM', 'RAM & Memory', 'Laptop RAM'),
    array('Desktop RAM', 'RAM & Memory', 'Desktop RAM'),
    array('TP-Link Router', 'Networking', 'Routers'),
    array('Tenda Router', 'Networking', 'Routers'),
    array('Mercury Router', 'Networking', 'Routers'),
    array('NanoStation M5 Loco', 'Networking', 'Access Points'),
    array('BLGP2500M Adapter', 'Networking', 'Wi-Fi Adapters'),
    array('AC1200 Adapter', 'Networking', 'Wi-Fi Adapters'),
    array('AX900 Adapter', 'Networking', 'Wi-Fi Adapters'),
    array('G146 Adapter', 'Networking', 'Wi-Fi Adapters'),
    array('LAN Cable CAT6', 'Networking', 'Network Accessories'),
    array('Wireless Display Dongle', 'Networking', 'Network Accessories'),
    array('HDMI Cable', 'Cables & Converters', 'HDMI'),
    array('HDMI Switch', 'Cables & Converters', 'HDMI'),
    array('VGA Cable', 'Cables & Converters', 'VGA'),
    array('HDMI to VGA Converter', 'Cables & Converters', 'VGA'),
    array('HDMI to Type-C Converter', 'Cables & Converters', 'USB-C / Type-C'),
    array('Type-C to HDMI Converter', 'Cables & Converters', 'USB-C / Type-C'),
    array('Laptop Stand', 'Laptop Accessories', 'Laptop Stands'),
    array('Laptop Stand with Cooler', 'Laptop Accessories', 'Laptop Stands'),
    array('Autoon Dock Station', 'Laptop Accessories', 'Docking Stations'),
    array('Internal Laptop Batteries', 'Laptop Accessories', 'Laptop Batteries'),
    array('Removable Laptop Batteries', 'Laptop Accessories', 'Laptop Batteries'),
    array('Dell Laptop Charger', 'Laptop Accessories', 'Chargers'),
    array('HP Laptop Charger', 'Laptop Accessories', 'Chargers'),
    array('Acer Laptop Charger', 'Laptop Accessories', 'Chargers'),
    array('Lenovo Laptop Charger', 'Laptop Accessories', 'Chargers'),
    array('Universal Laptop Charger', 'Laptop Accessories', 'Chargers'),
    array('Mouse Pad', 'Computer Accessories', null),
    array('Webcam HD3000', 'Computer Accessories', null),
    array('ThinkPlus KO1C', 'Computer Accessories', null),
    array('CMOS Batteries', 'Computer Accessories', null),
    array('HY880 Thermal Paste', 'Computer Accessories', null),
    array('RJ45 Crimping Tool', 'Tools & Repair', 'Networking Tools'),
    array('Cable Stripper', 'Tools & Repair', 'Networking Tools'),
    array('Network Tool Kit', 'Tools & Repair', 'Networking Tools'),
    array('6-in-1 Electric Screwdriver', 'Tools & Repair', 'Electronics Tools'),
    array('Computer Repair Toolkit', 'Tools & Repair', 'Electronics Tools'),
    array('USB Adapters', 'Adapters & Hubs', null),
    array('Network Adapters', 'Adapters & Hubs', null),
    array('HDMI Adapters', 'Adapters & Hubs', null),
    array('Type-C Adapters', 'Adapters & Hubs', null),
    array('Video Adapters', 'Adapters & Hubs', null),
    array('Multi-Port Adapters', 'Adapters & Hubs', null),
);

// Helper: get or create category
function reimport_get_cat($name, $parent_id = 0) {
    $term = get_term_by('name', $name, 'product_cat');
    if ($term && !is_wp_error($term)) return $term->term_id;
    $term = get_term_by('slug', sanitize_title($name), 'product_cat');
    if ($term && !is_wp_error($term)) return $term->term_id;
    $args = array('slug' => sanitize_title($name));
    if ($parent_id) $args['parent'] = $parent_id;
    $result = wp_insert_term($name, 'product_cat', $args);
    if (is_wp_error($result)) {
        $by_slug = get_term_by('slug', sanitize_title($name), 'product_cat');
        return $by_slug ? $by_slug->term_id : 0;
    }
    return $result['term_id'];
}

$created = 0;
$skipped = 0;
$errors  = 0;
$log     = array();

foreach ($catalog as $entry) {
    list($name, $cat_name, $sub_name) = $entry;

    // Skip if product already exists
    $existing = get_posts(array(
        'post_type'      => 'product',
        'title'          => $name,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'post_status'    => 'any',
    ));
    if (!empty($existing)) {
        $skipped++;
        continue;
    }

    // Create category hierarchy
    $parent_id = reimport_get_cat($cat_name);
    $cat_ids   = array($parent_id);
    if ($sub_name && $parent_id) {
        $sub_id = reimport_get_cat($sub_name, $parent_id);
        if ($sub_id) $cat_ids[] = $sub_id;
    }

    // Create product
    $product = new WC_Product_Simple();
    $product->set_name($name);
    $product->set_status('publish');
    $product->set_catalog_visibility('visible');
    $product->set_description('');
    $product->set_short_description('');
    $product->set_regular_price('');
    $product->set_category_ids($cat_ids);
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');
    $product_id = $product->save();

    if (!$product_id || is_wp_error($product_id)) {
        $log[] = "ERROR: $name";
        $errors++;
        continue;
    }

    // Try to attach image from uploads/product-images/
    $img_patterns = array($name . '.png', $name . '.jpg', $name . '.webp', str_replace(' ', '-', $name) . '.png', str_replace(' ', '-', $name) . '.jpg');
    foreach ($img_patterns as $pattern) {
        $filepath = $wp_img_dir . $pattern;
        if (file_exists($filepath)) {
            $wp_filetype = wp_check_filetype($pattern);
            $attachment  = array(
                'post_mime_type' => $wp_filetype['type'] ?: 'image/jpeg',
                'post_title'     => sanitize_file_name(pathinfo($pattern, PATHINFO_FILENAME)),
                'post_status'    => 'inherit',
            );
            $attach_id = wp_insert_attachment($attachment, $filepath);
            if ($attach_id && !is_wp_error($attach_id)) {
                require_once ABSPATH . 'wp-admin/includes/image.php';
                $meta = wp_generate_attachment_metadata($attach_id, $filepath);
                wp_update_attachment_metadata($attach_id, $meta);
                set_post_thumbnail($product_id, $attach_id);
            }
            break;
        }
    }

    $log[] = "CREATED: $name (ID $product_id)";
    $created++;
}

echo "<!DOCTYPE html><html><head><title>Product Import</title>";
echo "<style>body{font-family:monospace;max-width:800px;margin:40px auto;padding:20px;background:#1a1a2e;color:#e0e0e0}";
echo "h1{color:#7c3aed} .ok{color:#22c55e} .err{color:#ef4444} .skip{color:#f4c430}</style></head><body>";
echo "<h1>GamTech Product Re-Import</h1>";
echo "<p><span class='ok'>Created: $created</span> | <span class='skip'>Skipped (existing): $skipped</span> | <span class='err'>Errors: $errors</span></p>";
echo "<p>Total in catalog: " . count($catalog) . "</p>";
echo "<pre>";
foreach ($log as $line) {
    $cls = 'ok';
    if (strpos($line, 'ERROR') === 0) $cls = 'err';
    if (strpos($line, 'SKIP') === 0) $cls = 'skip';
    echo "<span class='$cls'>$line</span>\n";
}
echo "</pre>";
echo "<p><a href='" . esc_url(home_url('/price-editor/?key=gamtech2026prices')) . "' style='color:#7c3aed'>→ Open Price Editor</a></p>";
echo "</body></html>";
