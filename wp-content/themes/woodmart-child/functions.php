<?php
/**
 * Gamtech Electronic — Child Theme Functions
 * Woodmart Child Theme
 */

defined( 'ABSPATH' ) || exit;

// =====================================================
// 1. ENQUEUE STYLES & GOOGLE FONTS
// =====================================================
function woodmart_child_enqueue_styles() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'woodmart-style',
        get_template_directory_uri() . '/style.css',
        array(),
        woodmart_get_theme_info( 'Version' )
    );

    // Google Fonts — Inter
    wp_enqueue_style(
        'gamtech-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap',
        array(),
        null
    );

    // Child theme stylesheet
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'woodmart-style' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );


// =====================================================
// 2. THEME SUPPORT & IMAGE SIZES
// =====================================================
function gamtech_child_setup() {
    // Custom logo support
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // WooCommerce gallery zoom/lightbox
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );

    // Custom image sizes for product grid
    add_image_size( 'gamtech-product-card', 400, 400, true );
    add_image_size( 'gamtech-hero',         1400, 500, true );
    add_image_size( 'gamtech-banner',       600,  400, true );
}
add_action( 'after_setup_theme', 'gamtech_child_setup', 11 );


// =====================================================
// 3. ANNOUNCEMENT BAR CUSTOMISER OPTION
// =====================================================
function gamtech_customizer( $wp_customize ) {
    // Section
    $wp_customize->add_section( 'gamtech_general', array(
        'title'    => __( 'Gamtech Settings', 'woodmart' ),
        'priority' => 30,
    ) );

    // Hero image
    $wp_customize->add_setting( 'gamtech_hero_image', array(
        'default'   => '',
        'transport' => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'gamtech_hero_image', array(
        'label'   => __( 'Hero Section Image', 'woodmart' ),
        'section' => 'gamtech_general',
    ) ) );

    // Ticker text
    $wp_customize->add_setting( 'gamtech_ticker_text', array(
        'default'           => 'Jackpot Deals | Tap to get Flat 50% Off',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'gamtech_ticker_text', array(
        'label'   => __( 'Ticker / Announcement Text', 'woodmart' ),
        'section' => 'gamtech_general',
        'type'    => 'text',
    ) );

    // Primary color
    $wp_customize->add_setting( 'gamtech_primary_color', array(
        'default'           => '#1a237e',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gamtech_primary_color', array(
        'label'   => __( 'Primary Color (Navy)', 'woodmart' ),
        'section' => 'gamtech_general',
    ) ) );

    // Accent color
    $wp_customize->add_setting( 'gamtech_accent_color', array(
        'default'           => '#ff6f00',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gamtech_accent_color', array(
        'label'   => __( 'Accent Color (Orange)', 'woodmart' ),
        'section' => 'gamtech_general',
    ) ) );
}
add_action( 'customize_register', 'gamtech_customizer' );


// =====================================================
// 4. DYNAMIC CSS FROM CUSTOMIZER
// =====================================================
function gamtech_dynamic_css() {
    $primary = get_theme_mod( 'gamtech_primary_color', '#1a237e' );
    $accent  = get_theme_mod( 'gamtech_accent_color', '#ff6f00' );

    $css = '
    :root {
        --gt-primary:      ' . esc_attr( $primary ) . ';
        --gt-accent:       ' . esc_attr( $accent ) . ';
        --gt-primary-dark: ' . gamtech_darken_color( $primary, 15 ) . ';
        --gt-accent-dark:  ' . gamtech_darken_color( $accent, 15 ) . ';
    }
    ';

    wp_add_inline_style( 'child-style', $css );
}
add_action( 'wp_enqueue_scripts', 'gamtech_dynamic_css', 20 );

/**
 * Simple hex color darkener (no external lib needed)
 */
function gamtech_darken_color( $hex, $percent ) {
    $hex = ltrim( $hex, '#' );
    if ( strlen( $hex ) === 3 ) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    $r = max( 0, hexdec( substr( $hex, 0, 2 ) ) - round( 255 * $percent / 100 ) );
    $g = max( 0, hexdec( substr( $hex, 2, 2 ) ) - round( 255 * $percent / 100 ) );
    $b = max( 0, hexdec( substr( $hex, 4, 2 ) ) - round( 255 * $percent / 100 ) );
    return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
}


