<?php
/**
 * Single product content — Cello Electronics (Amazon-style)
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'cello-single-product-wrap', $product ); ?>>

  <!-- Main product section: Image Left, Details Right -->
  <div class="container" style="padding: 32px 20px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start;">

      <!-- LEFT: Product Image Gallery -->
      <div class="cello-product-gallery">
        <div style="position: relative; background: #f8f9fa; border-radius: 12px; padding: 24px; text-align: center; border: 1px solid #e8eaed;">
          <?php if ( $product->is_on_sale() ) : ?>
            <?php
            $reg  = (float) $product->get_regular_price();
            $sale = (float) $product->get_sale_price();
            $pct  = $reg > 0 ? round( ( $reg - $sale ) / $reg * 100 ) : 0;
            ?>
            <span style="position: absolute; top: 16px; left: 16px; z-index: 5; background: #cc0c39; color: #fff; font-size: 13px; font-weight: 800; padding: 6px 14px; border-radius: 6px;">
              -<?php echo esc_html( $pct ); ?>%
            </span>
          <?php endif; ?>

          <?php
          // Main product image
          $image_id  = $product->get_image_id();
          $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'woocommerce_single' );
          ?>
          <img src="<?php echo esc_url( $image_url ); ?>"
               alt="<?php echo esc_attr( $product->get_name() ); ?>"
               style="max-width: 100%; max-height: 460px; object-fit: contain; cursor: zoom-in;"
               id="cello-main-product-img">
        </div>

        <!-- Thumbnail gallery -->
        <?php
        $gallery_ids = $product->get_gallery_image_ids();
        $all_images  = array_merge( array( $image_id ), $gallery_ids );
        $all_images  = array_filter( $all_images );
        if ( count( $all_images ) > 1 ) :
        ?>
        <div style="display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap;">
          <?php foreach ( $all_images as $idx => $img_id ) :
            $thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
            $full_url  = wp_get_attachment_image_url( $img_id, 'large' );
            if ( ! $thumb_url ) continue;
          ?>
            <img src="<?php echo esc_url( $thumb_url ); ?>"
                 data-full="<?php echo esc_url( $full_url ); ?>"
                 alt="<?php echo esc_attr( $product->get_name() ); ?>"
                 class="cello-thumb"
                 style="width: 72px; height: 72px; object-fit: contain; border: 2px solid <?php echo $idx === 0 ? '#1a237e' : '#e8eaed'; ?>; border-radius: 8px; padding: 6px; cursor: pointer; background: #f8f9fa; transition: all 0.2s;"
                 onclick="document.getElementById('cello-main-product-img').src=this.dataset.full; document.querySelectorAll('.cello-thumb').forEach(function(t){t.style.borderColor='#e8eaed';}); this.style.borderColor='#1a237e';">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Product Details -->
      <div class="cello-product-details">
        <!-- Brand -->
        <?php
        $brand = $product->get_attribute( 'pa_brand' );
        if ( $brand ) :
        ?>
          <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?pa_brand=<?php echo esc_attr( $brand ); ?>"
             style="font-size: 14px; color: #1a237e; text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 8px;">
            <?php echo esc_html( $brand ); ?>
          </a>
        <?php endif; ?>

        <!-- Title -->
        <h1 style="font-size: 28px; font-weight: 800; color: #1a1a2e; line-height: 1.3; margin: 0 0 12px;">
          <?php the_title(); ?>
        </h1>

        <!-- Rating -->
        <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $product->get_review_count() > 0 ) : ?>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
          <span style="color: #f4c430; font-size: 16px; letter-spacing: 1px;">
            <?php
            $avg = $product->get_average_rating();
            $full = floor( $avg );
            for ( $i = 0; $i < $full; $i++ ) echo '&#9733;';
            if ( ( $avg - $full ) >= 0.5 ) echo '&#9733;';
            for ( $j = $full + ( ( $avg - $full ) >= 0.5 ? 1 : 0 ); $j < 5; $j++ ) echo '&#9734;';
            ?>
          </span>
          <span style="color: #1a237e; font-size: 14px; font-weight: 600;">
            <?php echo esc_html( $product->get_average_rating() ); ?>
          </span>
          <span style="color: #80868b; font-size: 14px;">
            | <?php echo esc_html( $product->get_review_count() ); ?> <?php esc_html_e( 'ratings', 'woodmart' ); ?>
          </span>
        </div>
        <?php endif; ?>

        <!-- Price -->
        <div style="background: #f8f9fa; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #1a237e;">
          <?php echo $product->get_price_html(); ?>
          <?php if ( $product->is_on_sale() ) : ?>
            <span style="display: inline-block; background: #cc0c39; color: #fff; font-size: 12px; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-left: 12px; vertical-align: middle;">
              <?php
              $reg = (float) $product->get_regular_price();
              $sale = (float) $product->get_sale_price();
              $saved = $reg > 0 ? $reg - $sale : 0;
              if ( $saved > 0 ) echo esc_html( 'Save ' . get_woocommerce_currency_symbol() . number_format( $saved, 2 ) );
              ?>
            </span>
          <?php endif; ?>
        </div>

        <!-- Short description -->
        <?php if ( $product->get_short_description() ) : ?>
        <div style="font-size: 15px; color: #5f6368; line-height: 1.7; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e8eaed;">
          <?php echo wp_kses_post( $product->get_short_description() ); ?>
        </div>
        <?php endif; ?>

        <!-- Stock status -->
        <div style="margin-bottom: 16px;">
          <?php if ( $product->is_in_stock() ) : ?>
            <span style="color: #0d652d; font-size: 16px; font-weight: 700;">&#10003; <?php esc_html_e( 'In Stock', 'woodmart' ); ?></span>
          <?php else : ?>
            <span style="color: #cc0c39; font-size: 16px; font-weight: 700;"><?php esc_html_e( 'Out of Stock', 'woodmart' ); ?></span>
          <?php endif; ?>
        </div>

        <!-- Add to cart form -->
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
          <?php
          if ( $product->is_in_stock() && $product->is_purchasable() ) :
          ?>
            <div style="display: flex; align-items: center; border: 2px solid #e8eaed; border-radius: 8px; overflow: hidden;">
              <button type="button" class="cello-qty-btn" onclick="var q=document.getElementById('cello-qty');q.value=Math.max(1,parseInt(q.value)-1);"
                      style="width:40px;height:44px;background:#f8f9fa;border:none;cursor:pointer;font-size:20px;font-weight:700;color:#1a1a2e;">-</button>
              <input type="number" id="cello-qty" value="1" min="1" max="99"
                     style="width:50px;height:44px;text-align:center;border:none;border-left:2px solid #e8eaed;border-right:2px solid #e8eaed;font-size:16px;font-weight:600;"
                     onchange="var s=document.getElementById('cello-hidden-qty');if(s)s.value=this.value;">
              <button type="button" class="cello-qty-btn" onclick="var q=document.getElementById('cello-qty');q.value=parseInt(q.value)+1;"
                      style="width:40px;height:44px;background:#f8f9fa;border:none;cursor:pointer;font-size:20px;font-weight:700;color:#1a1a2e;">+</button>
            </div>
          <?php endif; ?>
        </div>

        <!-- The actual add to cart form -->
        <form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
          <?php
          if ( $product->is_type( 'simple' ) ) :
          ?>
            <input type="hidden" name="quantity" value="1" id="cello-hidden-qty">
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
            <button type="submit" class="single_add_to_cart_button"
                    style="background: #1a237e; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 800; padding: 14px 40px; cursor: pointer; width: 100%; transition: all 0.2s;"
                    onmouseover="this.style.background='#f4c430'"
                    onmouseout="this.style.background='#1a237e'">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 8px;">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
              </svg>
              <?php esc_html_e( 'Add to Cart', 'woodmart' ); ?>
            </button>
          <?php else :
            // For variable/grouped products, use WooCommerce default
            do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
          endif;
          ?>
        </form>

        <!-- Product meta -->
        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e8eaed; font-size: 14px; color: #5f6368;">
          <?php if ( $product->get_sku() ) : ?>
            <p style="margin: 0 0 8px;"><strong>SKU:</strong> <?php echo esc_html( $product->get_sku() ); ?></p>
          <?php endif; ?>
          <?php
          $cats = get_the_terms( $product->get_id(), 'product_cat' );
          if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
          ?>
            <p style="margin: 0 0 8px;"><strong><?php esc_html_e( 'Category:', 'woodmart' ); ?></strong>
              <?php
              $cat_links = array();
              foreach ( $cats as $cat ) {
                $cat_links[] = '<a href="' . esc_url( get_term_link( $cat ) ) . '" style="color: #1a237e; text-decoration: none;">' . esc_html( $cat->name ) . '</a>';
              }
              echo implode( ', ', $cat_links );
              ?>
            </p>
          <?php endif; ?>

          <!-- Trust signals -->
          <div style="display: flex; gap: 24px; margin-top: 16px; flex-wrap: wrap;">
            <span style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #5f6368;">
              <svg width="16" height="16" fill="none" stroke="#0d652d" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
              Free Shipping
            </span>
            <span style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #5f6368;">
              <svg width="16" height="16" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Secure Payment
            </span>
            <span style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: #5f6368;">
              <svg width="16" height="16" fill="none" stroke="#1a237e" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
              90 Day Returns
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Product Tabs: Description, Reviews, etc. -->
  <div style="background: #f8f9fa; padding: 40px 0;">
    <div class="container">
      <?php
      $description = $product->get_description();
      $short_desc = $product->get_short_description();
      ?>

      <!-- Tabs header -->
      <div style="display: flex; border-bottom: 2px solid #e8eaed; margin-bottom: 24px; gap: 0;">
        <button class="cello-tab-btn active" onclick="celloSwitchTab('desc')"
                style="padding: 14px 28px; font-size: 15px; font-weight: 700; color: #1a237e; background: none; border: none; border-bottom: 3px solid #1a237e; cursor: pointer; margin-bottom: -2px;">
          <?php esc_html_e( 'Description', 'woodmart' ); ?>
        </button>
        <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) : ?>
        <button class="cello-tab-btn" onclick="celloSwitchTab('reviews')"
                style="padding: 14px 28px; font-size: 15px; font-weight: 600; color: #5f6368; background: none; border: none; border-bottom: 3px solid transparent; cursor: pointer; margin-bottom: -2px;">
          <?php esc_html_e( 'Reviews', 'woodmart' ); ?> (<?php echo esc_html( $product->get_review_count() ); ?>)
        </button>
        <?php endif; ?>
      </div>

      <!-- Description tab -->
      <div id="cello-tab-desc" class="cello-tab-content" style="display: block;">
        <div style="background: #fff; border-radius: 12px; padding: 32px; font-size: 15px; line-height: 1.8; color: #3c4043;">
          <?php
          if ( $description ) {
            echo wp_kses_post( wpautop( $description ) );
          } elseif ( $short_desc ) {
            echo wp_kses_post( wpautop( $short_desc ) );
          } else {
            echo '<p>' . esc_html__( 'No description available yet.', 'woodmart' ) . '</p>';
          }
          ?>
        </div>
      </div>

      <!-- Reviews tab -->
      <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) : ?>
      <div id="cello-tab-reviews" class="cello-tab-content" style="display: none;">
        <div style="background: #fff; border-radius: 12px; padding: 32px;">
          <?php comments_template(); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Related Products -->
  <?php
  $related = wc_get_related_products( $product->get_id(), 5 );
  if ( ! empty( $related ) ) :
    $related_query = new WP_Query( array(
      'post_type'      => 'product',
      'post__in'       => $related,
      'posts_per_page' => 5,
    ) );
    if ( $related_query->have_posts() ) :
  ?>
  <div class="container" style="padding: 40px 20px;">
    <h2 style="font-size: 20px; font-weight: 800; color: #1a1a2e; margin: 0 0 20px;">
      <?php esc_html_e( 'Customers Also Bought', 'woodmart' ); ?>
    </h2>
    <div class="cello-product-grid" style="grid-template-columns: repeat(5, 1fr) !important;">
      <?php while ( $related_query->have_posts() ) : $related_query->the_post();
        global $product;
        $permalink  = get_permalink();
        $image_url  = wp_get_attachment_url( $product->get_image_id() );
        if ( ! $image_url ) $image_url = wc_placeholder_img_src( 'woocommerce_thumbnail' );
      ?>
        <div class="cello-product-card">
          <?php if ( $product->is_on_sale() ) : ?>
            <span class="cello-sale-tag">Sale</span>
          <?php endif; ?>
          <a href="<?php echo esc_url( $permalink ); ?>">
            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" class="cello-product-image" loading="lazy">
          </a>
          <div class="cello-product-info">
            <h3 class="cello-product-title"><a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a></h3>
            <div class="cello-price"><?php echo $product->get_price_html(); ?></div>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>
  </div>
  <?php endif; endif; ?>

</div>

<!-- Tab switching script -->
<script>
function celloSwitchTab(tab) {
  document.querySelectorAll('.cello-tab-content').forEach(function(el) { el.style.display = 'none'; });
  document.querySelectorAll('.cello-tab-btn').forEach(function(el) {
    el.style.borderBottomColor = 'transparent';
    el.style.color = '#5f6368';
    el.style.fontWeight = '600';
  });
  var target = document.getElementById('cello-tab-' + tab);
  if (target) target.style.display = 'block';
  event.target.style.borderBottomColor = '#1a237e';
  event.target.style.color = '#1a237e';
  event.target.style.fontWeight = '700';
}
</script>

<style>
@media (max-width: 768px) {
  .cello-single-product-wrap > .container > div[style*="grid-template-columns"] {
    grid-template-columns: 1fr !important;
    gap: 24px !important;
  }
  .cello-product-grid {
    grid-template-columns: repeat(2, 1fr) !important;
  }
}
</style>
