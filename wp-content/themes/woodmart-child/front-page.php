<?php
/**
 * Homepage template — Cello Electronics
 * Replicates the Cello-style electronics store layout
 */
get_header();
?>

<!-- =====================================================
     ANNOUNCEMENT / TOP MARQUEE BAR
     ===================================================== -->
<div class="gt-ticker-strip">
  <div class="container">
    <div class="gt-ticker-inner">
      <span><?php esc_html_e( 'Free Shipping Worldwide When Order Above $500', 'woodmart' ); ?></span>
      <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
      <span><?php esc_html_e( 'Free Shipping Worldwide When Order Above $500', 'woodmart' ); ?></span>
      <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
      <span><?php esc_html_e( 'Free Shipping Worldwide When Order Above $500', 'woodmart' ); ?></span>
      <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    </div>
  </div>
</div>

<!-- =====================================================
     HERO SLIDER
     ===================================================== -->
<section class="gt-hero-section" style="background: linear-gradient(135deg, #0d1457 0%, #1a237e 50%, #283593 100%); padding: 60px 0 40px; position: relative; overflow: hidden;">
  <div class="container">
    <div style="display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 40px; min-height: 320px;">
      <!-- Hero Text -->
      <div style="color: #fff; z-index: 2;">
        <h1 style="font-size: clamp(28px,4vw,46px); font-weight: 900; color: #fff; line-height: 1.15; margin-bottom: 16px;">
          <?php esc_html_e( 'Stay Ahead With Latest Gadgets', 'woodmart' ); ?>
        </h1>
        <p style="font-size: 15px; color: rgba(255,255,255,0.75); margin-bottom: 28px; max-width: 420px;">
          <?php esc_html_e( 'Discover the newest gadgets and electronic games that keep you ahead.', 'woodmart' ); ?>
        </p>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
             style="background:#fff;color:#1a237e;padding:12px 28px;border-radius:6px;font-weight:700;font-size:14px;text-decoration:none;transition:all 0.25s;display:inline-block;"
             onmouseover="this.style.background='#f4c430';this.style.color='#fff'"
             onmouseout="this.style.background='#fff';this.style.color='#1a237e'">
            <?php esc_html_e( 'Buy Now', 'woodmart' ); ?> →
          </a>
          <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"
             style="background:transparent;color:#fff;padding:12px 28px;border-radius:6px;font-weight:700;font-size:14px;text-decoration:none;border:2px solid rgba(255,255,255,0.5);transition:all 0.25s;display:inline-block;"
             onmouseover="this.style.borderColor='#fff'"
             onmouseout="this.style.borderColor='rgba(255,255,255,0.5)'">
            <?php esc_html_e( 'See More', 'woodmart' ); ?> →
          </a>
        </div>
      </div>
      <!-- Hero Image -->
      <div style="text-align: center; z-index: 2;">
        <?php
        $hero_img = get_theme_mod( 'gamtech_hero_image', '' );
        if ( $hero_img ) :
        ?>
          <img src="<?php echo esc_url( $hero_img ); ?>" alt="<?php esc_attr_e( 'Latest Gadgets', 'woodmart' ); ?>"
               style="max-height: 320px; filter: drop-shadow(0 20px 40px rgba(0,0,0,0.4)); animation: gt-float 3s ease-in-out infinite;">
        <?php else : ?>
          <!-- Decorative SVG placeholder headphones shape -->
          <div style="width:280px;height:280px;margin:0 auto;background:rgba(255,255,255,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;">
            <svg width="140" height="140" viewBox="0 0 140 140" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity:0.6;">
              <circle cx="70" cy="70" r="68" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
              <path d="M35 75C35 55.1 51.1 39 71 39s36 16.1 36 36" stroke="#ff6f00" stroke-width="5" fill="none" stroke-linecap="round"/>
              <rect x="24" y="72" width="18" height="28" rx="9" fill="#ff6f00"/>
              <rect x="98" y="72" width="18" height="28" rx="9" fill="#ff6f00"/>
            </svg>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <!-- Decorative background circles -->
  <div style="position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:rgba(255,255,255,0.04);border-radius:50%;pointer-events:none;"></div>
  <div style="position:absolute;bottom:-80px;left:10%;width:200px;height:200px;background:rgba(255,111,0,0.08);border-radius:50%;pointer-events:none;"></div>
