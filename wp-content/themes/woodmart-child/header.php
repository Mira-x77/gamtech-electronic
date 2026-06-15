<?php
/**
 * Header template — Gamtech Electronics
 * Full-width, clean electronics marketplace header
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php do_action( 'woodmart_after_body_open' ); ?>

<div class="website-wrapper">

    <!-- ======================================
         ANNOUNCEMENT BAR
         ====================================== -->
    <div class="gt-topbar-wrap">
        <div class="container">
            <div class="gt-topbar-inner">
                <div class="gt-topbar-left">
                    <?php
                    $announcements = array(
                        __( '🚚 Free Shipping Worldwide When Order Above $500', 'woodmart' ),
                        __( '⚡ New Arrivals — Check out the latest gadgets', 'woodmart' ),
                        __( '🎁 Flat 50% Off On Selected Electronics', 'woodmart' ),
                    );
                    echo '<span class="gt-topbar-text">' . esc_html( $announcements[ array_rand( $announcements ) ] ) . '</span>';
                    ?>
                </div>
                <div class="gt-topbar-right">
                    <?php if ( is_user_logged_in() ) : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                            <?php esc_html_e( 'My Account', 'woodmart' ); ?>
                        </a>
                    <?php else : ?>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
                            <?php esc_html_e( 'Sign In', 'woodmart' ); ?>
                        </a>
                        <span style="margin: 0 6px; opacity: 0.5;">|</span>
                        <a href="<?php echo esc_url( wp_registration_url() ); ?>">
                            <?php esc_html_e( 'Register', 'woodmart' ); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ======================================
         MAIN HEADER
         ====================================== -->
    <header class="gt-header">
        <div class="container">
            <div class="gt-header-inner">

                <!-- Logo -->
                <div class="gt-logo">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <?php
                        $logo_url = get_stylesheet_directory_uri() . '/assets/images/gamtech-logo.png';
                        ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>"
                             alt="Gamtech"
                             style="max-height:80px;width:auto;"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                        <span style="display:none;font-size:28px;font-weight:900;color:#e74c3c;">
                            GAMTECH
                        </span>
                    </a>
                </div>

                <!-- Search -->
                <div class="gt-search-wrap">
                    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="gt-search-form">
                        <input type="search"
                               name="s"
                               class="gt-search-input"
                               placeholder="<?php esc_attr_e( 'Search for products, brands and more...', 'woodmart' ); ?>"
                               value="<?php echo esc_attr( get_search_query() ); ?>">
                        <input type="hidden" name="post_type" value="product">
                        <button type="submit" class="gt-search-btn" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <?php esc_html_e( 'Search', 'woodmart' ); ?>
                        </button>
                    </form>
                </div>

                <!-- Header Icons -->
                <div class="gt-header-icons">
                    <!-- Account -->
                    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gt-icon-btn" title="<?php esc_attr_e( 'My Account', 'woodmart' ); ?>">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="gt-icon-label"><?php esc_html_e( 'Account', 'woodmart' ); ?></span>
                    </a>

                    <!-- Wishlist -->
                    <a href="<?php echo esc_url( function_exists( 'yith_wcwl_wishlist_page_url' ) ? yith_wcwl_wishlist_page_url() : wc_get_page_permalink( 'myaccount' ) ); ?>"
                       class="gt-icon-btn" title="<?php esc_attr_e( 'Wishlist', 'woodmart' ); ?>">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <span class="gt-icon-label"><?php esc_html_e( 'Wishlist', 'woodmart' ); ?></span>
                    </a>

                    <!-- Cart -->
                    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="gt-icon-btn gt-cart-btn" title="<?php esc_attr_e( 'Cart', 'woodmart' ); ?>">
                        <div style="position:relative;display:inline-block;">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                            </svg>
                            <?php if ( function_exists( 'WC' ) && WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) : ?>
                                <span class="gt-cart-count"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
                            <?php endif; ?>
                        </div>
                        <span class="gt-icon-label">
                            <?php esc_html_e( 'Cart', 'woodmart' ); ?>
                            <?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
                                <strong><?php echo wp_kses_post( WC()->cart->get_cart_subtotal() ); ?></strong>
                            <?php endif; ?>
                        </span>
                    </a>
                </div>

                <!-- Mobile hamburger -->
                <button class="gt-mobile-menu-toggle" id="gt-menu-toggle" aria-label="<?php esc_attr_e( 'Menu', 'woodmart' ); ?>">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <line x1="3" y1="6" x2="21" y2="6"/>
                        <line x1="3" y1="12" x2="21" y2="12"/>
                        <line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>

            </div>
        </div>
    </header>

    <!-- ======================================
         NAVIGATION BAR
         ====================================== -->
    <nav class="gt-nav-bar" id="gt-nav-bar">
        <div class="container">
            <div class="gt-nav-inner">
                <ul class="gt-nav-menu" id="gt-nav-menu">
                    <li class="gt-nav-item">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gt-nav-link">
                            <?php esc_html_e( 'Home', 'woodmart' ); ?>
                        </a>
                    </li>
                    <li class="gt-nav-item">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gt-nav-link">
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="margin-right:6px;">
                                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                            </svg>
                            <?php esc_html_e( 'Shop All', 'woodmart' ); ?>
                        </a>
                    </li>
                    <?php
                    $nav_cats = get_terms( array(
                        'taxonomy'   => 'product_cat',
                        'hide_empty' => true,
                        'parent'     => 0,
                        'number'     => 6,
                        'exclude'    => array( get_option( 'default_product_cat' ) ),
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                    ) );
                    if ( ! empty( $nav_cats ) && ! is_wp_error( $nav_cats ) ) :
                        foreach ( $nav_cats as $cat ) :
                    ?>
                        <li class="gt-nav-item">
                            <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="gt-nav-link">
                                <?php echo esc_html( $cat->name ); ?>
                            </a>
                        </li>
                    <?php
                        endforeach;
                    else :
                        $fallback_nav = array(
                            'Electronics' => wc_get_page_permalink( 'shop' ),
                            'Offers'      => wc_get_page_permalink( 'shop' ) . '?on_sale=1',
                            'Contact'     => get_permalink( get_page_by_path( 'contact' ) ),
                        );
                        foreach ( $fallback_nav as $label => $url ) :
                    ?>
                        <li class="gt-nav-item">
                            <a href="<?php echo esc_url( $url ); ?>" class="gt-nav-link">
                                <?php echo esc_html( $label ); ?>
                            </a>
                        </li>
                    <?php
                        endforeach;
                    endif;
                    ?>
                    <li class="gt-nav-item">
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>" class="gt-nav-link" style="color: #cc0c39 !important; font-weight: 700 !important;">
                            🔥 <?php esc_html_e( 'Deals', 'woodmart' ); ?>
                        </a>
                    </li>
                </ul>

                <ul class="gt-nav-secondary">
                    <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>" class="gt-nav-link"><?php esc_html_e( 'About Us', 'woodmart' ); ?></a></li>
                    <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="gt-nav-link"><?php esc_html_e( 'Contact', 'woodmart' ); ?></a></li>
                </ul>
            </div>
        </div>

        <!-- Mobile menu dropdown -->
        <div class="gt-mobile-nav" id="gt-mobile-nav">
            <div class="container">
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'woodmart' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'woodmart' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>"><?php esc_html_e( 'Deals', 'woodmart' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Cart', 'woodmart' ); ?></a></li>
                    <li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'woodmart' ); ?></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <?php if ( function_exists( 'woodmart_page_top_part' ) ) woodmart_page_top_part(); ?>

    <!-- Page content begins -->
    <div class="main-page-wrapper">
