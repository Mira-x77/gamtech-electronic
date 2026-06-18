<?php
/**
 * Template Name: GamTech Store
 * Description: Black/Purple/White tech e-commerce layout matching the NovaShop reference design.
 */

// Suppress WordPress / Woodmart header & footer — we render our own full layout
defined( 'ABSPATH' ) || exit;

// Enqueue our dedicated assets (registered in functions.php)
// Assets are loaded via wp_head() below
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
  <?php wp_head(); ?>
</head>
<body <?php body_class( 'gs-body' ); ?>>
<?php wp_body_open(); ?>

<?php
/* -------------------------------------------------------
   DATA HELPERS
   ------------------------------------------------------- */

/** Fetch WC products safely */
function gs_get_products( $args = array() ) {
  $defaults = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 8,
    'orderby'        => 'date',
    'order'          => 'DESC',
  );
  $query = new WP_Query( array_merge( $defaults, $args ) );
  return $query;
}

/** Render one product card */
function gs_product_card( $product, $badge = '' ) {
  if ( ! $product ) return;
  $id        = $product->get_id();
  $link      = get_permalink( $id );
  $img_id    = $product->get_image_id();
  $img_url   = $img_id
    ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' )
    : wc_placeholder_img_src( 'woocommerce_thumbnail' );
  $name      = $product->get_name();
  $rating    = (float) $product->get_average_rating();
  $reviews   = (int)   $product->get_review_count();
  $price_html= $product->get_price_html();
  $reg       = (float) $product->get_regular_price();
  $sale      = (float) $product->get_sale_price();
  $on_sale   = $product->is_on_sale();
  $in_stock  = $product->is_in_stock();
  $add_url   = $product->is_purchasable() && $in_stock ? esc_url( $product->add_to_cart_url() ) : '#';
  $price_num = (float) $product->get_price();

  // Auto-badge
  if ( ! $badge && $on_sale ) $badge = 'sale';
  ?>
  <div class="gs-product-card gs-fade-in">

    <?php if ( $badge ) : ?>
      <span class="gs-product-badge <?php echo esc_attr( $badge ); ?>">
        <?php echo $badge === 'sale' ? 'Sale' : esc_html( ucfirst( $badge ) ); ?>
      </span>
    <?php endif; ?>

    <!-- Quick actions -->
    <div class="gs-product-actions">
      <button class="gs-product-action-btn" data-action="wish" title="<?php esc_attr_e( 'Wishlist', 'woodmart' ); ?>" aria-label="<?php esc_attr_e( 'Add to wishlist', 'woodmart' ); ?>">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      </button>
      <a href="<?php echo esc_url( $link ); ?>" class="gs-product-action-btn" title="<?php esc_attr_e( 'Quick view', 'woodmart' ); ?>">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
      </a>
    </div>

    <!-- Image -->
    <a href="<?php echo esc_url( $link ); ?>" class="gs-product-img-wrap">
      <img src="<?php echo esc_url( $img_url ); ?>"
           alt="<?php echo esc_attr( $name ); ?>"
           loading="lazy">
    </a>

    <!-- Info -->
    <div class="gs-product-info">
      <span class="gs-product-brand">GamTech</span>
      <a href="<?php echo esc_url( $link ); ?>" class="gs-product-name"><?php echo esc_html( $name ); ?></a>

      <?php if ( $rating > 0 ) : ?>
      <div class="gs-stars">
        <span class="stars"><?php
          $full = floor( $rating );
          $half = ( $rating - $full ) >= 0.5 ? 1 : 0;
          for ( $i = 0; $i < $full; $i++ ) echo '★';
          if ( $half ) echo '★';
          for ( $j = $full + $half; $j < 5; $j++ ) echo '☆';
        ?></span>
        <span class="count">(<?php echo esc_html( $reviews ); ?>)</span>
      </div>
      <?php endif; ?>

      <div class="gs-product-price">
        <?php if ( $on_sale && $reg > 0 ) : ?>
          <span class="gs-price-current">
            <?php echo wp_kses_post( wc_price( $sale ) ); ?>
          </span>
          <span class="gs-price-old"><?php echo wp_kses_post( wc_price( $reg ) ); ?></span>
          <?php
          $saved_pct = round( ( ( $reg - $sale ) / $reg ) * 100 );
          echo '<span class="gs-price-save">-' . esc_html( $saved_pct ) . '%</span>';
          ?>
        <?php else : ?>
          <span class="gs-price-current"><?php echo wp_kses_post( $price_html ); ?></span>
        <?php endif; ?>
      </div>
    </div>

    <!-- Add to cart -->
    <a href="<?php echo esc_url( $add_url ); ?>"
       class="gs-add-cart"
       data-product-id="<?php echo esc_attr( $id ); ?>"
       data-price="<?php echo esc_attr( $price_num ); ?>">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <?php esc_html_e( 'Add to Cart', 'woodmart' ); ?>
    </a>

  </div>
  <?php
}