</section>

<style>
@keyframes gt-float {
  0%,100% { transform: translateY(0px); }
  50%      { transform: translateY(-12px); }
}
</style>

<!-- =====================================================
     COLLECTION CATEGORIES
     ===================================================== -->
<section style="padding: 48px 0 32px;">
  <div class="container">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h2 class="gt-section-title"><?php esc_html_e( 'Collection', 'woodmart' ); ?></h2>
        <p class="gt-section-sub"><?php esc_html_e( 'Top 10 Most Sold This Week. Next Day Delivery', 'woodmart' ); ?></p>
      </div>
      <div style="display:flex;align-items:center;gap:10px;">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"
           style="font-size:13px;font-weight:600;color:var(--gt-primary);text-decoration:none;border-bottom:1px solid var(--gt-primary);">
          <?php esc_html_e( 'View all collections', 'woodmart' ); ?>
        </a>
      </div>
    </div>

    <!-- Category Grid -->
    <div class="gt-category-grid">
      <?php
      $categories = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => true,
        'number'     => 12,
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
      ?>
        <?php
        // Fallback static categories matching the reference image
        $static_cats = array(
          array( 'name' => 'Earbuds',      'count' => 4 ),
          array( 'name' => 'Hard Devices', 'count' => 8 ),
          array( 'name' => 'Keyboard',     'count' => 2 ),
          array( 'name' => 'Mobile',       'count' => 6 ),
          array( 'name' => 'Printer',      'count' => 3 ),
          array( 'name' => 'Earphone',     'count' => 4 ),
          array( 'name' => 'Headphones',   'count' => 5 ),
          array( 'name' => 'Laptop',       'count' => 5 ),
          array( 'name' => 'Pen Drive',    'count' => 4 ),
          array( 'name' => 'Tablet',       'count' => 4 ),
        );
        foreach ( $static_cats as $cat ) :
        ?>
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gt-category-item">
            <div style="width:52px;height:52px;background:var(--gt-bg-light);border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
              <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--gt-primary)" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
            </div>
            <span class="gt-cat-name"><?php echo esc_html( $cat['name'] ); ?></span>
            <span class="gt-cat-count"><?php echo esc_html( $cat['count'] ); ?> <?php esc_html_e( 'items', 'woodmart' ); ?></span>
          </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     MARQUEE / TICKER STRIP
     ===================================================== -->
<div class="gt-ticker-strip">
  <div class="gt-ticker-inner">
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
    <span><?php esc_html_e( 'Jackpot Deals | Tap to get Flat 50% Off', 'woodmart' ); ?></span>
  </div>
</div>

<!-- =====================================================
     FEATURED COLLECTION
     ===================================================== -->
