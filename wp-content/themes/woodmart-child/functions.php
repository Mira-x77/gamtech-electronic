<?php
/**
 * Cello Electronics — Child Theme Functions
 * Woodmart Child Theme
 */

// Catch PHP fatal errors and show a helpful page instead of blank 500
register_shutdown_function( function() {
    $error = error_get_last();
    if ( $error && in_array( $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ) ) ) {
        if ( ! headers_sent() ) {
            header( 'Content-Type: text/html; charset=utf-8' );
            http_response_code( 500 );
        }
        echo '<!DOCTYPE html><html><head><title>Site Error</title>';
        echo '<style>body{font-family:sans-serif;max-width:700px;margin:40px auto;padding:20px;background:#1a1a2e;color:#e0e0e0}';
        echo 'h1{color:#e74c3c}code{background:#333;padding:2px 6px;border-radius:3px;font-size:13px}';
        echo '.box{background:#16213e;padding:20px;border-radius:8px;border-left:4px solid #e74c3c}</style></head><body>';
        echo '<h1>Something went wrong</h1>';
        echo '<div class="box"><p><strong>Error:</strong> ' . htmlspecialchars( $error['message'] ) . '</p>';
        echo '<p><strong>File:</strong> <code>' . htmlspecialchars( $error['file'] ) . ':' . $error['line'] . '</code></p></div>';
        echo '<p style="margin-top:20px">Try refreshing the page. If this keeps happening, contact support.</p>';
        echo '</body></html>';
        exit;
    }
} );

defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/gamtech-core.php';
require_once get_stylesheet_directory() . '/inc/gamtech-import-products.php';
require_once get_stylesheet_directory() . '/inc/gamtech-fix-categories.php';
require_once get_stylesheet_directory() . '/bulk-import-from-gam.php';
require_once get_stylesheet_directory() . '/inc/gamtech-import-chargers.php';