/* -------------------------------------------------------
   FETCH PRODUCT SETS
   ------------------------------------------------------- */
$featured_q    = gs_get_products( array( 'posts_per_page' => 8, 'orderby' => 'date' ) );
$recommended_q = gs_get_products( array( 'posts_per_page' => 4, 'meta_key' => 'total_sales', 'orderby' => 'meta_value_num', 'order' => 'DESC' ) );

/* WC cart data for demo cart items */
$cart_items    = array();
$cart_subtotal = 0;
if ( function_exists( 'WC' ) && WC()->cart ) {
  foreach ( WC()->cart->get_cart() as $key => $val ) {
    $p = wc_get_product( $val['product_id'] );
    if ( ! $p ) continue;
    $cart_items[] = array(
      'name'  => $p->get_name(),
      'qty'   => $val['quantity'],
      'price' => (float) $p->get_price(),
      'img'   => $p->get_image_id()
        ? wp_get_attachment_image_url( $p->get_image_id(), 'thumbnail' )
        : wc_placeholder_img_src( 'thumbnail' ),
      'sub'   => $p->get_attribute( 'pa_brand' ) ?: 'GamTech',
    );
    $cart_subtotal += (float) $p->get_price() * (int) $val['quantity'];
  }
}
$cart_count    = count( $cart_items );
$cart_discount = 0;
$cart_total    = $cart_subtotal;
?>

<!-- =====================================================
     MOBILE TOP BAR
     ===================================================== -->
<div class="gs-mobile-bar">
  <button class="gs-mobile-toggle" id="gs-sidebar-toggle" aria-label="<?php esc_attr_e( 'Open menu', 'woodmart' ); ?>">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="font-size:16px;font-weight:800;color:#fff;text-decoration:none;">
    Gam<span style="color:#8b5cf6;">Tech</span>
  </a>
  <button class="gs-mobile-toggle" id="gs-cart-toggle" aria-label="<?php esc_attr_e( 'Open cart', 'woodmart' ); ?>">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
  </button>
</div>

<!-- OVERLAY -->
<div id="gs-overlay" class="gs-overlay" aria-hidden="true"></div>

<!-- =====================================================
     PAGE WRAPPER
     ===================================================== -->
