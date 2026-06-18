<?php
/**
 * Static pages — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="gs-page-head">
  <h1><?php the_title(); ?></h1>
</div>

<div class="gs-page-content">
  <?php
  while ( have_posts() ) :
      the_post();
      the_content();
  endwhile;
  ?>
</div>

<?php get_footer(); ?>