// =====================================================
// 1. ENQUEUE STYLES & GOOGLE FONTS
// =====================================================
function woodmart_child_enqueue_styles() {
    // Parent theme stylesheet (minimal dependency)
    wp_enqueue_style(
        'woodmart-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( 'woodmart' )->get( 'Version' )
    );

    // Google Fonts — Poppins
    wp_enqueue_style(
        'cello-google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap',
        array(),
        null
    );

    // GamTech unified dark theme
    wp_enqueue_style(
        'gamtech-unified-css',
        get_stylesheet_directory_uri() . '/assets/gamtech-unified.css',
        array( 'cello-google-fonts' ),
        '2.0.' . filemtime( get_stylesheet_directory() . '/assets/gamtech-unified.css' )
    );

    wp_enqueue_script(
        'gamtech-unified-js',
        get_stylesheet_directory_uri() . '/assets/gamtech-unified.js',
        array(),
        '2.0.' . filemtime( get_stylesheet_directory() . '/assets/gamtech-unified.js' ),
        true
    );

    wp_localize_script( 'gamtech-unified-js', 'gsData', array(
        'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ),
        'shopUrl'     => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' ),
        'cartCount'   => function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0,
        'currency'    => function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$',
    ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );


// =====================================================
// 2. THEME SUPPORT & IMAGE SIZES
// =====================================================
function cello_child_setup() {
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
add_action( 'after_setup_theme', 'cello_child_setup', 11 );


// =====================================================
// 3. ANNOUNCEMENT BAR CUSTOMISER OPTION
// =====================================================
function cello_customizer( $wp_customize ) {
    // Section
    $wp_customize->add_section( 'gamtech_general', array(
        'title'    => __( 'GamTech Settings', 'woodmart' ),
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
        'default'           => '#f4c430',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'gamtech_accent_color', array(
        'label'   => __( 'Accent Color (Gold)', 'woodmart' ),
        'section' => 'gamtech_general',
    ) ) );
}
add_action( 'customize_register', 'cello_customizer' );


// =====================================================
// 4. DYNAMIC CSS FROM CUSTOMIZER
// =====================================================
function cello_dynamic_css() {
    $primary = get_theme_mod( 'gamtech_primary_color', '#7c3aed' );
    $accent  = get_theme_mod( 'gamtech_accent_color', '#a78bfa' );

    $css = '
    :root {
        --gt-primary:      ' . esc_attr( $primary ) . ';
        --gt-accent:       ' . esc_attr( $accent ) . ';
        --pu:              ' . esc_attr( $primary ) . ';
        --pul:             ' . esc_attr( $accent ) . ';
    }
    ';

    wp_add_inline_style( 'gamtech-unified-css', $css );
}
add_action( 'wp_enqueue_scripts', 'cello_dynamic_css', 20 );

/**
 * Simple hex color darkener (no external lib needed)
 */
function cello_darken_color( $hex, $percent ) {
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
function cello_quick_look_button() {
    global $product;
    echo '<div class="gt-quick-look-btn" data-id="' . esc_attr( $product->get_id() ) . '">'
        . esc_html__( 'Quick Look', 'woodmart' )
        . '</div>';
}

// Hook the quick look button only if Woodmart's own quick view is not active
function cello_add_quick_look_hook() {
    if ( function_exists( 'woodmart_get_opt' ) && ! woodmart_get_opt( 'quick_view' ) ) {
        add_action( 'woocommerce_before_shop_loop_item_title', 'cello_quick_look_button', 15 );
    }
}
add_action( 'wp_loaded', 'cello_add_quick_look_hook' );


// =====================================================
// 6. PRODUCT CARD — SAVE BADGE (with amount)
// =====================================================
function cello_sale_badge_with_amount( $html, $post, $product ) {
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
add_filter( 'woocommerce_sale_flash', 'cello_sale_badge_with_amount', 10, 3 );


// =====================================================
// 7. REMOVE DEFAULT WOOCOMMERCE BREADCRUMBS ON HOMEPAGE
// =====================================================
function cello_remove_woo_breadcrumbs_homepage() {
    if ( is_front_page() ) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
    }
}
add_action( 'template_redirect', 'cello_remove_woo_breadcrumbs_homepage' );


// =====================================================
// 8. CUSTOM BODY CLASSES
// =====================================================
function cello_body_classes( $classes ) {
    $classes[] = 'gamtech-theme';
    $classes[] = 'gs-body';
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
add_filter( 'body_class', 'cello_body_classes' );


// =====================================================
// 9. WISHLIST COUNT IN HEADER (if YITH or similar active)
// =====================================================
function cello_header_wishlist_count() {
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
function cello_products_per_page( $count ) {
    if ( is_product_category() || is_shop() ) {
        return 20;
    }
    return $count;
}
add_filter( 'loop_shop_per_page', 'cello_products_per_page', 20 );


// =====================================================
// 11. SCHEMA / SEO — BUSINESS INFO IN HEAD
// =====================================================
function cello_schema_org() {
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
add_action( 'wp_head', 'cello_schema_org' );


// =====================================================
// 12. FORCE WOODMART CHILD TEMPLATE FOR FRONT PAGE
// =====================================================
function cello_front_page_template( $template ) {
    if ( is_front_page() ) {
        $child_front = get_stylesheet_directory() . '/front-page.php';
        if ( file_exists( $child_front ) ) {
            return $child_front;
        }
    }
    return $template;
}
add_filter( 'template_include', 'cello_front_page_template', 99 );

/**
 * Helper: render star rating HTML from average rating (0-5)
 */
function cello_star_rating( $avg ) {
    $avg  = floatval( $avg );
    $full = floor( $avg );
    $half = ( $avg - $full ) >= 0.5 ? 1 : 0;
    $out  = '<span class="gt-stars" aria-hidden="true">';
    for ( $i = 0; $i < $full; $i++ ) { $out .= '★'; }
    if ( $half ) { $out .= '☆'; }
    for ( $j = $full + $half; $j < 5; $j++ ) { $out .= '☆'; }
    $out .= '</span>';
    return $out;
}

// =====================================================
// 13. SITE NAME FILTER — DISPLAY AS CELLO (frontend only)
// =====================================================
add_filter( 'bloginfo', function( $output, $show ) {
    if ( is_admin() ) {
        return $output;
    }
    if ( $show === 'name' ) {
        return 'GamTech';
    }
    if ( $show === 'description' ) {
        return __( 'Your trusted electronics store. Quality products, fast delivery.', 'woodmart' );
    }
    return $output;
}, 10, 2 );

// =====================================================
// 14. DISABLE WOODMART POPUPS & OVERLAYS
// =====================================================
function cello_disable_woodmart_features() {
    // Disable newsletter popup
    if ( function_exists( 'woodmart_get_opt' ) ) {
        add_filter( 'woodmart_get_opt', function( $value, $key ) {
            $disable_keys = array(
                'promo_popup',
                'newsletter_popup', 
                'signup_popup',
                'cookies_info',
                'scroll_top',
                'back_to_top',
            );
            if ( in_array( $key, $disable_keys ) ) {
                return false;
            }
            return $value;
        }, 99, 2 );
    }
}
add_action( 'wp', 'cello_disable_woodmart_features' );

// Remove WoodMart promo popup action — run late to ensure hooks exist
function cello_remove_woodmart_hooks() {
    remove_action( 'wp_footer', 'woodmart_promo_popup' );
    remove_action( 'wp_footer', 'woodmart_newsletter_popup' );
    remove_action( 'wp_footer', 'woodmart_scroll_top' );
    remove_action( 'woodmart_before_wp_footer', 'woodmart_promo_popup' );
    remove_action( 'woodmart_before_wp_footer', 'woodmart_newsletter_popup' );
    // Also try removing at higher priorities
    remove_action( 'wp_footer', 'woodmart_promo_popup', 99 );
    remove_action( 'wp_footer', 'woodmart_newsletter_popup', 99 );
}
add_action( 'wp_loaded', 'cello_remove_woodmart_hooks', 999 );
add_action( 'template_redirect', 'cello_remove_woodmart_hooks', 999 );

// Critical inline CSS to hide WoodMart elements — bypasses Varnish cache entirely
function cello_critical_hide_css() {
    echo '<style id="cello-critical-hide">
    .woodmart-promo-popup,.wd-promo-popup,.woodmart-newsletter-popup,.wd-newsletter-popup,
    .wd-popup,.xts-popup,.wd-signup-modal,.woodmart-signup-popup,.xts-signup-popup,
    .whb-mobile-bar,.wd-mobile-nav,.woodmart-mobile-bar,.wd-bottom-toolbar,
    .woodmart-scroll-top,.wd-scroll-top,.scroll-to-top,#back-to-top,
    .woodmart-preloader,.wd-preloader,.page-preloader,
    .woodmart-cart-sidebar,.wd-cart-sidebar,.woodmart-side-cart,
    .woodmart-close-side,.wd-buttons,.woodmart-buttons,.wd-overlay,
    .wd-notification,.woodmart-notification,
    .woodmart-toolbar,.woodmart-toolbar-label-show,.scrollToTop,
    a.scrollToTop,.woodmart-toolbar-shop,.woodmart-toolbar-item {
        display:none!important;visibility:hidden!important;opacity:0!important;
        pointer-events:none!important;height:0!important;overflow:hidden!important;
    }
    </style>';
    echo '<script>document.addEventListener("DOMContentLoaded",function(){document.querySelectorAll(".woodmart-promo-popup,.wd-promo-popup,.woodmart-newsletter-popup,.wd-popup,.xts-popup,.wd-signup-modal,.xts-signup-popup,.whb-mobile-bar,.wd-scroll-top,.woodmart-scroll-top").forEach(function(e){e.remove()})});</script>';
}
add_action( 'wp_head', 'cello_critical_hide_css', 1 );

// Disable WoodMart's header builder on our pages
function cello_disable_woodmart_header_builder() {
    // Force our custom header
    remove_action( 'woodmart_header', 'woodmart_header_builder' );
    remove_action( 'woodmart_after_header', 'woodmart_header_bottom_part' );
}
add_action( 'wp', 'cello_disable_woodmart_header_builder', 5 );

