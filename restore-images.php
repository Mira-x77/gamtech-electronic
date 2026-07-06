<?php
/**
 * Restore product images from product-images/ source folder
 * Re-registers them in WordPress media library
 */
require_once dirname(__FILE__) . '/wp-blog-header.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(300);

echo "<h1>Restore Product Images</h1>";
echo "<pre style='background:#111;color:#0f0;padding:15px;font-size:13px'>";

$source_dir = dirname(__FILE__) . '/product-images';
$upload_dir = wp_upload_dir();

// Ensure uploads dir exists
if (!is_dir($upload_dir['path'])) {
    @mkdir($upload_dir['path'], 0755, true);
}

// Ensure product-images subdir exists in uploads
$dest_dir = $upload_dir['basedir'] . '/product-images';
if (!is_dir($dest_dir)) {
    @mkdir($dest_dir, 0755, true);
    echo "Created: {$dest_dir}\n";
}

// Copy all source images to uploads
$count = 0;
$files = glob($source_dir . '/*');
if (!$files) {
    echo "ERROR: No files found in product-images/\n";
    echo "Checking if directory exists: " . (is_dir($source_dir) ? 'YES' : 'NO') . "\n";
} else {
    foreach ($files as $file) {
        $name = basename($file);
        $dest = $dest_dir . '/' . $name;
        if (!file_exists($dest)) {
            if (copy($file, $dest)) {
                $count++;
            } else {
                echo "FAILED: {$name}\n";
            }
        }
    }
    echo "Copied {$count} images to uploads/product-images/\n";
}

// Now re-attach images to products
echo "\nRe-attaching images to products...\n";
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$products = wc_get_products(array('limit' => -1, 'status' => 'publish'));
$attached = 0;
$skipped = 0;

foreach ($products as $product) {
    if ($product->get_image_id()) {
        $skipped++;
        continue; // Already has image
    }
    
    $name = $product->get_name();
    $sku = $product->get_sku();
    
    // Try to find matching image by SKU or name
    $image_file = null;
    
    // Try SKU first
    if ($sku) {
        $candidates = glob($dest_dir . '/' . $sku . '.*');
        if ($candidates) $image_file = $candidates[0];
    }
    
    // Try name-based matching
    if (!$image_file) {
        $clean_name = preg_replace('/[^a-zA-Z0-9\-]/', '-', $name);
        $clean_name = preg_replace('/-+/', '-', trim($clean_name, '-'));
        $candidates = glob($dest_dir . '/' . $clean_name . '.*');
        if ($candidates) $image_file = $candidates[0];
        
        // Try case-insensitive partial match
        if (!$image_file) {
            $all_files = glob($dest_dir . '/*');
            foreach ($all_files as $f) {
                $fname = strtolower(basename($f));
                $lname = strtolower($clean_name);
                if (strpos($fname, $lname) !== false || strpos($lname, pathinfo($fname, PATHINFO_FILENAME)) !== false) {
                    $image_file = $f;
                    break;
                }
            }
        }
    }
    
    if ($image_file) {
        $rel_path = 'product-images/' . basename($image_file);
        
        // Check if already in media library
        $existing = get_posts(array(
            'post_type'   => 'attachment',
            'meta_key'    => '_wp_attached_file',
            'meta_value'  => $rel_path,
            'numberposts' => 1,
        ));
        
        if ($existing) {
            $attach_id = $existing[0]->ID;
        } else {
            // Register in media library
            $filetype = wp_check_filetype(basename($image_file), null);
            $attachment = array(
                'guid'           => $upload_dir['baseurl'] . '/' . $rel_path,
                'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
                'post_title'     => sanitize_file_name(pathinfo($image_file, PATHINFO_FILENAME)),
                'post_content'   => '',
                'post_status'    => 'inherit',
            );
            $attach_id = wp_insert_attachment($attachment, $image_file);
            if (!is_wp_error($attach_id)) {
                wp_update_attachment_metadata($attach_id, wp_generate_attachment_metadata($attach_id, $image_file));
            }
        }
        
        if ($attach_id && !is_wp_error($attach_id)) {
            set_post_thumbnail($product->get_id(), $attach_id);
            $attached++;
            echo "  ✅ {$name} → attached\n";
        }
    } else {
        echo "  ❌ {$name} — no matching image found\n";
    }
}

echo "\n=== DONE ===\n";
echo "Attached: {$attached}\n";
echo "Already had images: {$skipped}\n";
echo "Total products: " . count($products) . "\n";
echo "\nTest: https://gamtech-electronic.com/\n";
echo "</pre>";
echo "<br><a href='/' style='color:#0f0;font-size:18px'>Test Homepage</a>";