<section style="padding: 48px 0;">
  <div class="container">
    <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
      <div>
        <h2 class="gt-section-title"><?php esc_html_e( 'Featured Collection', 'woodmart' ); ?></h2>
        <p class="gt-section-sub"><?php esc_html_e( 'Top 10 Most Sold This Week. Next Day Delivery', 'woodmart' ); ?></p>
      </div>
    </div>

    <?php
    $featured_args = array(
      'post_type'      => 'product',
      'posts_per_page' => 5,
      'tax_query'      => array( array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
      ) ),
      'orderby' => 'date',
      'order'   => 'DESC',
    );
    $featured_query = new WP_Query( $featured_args );

    if ( $featured_query->have_posts() ) :
    ?>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
      <?php while ( $featured_query->have_posts() ) : $featured_query->the_post();
        global $product; ?>
        <div class="product-grid-item" style="padding:12px;position:relative;">
          <?php if ( $product->is_on_sale() ) : ?>
            <div style="position:absolute;top:10px;left:10px;z-index:3;background:#e53935;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;text-transform:uppercase;">
              <?php
              $reg   = (float) $product->get_regular_price();
              $sale  = (float) $product->get_sale_price();
              $saved = $reg > 0 ? round( ( $reg - $sale ) / $reg * 100 ) : 0;
              echo 'Save ' . esc_html( get_woocommerce_currency_symbol() ) . esc_html( number_format( $reg - $sale, 0 ) );
              ?>
            </div>
          <?php endif; ?>

          <div style="position:absolute;top:10px;right:10px;z-index:3;background:var(--gt-primary);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;cursor:pointer;"
               onclick="location.href='<?php echo esc_url( get_permalink() ); ?>'">
            <?php esc_html_e( 'Quick Look', 'woodmart' ); ?>
          </div>

          <a href="<?php the_permalink(); ?>" style="display:block;text-align:center;margin-bottom:10px;overflow:hidden;border-radius:6px;">
            <?php the_post_thumbnail( 'woocommerce_thumbnail', array( 'style' => 'width:100%;height:180px;object-fit:contain;transition:transform 0.3s;' ) ); ?>
          </a>

          <div style="padding:0 4px;">
            <p style="font-size:11px;color:var(--gt-text-light);margin-bottom:4px;"><?php bloginfo('name'); ?></p>
            <a href="<?php the_permalink(); ?>" style="font-size:13px;font-weight:600;color:var(--gt-text);text-decoration:none;display:block;margin-bottom:8px;line-height:1.4;">
              <?php the_title(); ?>
            </a>
            <?php woocommerce_template_loop_price(); ?>
            <?php echo wc_get_product_variation_attributes_swatches( $product ); ?>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php else : ?>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:16px;">
      <?php
      $recent_args = array( 'post_type' => 'product', 'posts_per_page' => 5, 'orderby' => 'date', 'order' => 'DESC' );
      $recent_q    = new WP_Query( $recent_args );
      if ( $recent_q->have_posts() ) :
        while ( $recent_q->have_posts() ) : $recent_q->the_post();
          global $product;
      ?>
        <div class="product-grid-item" style="padding:12px;position:relative;">
          <?php if ( $product->is_on_sale() ) : ?>
            <div style="position:absolute;top:10px;left:10px;z-index:3;background:#e53935;color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:4px;">
              <?php $reg = (float)$product->get_regular_price(); $sale = (float)$product->get_sale_price();
              echo 'Save ' . esc_html( get_woocommerce_currency_symbol() ) . esc_html( number_format( $reg - $sale, 0 ) ); ?>
            </div>
          <?php endif; ?>
          <a href="<?php the_permalink(); ?>" style="display:block;text-align:center;margin-bottom:10px;overflow:hidden;border-radius:6px;">
            <?php the_post_thumbnail( 'woocommerce_thumbnail', array( 'style' => 'width:100%;height:180px;object-fit:contain;' ) ); ?>
          </a>
          <div style="padding:0 4px;">
            <a href="<?php the_permalink(); ?>" style="font-size:13px;font-weight:600;color:var(--gt-text);text-decoration:none;display:block;margin-bottom:8px;line-height:1.4;">
              <?php the_title(); ?>
            </a>
            <?php woocommerce_template_loop_price(); ?>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- =====================================================
     DEAL OF THE DAY
     ===================================================== -->
<section class="gt-deal-section">
  <div class="container">
    <div class="gt-deal-header">
      <div>
        <h2><?php esc_html_e( 'Deal Of The Days', 'woodmart' ); ?></h2>
        <p><?php esc_html_e( "Deal Of The Day: Unbelievable Savings Await!", 'woodmart' ); ?></p>
      </div>
      <!-- Countdown timer -->
      <div class="gt-countdown" id="gt-deal-countdown" data-end="<?php echo esc_attr( date( 'Y-m-d', strtotime( '+3 days' ) ) ); ?>">
        <div class="gt-countdown-block">
          <span class="number" id="gt-days">00</span>
          <span class="label"><?php esc_html_e( 'Days', 'woodmart' ); ?></span>
        </div>
        <span style="color:var(--gt-primary);font-weight:800;font-size:22px;align-self:flex-start;margin-top:10px;">:</span>
        <div class="gt-countdown-block">
          <span class="number" id="gt-hours">00</span>
          <span class="label"><?php esc_html_e( 'Hours', 'woodmart' ); ?></span>
        </div>
        <span style="color:var(--gt-primary);font-weight:800;font-size:22px;align-self:flex-start;margin-top:10px;">:</span>
        <div class="gt-countdown-block">
          <span class="number" id="gt-mins">00</span>
          <span class="label"><?php esc_html_e( 'Mins', 'woodmart' ); ?></span>
        </div>
        <span style="color:var(--gt-primary);font-weight:800;font-size:22px;align-self:flex-start;margin-top:10px;">:</span>
        <div class="gt-countdown-block">
          <span class="number" id="gt-secs">00</span>
          <span class="label"><?php esc_html_e( 'Sec', 'woodmart' ); ?></span>
        </div>
      </div>
    </div>

    <!-- Deal products -->
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
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
        // Fallback: just show latest products
        $deal_query = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
      }
      if ( $deal_query->have_posts() ) :
        while ( $deal_query->have_posts() ) : $deal_query->the_post();
          global $product;
      ?>
        <div style="background:#fff;border-radius:8px;padding:14px;display:flex;gap:14px;align-items:center;border:1px solid var(--gt-border);">
          <a href="<?php the_permalink(); ?>" style="flex-shrink:0;width:80px;height:80px;display:block;overflow:hidden;border-radius:6px;">
            <?php the_post_thumbnail( 'thumbnail', array( 'style' => 'width:80px;height:80px;object-fit:contain;' ) ); ?>
          </a>
          <div>
            <p style="font-size:11px;color:var(--gt-text-light);margin-bottom:3px;"><?php bloginfo('name'); ?></p>
            <a href="<?php the_permalink(); ?>" style="font-size:13px;font-weight:600;color:var(--gt-text);text-decoration:none;display:block;margin-bottom:6px;line-height:1.3;">
              <?php the_title(); ?>
            </a>
            <?php woocommerce_template_loop_price(); ?>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); endif; ?>
    </div>
  </div>
