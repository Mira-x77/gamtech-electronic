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
    <div style="display:flex;flex-direction:column;gap:8px;">
      <button type="button" class="gs-checkout-btn gs-whatsapp-btn" data-phone="22890597003" onclick="gamtechWhatsApp(this)">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        <span><?php esc_html_e( 'Order via WhatsApp #1', 'woodmart' ); ?></span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
      <button type="button" class="gs-checkout-btn gs-whatsapp-btn" data-phone="22879193772" onclick="gamtechWhatsApp(this)" style="background:var(--bg3);border:1.5px solid var(--b2);">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
        <span><?php esc_html_e( 'Order via WhatsApp #2', 'woodmart' ); ?></span>
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </button>
    </div>
    <script>
    /* Inline WhatsApp checkout — zero external dependencies */
    function gamtechWhatsApp(btn) {
      var phone = btn.getAttribute('data-phone') || '22890597003';
      var items = document.querySelectorAll('.gs-ct-item');
      
      if (!items || items.length === 0) {
        alert('Your cart is empty! Add items before ordering.');
        return;
      }
      
      var orderId = 'ORD-' + Date.now();
      var message = 'New Order - ' + orderId + '\n\n';
      var total = 0;
      var num = 1;
      
      items.forEach(function(item) {
        var nameEl = item.querySelector('.gs-ct-name');
        var priceEl = item.querySelector('.gs-ct-price');
        var qtyEl = item.querySelector('.gs-qty-n');
        var name = nameEl ? nameEl.textContent.trim() : 'Product';
        var price = priceEl ? (parseFloat(priceEl.getAttribute('data-price')) || 0) : 0;
        var qty = qtyEl ? (parseInt(qtyEl.textContent) || 1) : 1;
        var lineTotal = price * qty;
        total += lineTotal;
        
        message += num + '. ' + name + '\n';
        message += 'Qty: ' + qty + ' x $' + price.toFixed(2) + ' = $' + lineTotal.toFixed(2) + '\n\n';
        num++;
      });
      
      message += 'Total: $' + total.toFixed(2) + '\n\n';
      message += 'Order placed from: ' + window.location.origin;
      
      var url = 'https://wa.me/' + phone + '?text=' + encodeURIComponent(message);
      console.log('GamTech WhatsApp redirect:', url);
      window.location.href = url;
    }
    </script>
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
