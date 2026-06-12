<?php
/**
 * Template Name: Gamtech Custom Homepage
 */

get_header();
?>

<div class="hero-banner" style="margin-top: 20px;">
    <div class="hero-text">
        <span>New Collection</span>
        <h1>Upgrade Your Setup,<br>Level Up Your Game ✨</h1>
        <p>Discover the latest in high-performance electronics, components, and gaming gear.</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="hero-btn">Shop Now →</a>
    </div>
</div>

<div class="categories-row">
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
            </div>
            Laptops
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg>
            </div>
            Components
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg>
            </div>
            Audio
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>
            </div>
            Phones
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg>
            </div>
            Gaming
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            </div>
            More
        </a>
    </div>
</div>

<div class="promo-row">
    <div class="promo-card pink">
        <h3>Flash Sale</h3>
        <p>Limited time deals. Up to 70% Off</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1">Shop now →</a>
    </div>
    <div class="promo-card green">
        <h3>Free Shipping</h3>
        <p>On orders over $50</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop now →</a>
    </div>
    <div class="promo-card orange">
        <h3>New Arrivals</h3>
        <p>Check out the latest tech trends</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date">Shop now →</a>
    </div>
</div>

<?php
// Fetch more products (12 for deals, 12 for recommended)
$args = array('limit' => 24, 'status' => 'publish');
$products = wc_get_products($args);
$best_deals = array_slice($products, 0, 12);
$recommended = array_slice($products, 12, 12);
?>

<div class="section-header">
    <h2>Best Deals for You</h2>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1">View All</a>
</div>
<div class="product-grid">
    <?php foreach($best_deals as $product): ?>
    <div class="product-card">
        <div class="discount">-20%</div>
        <div class="heart">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        </div>
        <a href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php $img_url = wp_get_attachment_url( $product->get_image_id() ); ?>
            <img src="<?php echo $img_url ? $img_url : 'https://via.placeholder.com/150'; ?>" alt="" class="product-image">
        </a>
        <h4><a href="<?php echo esc_url($product->get_permalink()); ?>" style="text-decoration:none; color:inherit;"><?php echo $product->get_name(); ?></a></h4>
        <div class="cat">Gamtech Electronics</div>
        <div class="product-price">
            <span class="current"><?php echo wc_price($product->get_price()); ?></span>
            <span class="old"><?php echo wc_price((float)$product->get_price() * 1.2); ?></span>
        </div>
        <div class="product-rating"><span style="color:#fbbf24;">★</span> 4.8 (124)</div>
    </div>
    <?php endforeach; ?>
</div>

<div class="section-header">
    <h2>Recommended for You</h2>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">View All</a>
</div>
<div class="product-grid">
    <?php foreach($recommended as $product): ?>
    <div class="product-card">
        <div class="discount">-15%</div>
        <div class="heart">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        </div>
        <a href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php $img_url = wp_get_attachment_url( $product->get_image_id() ); ?>
            <img src="<?php echo $img_url ? $img_url : 'https://via.placeholder.com/150'; ?>" alt="" class="product-image">
        </a>
        <h4><a href="<?php echo esc_url($product->get_permalink()); ?>" style="text-decoration:none; color:inherit;"><?php echo $product->get_name(); ?></a></h4>
        <div class="cat">Gamtech Electronics</div>
        <div class="product-price">
            <span class="current"><?php echo wc_price($product->get_price()); ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php
get_footer();
