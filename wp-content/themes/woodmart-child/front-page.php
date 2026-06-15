<?php
/**
 * Homepage template — Cello Electronics
 * Full-width, Amazon-style electronics store layout
 */
get_header();

/**
 * Helper: render a product card (Amazon-style)
 */
function cello_render_product_card( $product ) {
    if ( ! $product ) return;
    $permalink  = get_permalink( $product->get_id() );
    $image_url  = wp_get_attachment_url( $product->get_image_id() );
    if ( ! $image_url ) $image_url = wc_placeholder_img_src( 'woocommerce_thumbnail' );
    $title      = $product->get_name();
    $price_html = $product->get_price_html();
    $avg_rating = $product->get_average_rating();
    $review_cnt = $product->get_review_count();
    $is_on_sale = $product->is_on_sale();
    $brand      = '';
    // Try to get brand from pa_brand attribute
    $brand_attr = $product->get_attribute( 'pa_brand' );
    if ( $brand_attr ) $brand = $brand_attr;

    echo '<div class="cello-product-card">';

    // Sale tag
    if ( $is_on_sale ) {
        $reg   = (float) $product->get_regular_price();
        $sale  = (float) $product->get_sale_price();
        $saved = $reg > 0 ? $reg - $sale : 0;
        if ( $saved > 0 ) {
            echo '<span class="cello-sale-tag">Save ' . esc_html( get_woocommerce_currency_symbol() . number_format( $saved, 0 ) ) . '</span>';
        } else {
            echo '<span class="cello-sale-tag">Sale</span>';
        }
    }

    // Add to cart button
    if ( $product->is_purchasable() && $product->is_in_stock() ) {
        echo '<a href="' . esc_url( $product->add_to_cart_url() ) . '" class="cello-add-cart-btn" title="' . esc_attr__( 'Add to Cart', 'woodmart' ) . '">+</a>';
    }

    // Product image
    echo '<a href="' . esc_url( $permalink ) . '">';
    echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $title ) . '" class="cello-product-image" loading="lazy">';
    echo '</a>';

    // Product info
    echo '<div class="cello-product-info">';
    if ( $brand ) {
        echo '<span class="cello-product-brand">' . esc_html( $brand ) . '</span>';
    }
    echo '<h3 class="cello-product-title"><a href="' . esc_url( $permalink ) . '">' . esc_html( $title ) . '</a></h3>';

    // Stars
    if ( $avg_rating > 0 ) {
        echo '<div>';
        echo '<span class="cello-stars">';
        $full = floor( $avg_rating );
        $half = ( $avg_rating - $full ) >= 0.5 ? 1 : 0;
        for ( $i = 0; $i < $full; $i++ ) echo '&#9733;';
        if ( $half ) echo '&#9733;';
        for ( $j = $full + $half; $j < 5; $j++ ) echo '&#9734;';
        echo '</span>';
        echo '<span class="cello-stars-count">(' . esc_html( $review_cnt ) . ')</span>';
        echo '</div>';
    }

    // Price
    echo '<div class="cello-price">' . $price_html . '</div>';
    echo '</div>';

    echo '</div>';
}
?>

<!-- =====================================================
     ANNOUNCEMENT TICKER
     ===================================================== -->
