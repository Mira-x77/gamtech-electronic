<?php
/**
 * Shop / Archive page — Cello Electronics
 * Full-width, clean Amazon-style product listing
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<!-- Breadcrumb & Category Header -->
<div style="background: #f8f9fa; padding: 20px 0; border-bottom: 1px solid #e8eaed;">
  <div class="container">
    <?php woocommerce_breadcrumb(); ?>

    <?php if ( is_product_category() ) :
      $term = get_queried_object();
    ?>
      <h1 style="font-size: 28px; font-weight: 800; color: #1a1a2e; margin: 12px 0 4px;">
        <?php echo esc_html( $term->name ); ?>
      </h1>
      <?php if ( $term->description ) : ?>
        <p style="font-size: 15px; color: #5f6368; margin: 0; max-width: 700px; line-height: 1.6;">
          <?php echo wp_kses_post( $term->description ); ?>
        </p>
      <?php endif; ?>
    <?php elseif ( is_shop() ) : ?>
      <h1 style="font-size: 28px; font-weight: 800; color: #1a1a2e; margin: 12px 0 4px;">
        <?php esc_html_e( 'All Products', 'woodmart' ); ?>
      </h1>
    <?php endif; ?>
  </div>
</div>

<!-- Shop Toolbar: Result count + Sorting -->
<div style="padding: 16px 0; border-bottom: 1px solid #e8eaed;">
  <div class="container" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div>
      <?php woocommerce_result_count(); ?>
    </div>
    <div>
      <?php woocommerce_catalog_ordering(); ?>
    </div>
  </div>
</div>

<!-- Product Grid -->
<div class="container" style="padding: 24px 20px 48px;">
  <?php woocommerce_product_loop_start(); ?>

  <?php if ( woocommerce_product_loop() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
      <?php wc_get_template_part( 'content', 'product' ); ?>
    <?php endwhile; ?>
  <?php else : ?>
    <?php woocommerce_no_products_found(); ?>
  <?php endif; ?>

  <?php woocommerce_product_loop_end(); ?>

  <!-- Pagination -->
  <?php woocommerce_pagination(); ?>
</div>

<?php get_footer(); ?>
