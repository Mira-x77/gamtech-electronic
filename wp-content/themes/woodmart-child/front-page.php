<?php
/**
 * Homepage — GamTech Electronics (uses unified dark layout)
 */
defined( 'ABSPATH' ) || exit;

require_once get_stylesheet_directory() . '/inc/gamtech-core.php';

$store_cats = gamtech_store_categories();
$q_deals    = gamtech_product_query( array(
    'posts_per_page' => 8,
    'meta_query'     => array( array( 'key' => '_sale_price', 'value' => '', 'compare' => '!=' ) ),
    'orderby'        => 'rand',
) );
if ( ! $q_deals->have_posts() ) {
    $q_deals = gamtech_product_query( array( 'posts_per_page' => 8, 'orderby' => 'date' ) );
}
$q_rec = gamtech_product_query( array(
    'posts_per_page' => 4,
    'meta_key'       => 'total_sales',
    'orderby'        => 'meta_value_num',
    'order'          => 'DESC',
) );

get_header();
?>

<section class="gs-hero">
  <div>
    <div class="gs-hero-tag"><span class="dot"></span><?php esc_html_e( 'Tech Store 2026', 'woodmart' ); ?></div>
    <h1><?php esc_html_e( 'Power Up Your', 'woodmart' ); ?><br><span><?php esc_html_e( 'Workspace ✦', 'woodmart' ); ?></span></h1>
    <p><?php esc_html_e( 'Mouse, keyboards, headphones, storage, networking and more — everything for your tech setup.', 'woodmart' ); ?></p>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-hero-cta">
      <?php esc_html_e( 'Shop Now', 'woodmart' ); ?>
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
  <div class="gs-hero-img">
    <?php
    $hi = get_theme_mod( 'gamtech_hero_image', '' );
    if ( $hi ) :
        ?>
      <img src="<?php echo esc_url( $hi ); ?>" alt="<?php esc_attr_e( 'Hero product', 'woodmart' ); ?>">
    <?php else : ?>
      <div class="gs-hero-ph">
        <svg width="80" height="80" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="gs-cats">
  <?php foreach ( $store_cats as $cname => $cico ) :
    $short = strlen( $cname ) > 12 ? substr( $cname, 0, 11 ) . '…' : $cname;
    ?>
    <a href="<?php echo esc_url( gamtech_category_url( $cname ) ); ?>" class="gs-cat">
      <div class="gs-cat-ico">
        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?php echo $cico; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
      </div>
      <span class="gs-cat-lbl"><?php echo esc_html( $short ); ?></span>
    </a>
  <?php endforeach; ?>
</div>

<div class="gs-promos">
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>" class="gs-promo">
    <div class="gs-pi r"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
    <div class="gs-promo-b"><h4><?php esc_html_e( 'Flash Sale', 'woodmart' ); ?></h4><p><?php esc_html_e( 'Up to 70% Off', 'woodmart' ); ?></p><span class="pcta"><?php esc_html_e( 'Shop now →', 'woodmart' ); ?></span></div>
  </a>
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-promo">
    <div class="gs-pi g"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
    <div class="gs-promo-b"><h4><?php esc_html_e( 'Free Shipping', 'woodmart' ); ?></h4><p><?php esc_html_e( 'On orders over $350', 'woodmart' ); ?></p><span class="pcta"><?php esc_html_e( 'Shop now →', 'woodmart' ); ?></span></div>
  </a>
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?orderby=date' ); ?>" class="gs-promo">
    <div class="gs-pi p"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
    <div class="gs-promo-b"><h4><?php esc_html_e( 'New Arrivals', 'woodmart' ); ?></h4><p><?php esc_html_e( 'Latest tech just dropped', 'woodmart' ); ?></p><span class="pcta"><?php esc_html_e( 'Shop now →', 'woodmart' ); ?></span></div>
  </a>
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>" class="gs-promo">
    <div class="gs-pi y"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg></div>
    <div class="gs-promo-b"><h4><?php esc_html_e( 'Limited Offers', 'woodmart' ); ?></h4><p><?php esc_html_e( 'Exclusive discounts', 'woodmart' ); ?></p><span class="pcta"><?php esc_html_e( 'Shop now →', 'woodmart' ); ?></span></div>
  </a>
</div>

<div class="gs-sh">
  <div><h2><?php esc_html_e( 'Best Deals for You', 'woodmart' ); ?></h2><p><?php esc_html_e( 'Hand-picked at unbeatable prices', 'woodmart' ); ?></p></div>
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>" class="gs-viewall"><?php esc_html_e( 'View All', 'woodmart' ); ?></a>
</div>
<div class="gs-grid">
  <?php
  if ( $q_deals->have_posts() ) :
      while ( $q_deals->have_posts() ) {
          $q_deals->the_post();
          global $product;
          gamtech_product_card( $product );
      }
      wp_reset_postdata();
  endif;
  ?>
</div>

<div class="gs-sh">
  <div><h2><?php esc_html_e( 'Recommended for You', 'woodmart' ); ?></h2><p><?php esc_html_e( 'Based on popular picks', 'woodmart' ); ?></p></div>
  <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?orderby=popularity' ); ?>" class="gs-viewall"><?php esc_html_e( 'View All', 'woodmart' ); ?></a>
</div>
<div class="gs-grid">
  <?php
  if ( $q_rec->have_posts() ) :
      while ( $q_rec->have_posts() ) {
          $q_rec->the_post();
          global $product;
          gamtech_product_card( $product );
      }
      wp_reset_postdata();
  endif;
  ?>
</div>

<?php get_footer(); ?>
