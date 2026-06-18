<?php
/**
 * Footer — GamTech dark layout (main close + cart panel + site footer)
 */
defined( 'ABSPATH' ) || exit;

if ( ! isset( $cart_data ) ) {
    require_once get_stylesheet_directory() . '/inc/gamtech-core.php';
    $cart_data = gamtech_get_cart_data();
}

$wc_count = $cart_data['count'];
$wc_total = $cart_data['total'];
$wc_items = $cart_data['items'];
?>
  </main>

  <footer class="gs-ft">
    <div class="gs-ft-features">
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div class="gs-ft-b"><h4><?php esc_html_e( 'Secure Payment', 'woodmart' ); ?></h4><p><?php esc_html_e( '100% secure checkout', 'woodmart' ); ?></p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div>
        <div class="gs-ft-b"><h4><?php esc_html_e( 'Easy Returns', 'woodmart' ); ?></h4><p><?php esc_html_e( '30-day return policy', 'woodmart' ); ?></p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
        <div class="gs-ft-b"><h4><?php esc_html_e( '24/7 Support', 'woodmart' ); ?></h4><p><?php esc_html_e( 'Dedicated support team', 'woodmart' ); ?></p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
        <div class="gs-ft-b"><h4><?php esc_html_e( 'Trusted Store', 'woodmart' ); ?></h4><p><?php esc_html_e( '4.8 average rating', 'woodmart' ); ?></p></div>
      </div>
    </div>

    <div class="gs-ft-links">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'woodmart' ); ?></a>
      <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'woodmart' ); ?></a>
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>"><?php esc_html_e( 'About', 'woodmart' ); ?></a>
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>"><?php esc_html_e( 'Contact', 'woodmart' ); ?></a>
      <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'privacy-policy' ) ) ); ?>"><?php esc_html_e( 'Privacy', 'woodmart' ); ?></a>
      <span class="gs-ft-copy">&copy; <?php echo esc_html( date( 'Y' ) ); ?> GamTech</span>
    </div>
  </footer>
</div><!-- .gs-center -->

<aside class="gs-ct" id="gs-ct">
  <div class="gs-ct-hd">
    <h3><?php esc_html_e( 'My Cart', 'woodmart' ); ?> <span class="gs-ct-cnt" id="gs-ct-cnt"><?php echo esc_html( $wc_count ); ?></span></h3>
    <button class="gs-ct-close" id="gs-ct-close" aria-label="<?php esc_attr_e( 'Close', 'woodmart' ); ?>">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="gs-ct-items" id="gs-ct-items">
    <?php if ( ! empty( $wc_items ) ) : ?>
      <?php foreach ( $wc_items as $ci ) : ?>
        <div class="gs-ct-item">
          <img class="gs-ct-thumb" src="<?php echo esc_url( $ci['img'] ); ?>" alt="<?php echo esc_attr( $ci['name'] ); ?>" loading="lazy">
          <div class="gs-ct-info">
            <div class="gs-ct-name"><?php echo esc_html( $ci['name'] ); ?></div>
            <div class="gs-ct-sub">GamTech</div>
            <div class="gs-ct-price" data-price="<?php echo esc_attr( $ci['price'] ); ?>"><?php echo wp_kses_post( wc_price( $ci['price'] ) ); ?></div>
            <div class="gs-qty">
              <button class="gs-qty-btn" data-action="minus">−</button>
              <span class="gs-qty-n"><?php echo esc_html( $ci['qty'] ); ?></span>
              <button class="gs-qty-btn" data-action="plus">+</button>
            </div>
          </div>
          <button class="gs-ct-del" aria-label="<?php esc_attr_e( 'Remove', 'woodmart' ); ?>">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
          </button>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <div class="gs-ct-empty">
        <svg width="40" height="40" fill="none" stroke="var(--di)" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <p><?php esc_html_e( 'Your cart is empty', 'woodmart' ); ?></p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="gs-ct-empty-btn"><?php esc_html_e( 'Browse Products', 'woodmart' ); ?></a>
      </div>
    <?php endif; ?>
  </div>
  <div class="gs-ct-promo">
    <div class="gs-prom-row">
      <input type="text" id="gs-promo-in" placeholder="<?php esc_attr_e( 'Promo Code', 'woodmart' ); ?>">
      <button id="gs-promo-btn"><?php esc_html_e( 'Apply', 'woodmart' ); ?></button>
    </div>
    <p class="gs-prom-msg" id="gs-promo-msg"></p>
  </div>
  <div class="gs-ct-sum">
    <div class="gs-srow"><span class="l"><?php esc_html_e( 'Subtotal', 'woodmart' ); ?></span><span class="v" id="gs-sub"><?php echo wp_kses_post( wc_price( $wc_total ) ); ?></span></div>
    <div class="gs-srow disc"><span class="l"><?php esc_html_e( 'Discount', 'woodmart' ); ?></span><span class="v" id="gs-disc"><?php echo wp_kses_post( wc_price( 0 ) ); ?></span></div>
    <div class="gs-srow ship"><span class="l"><?php esc_html_e( 'Shipping', 'woodmart' ); ?></span><span class="v"><?php esc_html_e( 'Free', 'woodmart' ); ?></span></div>
    <div class="gs-srow tot"><span class="l"><?php esc_html_e( 'Total', 'woodmart' ); ?></span><span class="v" id="gs-tot"><?php echo wp_kses_post( wc_price( $wc_total ) ); ?></span></div>
    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="gs-checkout-btn">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      <?php printf( esc_html__( 'Checkout (%d)', 'woodmart' ), (int) $wc_count ); ?>
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <div class="gs-pay-ico"><span>VISA</span><span>MC</span><span>PayPal</span><span>Apple Pay</span></div>
  </div>
  <div class="gs-ct-sugg">
    <h4><?php esc_html_e( 'You might also like', 'woodmart' ); ?></h4>
    <?php
    $sq = gamtech_product_query( array( 'posts_per_page' => 3, 'orderby' => 'rand' ) );
    if ( $sq->have_posts() ) :
        while ( $sq->have_posts() ) :
            $sq->the_post();
            global $product;
            $si = $product->get_image_id()
                ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                : wc_placeholder_img_src( 'thumbnail' );
            ?>
            <div class="gs-sugg-item">
              <img class="gs-sugg-img" src="<?php echo esc_url( $si ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
              <span class="gs-sugg-nm"><?php echo esc_html( $product->get_name() ); ?></span>
              <span class="gs-sugg-pr"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
              <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="gs-sugg-add">+</a>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
    endif;
    ?>
  </div>
</aside>

</div><!-- .gs-page -->

<?php wp_footer(); ?>
</body>
</html>
