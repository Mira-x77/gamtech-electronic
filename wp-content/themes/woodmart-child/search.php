<?php
/**
 * Search results — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="gs-page-head">
  <h1>
    <?php
    printf(
        esc_html__( 'Search: %s', 'woodmart' ),
        '<span>' . esc_html( get_search_query() ) . '</span>'
    );
    ?>
  </h1>
  <p><?php esc_html_e( 'Results from our product catalog.', 'woodmart' ); ?></p>
</div>

<?php if ( have_posts() ) : ?>
  <div class="gs-shop-toolbar">
    <span><?php esc_html_e( 'Showing search results', 'woodmart' ); ?></span>
  </div>
  <ul class="gs-grid gs-shop-grid">
    <?php
    while ( have_posts() ) :
        the_post();
        if ( 'product' === get_post_type() ) {
            global $product;
            echo '<li>';
            gamtech_product_card( $product );
            echo '</li>';
        }
    endwhile;
    ?>
  </ul>
  <div class="gs-pagination">
    <?php the_posts_pagination(); ?>
  </div>
<?php else : ?>
  <div class="gs-no-products">
    <h2><?php esc_html_e( 'Nothing found', 'woodmart' ); ?></h2>
    <p><?php esc_html_e( 'Try different keywords or browse categories.', 'woodmart' ); ?></p>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-ct-empty-btn" style="margin-top:16px;display:inline-block;">
      <?php esc_html_e( 'Browse Shop', 'woodmart' ); ?>
    </a>
  </div>
<?php endif; ?>

<?php get_footer(); ?>
