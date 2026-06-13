<?php
/**
 * Ogo-style Header — Gamtech Electronic (child theme)
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'woodmart_after_body_open' ); ?>
<div class="website-wrapper">
    <!-- Top bar -->
    <div class="gt-topbar-wrap gt-topbar-dark">
        <div class="container gt-topbar-inner">
            <div class="gt-topbar-left">
                <span class="gt-topbar-item">📧 support@gamtech.example</span>
                <span class="gt-topbar-sep">|</span>
                <span class="gt-topbar-item"><?php esc_html_e( 'Free Shipping Over $100', 'woodmart' ); ?></span>
            </div>
            <div class="gt-topbar-right">
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'woodmart' ); ?></a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Login', 'woodmart' ); ?></a>
                    <span class="gt-topbar-sep">|</span>
                    <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Register', 'woodmart' ); ?></a>
                <?php endif; ?>
                <span class="gt-topbar-sep">|</span>
                <span class="gt-topbar-item">USD</span>
                <span class="gt-topbar-sep">|</span>
                <span class="gt-topbar-item">EN</span>
            </div>
        </div>
    </div>

    <!-- Main header -->
    <header class="gt-header gt-header-ogo">
        <div class="container">
            <div class="gt-header-inner">
                <div class="gt-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php
                        $logo_id = get_theme_mod( 'custom_logo' );
                        if ( $logo_id ) {
                            echo wp_get_attachment_image( $logo_id, 'full', false, array( 'alt' => get_bloginfo( 'name' ) ) );
                        } else {
                            $logo_url = get_stylesheet_directory_uri() . '/assets/images/gamtech-logo.png';
                            echo '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="max-height:64px;">';
                        }
                        ?>
                    </a>
                </div>

                <div class="gt-search-block">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="gt-search-form">
                        <input type="search" name="s" class="gt-search-input" placeholder="<?php esc_attr_e( 'Search products, brands...', 'woodmart' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="gt-btn gt-btn-primary gt-search-btn"><?php esc_html_e( 'Search', 'woodmart' ); ?></button>
                    </form>
                </div>

                <div class="gt-contact-icons">
                    <div class="gt-contact-number">
                        <small><?php esc_html_e( 'Call us now', 'woodmart' ); ?></small>
                        <a href="tel:+18001234567" class="gt-phone">+1 (800) 123-4567</a>
                    </div>

                    <div class="gt-quick-icons">
                        <a class="gt-icon" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" title="<?php esc_attr_e( 'Compare', 'woodmart' ); ?>">
                            <span class="dashicons dashicons-chart-bar"></span>
                        </a>
                        <a class="gt-icon" href="<?php echo esc_url( function_exists( 'yith_wcwl_wishlist_page_url' ) ? yith_wcwl_wishlist_page_url() : wc_get_page_permalink( 'myaccount' ) ); ?>" title="<?php esc_attr_e( 'Wishlist', 'woodmart' ); ?>">
                            <span class="dashicons dashicons-heart"></span>
                        </a>
                        <a class="gt-icon gt-cart" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'Cart', 'woodmart' ); ?>">
                            <span class="dashicons dashicons-cart"></span>
                            <?php if ( function_exists( 'WC' ) && WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                                <span class="gt-cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                <button class="gt-mobile-menu-toggle" id="gt-menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'woodmart' ); ?>">
                    <span class="dashicons dashicons-menu"></span>
                </button>

            </div>
        </div>
    </header>

    <!-- Navigation bar -->
    <nav class="gt-nav-bar gt-nav-ogo" id="gt-nav-bar">
        <div class="container gt-nav-inner">
            <div class="gt-browse-cats">
                <button class="gt-browse-btn">
                    <span class="dashicons dashicons-menu"></span>
                    <?php esc_html_e( 'Browse categories', 'woodmart' ); ?>
                </button>
                <div class="gt-browse-dropdown">
                    <ul>
                        <?php
                        $cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 20 ) );
                        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) {
                            foreach ( $cats as $cat ) {
                                echo '<li><a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>

            <ul class="gt-main-menu">
                <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'woodmart' ); ?></a></li>
                <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'woodmart' ); ?></a></li>
                <li><a href="#"><?php esc_html_e( 'Features', 'woodmart' ); ?></a></li>
                <li><a href="#"><?php esc_html_e( 'Pages', 'woodmart' ); ?></a></li>
                <li><a href="#"><?php esc_html_e( 'Blog', 'woodmart' ); ?></a></li>
                <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>"><?php esc_html_e( 'About', 'woodmart' ); ?></a></li>
                <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>"><?php esc_html_e( 'Contact', 'woodmart' ); ?></a></li>
            </ul>

            <div class="gt-nav-actions">
                <?php if ( is_user_logged_in() ) : ?>
                    <a class="gt-small-btn" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Account', 'woodmart' ); ?></a>
                <?php else : ?>
                    <a class="gt-small-btn gt-login" href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Login', 'woodmart' ); ?></a>
                    <a class="gt-small-btn gt-register" href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Register', 'woodmart' ); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mobile nav panel -->
        <div class="gt-mobile-nav" id="gt-mobile-nav"></div>
    </nav>

    <?php woodmart_page_top_part(); ?>

    <div class="main-page-wrapper">
