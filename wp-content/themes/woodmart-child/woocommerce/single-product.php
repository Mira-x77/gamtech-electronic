<?php
/**
 * Single product template — Cello Electronics
 * Amazon-style product detail page
 */
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<div style="padding: 16px 0; background: #f8f9fa; border-bottom: 1px solid #e8eaed;">
  <div class="container">
    <?php woocommerce_breadcrumb(); ?>
  </div>
</div>

<?php while ( have_posts() ) : the_post(); ?>
  <?php wc_get_template_part( 'content', 'single-product' ); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
