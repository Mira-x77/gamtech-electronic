<?php
/**
 * Single product — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="gs-page-head">
  <div class="gs-breadcrumb">
    <?php woocommerce_breadcrumb(); ?>
  </div>
</div>

<?php while ( have_posts() ) : the_post(); ?>
  <?php wc_get_template_part( 'content', 'single-product' ); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
