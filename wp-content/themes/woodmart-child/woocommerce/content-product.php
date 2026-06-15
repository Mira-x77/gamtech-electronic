<?php
/**
 * Product card in loop — Cello Electronics (Amazon-style)
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) return;

$permalink  = get_permalink();
$image_id   = $product->get_image_id();
$image_url  = $image_id ? wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
$title      = $product->get_name();
$avg_rating = $product->get_average_rating();
$review_cnt = $product->get_review_count();
$is_on_sale = $product->is_on_sale();
$brand      = $product->get_attribute( 'pa_brand' );
?>

<li class="cello-product-card" style="list-style: none;">
  <!-- Sale tag -->
  <?php if ( $is_on_sale ) :
    $reg   = (float) $product->get_regular_price();
    $sale  = (float) $product->get_sale_price();
    $saved = $reg > 0 ? $reg - $sale : 0;
  ?>
    <span class="cello-sale-tag">
      <?php echo $saved > 0 ? esc_html( 'Save ' . get_woocommerce_currency_symbol() . number_format( $saved, 0 ) ) : 'Sale'; ?>
    </span>
  <?php endif; ?>

  <!-- Quick add to cart -->
  <?php if ( $product->is_purchasable() && $product->is_in_stock() && $product->is_type( 'simple' ) ) : ?>
    <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
       data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
       class="cello-add-cart-btn add_to_cart_button"
       title="<?php esc_attr_e( 'Add to Cart', 'woodmart' ); ?>">+</a>
  <?php endif; ?>

  <!-- Product image -->
  <a href="<?php echo esc_url( $permalink ); ?>">
    <img src="<?php echo esc_url( $image_url ); ?>"
         alt="<?php echo esc_attr( $title ); ?>"
         class="cello-product-image"
         loading="lazy">
  </a>

  <!-- Product info -->
  <div class="cello-product-info">
    <?php if ( $brand ) : ?>
      <span class="cello-product-brand"><?php echo esc_html( $brand ); ?></span>
    <?php endif; ?>

    <h3 class="cello-product-title">
      <a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $title ); ?></a>
    </h3>

    <?php if ( $avg_rating > 0 ) : ?>
    <div>
      <span class="cello-stars">
        <?php
        $full = floor( $avg_rating );
        $half = ( $avg_rating - $full ) >= 0.5 ? 1 : 0;
        for ( $i = 0; $i < $full; $i++ ) echo '&#9733;';
        if ( $half ) echo '&#9733;';
        for ( $j = $full + $half; $j < 5; $j++ ) echo '&#9734;';
        ?>
      </span>
      <span class="cello-stars-count">(<?php echo esc_html( $review_cnt ); ?>)</span>
    </div>
    <?php endif; ?>

    <div class="cello-price"><?php echo $product->get_price_html(); ?></div>
  </div>
</li>