<div class="gt-ticker-strip">
  <div class="gt-ticker-inner">
    <span><?php esc_html_e( 'Free Shipping Worldwide On Orders Above $500', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Flat 50% Off On Selected Electronics', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Free Shipping Worldwide On Orders Above $500', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Flat 50% Off On Selected Electronics', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Free Shipping Worldwide On Orders Above $500', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Flat 50% Off On Selected Electronics', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Free Shipping Worldwide On Orders Above $500', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Flat 50% Off On Selected Electronics', 'woodmart' ); ?></span>
  </div>
</div>

<!-- =====================================================
     HERO SECTION — FULL WIDTH
     ===================================================== -->
<section class="gt-hero-section" style="background: linear-gradient(135deg, #0d1457 0%, #1a237e 40%, #283593 100%); padding: 0;">
  <div class="container">
    <div class="gt-hero-grid">
      <!-- Left: Text content -->
      <div style="color: #fff; z-index: 2; padding: 40px 0;">
        <p style="font-size: 13px; text-transform: uppercase; letter-spacing: 2px; color: rgba(255,255,255,0.7); margin-bottom: 12px; font-weight: 600;">
          <?php esc_html_e( 'New Collection 2026', 'woodmart' ); ?>
        </p>
        <h1 style="font-size: clamp(32px, 4.5vw, 52px); font-weight: 900; color: #fff; line-height: 1.1; margin: 0 0 16px;">
          <?php esc_html_e( 'Stay Ahead With Latest Gadgets', 'woodmart' ); ?>
        </h1>
        <p style="font-size: 16px; color: rgba(255,255,255,0.75); margin-bottom: 32px; max-width: 440px; line-height: 1.7;">
          <?php esc_html_e( 'Discover the newest electronics, smartphones, and tech gadgets that keep you connected and ahead of the curve.', 'woodmart' ); ?>
        </p>
        <div style="display: flex; gap: 14px; flex-wrap: wrap;">
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
             style="background: #f4c430; color: #1a237e; padding: 14px 32px; border-radius: 8px; font-weight: 800; font-size: 15px; text-decoration: none; transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px;">
            <?php esc_html_e( 'Shop Now', 'woodmart' ); ?>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1"
             style="background: transparent; color: #fff; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 15px; text-decoration: none; border: 2px solid rgba(255,255,255,0.4); transition: all 0.25s; display: inline-flex; align-items: center; gap: 8px;">
            <?php esc_html_e( 'View Deals', 'woodmart' ); ?>
          </a>
        </div>
      </div>
      <!-- Right: Hero Image -->
      <div style="text-align: center; z-index: 2; padding: 40px 0;">
        <?php
        $hero_img = get_theme_mod( 'gamtech_hero_image', '' );
        if ( $hero_img ) :
        ?>
          <img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'Latest Gadgets', 'woodmart' ); ?>"
               style="max-height: 360px; max-width: 100%; filter: drop-shadow(0 20px 60px rgba(0,0,0,0.5)); animation: gt-float 3s ease-in-out infinite;">
        <?php else : ?>
          <div style="width: 320px; height: 320px; margin: 0 auto; background: rgba(255,255,255,0.06); border-radius: 50%; display: flex; align-items: center; justify-content: center; position: relative;">
            <div style="position: absolute; inset: -20px; border: 2px dashed rgba(255,255,255,0.1); border-radius: 50%; animation: spin 20s linear infinite;"></div>
            <svg width="160" height="160" viewBox="0 0 160 160" fill="none" style="opacity: 0.7;">
              <circle cx="80" cy="80" r="78" stroke="rgba(255,255,255,0.2)" stroke-width="2"/>
              <path d="M40 85C40 62 60 42 83 42s43 20 43 43" stroke="#ff6f00" stroke-width="6" fill="none" stroke-linecap="round"/>
              <rect x="28" y="82" width="22" height="32" rx="11" fill="#ff6f00"/>
              <rect x="110" y="82" width="22" height="32" rx="11" fill="#ff6f00"/>
              <circle cx="80" cy="110" r="6" fill="rgba(255,255,255,0.3)"/>
            </svg>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- Decorative elements -->
  <div style="position:absolute;top:-80px;right:-80px;width:400px;height:400px;background:rgba(255,255,255,0.03);border-radius:50%;pointer-events:none;"></div>
  <div style="position:absolute;bottom:-60px;left:5%;width:250px;height:250px;background:rgba(255,111,0,0.06);border-radius:50%;pointer-events:none;"></div>
</section>

<style>
@keyframes gt-float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-14px); }
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>

<!-- =====================================================
     TRUST BADGES ROW
     ===================================================== -->
