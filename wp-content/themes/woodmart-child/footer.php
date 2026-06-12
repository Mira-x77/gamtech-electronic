            <!-- Main content ends here -->
        </main> <!-- close main-content -->

        <!-- RIGHT SIDEBAR (CART) -->
        <aside class="sidebar-right">
            <div class="profile-top">
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="icon">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="avatar"><img src="https://ui-avatars.com/api/?name=User&background=random" alt=""></a>
                <span>Profile ⌄</span>
            </div>

            <?php
            // Get WooCommerce Cart
            $cart_count = 0;
            $cart_subtotal = 0;
            $cart_items = array();
            
            if ( function_exists( 'WC' ) && WC()->cart ) {
                $cart_count = WC()->cart->get_cart_contents_count();
                $cart_subtotal = WC()->cart->get_cart_subtotal();
                $cart_items = WC()->cart->get_cart();
            }
            ?>

            <div class="cart-header">
                <h3>My Cart (<?php echo esc_html($cart_count); ?>)</h3>
                <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="close">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </a>
            </div>

            <div class="cart-items">
                <?php 
                if ( empty($cart_items) ) {
                    echo '<p style="color: var(--text-muted); font-size: 13px; text-align: center; margin-top: 20px;">Your cart is currently empty.</p>';
                    echo '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" style="display: block; text-align: center; margin-top: 10px; color: var(--accent); font-weight: 600; text-decoration: none;">Browse Products</a>';
                } else {
                    foreach ( $cart_items as $cart_item_key => $cart_item ) {
                        $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

                        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                            $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                            $thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
                            $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                            
                            // Extract just URL from thumbnail img tag
                            preg_match( '/src="([^"]*)"/i', $thumbnail, $matches ) ;
                            $img_url = isset($matches[1]) ? $matches[1] : 'https://via.placeholder.com/150';
                            ?>
                            <div class="cart-item">
                                <img src="<?php echo esc_url($img_url); ?>" alt="">
                                <div class="cart-item-info">
                                    <h4><?php echo esc_html(substr($product_name, 0, 30)); ?>...</h4>
                                    <div class="cart-item-price"><?php echo $product_price; ?></div>
                                    <div class="qty-controls">
                                        <span>Qty: <?php echo $cart_item['quantity']; ?></span>
                                        <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" class="remove">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
            </div>

            <div class="cart-summary">
                <div class="promo-code">
                    <input type="text" placeholder="Promo Code">
                    <button>Apply</button>
                </div>
                <div class="summary-row total">
                    <span>Subtotal</span>
                    <span style="color: var(--accent);"><?php echo $cart_subtotal; ?></span>
                </div>
                <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-btn">
                    🔒 Checkout (<?php echo esc_html($cart_count); ?>) 
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>

            <div class="club-banner">
                <h3>Join Gamtech Club</h3>
                <p>Get exclusive tech offers and early access!</p>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">Join Now</a>
            </div>
        </aside>

    </div> <!-- close website-wrapper / dashboard -->

    <!-- MOBILE BOTTOM NAVIGATION -->
    <style>
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: var(--white);
            border-top: 1px solid var(--border);
            z-index: 9999;
            padding: 12px 20px;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.2);
        }
        .mobile-nav a {
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 11px;
            gap: 6px;
            font-weight: 500;
        }
        .mobile-nav a.active, .mobile-nav a:hover {
            color: var(--accent);
        }
        .mobile-nav a svg { width: 22px; height: 22px; stroke: currentColor; }
        
        @media (max-width: 900px) {
            .mobile-nav { display: flex; }
        }
    </style>
    <div class="mobile-nav">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="<?php echo is_front_page() ? 'active' : ''; ?>">
            <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Home
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="<?php echo is_shop() ? 'active' : ''; ?>">
            <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Shop
        </a>
        <a href="<?php echo esc_url( wc_get_cart_url() ); ?>">
            <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
            Cart
        </a>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
            <svg fill="none" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            Profile
        </a>
    </div>
    
    <div class="woodmart-close-side"></div>
    <?php do_action( 'woodmart_before_wp_footer' ); ?>
    <?php wp_footer(); ?>
</body>
</html>
