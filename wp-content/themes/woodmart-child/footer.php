<?php
/**
 * Footer template — Cello Electronics
 */
?>
    </div><!-- .main-page-wrapper -->

    <!-- ======================================
         FOOTER
         ====================================== -->
    <footer class="gt-footer">
        <div class="container">
            <div class="gt-footer-grid">

                <!-- Col 1: Brand -->
                <div class="gt-footer-col gt-footer-brand">
                    <?php
                    $footer_logo = get_stylesheet_directory_uri() . '/assets/images/logo-light.png?v=3';
                    echo '<img src="' . esc_url( $footer_logo ) . '" alt="Cello" style="max-height:44px;width:auto;margin-bottom:16px;" onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'block\'">';
                    echo '<span style="display:none;font-size:24px;font-weight:900;color:#fff;">Cello</span>';
                    ?>
                    <p><?php echo esc_html( get_bloginfo( 'description' ) ?: __( 'Your trusted electronics store. Phones, laptops, gadgets and more — quality products, fast delivery.', 'woodmart' ) ); ?></p>
                    <div class="gt-social-links">
                        <a href="#" aria-label="Facebook" rel="noopener noreferrer">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                        <a href="#" aria-label="Instagram" rel="noopener noreferrer">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </a>
                        <a href="#" aria-label="Twitter" rel="noopener noreferrer">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div class="gt-footer-col">
                    <h4><?php esc_html_e( 'Quick Links', 'woodmart' ); ?></h4>
                    <ul>
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?on_sale=1' ); ?>"><?php esc_html_e( 'Deals & Offers', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) . '?orderby=date' ); ?>"><?php esc_html_e( 'New Arrivals', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about' ) ) ); ?>"><?php esc_html_e( 'About Us', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'contact' ) ) ); ?>"><?php esc_html_e( 'Contact', 'woodmart' ); ?></a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Service -->
                <div class="gt-footer-col">
                    <h4><?php esc_html_e( 'Customer Service', 'woodmart' ); ?></h4>
                    <ul>
                        <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Track Your Order', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Returns & Exchanges', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My Account', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'privacy-policy' ) ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'woodmart' ); ?></a></li>
                        <li><a href="<?php echo esc_url( get_permalink( get_page_by_path( 'terms-and-conditions' ) ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'woodmart' ); ?></a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact Info -->
                <div class="gt-footer-col">
                    <h4><?php esc_html_e( 'Contact Us', 'woodmart' ); ?></h4>
                    <ul class="gt-contact-list">
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <?php esc_html_e( 'Algiers, Algeria', 'woodmart' ); ?>
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <a href="tel:+213000000000">+213 000 000 000</a>
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            <a href="mailto:contact@cello-electronics.com">contact@cello-electronics.com</a>
                        </li>
                        <li>
                            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?php esc_html_e( 'Sat-Thu: 9am - 7pm', 'woodmart' ); ?>
                        </li>
                    </ul>

                    <!-- Payment icons -->
                    <div style="margin-top: 20px;">
                        <p style="font-size:12px;opacity:0.6;margin-bottom:8px;"><?php esc_html_e( 'We Accept', 'woodmart' ); ?></p>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <?php
                            $payment_methods = array( 'Visa', 'MC', 'PayPal', 'CIB' );
                            foreach ( $payment_methods as $pm ) :
                            ?>
                            <span style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.2);border-radius:4px;padding:4px 10px;font-size:11px;font-weight:700;">
                                <?php echo esc_html( $pm ); ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="gt-footer-bottom">
            <div class="container">
                <div class="gt-footer-bottom-inner">
                    <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>. <?php esc_html_e( 'All rights reserved.', 'woodmart' ); ?></p>
                    <p style="font-size:12px;opacity:0.5;"><?php esc_html_e( 'Powered by WooCommerce', 'woodmart' ); ?></p>
                </div>
            </div>
        </div>
    </footer>

</div><!-- .website-wrapper -->

<!-- Mobile nav toggle script -->
<script>
(function() {
    var toggle = document.getElementById('gt-menu-toggle');
    var mobileNav = document.getElementById('gt-mobile-nav');
    if ( toggle && mobileNav ) {
        toggle.addEventListener('click', function() {
            var isOpen = mobileNav.style.display === 'block';
            mobileNav.style.display = isOpen ? 'none' : 'block';
        });
    }
})();
</script>

<div class="woodmart-close-side"></div>
<?php do_action( 'woodmart_before_wp_footer' ); ?>
<?php wp_footer(); ?>
</body>
</html>
