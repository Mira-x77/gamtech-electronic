<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( function_exists( 'wp_body_open' ) ) { wp_body_open(); } ?>

<header class="gt-header">

  <!-- Topbar -->
  <div class="gt-topbar">
    <div class="gt-container">
      <span class="gt-topbar-text">Free Shipping on Orders Over $50 &mdash; Shop Now &amp; Save Big!</span>
      <button class="gt-topbar-close" aria-label="Dismiss promotion">×</button>
    </div>
  </div>

  <!-- Main Header Row -->
  <div class="gt-main-header">
    <div class="gt-container">
      <div class="gt-main-header__inner">

        <!-- Logo -->
        <a class="gt-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
          <?php echo esc_html( get_bloginfo( 'name' ) ); ?>
        </a>

        <!-- Search Form -->
        <form class="gt-search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
          <input type="search" name="s" placeholder="<?php echo esc_attr__( 'Search for products…', 'woodmart-child' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" aria-label="<?php echo esc_attr__( 'Search', 'woodmart-child' ); ?>">
          <button type="submit" class="gt-search-btn" aria-label="<?php echo esc_attr__( 'Submit search', 'woodmart-child' ); ?>">Search</button>
        </form>

        <!-- Phone -->
        <div class="gt-header-phone">
          <span>Call Us:</span>
          <strong>+1 (800) 123-4567</strong>
        </div>

        <!-- Header Actions -->
        <div class="gt-header-actions">

          <!-- Compare -->
          <a class="gt-header-icon" href="<?php echo esc_url( home_url( '/compare/' ) ); ?>" title="Compare">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
            <span>Compare</span>
          </a>

          <!-- Wishlist -->
          <a class="gt-header-icon" href="<?php echo esc_url( home_url( '/wishlist/' ) ); ?>" title="Wishlist">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            <span>Wishlist</span>
          </a>

          <!-- Cart -->
          <a class="gt-header-icon" href="<?php echo esc_url( gamtech_woo_active() ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>" title="Cart">
            <span style="position:relative;">
              <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              <?php if ( gamtech_woo_active() && WC()->cart ) : ?>
                <span class="gt-cart-badge"><?php echo esc_html( WC()->cart->get_cart_contents_count() ); ?></span>
              <?php else : ?>
                <span class="gt-cart-badge">0</span>
              <?php endif; ?>
            </span>
            <span>Cart</span>
          </a>

          <!-- My Account -->
          <a class="gt-header-icon" href="<?php echo esc_url( gamtech_woo_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>" title="My Account">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span>Account</span>
          </a>

        </div><!-- /.gt-header-actions -->
      </div><!-- /.gt-main-header__inner -->
    </div><!-- /.gt-container -->
  </div><!-- /.gt-main-header -->

  <!-- Navbar -->
  <nav class="gt-navbar" aria-label="Primary navigation">
    <div class="gt-container">
      <div class="gt-navbar__inner">

        <!-- Browse Categories Button -->
        <button class="gt-browse-btn" aria-expanded="false" aria-controls="gt-category-dropdown">
          <span class="gt-browse-icon" aria-hidden="true">
            <span></span><span></span><span></span>
          </span>
          Browse Categories
        </button>

        <!-- Primary Navigation Menu -->
        <?php
        wp_nav_menu( array(
          'theme_location' => 'primary',
          'menu_class'     => 'menu',
          'container'      => 'div',
          'container_class'=> 'gt-nav-menu',
          'fallback_cb'    => 'wp_page_menu',
          'depth'          => 2,
        ) );
        ?>

        <!-- Login / Register -->
        <div class="gt-nav-auth">
          <a href="<?php echo esc_url( gamtech_woo_active() ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>">Login</a>
          <span>/</span>
          <a href="<?php echo esc_url( gamtech_woo_active() ? wc_get_page_permalink( 'myaccount' ) : ( function_exists( 'wp_registration_url' ) ? wp_registration_url() : wp_login_url() ) ); ?>">Register</a>
        </div>

      </div><!-- /.gt-navbar__inner -->
    </div><!-- /.gt-container -->
  </nav><!-- /.gt-navbar -->

</header><!-- /.gt-header -->

<div class="main-page-wrapper">
