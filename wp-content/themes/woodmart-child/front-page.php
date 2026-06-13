<?php
/**
 * Ogo-style Homepage — Gamtech Electronic (child theme)
 * Pulls live WooCommerce data and uses vanilla JS for interactions
 */
get_header();
?>

<section class="gt-hero-ogo">
  <div class="gt-hero-slider" id="gt-hero-slider">
    <?php
    // Featured products for hero slides (top 3 newest products)
    $hero_q = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 3, 'orderby' => 'date', 'order' => 'DESC' ) );
    if ( $hero_q->have_posts() ) : $i = 0;
      while ( $hero_q->have_posts() ) : $hero_q->the_post(); global $product; $i++; ?>
      <div class="gt-hero-slide<?php echo $i === 1 ? ' active' : ''; ?>">
        <div class="container gt-hero-inner">
          <div class="gt-hero-text">
            <h1><?php the_title(); ?></h1>
            <p><?php echo wp_trim_words( get_the_excerpt(), 24 ); ?></p>
            <div class="gt-hero-ctas">
              <a href="<?php the_permalink(); ?>" class="gt-btn gt-btn-outline"><?php esc_html_e( 'View product', 'woodmart' ); ?></a>
              <a href="<?php echo esc_url( add_query_arg( array( 'add-to-cart' => $product->get_id() ), home_url() ) ); ?>" class="gt-btn gt-btn-primary"><?php esc_html_e( 'Add to cart', 'woodmart' ); ?></a>
            </div>
          </div>
          <div class="gt-hero-media">
            <?php the_post_thumbnail( 'gamtech-hero', array( 'class' => 'gt-hero-img' ) ); ?>
            <?php if ( $product->is_on_sale() ) : ?>
              <span class="gt-save-badge"><?php echo sprintf( __( 'Save %s', 'woodmart' ), wc_price( $product->get_regular_price() - $product->get_sale_price() ) ); ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endwhile; wp_reset_postdata(); endif; ?>

    <button class="gt-hero-prev" aria-label="Prev">‹</button>
    <button class="gt-hero-next" aria-label="Next">›</button>
    <div class="gt-hero-dots" id="gt-hero-dots"></div>
  </div>
</section>

<section class="gt-trust-badges">
  <div class="container">
    <div class="gt-badges-grid">
      <div class="badge"><strong>Free Shipping</strong><span><?php esc_html_e( 'On orders over $100', 'woodmart' ); ?></span></div>
      <div class="badge"><strong>Support 24/7</strong><span><?php esc_html_e( 'Live chat & phone', 'woodmart' ); ?></span></div>
      <div class="badge"><strong>100% Money Back</strong><span><?php esc_html_e( '30 days guarantee', 'woodmart' ); ?></span></div>
      <div class="badge"><strong>90 Days Return</strong><span><?php esc_html_e( 'No questions asked', 'woodmart' ); ?></span></div>
      <div class="badge"><strong>Payment Secure</strong><span><?php esc_html_e( 'SSL & PCI DSS', 'woodmart' ); ?></span></div>
    </div>
  </div>
</section>

<section class="gt-top-categories">
  <div class="container">
    <h2 class="gt-section-title"><?php esc_html_e( 'Top Categories of the Month', 'woodmart' ); ?></h2>
    <div class="gt-cat-scroll" id="gt-cat-scroll">
      <?php
      $cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 12, 'orderby' => 'count', 'order' => 'DESC' ) );
      if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
        foreach ( $cats as $c ) :
          $thumb = get_term_meta( $c->term_id, 'thumbnail_id', true );
          $img = $thumb ? wp_get_attachment_image_url( $thumb, 'thumbnail' ) : wc_placeholder_img_src();
          ?>
          <a class="gt-cat-pill" href="<?php echo esc_url( get_term_link( $c ) ); ?>">
            <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $c->name ); ?>">
            <span><?php echo esc_html( $c->name ); ?></span>
          </a>
        <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<section class="gt-promos">
  <div class="container gt-promo-grid">
    <a class="promo" href="#">
      <div class="promo-inner">
        <h3>Super UHD TV</h3>
        <p>Up to 40% off</p>
        <span class="gt-shop-now"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?></span>
      </div>
    </a>
    <a class="promo" href="#">
      <div class="promo-inner">
        <h3>MacBook Pro</h3>
        <p>Latest M series</p>
        <span class="gt-shop-now"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?></span>
      </div>
    </a>
    <a class="promo" href="#">
      <div class="promo-inner">
        <h3>Reolink Camera</h3>
        <p>Smart home security</p>
        <span class="gt-shop-now"><?php esc_html_e( 'Shop Now', 'woodmart' ); ?></span>
      </div>
    </a>
  </div>
</section>