</section>

<!-- =====================================================
     3-COLUMN BANNER CARDS
     ===================================================== -->
<section style="padding: 40px 0;">
  <div class="container">
    <div class="gt-banner-grid">

      <!-- Banner 1: Smartphones -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?product_cat=smartphones' ); ?>"
         class="gt-banner-card"
         style="background-image: linear-gradient(135deg, #1a237e, #283593);">
        <div class="gt-banner-card-content">
          <p class="gt-cat-label"><?php esc_html_e( 'Smartphones', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( 'Smartphones Innovation', 'woodmart' ); ?></h3>
          <p class="gt-banner-sub"><?php esc_html_e( 'Next Gen Tech Now', 'woodmart' ); ?></p>
          <span class="gt-shop-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> →</span>
        </div>
      </a>

      <!-- Banner 2: Audio / Earbuds -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?product_cat=earbuds' ); ?>"
         class="gt-banner-card"
         style="background-image: linear-gradient(135deg, #0d1457, #1565c0);">
        <div class="gt-banner-card-content">
          <p class="gt-cat-label"><?php esc_html_e( 'Earbuds', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( 'Audio Freedom', 'woodmart' ); ?></h3>
          <p class="gt-banner-sub"><?php esc_html_e( 'Sound Meets Innovation', 'woodmart' ); ?></p>
          <span class="gt-shop-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> →</span>
        </div>
      </a>

      <!-- Banner 3: Tablets -->
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?product_cat=tablets' ); ?>"
         class="gt-banner-card"
         style="background-image: linear-gradient(135deg, #1a237e, #0d47a1);">
        <div class="gt-banner-card-content">
          <p class="gt-cat-label"><?php esc_html_e( 'Tablets', 'woodmart' ); ?></p>
          <h3><?php esc_html_e( 'Power Of Portable Tablets', 'woodmart' ); ?></h3>
          <p class="gt-banner-sub"><?php esc_html_e( 'Performance On The Go', 'woodmart' ); ?></p>
          <span class="gt-shop-link"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?> →</span>
        </div>
      </a>

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

    var days  = Math.floor( diff / 86400000 );
    var hours = Math.floor( ( diff % 86400000 ) / 3600000 );
    var mins  = Math.floor( ( diff % 3600000 )  / 60000 );
    var secs  = Math.floor( ( diff % 60000 )    / 1000 );

    function pad(n) { return n < 10 ? '0' + n : n; }

    var d = document.getElementById('gt-days');
    var h = document.getElementById('gt-hours');
    var m = document.getElementById('gt-mins');
    var s = document.getElementById('gt-secs');

    if (d) d.textContent = pad(days);
    if (h) h.textContent = pad(hours);
    if (m) m.textContent = pad(mins);
    if (s) s.textContent = pad(secs);
  }

  updateCountdown();
  setInterval( updateCountdown, 1000 );
})();
</script>

<?php get_footer(); ?>
