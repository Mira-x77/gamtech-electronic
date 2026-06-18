<?php
/**
 * GamTech — shared helpers, categories, layout parts
 */
defined( 'ABSPATH' ) || exit;

/**
 * Store categories with SVG icon paths (inner HTML for use inside <svg>).
 */
function gamtech_store_categories() {
    return array(
        'Mouse'                => '<path d="M12 2a6 6 0 0 1 6 6v8a6 6 0 0 1-12 0V8a6 6 0 0 1 6-6z"/><line x1="12" y1="2" x2="12" y2="8"/>',
        'Keyboards'            => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 11h.01M10 11h.01M14 11h.01M18 11h.01M8 15h8"/>',
        'Headphones & Audio'   => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
        'Storage'              => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        'RAM & Memory'         => '<rect x="4" y="8" width="16" height="8" rx="1"/><path d="M8 8V6M12 8V6M16 8V6M8 16v2M12 16v2M16 16v2"/>',
        'Networking'           => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
        'Cables & Converters'  => '<path d="M18 4l3 3-3 3"/><path d="M3 7h18M6 20l-3-3 3-3"/><path d="M21 17H3"/>',
        'Laptop Accessories'   => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
        'Computer Accessories' => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
        'Tools & Repair'       => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'Adapters & Hubs'      => '<circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>',
    );
}

/**
 * Resolve WooCommerce category URL by name or slug.
 */
function gamtech_category_url( $name ) {
    $term = get_term_by( 'name', $name, 'product_cat' );
    if ( ! $term ) {
        $term = get_term_by( 'slug', sanitize_title( $name ), 'product_cat' );
    }
    if ( $term && ! is_wp_error( $term ) ) {
        return get_term_link( $term );
    }
    return add_query_arg( 's', $name, wc_get_page_permalink( 'shop' ) );
}

/**
 * Current nav key for active states.
 */
function gamtech_current_nav() {
    if ( is_front_page() ) {
        return 'home';
    }
    if ( is_shop() ) {
        return 'shop';
    }
    if ( is_product_category() || is_product_tag() ) {
        return 'category';
    }
    if ( is_product() ) {
        return 'product';
    }
    if ( is_account_page() ) {
        return 'account';
    }
    return 'page';
}

/**
 * Nav link active class.
 */
function gamtech_nav_class( $key ) {
    return gamtech_current_nav() === $key ? ' active' : '';
}

/**
 * Is current product category active in sidebar.
 */
function gamtech_cat_active( $name ) {
    if ( ! is_product_category() ) {
        return false;
    }
    $term = get_queried_object();
    if ( ! $term || ! isset( $term->name ) ) {
        return false;
    }
    return strcasecmp( $term->name, $name ) === 0 || strcasecmp( $term->slug, sanitize_title( $name ) ) === 0;
}

/**
 * WooCommerce cart data for cart panel.
 */
function gamtech_get_cart_data() {
    $data = array(
        'items'  => array(),
        'total'  => 0,
        'count'  => 0,
    );

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return $data;
    }

    foreach ( WC()->cart->get_cart() as $item ) {
        $product = wc_get_product( $item['product_id'] );
        if ( ! $product ) {
            continue;
        }
        $data['items'][] = array(
            'name'  => $product->get_name(),
            'qty'   => $item['quantity'],
            'price' => (float) $product->get_price(),
            'img'   => $product->get_image_id()
                ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                : wc_placeholder_img_src( 'thumbnail' ),
        );
        $data['total'] += (float) $product->get_price() * (int) $item['quantity'];
    }
    $data['count'] = WC()->cart->get_cart_contents_count();

    return $data;
}

/**
 * Product query helper.
 */
function gamtech_product_query( $args = array() ) {
    return new WP_Query( array_merge(
        array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
        ),
        $args
    ) );
}

/**
 * Render product card (homepage grid style).
 */
function gamtech_product_card( $product, $badge = '' ) {
    if ( ! $product ) {
        return;
    }

    $id    = $product->get_id();
    $link  = get_permalink( $id );
    $img   = $product->get_image_id()
        ? wp_get_attachment_image_url( $product->get_image_id(), 'woocommerce_thumbnail' )
        : wc_placeholder_img_src();
    $name  = $product->get_name();
    $rat   = (float) $product->get_average_rating();
    $rev   = (int) $product->get_review_count();
    $reg   = (float) $product->get_regular_price();
    $sal   = (float) $product->get_sale_price();
    $sale  = $product->is_on_sale();
    $prc   = (float) $product->get_price();
    $add   = ( $product->is_purchasable() && $product->is_in_stock() ) ? $product->add_to_cart_url() : $link;

    if ( ! $badge && $sale ) {
        $badge = 'sale';
    }
    $pct = ( $sale && $reg > 0 ) ? round( ( $reg - $sal ) / $reg * 100 ) : 0;
    ?>
    <div class="gs-card">
        <?php if ( $badge ) : ?>
            <span class="gs-bdg gs-bdg-<?php echo esc_attr( $badge ); ?>"><?php echo 'sale' === $badge ? 'Sale' : esc_html( ucfirst( $badge ) ); ?></span>
        <?php endif; ?>
        <a href="<?php echo esc_url( $link ); ?>" class="gs-card-img">
            <img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $name ); ?>" loading="lazy">
        </a>
        <div class="gs-card-body">
            <span class="gs-card-brand">GamTech</span>
            <a href="<?php echo esc_url( $link ); ?>" class="gs-card-name"><?php echo esc_html( $name ); ?></a>
            <?php if ( $rat > 0 ) : ?>
                <div class="gs-stars">
                    <span class="gs-st-f"><?php for ( $i = 0; $i < min( 5, round( $rat ) ); $i++ ) { echo '★'; } ?></span>
                    <span class="gs-st-e"><?php for ( $i = round( $rat ); $i < 5; $i++ ) { echo '★'; } ?></span>
                    <span class="gs-st-c">(<?php echo esc_html( $rev ); ?>)</span>
                </div>
            <?php endif; ?>
            <div class="gs-card-price">
                <?php if ( $sale && $reg > 0 ) : ?>
                    <span class="gs-pnow"><?php echo wp_kses_post( wc_price( $sal ) ); ?></span>
                    <span class="gs-pwas"><?php echo wp_kses_post( wc_price( $reg ) ); ?></span>
                    <span class="gs-psave">-<?php echo esc_html( $pct ); ?>%</span>
                <?php else : ?>
                    <span class="gs-pnow"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <a href="<?php echo esc_url( $add ); ?>" class="gs-add-btn"
           data-id="<?php echo esc_attr( $id ); ?>"
           data-price="<?php echo esc_attr( $prc ); ?>"
           data-name="<?php echo esc_attr( $name ); ?>"
           data-img="<?php echo esc_attr( $img ); ?>">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            Add to Cart
        </a>
    </div>
    <?php
}

/**
 * Category icon row for shop/archive pages.
 */
function gamtech_render_category_row() {
    $store_cats = gamtech_store_categories();
    ?>
    <div class="gs-cats gs-cats-page">
        <?php foreach ( $store_cats as $cname => $cico ) :
            $short = strlen( $cname ) > 12 ? substr( $cname, 0, 11 ) . '…' : $cname;
            $active = gamtech_cat_active( $cname ) ? ' active' : '';
            ?>
            <a href="<?php echo esc_url( gamtech_category_url( $cname ) ); ?>" class="gs-cat<?php echo esc_attr( $active ); ?>">
                <div class="gs-cat-ico">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?php echo $cico; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg>
                </div>
                <span class="gs-cat-lbl"><?php echo esc_html( $short ); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php
}
