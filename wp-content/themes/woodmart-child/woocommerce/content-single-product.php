<?php
/**
 * Single product content — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( post_password_required() ) {
    echo get_the_password_form();
    return;
}

$image_id  = $product->get_image_id();
$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'woocommerce_single' );
$gallery   = array_filter( array_merge( array( $image_id ), $product->get_gallery_image_ids() ) );
$brand     = $product->get_attribute( 'pa_brand' );
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'gs-product-page', $product ); ?>>

  <div class="gs-product-wrap">
    <div class="gs-product-gallery">
      <?php if ( $product->is_on_sale() ) :
        $reg  = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        $pct  = $reg > 0 ? round( ( $reg - $sale ) / $reg * 100 ) : 0;
        ?>
        <span class="gs-product-sale">-<?php echo esc_html( $pct ); ?>%</span>
      <?php endif; ?>
      <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" id="gs-main-product-img">
      <?php if ( count( $gallery ) > 1 ) : ?>
        <div class="gs-product-thumbs">
          <?php foreach ( $gallery as $idx => $img_id ) :
            $thumb = wp_get_attachment_image_url( $img_id, 'thumbnail' );
            $full  = wp_get_attachment_image_url( $img_id, 'large' );
            if ( ! $thumb ) {
                continue;
            }
            ?>
            <img src="<?php echo esc_url( $thumb ); ?>"
                 data-full="<?php echo esc_url( $full ); ?>"
                 alt="<?php echo esc_attr( $product->get_name() ); ?>"
                 class="<?php echo 0 === $idx ? 'active' : ''; ?>"
                 onclick="var m=document.getElementById('gs-main-product-img');if(m)m.src=this.dataset.full;document.querySelectorAll('.gs-product-thumbs img').forEach(function(t){t.classList.remove('active')});this.classList.add('active');">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="gs-product-details">
      <?php if ( $brand ) : ?>
        <span class="gs-product-brand"><?php echo esc_html( $brand ); ?></span>
      <?php endif; ?>
      <h1><?php the_title(); ?></h1>

      <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && $product->get_review_count() > 0 ) : ?>
        <div class="gs-product-rating">
          <span class="gs-st-f"><?php echo esc_html( str_repeat( '★', (int) floor( $product->get_average_rating() ) ) ); ?></span>
          <span><?php echo esc_html( $product->get_average_rating() ); ?></span>
          <span style="color:var(--di)">| <?php echo esc_html( $product->get_review_count() ); ?> <?php esc_html_e( 'ratings', 'woodmart' ); ?></span>
        </div>
      <?php endif; ?>

      <div class="gs-product-price-box">
        <?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>

      <?php if ( $product->get_short_description() ) : ?>
        <div class="gs-product-short"><?php echo wp_kses_post( $product->get_short_description() ); ?></div>
      <?php endif; ?>

      <?php if ( $product->is_in_stock() ) : ?>
        <p class="gs-stock-in">&#10003; <?php esc_html_e( 'In Stock', 'woodmart' ); ?></p>
      <?php else : ?>
        <p class="gs-stock-out"><?php esc_html_e( 'Out of Stock', 'woodmart' ); ?></p>
      <?php endif; ?>

      <form class="cart gs-qty-row" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
        <?php if ( $product->is_type( 'simple' ) && $product->is_in_stock() ) : ?>
          <div class="gs-qty-control">
            <button type="button" onclick="var q=document.getElementById('gs-qty');q.value=Math.max(1,parseInt(q.value)-1);var h=document.getElementById('gs-hidden-qty');if(h)h.value=q.value;">−</button>
            <input type="number" id="gs-qty" value="1" min="1" max="99" onchange="var h=document.getElementById('gs-hidden-qty');if(h)h.value=this.value;">
            <button type="button" onclick="var q=document.getElementById('gs-qty');q.value=parseInt(q.value)+1;var h=document.getElementById('gs-hidden-qty');if(h)h.value=q.value;">+</button>
          </div>
          <input type="hidden" name="quantity" value="1" id="gs-hidden-qty">
          <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>">
          <button type="submit" class="gs-add-cart-main single_add_to_cart_button">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <?php esc_html_e( 'Add to Cart', 'woodmart' ); ?>
          </button>
        <?php else : ?>
          <?php do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' ); ?>
        <?php endif; ?>
      </form>

      <div class="gs-product-meta">
        <?php if ( $product->get_sku() ) : ?>
          <p><strong>SKU:</strong> <?php echo esc_html( $product->get_sku() ); ?></p>
        <?php endif; ?>
        <?php
        $cats = get_the_terms( $product->get_id(), 'product_cat' );
        if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) :
            $links = array();
            foreach ( $cats as $cat ) {
                $links[] = '<a href="' . esc_url( get_term_link( $cat ) ) . '">' . esc_html( $cat->name ) . '</a>';
            }
            ?>
          <p><strong><?php esc_html_e( 'Category:', 'woodmart' ); ?></strong> <?php echo implode( ', ', $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
        <?php endif; ?>
        <div class="gs-trust-row">
          <span><svg width="14" height="14" fill="none" stroke="var(--gr)" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> <?php esc_html_e( 'Free Shipping', 'woodmart' ); ?></span>
          <span><svg width="14" height="14" fill="none" stroke="var(--pul)" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> <?php esc_html_e( 'Secure Payment', 'woodmart' ); ?></span>
          <span><svg width="14" height="14" fill="none" stroke="var(--pul)" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> <?php esc_html_e( '90 Day Returns', 'woodmart' ); ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="gs-product-tabs">
    <div class="gs-tab-nav">
      <button type="button" class="gs-tab-btn active" data-tab="desc"><?php esc_html_e( 'Description', 'woodmart' ); ?></button>
      <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) : ?>
        <button type="button" class="gs-tab-btn" data-tab="reviews"><?php esc_html_e( 'Reviews', 'woodmart' ); ?> (<?php echo esc_html( $product->get_review_count() ); ?>)</button>
      <?php endif; ?>
    </div>
    <div id="gs-tab-desc" class="gs-tab-panel active">
      <?php
      $desc = $product->get_description();
      echo $desc ? wp_kses_post( wpautop( $desc ) ) : '<p>' . esc_html__( 'No description available yet.', 'woodmart' ) . '</p>';
      ?>
    </div>
    <?php if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) : ?>
      <div id="gs-tab-reviews" class="gs-tab-panel">
        <?php comments_template(); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php
  $related = wc_get_related_products( $product->get_id(), 4 );
  if ( ! empty( $related ) ) :
    $related_query = new WP_Query( array(
        'post_type'      => 'product',
        'post__in'       => $related,
        'posts_per_page' => 4,
    ) );
    if ( $related_query->have_posts() ) :
      ?>
      <div class="gs-related">
        <h2><?php esc_html_e( 'Customers Also Bought', 'woodmart' ); ?></h2>
        <div class="gs-grid">
          <?php
          while ( $related_query->have_posts() ) {
              $related_query->the_post();
              global $product;
              gamtech_product_card( $product );
          }
          wp_reset_postdata();
          ?>
        </div>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