<section style="padding: 0;">
  <div class="container">
    <div class="gt-trust-badges" style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;padding:24px 0;border-bottom:1px solid #e8eaed;">
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:160px;">
        <div style="width:44px;height:44px;background:#f0f2f5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="22" height="22" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div>
          <h4 style="font-size:13px;font-weight:700;margin:0 0 2px;color:#1a1a2e;">Free Shipping</h4>
          <p style="font-size:11px;color:#80868b;margin:0;">On orders above $500</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:160px;">
        <div style="width:44px;height:44px;background:#f0f2f5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="22" height="22" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div>
          <h4 style="font-size:13px;font-weight:700;margin:0 0 2px;color:#1a1a2e;">Support 24/7</h4>
          <p style="font-size:11px;color:#80868b;margin:0;">Always available</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:160px;">
        <div style="width:44px;height:44px;background:#f0f2f5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="22" height="22" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
        </div>
        <div>
          <h4 style="font-size:13px;font-weight:700;margin:0 0 2px;color:#1a1a2e;">100% Money Back</h4>
          <p style="font-size:11px;color:#80868b;margin:0;">Guaranteed refund</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:160px;">
        <div style="width:44px;height:44px;background:#f0f2f5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="22" height="22" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <h4 style="font-size:13px;font-weight:700;margin:0 0 2px;color:#1a1a2e;">90 Days Return</h4>
          <p style="font-size:11px;color:#80868b;margin:0;">Easy returns policy</p>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:160px;">
        <div style="width:44px;height:44px;background:#f0f2f5;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <svg width="22" height="22" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <div>
          <h4 style="font-size:13px;font-weight:700;margin:0 0 2px;color:#1a1a2e;">Secure Payment</h4>
          <p style="font-size:11px;color:#80868b;margin:0;">100% secure checkout</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =====================================================
     TOP CATEGORIES OF THE MONTH
     ===================================================== -->
<section style="padding: 40px 0 16px;">
  <div class="container">
    <div class="cello-section-header">
      <div>
        <h2 class="cello-section-title"><?php esc_html_e( 'Top Categories Of The Month', 'woodmart' ); ?></h2>
        <p class="cello-section-sub"><?php esc_html_e( 'Browse our most popular product categories', 'woodmart' ); ?></p>
      </div>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="cello-view-all">
        <?php esc_html_e( 'View All Categories', 'woodmart' ); ?>
      </a>
    </div>

    <div class="gt-category-grid">
      <?php
      $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 8,
        'parent'     => 0,
        'exclude'    => array( get_option( 'default_product_cat' ) ),
        'orderby'    => 'count',
        'order'      => 'DESC',
      ) );

      if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) :
        foreach ( $categories as $category ) :
          $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
          $image_url    = $thumbnail_id
            ? wp_get_attachment_image_url( $thumbnail_id, 'thumbnail' )
            : wc_placeholder_img_src( 'thumbnail' );
          $cat_url      = get_term_link( $category );
      ?>
        <a href="<?php echo esc_url( $cat_url ); ?>" class="gt-category-item">
          <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>">
          <span class="gt-cat-name"><?php echo esc_html( $category->name ); ?></span>
          <span class="gt-cat-count"><?php echo esc_html( $category->count ); ?> <?php esc_html_e( 'items', 'woodmart' ); ?></span>
        </a>
      <?php
        endforeach;
      else :
        // Fallback static categories
        $static_cats = array(
          array( 'name' => 'Smartphones',   'icon' => '&#128241;' ),
          array( 'name' => 'Laptops',       'icon' => '&#128187;' ),
          array( 'name' => 'Headphones',    'icon' => '&#127911;' ),
          array( 'name' => 'Tablets',       'icon' => '&#128195;' ),
          array( 'name' => 'Smart Watches', 'icon' => '&#8986;' ),
          array( 'name' => 'Cameras',       'icon' => '&#128247;' ),
          array( 'name' => 'Gaming',        'icon' => '&#127918;' ),
          array( 'name' => 'Audio',         'icon' => '&#127925;' ),
        );
        foreach ( $static_cats as $cat ) :
      ?>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gt-category-item">
          <div style="width:56px;height:56px;background:#f0f2f5;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;font-size:24px;">
            <?php echo $cat['icon']; ?>
          </div>
          <span class="gt-cat-name"><?php echo esc_html( $cat['name'] ); ?></span>
        </a>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     3-COLUMN PROMO BANNERS
     ===================================================== -->