<section class="gt-tabs-products">
  <div class="container">
    <div class="gt-tabs-head">
      <button class="tab-btn active" data-tab="new-arrivals"><?php esc_html_e( 'New Arrivals', 'woodmart' ); ?></button>
      <button class="tab-btn" data-tab="featured"><?php esc_html_e( 'Featured', 'woodmart' ); ?></button>
      <button class="tab-btn" data-tab="on-sale"><?php esc_html_e( 'On Sale', 'woodmart' ); ?></button>
    </div>

    <div class="gt-tab-panels">
      <div id="new-arrivals" class="tab-panel active">
        <div class="gt-products-grid">
          <?php
          $na = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );
          if ( $na->have_posts() ) : while ( $na->have_posts() ) : $na->the_post(); global $product; ?>
            <div class="product-card">
              <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'woocommerce_thumbnail' ); ?></a>
              <div class="prod-info">
                <a class="prod-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <div class="prod-meta">
                  <span class="cat"><?php echo wc_get_product_category_list( $product->get_id(), ', ', '', '' ); ?></span>
                  <div class="rating"><?php echo gamtech_star_rating( $product->get_average_rating() ); ?></div>
                </div>
                <?php woocommerce_template_loop_price(); ?>
                <?php woocommerce_template_loop_add_to_cart(); ?>
              </div>
            </div>
          <?php endwhile; wp_reset_postdata(); endif; ?>
        </div>
      </div>

      <div id="featured" class="tab-panel">
        <div class="gt-products-grid">
          <?php
          $feat = new WP_Query( array( 'post_type' => 'product', 'posts_per_page' => 8, 'tax_query' => array( array( 'taxonomy' => 'product_visibility', 'field' => 'name', 'terms' => 'featured' ) ) ) );
          if ( $feat->have_posts() ) : while ( $feat->have_posts() ) : $feat->the_post(); global $product; ?>
            <div class="product-card">
              <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'woocommerce_thumbnail' ); ?></a>
              <div class="prod-info">
                <a class="prod-title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                <?php woocommerce_template_loop_price(); ?>
                <?php woocommerce_template_loop_add_to_cart(); ?>
              </div>
            </div>
          <?php endwhile; wp_reset_postdata(); else: ?>
            <p><?php esc_html_e( 'No featured products found.', 'woodmart' ); ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div id="on-sale" class="tab-panel">
        <div class="gt-products-grid">
          <?php
          $sale = wc_get_products( array( 'limit' => 8, 'status' => 'publish', 'on_sale' => true ) );
          foreach ( $sale as $s ) : $p = wc_get_product( $s->get_id() ); ?>
            <div class="product-card">
              <a href="<?php echo get_permalink( $p->get_id() ); ?>"><?php echo $p->get_image(); ?></a>
              <div class="prod-info">
                <a class="prod-title" href="<?php echo get_permalink( $p->get_id() ); ?>"><?php echo esc_html( $p->get_name() ); ?></a>
                <?php echo $p->get_price_html(); ?>
                <?php echo do_shortcode( '[add_to_cart id="' . esc_attr( $p->get_id() ) . '"]' ); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="gt-best-sellers">
  <div class="container">
    <h2 class="gt-section-title"><?php esc_html_e( 'Best Seller in the Last Month', 'woodmart' ); ?></h2>
    <div class="gt-best-grid">
      <div class="best-feature">
        <?php
        // Get best-selling product (by sales) in last 30 days — approximate by total_sales
        $best = wc_get_products( array( 'limit' => 1, 'orderby' => 'total_sales', 'order' => 'DESC' ) );
        if ( ! empty( $best ) ) {
          $b = wc_get_product( $best[0]->get_id() );
          echo '<a href="' . get_permalink( $b->get_id() ) . '" class="best-img">' . $b->get_image( 'large' ) . '</a>';
          echo '<div class="best-meta"><h3>' . esc_html( $b->get_name() ) . '</h3>' . $b->get_price_html() . '<p>' . wp_trim_words( $b->get_short_description(), 20 ) . '</p>' . do_shortcode( '[add_to_cart id="' . esc_attr( $b->get_id() ) . '"]' ) . '</div>';
        }
        ?>
      </div>

      <div class="best-list">
        <?php
        $popular = wc_get_products( array( 'limit' => 6, 'orderby' => 'popularity' ) );
        foreach ( $popular as $pp ) : ?>
          <div class="best-item">
            <a href="<?php echo get_permalink( $pp->get_id() ); ?>"><?php echo $pp->get_image( 'woocommerce_thumbnail' ); ?></a>
            <div class="best-info"><a href="<?php echo get_permalink( $pp->get_id() ); ?>"><?php echo esc_html( $pp->get_name() ); ?></a><?php echo $pp->get_price_html(); ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
// Hero slider (vanilla JS)
(function(){
  var slides = document.querySelectorAll('#gt-hero-slider .gt-hero-slide');
  var dotsWrap = document.getElementById('gt-hero-dots');
  var cur = 0, t;
  if(!slides.length) return;
  function go(n){
    slides[cur].classList.remove('active');
    cur = (n+slides.length)%slides.length;
    slides[cur].classList.add('active');
    updateDots();
  }
  function next(){ go(cur+1); }
  function prev(){ go(cur-1); }
  document.querySelector('.gt-hero-next').addEventListener('click', next);
  document.querySelector('.gt-hero-prev').addEventListener('click', prev);

  // dots
  slides.forEach(function(s,i){ var d = document.createElement('button'); d.className='dot'+(i===0?' active':''); d.addEventListener('click',function(){go(i)}); dotsWrap.appendChild(d); });
  function updateDots(){ var ds = dotsWrap.children; for(var i=0;i<ds.length;i++){ ds[i].classList.toggle('active', i===cur); } }

  t = setInterval(next, 5000);
  var wrap = document.getElementById('gt-hero-slider'); wrap.addEventListener('mouseover', function(){ clearInterval(t); }); wrap.addEventListener('mouseout', function(){ t = setInterval(next,5000); });
})();

// Tabs
(function(){
  var tabs = document.querySelectorAll('.tab-btn');
  tabs.forEach(function(b){ b.addEventListener('click', function(){ document.querySelectorAll('.tab-btn').forEach(function(x){x.classList.remove('active')}); this.classList.add('active'); var tab = this.getAttribute('data-tab'); document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.toggle('active', p.id===tab); }); }); });
})();
</script>

<?php get_footer();