<div class="gs-page">

  <!-- ===================================================
       LEFT SIDEBAR
       =================================================== -->
  <aside class="gs-sidebar" role="navigation" aria-label="<?php esc_attr_e( 'Store navigation', 'woodmart' ); ?>">

    <!-- Logo -->
    <div class="gs-sidebar-logo">
      <div class="logo-icon">
        <svg width="20" height="20" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      </div>
      <span class="logo-text">Gam<span>Tech</span></span>
    </div>

    <!-- Main nav -->
    <div class="gs-nav-group">
      <p class="gs-nav-group-label"><?php esc_html_e( 'Main', 'woodmart' ); ?></p>
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="gs-nav-item active">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <?php esc_html_e( 'Home', 'woodmart' ); ?>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <?php esc_html_e( 'Categories', 'woodmart' ); ?>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        <?php esc_html_e( 'Deals', 'woodmart' ); ?>
        <span class="gs-nav-badge"><?php esc_html_e( 'Hot', 'woodmart' ); ?></span>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        <?php esc_html_e( 'New Arrivals', 'woodmart' ); ?>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=popularity" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <?php esc_html_e( 'Best Sellers', 'woodmart' ); ?>
      </a>
    </div>

    <!-- Account nav -->
    <div class="gs-nav-group">
      <p class="gs-nav-group-label"><?php esc_html_e( 'Account', 'woodmart' ); ?></p>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?php esc_html_e( 'My Orders', 'woodmart' ); ?>
        <?php if ( function_exists( 'wc_get_orders' ) ) :
          $orders = wc_get_orders( array( 'customer' => get_current_user_id(), 'limit' => 1, 'status' => 'processing' ) );
          if ( $orders ) : ?>
            <span class="gs-nav-badge purple"><?php echo esc_html( count( $orders ) ); ?></span>
          <?php endif;
        endif; ?>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <?php esc_html_e( 'Wishlist', 'woodmart' ); ?>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="gs-nav-item">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <?php esc_html_e( 'Account', 'woodmart' ); ?>
      </a>
    </div>

    <!-- Promo card -->
    <div class="gs-sidebar-promo">
      <p class="promo-tag"><?php esc_html_e( 'Special Offer', 'woodmart' ); ?></p>
      <h4><?php esc_html_e( 'Tech Sale', 'woodmart' ); ?><br><?php esc_html_e( 'Up to 60% Off', 'woodmart' ); ?></h4>
      <p><?php esc_html_e( 'Limited-time deals on top gadgets', 'woodmart' ); ?></p>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="promo-btn">
        <?php esc_html_e( 'Shop Now', 'woodmart' ); ?>
      </a>
    </div>

    <!-- Support -->
    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>" class="gs-sidebar-support">
      <span class="support-dot gs-pulse"></span>
      <div>
        <strong style="display:block;font-size:12px;color:var(--gs-text);"><?php esc_html_e( 'Need Help?', 'woodmart' ); ?></strong>
        <span><?php esc_html_e( '24/7 Support Center', 'woodmart' ); ?></span>
      </div>
    </a>

  </aside><!-- /sidebar -->


  <!-- ===================================================
       TOP HEADER
       =================================================== -->
  <header class="gs-header" role="banner">

    <!-- Search -->
    <div class="gs-search-wrap">
      <input type="search"
             name="s"
             placeholder="<?php esc_attr_e( 'Search for products, brands and more...', 'woodmart' ); ?>"
             aria-label="<?php esc_attr_e( 'Search products', 'woodmart' ); ?>"
             value="<?php echo esc_attr( get_search_query() ); ?>"
             form="gs-search-form">
      <button class="gs-search-btn" form="gs-search-form" aria-label="<?php esc_attr_e( 'Search', 'woodmart' ); ?>">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
      <form id="gs-search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display:none;"></form>
    </div>

    <!-- Icons -->
    <div class="gs-header-icons">
      <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"
         class="gs-icon-btn" title="<?php esc_attr_e( 'Wishlist', 'woodmart' ); ?>" aria-label="<?php esc_attr_e( 'Wishlist', 'woodmart' ); ?>">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        <span class="badge">0</span>
      </a>
      <button class="gs-icon-btn" id="gs-notif-btn" title="<?php esc_attr_e( 'Notifications', 'woodmart' ); ?>" aria-label="<?php esc_attr_e( 'Notifications', 'woodmart' ); ?>">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
        <span class="badge">3</span>
      </button>
      <button class="gs-icon-btn" id="gs-cart-toggle" aria-label="<?php esc_attr_e( 'Open cart', 'woodmart' ); ?>">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="badge" id="gs-cart-header-badge"><?php echo esc_html( $cart_count ); ?></span>
      </button>
      <div class="gs-user-avatar" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'User menu', 'woodmart' ); ?>">
        <div class="avatar-img">
          <?php echo esc_html( strtoupper( substr( wp_get_current_user()->display_name ?: 'G', 0, 1 ) ) ); ?>
        </div>
        <span class="avatar-name">
          <?php echo esc_html( wp_get_current_user()->display_name ?: __( 'Guest', 'woodmart' ) ); ?>
        </span>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </div>

  </header><!-- /header -->

  <!-- ===================================================
       MAIN CONTENT
       =================================================== -->
  <main class="gs-content" id="gs-content">

    <!-- HERO BANNER -->
    <section class="gs-hero" aria-label="<?php esc_attr_e( 'Hero promotion', 'woodmart' ); ?>">
      <div>
        <div class="gs-hero-tag">
          <span class="dot"></span>
          <?php esc_html_e( 'New Collection 2026', 'woodmart' ); ?>
        </div>
        <h1>
          <?php esc_html_e( 'Upgrade Your', 'woodmart' ); ?><br>
          <span><?php esc_html_e( 'Tech Setup ✦', 'woodmart' ); ?></span>
        </h1>
        <p><?php esc_html_e( 'Explore the latest laptops, gaming gear, smart devices, and accessories — built for performance-driven people.', 'woodmart' ); ?></p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-hero-cta">
          <?php esc_html_e( 'Shop Now', 'woodmart' ); ?>
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
      <div class="gs-hero-image-wrap">
        <?php
        $hero_img = get_theme_mod( 'gamtech_hero_image', '' );
        if ( $hero_img ) :
        ?>
          <img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'Hero product', 'woodmart' ); ?>">
        <?php else : ?>
          <div class="gs-hero-placeholder">
            <svg width="90" height="90" fill="none" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" viewBox="0 0 24 24">
              <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
              <line x1="8" y1="21" x2="16" y2="21"/>
              <line x1="12" y1="17" x2="12" y2="21"/>
            </svg>
          </div>
        <?php endif; ?>
      </div>
    </section><!-- /hero -->

    <!-- CATEGORY SHORTCUTS -->
    <div class="gs-categories" role="list" aria-label="<?php esc_attr_e( 'Product categories', 'woodmart' ); ?>">
      <?php
      $cats_svg = array(
        'Laptops'      => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
        'Phones'       => '<rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>',
        'Headphones'   => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
        'Gaming'       => '<line x1="6" y1="11" x2="10" y2="11"/><line x1="8" y1="9" x2="8" y2="13"/><line x1="15" y1="12" x2="15.01" y2="12"/><line x1="17" y1="10" x2="17.01" y2="10"/><path d="M6 3h12l4 8-2 8H4L2 11z"/>',
        'Smart Watch'  => '<circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.83a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.83 1h4.35a2 2 0 0 1 2 1.82l.35 3.83"/>',
        'Cameras'      => '<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
        'Networking'   => '<rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'More'         => '<circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>',
      );

      $wc_cats = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 7,
        'parent'     => 0,
        'orderby'    => 'count',
        'order'      => 'DESC',
        'exclude'    => array( absint( get_option( 'default_product_cat' ) ) ),
      ) );

      if ( ! empty( $wc_cats ) && ! is_wp_error( $wc_cats ) ) :
        foreach ( $wc_cats as $cat ) :
          $cat_url   = get_term_link( $cat );
          $thumb_id  = get_term_meta( $cat->term_id, 'thumbnail_id', true );
          // Pick a matching SVG icon from our list or use generic
          $cat_svg   = isset( $cats_svg[ $cat->name ] ) ? $cats_svg[ $cat->name ] : $cats_svg['More'];
      ?>
        <a href="<?php echo esc_url( $cat_url ); ?>" class="gs-cat-item" role="listitem">
          <div class="gs-cat-icon">
            <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <?php echo $cat_svg; ?>
            </svg>
          </div>
          <span class="gs-cat-label"><?php echo esc_html( $cat->name ); ?></span>
        </a>
      <?php endforeach;
      else :
        foreach ( $cats_svg as $label => $path ) : ?>
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-cat-item" role="listitem">
            <div class="gs-cat-icon">
              <svg width="26" height="26" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?php echo $path; ?></svg>
            </div>
            <span class="gs-cat-label"><?php echo esc_html( $label ); ?></span>
          </a>
        <?php endforeach;
      endif; ?>
    </div><!-- /categories -->

    <!-- PROMO CARDS -->
    <div class="gs-promo-grid">
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="gs-promo-card">
        <div class="gs-promo-icon red">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
        </div>
        <div class="gs-promo-body">
          <h4><?php esc_html_e( 'Flash Sale', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( 'Limited time deals. Up to 70% Off', 'woodmart' ); ?></p>
          <span class="promo-cta"><?php esc_html_e( 'Shop now', 'woodmart' ); ?> →</span>
        </div>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-promo-card">
        <div class="gs-promo-icon green">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="gs-promo-body">
          <h4><?php esc_html_e( 'Free Shipping', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( 'On orders over $350', 'woodmart' ); ?></p>
          <span class="promo-cta"><?php esc_html_e( 'Shop now', 'woodmart' ); ?> →</span>
        </div>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date" class="gs-promo-card">
        <div class="gs-promo-icon purple">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="gs-promo-body">
          <h4><?php esc_html_e( 'New Arrivals', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( 'Check out the latest trends', 'woodmart' ); ?></p>
          <span class="promo-cta"><?php esc_html_e( 'Shop now', 'woodmart' ); ?> →</span>
        </div>
      </a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="gs-promo-card">
        <div class="gs-promo-icon yellow">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <div class="gs-promo-body">
          <h4><?php esc_html_e( 'Limited Offers', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( 'Exclusive member discounts', 'woodmart' ); ?></p>
          <span class="promo-cta"><?php esc_html_e( 'Shop now', 'woodmart' ); ?> →</span>
        </div>
      </a>
    </div><!-- /promo grid -->

    <!-- BEST DEALS PRODUCT GRID -->
    <section aria-label="<?php esc_attr_e( 'Best deals for you', 'woodmart' ); ?>">
      <div class="gs-section-header">
        <div>
          <h2 class="gs-section-title"><?php esc_html_e( 'Best Deals for You', 'woodmart' ); ?></h2>
          <p class="gs-section-sub"><?php esc_html_e( 'Hand-picked products at unbeatable prices', 'woodmart' ); ?></p>
        </div>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="gs-view-all">
          <?php esc_html_e( 'View All', 'woodmart' ); ?>
        </a>
      </div>

      <div class="gs-product-grid">
        <?php
        $deals_q = gs_get_products( array(
          'posts_per_page' => 8,
          'meta_query'     => array( array(
            'key'     => '_sale_price',
            'value'   => '',
            'compare' => '!=',
          ) ),
          'orderby' => 'rand',
        ) );

        if ( $deals_q->have_posts() ) :
          while ( $deals_q->have_posts() ) :
            $deals_q->the_post();
            global $product;
            gs_product_card( $product, 'sale' );
          endwhile;
          wp_reset_postdata();
        elseif ( $featured_q->have_posts() ) :
          // Fallback to recent products
          while ( $featured_q->have_posts() ) :
            $featured_q->the_post();
            global $product;
            gs_product_card( $product );
          endwhile;
          wp_reset_postdata();
        else :
          // Static placeholder cards when store has no products
          for ( $p = 1; $p <= 4; $p++ ) :
        ?>
          <div class="gs-product-card">
            <span class="gs-product-badge sale">Sale</span>
            <div class="gs-product-img-wrap" style="background:var(--gs-bg3);aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;">
              <svg width="48" height="48" fill="none" stroke="var(--gs-border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div class="gs-product-info">
              <span class="gs-product-brand">GamTech</span>
              <span class="gs-product-name"><?php echo esc_html( 'Sample Product ' . $p ); ?></span>
              <div class="gs-stars"><span class="stars">★★★★☆</span><span class="count">(0)</span></div>
              <div class="gs-product-price">
                <span class="gs-price-current">$0.00</span>
              </div>
            </div>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-add-cart">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              <?php esc_html_e( 'Add to Cart', 'woodmart' ); ?>
            </a>
          </div>
        <?php endfor; endif; ?>
      </div>
    </section><!-- /deals -->

    <!-- RECOMMENDED FOR YOU -->
    <section aria-label="<?php esc_attr_e( 'Recommended for you', 'woodmart' ); ?>">
      <div class="gs-section-header">
        <div>
          <h2 class="gs-section-title"><?php esc_html_e( 'Recommended for You', 'woodmart' ); ?></h2>
          <p class="gs-section-sub"><?php esc_html_e( 'Based on popular picks', 'woodmart' ); ?></p>
        </div>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=popularity" class="gs-view-all">
          <?php esc_html_e( 'View All', 'woodmart' ); ?>
        </a>
      </div>

      <div class="gs-rec-grid">
        <?php
        if ( $recommended_q->have_posts() ) :
          while ( $recommended_q->have_posts() ) :
            $recommended_q->the_post();
            global $product;
            gs_product_card( $product );
          endwhile;
          wp_reset_postdata();
        else :
          for ( $p = 1; $p <= 4; $p++ ) :
        ?>
          <div class="gs-product-card">
            <div class="gs-product-img-wrap" style="background:var(--gs-bg3);aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;">
              <svg width="48" height="48" fill="none" stroke="var(--gs-border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>
            </div>
            <div class="gs-product-info">
              <span class="gs-product-brand">GamTech</span>
              <span class="gs-product-name"><?php echo esc_html( 'Top Pick ' . $p ); ?></span>
              <div class="gs-stars"><span class="stars">★★★★★</span><span class="count">(0)</span></div>
              <div class="gs-product-price"><span class="gs-price-current">$0.00</span></div>
            </div>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-add-cart">
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
              <?php esc_html_e( 'Add to Cart', 'woodmart' ); ?>
            </a>
          </div>
        <?php endfor; endif; ?>
      </div>
    </section><!-- /recommended -->

  </main><!-- /content -->

  <!-- ===================================================
       FOOTER FEATURES BAR
       =================================================== -->
  <footer class="gs-footer-bar" aria-label="<?php esc_attr_e( 'Store features', 'woodmart' ); ?>">
    <div class="gs-footer-features">
      <div class="gs-feature-card">
        <div class="gs-feature-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div class="gs-feature-body">
          <h4><?php esc_html_e( 'Secure Payment', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( '100% secure payment', 'woodmart' ); ?></p>
        </div>
      </div>
      <div class="gs-feature-card">
        <div class="gs-feature-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
        </div>
        <div class="gs-feature-body">
          <h4><?php esc_html_e( 'Easy Returns', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( '30-day return policy', 'woodmart' ); ?></p>
        </div>
      </div>
      <div class="gs-feature-card">
        <div class="gs-feature-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div class="gs-feature-body">
          <h4><?php esc_html_e( '24/7 Support', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( 'Dedicated support team', 'woodmart' ); ?></p>
        </div>
      </div>
      <div class="gs-feature-card">
        <div class="gs-feature-icon">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div class="gs-feature-body">
          <h4><?php esc_html_e( 'Trusted by Customers', 'woodmart' ); ?></h4>
          <p><?php esc_html_e( '4.8 average rating', 'woodmart' ); ?></p>
        </div>
      </div>
    </div>
  </footer>


  <!-- ===================================================
       RIGHT CART PANEL
       =================================================== -->
  <aside class="gs-cart" role="complementary" aria-label="<?php esc_attr_e( 'Shopping cart', 'woodmart' ); ?>">

    <div class="gs-cart-header">
      <h3>
        <?php esc_html_e( 'My Cart', 'woodmart' ); ?>
        <span class="gs-cart-count-badge"><?php echo esc_html( $cart_count ); ?></span>
      </h3>
      <button class="gs-cart-close" id="gs-cart-close" aria-label="<?php esc_attr_e( 'Close cart', 'woodmart' ); ?>">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="gs-cart-items">
      <?php if ( ! empty( $cart_items ) ) :
        foreach ( $cart_items as $ci ) : ?>
        <div class="gs-cart-item">
          <img class="gs-cart-item-img"
               src="<?php echo esc_url( $ci['img'] ); ?>"
               alt="<?php echo esc_attr( $ci['name'] ); ?>"
               loading="lazy">
          <div class="gs-cart-item-info">
            <div class="gs-cart-item-name"><?php echo esc_html( $ci['name'] ); ?></div>
            <div class="gs-cart-item-sub"><?php echo esc_html( $ci['sub'] ); ?></div>
            <div class="gs-cart-item-price" data-price="<?php echo esc_attr( $ci['price'] ); ?>">
              <?php echo wp_kses_post( wc_price( $ci['price'] ) ); ?>
            </div>
            <div class="gs-qty">
              <button class="gs-qty-btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease', 'woodmart' ); ?>">−</button>
              <span class="gs-qty-num"><?php echo esc_html( $ci['qty'] ); ?></span>
              <button class="gs-qty-btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase', 'woodmart' ); ?>">+</button>
            </div>
          </div>
          <button class="gs-cart-del" aria-label="<?php esc_attr_e( 'Remove item', 'woodmart' ); ?>">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </button>
        </div>
      <?php endforeach;
      else : ?>
        <!-- Demo items when cart is empty -->
        <div class="gs-cart-item">
          <div class="gs-cart-item-img" style="background:var(--gs-bg3);display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:8px;">
            <svg width="22" height="22" fill="none" stroke="var(--gs-border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
          </div>
          <div class="gs-cart-item-info">
            <div class="gs-cart-item-name"><?php esc_html_e( 'Wireless Headphones Pro', 'woodmart' ); ?></div>
            <div class="gs-cart-item-sub">GamTech Audio</div>
            <div class="gs-cart-item-price" data-price="129.99">$129.99</div>
            <div class="gs-qty">
              <button class="gs-qty-btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease', 'woodmart' ); ?>">−</button>
              <span class="gs-qty-num">1</span>
              <button class="gs-qty-btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase', 'woodmart' ); ?>">+</button>
            </div>
          </div>
          <button class="gs-cart-del" aria-label="<?php esc_attr_e( 'Remove', 'woodmart' ); ?>">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </button>
        </div>
        <div class="gs-cart-item">
          <div class="gs-cart-item-img" style="background:var(--gs-bg3);display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:8px;">
            <svg width="22" height="22" fill="none" stroke="var(--gs-border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/></svg>
          </div>
          <div class="gs-cart-item-info">
            <div class="gs-cart-item-name"><?php esc_html_e( 'Mechanical Keyboard RGB', 'woodmart' ); ?></div>
            <div class="gs-cart-item-sub">GamTech Gaming</div>
            <div class="gs-cart-item-price" data-price="89.99">$89.99</div>
            <div class="gs-qty">
              <button class="gs-qty-btn" data-action="minus" aria-label="<?php esc_attr_e( 'Decrease', 'woodmart' ); ?>">−</button>
              <span class="gs-qty-num">2</span>
              <button class="gs-qty-btn" data-action="plus" aria-label="<?php esc_attr_e( 'Increase', 'woodmart' ); ?>">+</button>
            </div>
          </div>
          <button class="gs-cart-del" aria-label="<?php esc_attr_e( 'Remove', 'woodmart' ); ?>">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </button>
        </div>
      <?php endif; ?>
    </div><!-- /cart-items -->

    <!-- Promo code -->
    <div class="gs-cart-promo">
      <div class="gs-cart-promo-row">
        <input type="text"
               id="gs-promo-input"
               placeholder="<?php esc_attr_e( 'Promo Code', 'woodmart' ); ?>"
               aria-label="<?php esc_attr_e( 'Enter promo code', 'woodmart' ); ?>">
        <button id="gs-promo-apply"><?php esc_html_e( 'Apply', 'woodmart' ); ?></button>
      </div>
      <p id="gs-promo-msg" style="font-size:11px;margin-top:6px;"></p>
    </div>

    <!-- Summary -->
    <div class="gs-cart-summary">
      <div class="gs-summary-row">
        <span class="label"><?php esc_html_e( 'Subtotal', 'woodmart' ); ?></span>
        <span class="value" id="gs-subtotal">
          <?php echo wp_kses_post( wc_price( ! empty( $cart_items ) ? $cart_subtotal : 309.97 ) ); ?>
        </span>
      </div>
      <div class="gs-summary-row discount">
        <span class="label"><?php esc_html_e( 'Discount', 'woodmart' ); ?></span>
        <span class="value" id="gs-discount">$0.00</span>
      </div>
      <div class="gs-summary-row shipping">
        <span class="label"><?php esc_html_e( 'Shipping', 'woodmart' ); ?></span>
        <span class="value"><?php esc_html_e( 'Free', 'woodmart' ); ?></span>
      </div>
      <div class="gs-summary-row total">
        <span class="label"><?php esc_html_e( 'Total', 'woodmart' ); ?></span>
        <span class="value" id="gs-total">
          <?php echo wp_kses_post( wc_price( ! empty( $cart_items ) ? $cart_total : 309.97 ) ); ?>
        </span>
      </div>

      <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="gs-checkout-btn" id="gs-checkout-btn">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        <?php esc_html_e( 'Checkout', 'woodmart' ); ?>
        <span class="btn-count">(<?php echo esc_html( max( $cart_count, 2 ) ); ?>)</span>
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>

      <div class="gs-payment-icons">
        <span>VISA</span>
        <span>MC</span>
        <span>PayPal</span>
        <span>Apple Pay</span>
      </div>
    </div><!-- /summary -->

    <!-- Suggestions -->
    <div class="gs-cart-suggestions">
      <h4><?php esc_html_e( 'You might also like', 'woodmart' ); ?></h4>
      <?php
      $sugg_q = gs_get_products( array( 'posts_per_page' => 3, 'orderby' => 'rand' ) );
      if ( $sugg_q->have_posts() ) :
        while ( $sugg_q->have_posts() ) :
          $sugg_q->the_post();
          global $product;
          $s_img = $product->get_image_id()
            ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
            : wc_placeholder_img_src( 'thumbnail' );
          ?>
        <div class="gs-suggestion-item">
          <img class="gs-suggestion-img" src="<?php echo esc_url( $s_img ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
          <span class="gs-suggestion-name"><?php echo esc_html( $product->get_name() ); ?></span>
          <span class="gs-suggestion-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
          <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="gs-suggestion-add" aria-label="<?php esc_attr_e( 'Add to cart', 'woodmart' ); ?>">+</a>
        </div>
        <?php endwhile;
        wp_reset_postdata();
      else :
        // Static fallback suggestions
        $static_sugg = array(
          array( 'name' => 'USB-C Hub 7-in-1',      'price' => '$49.99' ),
          array( 'name' => 'Webcam 4K Ultra HD',     'price' => '$79.99' ),
          array( 'name' => 'NVMe SSD 1TB',           'price' => '$109.99' ),
        );
        foreach ( $static_sugg as $s ) : ?>
        <div class="gs-suggestion-item">
          <div class="gs-suggestion-img" style="background:var(--gs-bg3);display:flex;align-items:center;justify-content:center;">
            <svg width="20" height="20" fill="none" stroke="var(--gs-border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg>
          </div>
          <span class="gs-suggestion-name"><?php echo esc_html( $s['name'] ); ?></span>
          <span class="gs-suggestion-price"><?php echo esc_html( $s['price'] ); ?></span>
          <button class="gs-suggestion-add" aria-label="<?php esc_attr_e( 'Add to cart', 'woodmart' ); ?>">+</button>
        </div>
      <?php endforeach; endif; ?>
    </div><!-- /suggestions -->

  </aside><!-- /cart -->

</div><!-- /gs-page -->

<?php wp_footer(); ?>
</body>
</html>
