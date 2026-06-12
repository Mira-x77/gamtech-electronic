<?php
/**
 * Product Sale Flash — Gamtech Child Override
 * Shows "Save Rs. X" style badge matching reference design
 */

defined( 'ABSPATH' ) || exit;

global $post, $product;

if ( ! $product->is_on_sale() ) {
    return;
}

$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$currency      = get_woocommerce_currency_symbol();

if ( $regular_price > 0 && $sale_price >= 0 ) {
    $saved      = $regular_price - $sale_price;
    $percentage = round( ( $saved / $regular_price ) * 100 );
    ?>
    <span class="onsale gt-sale-badge">
        <?php
        printf(
            /* translators: %1$s currency symbol, %2$s saved amount */
            esc_html__( 'Save %1$s%2$s', 'woodmart' ),
            esc_html( $currency ),
            esc_html( number_format( $saved, 0 ) )
        );
        ?>
    </span>
    <?php
}
