<?php
/**
 * Product card in shop loop — GamTech dark theme
 */
defined( 'ABSPATH' ) || exit;
global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<li>
  <?php gamtech_product_card( $product ); ?>
</li>