// =====================================================
// 5. PRODUCT CARD — ADD QUICK LOOK BUTTON
// =====================================================
function gamtech_quick_look_button() {
    global $product;
    echo '<div class="gt-quick-look-btn" data-id="' . esc_attr( $product->get_id() ) . '">'
        . esc_html__( 'Quick Look', 'woodmart' )
        . '</div>';
}
// Only add if Woodmart's own quick view is not active
if ( ! woodmart_get_opt( 'quick_view' ) ) {
    add_action( 'woocommerce_before_shop_loop_item_title', 'gamtech_quick_look_button', 15 );
}


// =====================================================
// 6. PRODUCT CARD — SAVE BADGE (with amount)
// =====================================================
function gamtech_sale_badge_with_amount( $html, $post, $product ) {
    if ( $product->is_on_sale() ) {
        $reg  = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ( $reg > 0 ) {
            $saved   = $reg - $sale;
            $symbol  = get_woocommerce_currency_symbol();
            $html    = '<span class="onsale">Save ' . esc_html( $symbol ) . esc_html( number_format( $saved, 0 ) ) . '</span>';
        }
    }
    return $html;
}
add_filter( 'woocommerce_sale_flash', 'gamtech_sale_badge_with_amount', 10, 3 );


// =====================================================
// 7. REMOVE DEFAULT WOOCOMMERCE BREADCRUMBS ON HOMEPAGE
// =====================================================
function gamtech_remove_woo_breadcrumbs_homepage() {
    if ( is_front_page() ) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
    }
}
add_action( 'template_redirect', 'gamtech_remove_woo_breadcrumbs_homepage' );


// =====================================================
// 8. CUSTOM BODY CLASSES
// =====================================================
function gamtech_body_classes( $classes ) {
    $classes[] = 'gamtech-theme';
    if ( is_front_page() ) {
        $classes[] = 'gamtech-homepage';
    }
    if ( is_shop() || is_product_category() || is_product_tag() ) {
        $classes[] = 'gamtech-shop';
    }
    if ( is_product() ) {
        $classes[] = 'gamtech-single-product';
    }
    return $classes;
}
add_filter( 'body_class', 'gamtech_body_classes' );


// =====================================================
// 9. WISHLIST COUNT IN HEADER (if YITH or similar active)
// =====================================================
function gamtech_header_wishlist_count() {
    if ( function_exists( 'YITH_WCWL' ) ) {
        $count = YITH_WCWL()->count_products();
        if ( $count > 0 ) {
            echo '<span class="gt-wishlist-count">' . esc_html( $count ) . '</span>';
        }
    }
}


// =====================================================
// 10. WOOCOMMERCE — PRODUCTS PER PAGE
// =====================================================
function gamtech_products_per_page( $count ) {
    if ( is_product_category() || is_shop() ) {
        return 20;
    }
    return $count;
}
add_filter( 'loop_shop_per_page', 'gamtech_products_per_page', 20 );


// =====================================================
// 11. SCHEMA / SEO — BUSINESS INFO IN HEAD
// =====================================================
function gamtech_schema_org() {
    if ( is_front_page() ) {
        $schema = array(
            '@context'  => 'https://schema.org',
            '@type'     => 'ElectronicsStore',
            'name'      => get_bloginfo( 'name' ),
            'url'       => home_url(),
            'logo'      => wp_get_attachment_image_url( get_option( 'site_icon' ), 'full' ),
            'description' => get_bloginfo( 'description' ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'gamtech_schema_org' );


// =====================================================
// 12. FORCE WOODMART CHILD TEMPLATE FOR FRONT PAGE
// =====================================================
function gamtech_front_page_template( $template ) {
    if ( is_front_page() ) {
        $child_front = get_stylesheet_directory() . '/front-page.php';
        if ( file_exists( $child_front ) ) {
            return $child_front;
        }
    }
    return $template;
}
add_filter( 'template_include', 'gamtech_front_page_template', 99 );