<section style="padding: 24px 0 40px;">
  <div class="container">
    <div class="cello-banner-grid">
      <!-- Banner 1: Smartphones -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?product_cat=smartphones"
         class="cello-banner-card"
         style="background-image: linear-gradient(135deg, #1a237e, #283593);">
        <div class="cello-banner-content">
          <p class="banner-cat"><?php esc_html_e( 'Smartphones', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( 'Super UHD Display', 'woodmart' ); ?></h3>
          <p class="banner-sub"><?php esc_html_e( 'Nano Cell Technology', 'woodmart' ); ?></p>
          <div class="banner-price">$94.99 <del>$129</del></div>
          <span class="shop-now-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> &rarr;</span>
        </div>
      </a>

      <!-- Banner 2: Laptops -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?product_cat=laptops"
         class="cello-banner-card"
         style="background-image: linear-gradient(135deg, #0d1457, #1565c0);">
        <div class="cello-banner-content">
          <p class="banner-cat"><?php esc_html_e( 'Laptops', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( 'Macbook Pro — Genius Touch', 'woodmart' ); ?></h3>
          <p class="banner-sub"><?php esc_html_e( 'Performance Redefined', 'woodmart' ); ?></p>
          <div class="banner-price">$94.99 <del>$129</del></div>
          <span class="shop-now-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> &rarr;</span>
        </div>
      </a>

      <!-- Banner 3: Cameras -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?product_cat=cameras"
         class="cello-banner-card"
         style="background-image: linear-gradient(135deg, #1a237e, #0d47a1);">
        <div class="cello-banner-content">
          <p class="banner-cat"><?php esc_html_e( 'Cameras', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( '720P WIFI Camera', 'woodmart' ); ?></h3>
          <p class="banner-sub"><?php esc_html_e( 'Smart Home Security', 'woodmart' ); ?></p>
          <div class="banner-price"><?php esc_html_e( 'Starting at $295', 'woodmart' ); ?></div>
          <span class="shop-now-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> &rarr;</span>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- =====================================================
     NEW ARRIVALS
     ===================================================== -->
<section style="padding: 32px 0 40px;">
  <div class="container">
    <div class="cello-section-header">
      <div>
        <h2 class="cello-section-title"><?php esc_html_e( 'New Arrivals', 'woodmart' ); ?></h2>
        <p class="cello-section-sub"><?php esc_html_e( 'Latest products added to our store', 'woodmart' ); ?></p>
      </div>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date" class="cello-view-all">
        <?php esc_html_e( 'View All', 'woodmart' ); ?>
      </a>
    </div>

    <div class="cello-product-grid">
      <?php
      $new_args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
      );
      $new_query = new WP_Query( $new_args );

      if ( $new_query->have_posts() ) :
        while ( $new_query->have_posts() ) : $new_query->the_post();
          global $product;
          cello_render_product_card( $product );
        endwhile;
        wp_reset_postdata();
      else :
        // Show placeholder cards when no products
        for ( $i = 1; $i <= 5; $i++ ) :
      ?>
        <div class="cello-product-card">
          <div style="width:100%;height:200px;background:#f0f2f5;display:flex;align-items:center;justify-content:center;">
            <svg width="48" height="48" fill="none" stroke="#dadce0" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg>
          </div>
          <div class="cello-product-info">
            <span class="cello-product-brand">Cello</span>
            <h3 class="cello-product-title"><?php echo esc_html( 'Sample Product ' . $i ); ?></h3>
            <div class="cello-price">$0.00</div>
          </div>
        </div>
      <?php endfor; endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     DEAL OF THE DAY
     ===================================================== -->
<section class="cello-deal-section">
  <div class="container">
    <div class="cello-deal-header">
      <div>
        <h2><?php esc_html_e( 'Deal Of The Day', 'woodmart' ); ?></h2>
        <p><?php esc_html_e( 'Unbelievable savings — limited time only!', 'woodmart' ); ?></p>
      </div>
      <div class="cello-countdown" id="cello-countdown">
        <div class="cello-countdown-block">
          <span class="number" id="cd-days">00</span>
          <span class="label"><?php esc_html_e( 'Days', 'woodmart' ); ?></span>
        </div>
        <span class="cello-countdown-sep">:</span>
        <div class="cello-countdown-block">
          <span class="number" id="cd-hours">00</span>
          <span class="label"><?php esc_html_e( 'Hours', 'woodmart' ); ?></span>
        </div>
        <span class="cello-countdown-sep">:</span>
        <div class="cello-countdown-block">
          <span class="number" id="cd-mins">00</span>
          <span class="label"><?php esc_html_e( 'Mins', 'woodmart' ); ?></span>
        </div>
        <span class="cello-countdown-sep">:</span>
        <div class="cello-countdown-block">
          <span class="number" id="cd-secs">00</span>
          <span class="label"><?php esc_html_e( 'Sec', 'woodmart' ); ?></span>
        </div>
      </div>
    </div>

    <div class="cello-product-grid" style="grid-template-columns: repeat(4, 1fr) !important;">
      <?php
      $deal_args = array(
        'post_type'      => 'product',
        'posts_per_page' => 4,
        'meta_query'     => array( array(
          'key'     => '_sale_price',
          'value'   => '',
          'compare' => '!=',
        ) ),
        'orderby' => 'rand',
      );
      $deal_query = new WP_Query( $deal_args );
      if ( ! $deal_query->have_posts() ) {
        $deal_query = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
      }
      if ( $deal_query->have_posts() ) :
        while ( $deal_query->have_posts() ) : $deal_query->the_post();
          global $product;
          cello_render_product_card( $product );
        endwhile;
        wp_reset_postdata();
      else :
        for ( $i = 1; $i <= 4; $i++ ) :
      ?>
        <div class="cello-product-card">
          <span class="cello-sale-tag">Sale</span>
          <div style="width:100%;height:200px;background:#f0f2f5;display:flex;align-items:center;justify-content:center;">
            <svg width="48" height="48" fill="none" stroke="#dadce0" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg>
          </div>
          <div class="cello-product-info">
            <h3 class="cello-product-title"><?php echo esc_html( 'Deal Product ' . $i ); ?></h3>
            <div class="cello-price cello-price-sale">$0.00</div>
          </div>
        </div>
      <?php endfor; endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     BEST SELLERS
     ===================================================== -->
<section style="padding: 40px 0 48px;">
  <div class="container">
    <div class="cello-section-header">
      <div>
        <h2 class="cello-section-title"><?php esc_html_e( 'Best Sellers', 'woodmart' ); ?></h2>
        <p class="cello-section-sub"><?php esc_html_e( 'Most popular products this month', 'woodmart' ); ?></p>
      </div>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=popularity" class="cello-view-all">
        <?php esc_html_e( 'View All', 'woodmart' ); ?>
      </a>
    </div>

    <div class="cello-product-grid">
      <?php
      $best_args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'meta_key'       => 'total_sales',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
      );
      $best_query = new WP_Query( $best_args );

      if ( $best_query->have_posts() ) :
        while ( $best_query->have_posts() ) : $best_query->the_post();
          global $product;
          cello_render_product_card( $product );
        endwhile;
        wp_reset_postdata();
      else :
        for ( $i = 1; $i <= 5; $i++ ) :
      ?>
        <div class="cello-product-card">
          <div style="width:100%;height:200px;background:#f0f2f5;display:flex;align-items:center;justify-content:center;">
            <svg width="48" height="48" fill="none" stroke="#dadce0" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 0 0-8 0v2"/></svg>
          </div>
          <div class="cello-product-info">
            <span class="cello-product-brand">Cello</span>
            <h3 class="cello-product-title"><?php echo esc_html( 'Best Seller ' . $i ); ?></h3>
            <div>
              <span class="cello-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</span>
              <span class="cello-stars-count">(0)</span>
            </div>
            <div class="cello-price">$0.00</div>
          </div>
        </div>
      <?php endfor; endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     COUNTDOWN JS
     ===================================================== -->
<script>
(function() {
  var endDate = new Date();
  endDate.setDate( endDate.getDate() + 3 );
  endDate.setHours( 23, 59, 59, 0 );

  function updateCountdown() {
    var now  = new Date();
    var diff = endDate - now;
    if ( diff <= 0 ) return;
    var d = Math.floor( diff / 86400000 );
    var h = Math.floor( ( diff % 86400000 ) / 3600000 );
    var m = Math.floor( ( diff % 3600000 )  / 60000 );
    var s = Math.floor( ( diff % 60000 )    / 1000 );
    function pad(n) { return n < 10 ? '0' + n : n; }
    var el = { d: 'cd-days', h: 'cd-hours', m: 'cd-mins', s: 'cd-secs' };
    if ( document.getElementById( el.d ) ) document.getElementById( el.d ).textContent = pad(d);
    if ( document.getElementById( el.h ) ) document.getElementById( el.h ).textContent = pad(h);
    if ( document.getElementById( el.m ) ) document.getElementById( el.m ).textContent = pad(m);
    if ( document.getElementById( el.s ) ) document.getElementById( el.s ).textContent = pad(s);
  }
  updateCountdown();
  setInterval( updateCountdown, 1000 );
})();
</script>

<?php get_footer(); ?>
