<?php
/**
 * Shop / Category archive — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="gs-page-head">
  <div class="gs-breadcrumb">
    <?php woocommerce_breadcrumb(); ?>
  </div>
  <?php if ( is_product_category() ) :
    $term = get_queried_object();
    ?>
    <h1><?php echo esc_html( $term->name ); ?></h1>
    <?php if ( $term->description ) : ?>
      <p><?php echo wp_kses_post( $term->description ); ?></p>
    <?php endif; ?>
  <?php elseif ( is_shop() ) : ?>
    <h1><?php esc_html_e( 'All Products', 'woodmart' ); ?></h1>
    <p><?php esc_html_e( 'Browse our full range of tech accessories and components.', 'woodmart' ); ?></p>
  <?php else : ?>
    <h1><?php woocommerce_page_title(); ?></h1>
  <?php endif; ?>
</div>

<?php gamtech_render_category_row(); ?>

<div class="gs-shop-toolbar">
  <?php woocommerce_result_count(); ?>
  <?php woocommerce_catalog_ordering(); ?>
</div>

<?php woocommerce_product_loop_start(); ?>

<?php if ( woocommerce_product_loop() ) : ?>
  <?php while ( have_posts() ) : the_post(); ?>
    <?php wc_get_template_part( 'content', 'product' ); ?>
  <?php endwhile; ?>
<?php else : ?>
  <div class="gs-no-products">
    <h2><?php esc_html_e( 'No products found', 'woodmart' ); ?></h2>
    <p><?php esc_html_e( 'Try a different category or search term.', 'woodmart' ); ?></p>
  </div>
<?php endif; ?>

<?php woocommerce_product_loop_end(); ?>

<div class="gs-pagination">
  <?php woocommerce_pagination(); ?>
</div>

<?php get_footer(); ?>
