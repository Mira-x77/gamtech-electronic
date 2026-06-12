<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );

/**
 * Force gamtech logo — overrides Woodmart header builder logo output
 */
function gamtech_force_logo( $html ) {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/gamtech-logo.png';
    return '<img src="' . esc_url( $logo_url ) . '" alt="Gamtech Electronic" style="max-width:180px;height:auto;" />';
}
add_filter( 'woodmart_logo_img', 'gamtech_force_logo' );

/**
 * Also override via output buffer on the site-logo div as fallback
 */
function gamtech_logo_css() {
    $logo_url = get_stylesheet_directory_uri() . '/assets/images/gamtech-logo.png';
    echo '<style>
        .woodmart-logo img,
        .woodmart-main-logo img,
        .woodmart-sticky-logo img {
            content: url("' . esc_url( $logo_url ) . '") !important;
            max-width: 180px !important;
            height: auto !important;
        }
        .site-logo .woodmart-logo-wrap {
            min-width: 180px;
        }
    </style>';
}
add_action( 'wp_head', 'gamtech_logo_css' );