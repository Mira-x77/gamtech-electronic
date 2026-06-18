<?php
/**
 * Header — GamTech dark layout (sidebar + top bar + main open)
 */
defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/gamtech-core.php';

$store_cats = gamtech_store_categories();
$cart_data  = gamtech_get_cart_data();
$wc_count   = $cart_data['count'];
$wc_total   = $cart_data['total'];
$wc_items   = $cart_data['items'];
$user       = wp_get_current_user();
$contact    = get_permalink( get_page_by_path( 'contact' ) );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'gs-body' ); ?>>
<?php wp_body_open(); ?>

<div class="gs-page">

<div id="gs-ov" class="gs-overlay"></div>

<div class="gs-mob-bar">
  <button class="gs-mob-btn" id="gs-sb-tog" aria-label="<?php esc_attr_e( 'Menu', 'woodmart' ); ?>">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gs-mob-logo">Gam<span>Tech</span></a>
  <div class="gs-mob-cart-wrap">
    <button class="gs-mob-btn" id="gs-ct-tog-mob" aria-label="<?php esc_attr_e( 'Cart', 'woodmart' ); ?>">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
    </button>
    <span class="gs-mob-cart-bdg" id="gs-badge-mob"><?php echo esc_html( $wc_count ); ?></span>
  </div>
</div>

<aside class="gs-sb" id="gs-sb">
  <div class="gs-logo">
    <div class="gs-logo-ico">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    </div>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gs-logo-txt">Gam<span>Tech</span></a>
  </div>

  <div class="gs-nav-sec">
    <p class="gs-nav-lbl"><?php esc_html_e( 'Menu', 'woodmart' ); ?></p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gs-nav-a<?php echo esc_attr( gamtech_nav_class( 'home' ) ); ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
      <?php esc_html_e( 'Home', 'woodmart' ); ?>
    </a>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-nav-a<?php echo esc_attr( gamtech_nav_class( 'shop' ) ); ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
      <?php esc_html_e( 'Shop All', 'woodmart' ); ?>
    </a>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?orderby=date' ); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
      <?php esc_html_e( 'New Arrivals', 'woodmart' ); ?>
    </a>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?orderby=popularity' ); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
      <?php esc_html_e( 'Best Sellers', 'woodmart' ); ?>
    </a>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      <?php esc_html_e( 'Deals', 'woodmart' ); ?>
    </a>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gs-nav-a<?php echo esc_attr( gamtech_nav_class( 'account' ) ); ?>">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <?php esc_html_e( 'My Account', 'woodmart' ); ?>
    </a>
  </div>

  <div class="gs-sb-cats">
    <div class="gs-sb-cats-title open" id="gs-cats-tog">
      <span><?php esc_html_e( 'Categories', 'woodmart' ); ?></span>
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <div class="gs-sb-cats-list" id="gs-cats-list">
      <?php foreach ( $store_cats as $cname => $cico ) :
        $cat_class = gamtech_cat_active( $cname ) ? ' gs-sb-cat-active' : '';
        ?>
        <a href="<?php echo esc_url( gamtech_category_url( $cname ) ); ?>" class="gs-sb-cat<?php echo esc_attr( $cat_class ); ?>">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?php echo $cico; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
          <?php echo esc_html( $cname ); ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="gs-sb-promo">
    <p class="sptag"><?php esc_html_e( 'Special Offer', 'woodmart' ); ?></p>
    <h4><?php esc_html_e( 'Tech Sale', 'woodmart' ); ?><br><?php esc_html_e( 'Up to 60% Off', 'woodmart' ); ?></h4>
    <p><?php esc_html_e( 'Limited-time deals on top gadgets', 'woodmart' ); ?></p>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?></a>
  </div>

  <?php if ( $contact ) : ?>
  <a href="<?php echo esc_url( $contact ); ?>" class="gs-sb-support">
    <span class="sdot"></span>
    <div><strong><?php esc_html_e( 'Need Help?', 'woodmart' ); ?></strong><?php esc_html_e( '24/7 Support Center', 'woodmart' ); ?></div>
  </a>
  <?php endif; ?>
</aside>

<div class="gs-center">
  <header class="gs-hd">
    <div class="gs-search">
      <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search products, brands...', 'woodmart' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="gs-search-btn" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </form>
    </div>
    <div class="gs-hd-icons">
      <button class="gs-hd-btn" id="gs-ct-tog" aria-label="<?php esc_attr_e( 'Cart', 'woodmart' ); ?>">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="bdg" id="gs-badge-hd"><?php echo esc_html( $wc_count ); ?></span>
      </button>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gs-avatar">
        <div class="av-img"><?php echo esc_html( strtoupper( substr( $user->display_name ? $user->display_name : 'G', 0, 1 ) ) ); ?></div>
        <span class="av-nm"><?php echo esc_html( $user->display_name ? $user->display_name : __( 'Guest', 'woodmart' ) ); ?></span>
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </a>
    </div>
  </header>

  <main class="gs-mn">
