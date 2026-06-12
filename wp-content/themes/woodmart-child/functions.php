<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
    $version = filemtime( get_stylesheet_directory() . '/style.css' );
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), $version );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

/**
 * Force gamtech logo — overrides Woodmart header builder logo output
 */
function gamtech_force_logo( $html ) {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/logo-light.png';
    return '<img src="' . esc_url( $logo_url ) . '" alt="Gamtech Electronic" style="max-width:250px;height:auto;" />';
}
add_filter( 'woodmart_logo_img', 'gamtech_force_logo' );

/**
 * Also override via output buffer on the site-logo div as fallback
 */
function gamtech_logo_css() {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/logo-light.png';
    echo '<style>
        .woodmart-logo img,
        .woodmart-main-logo img,
        .woodmart-sticky-logo img {
            content: url("' . esc_url( $logo_url ) . '") !important;
            max-width: 250px !important;
            height: auto !important;
        }
        .site-logo .woodmart-logo-wrap {
            min-width: 250px;
        }
    </style>';
}
add_action( 'wp_head', 'gamtech_logo_css' );

/**
 * Add Favicon
 */
function gamtech_add_favicon() {
    $favicon_url = get_stylesheet_directory_uri() . '/assets/images/favicon.ico';
    echo '<link rel="icon" href="' . esc_url( $favicon_url ) . '" type="image/x-icon" />';
    echo '<link rel="shortcut icon" href="' . esc_url( $favicon_url ) . '" type="image/x-icon" />';
}
add_action('wp_head', 'gamtech_add_favicon');
add_action('admin_head', 'gamtech_add_favicon');

/**
 * Force custom homepage template for the front page
 */
function gamtech_force_homepage_template( $template ) {
    if ( is_front_page() || is_page(2124) ) {
        $new_template = locate_template( array( 'page-homepage.php' ) );
        if ( !empty( $new_template ) ) {
            return $new_template;
        }
    }
    return $template;
}
add_filter( 'template_include', 'gamtech_force_homepage_template', 99 );