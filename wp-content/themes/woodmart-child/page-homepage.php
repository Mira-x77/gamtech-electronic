<?php
/**
 * Template Name: Cello Custom Homepage
 */

get_header();
?>

<div class="hero-banner" style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center; background: #111113; border: 1px solid var(--border); overflow: visible;">
    <div class="hero-text" style="flex: 1; padding: 40px; z-index: 10;">
        <span>New Collection</span>
        <h1 style="font-size: 42px; font-weight: 800; margin: 20px 0; line-height: 1.1;">Experience The Future<br>Of <span style="color: var(--accent);">Cello</span> ✨</h1>
        <p style="font-size: 16px; color: var(--text-muted); margin-bottom: 30px;">Discover the ultimate high-performance electronics, components, and premium gaming gear.</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="hero-btn">Explore Now →</a>
    </div>
    <div class="hero-images" style="flex: 1; display: flex; gap: 20px; padding: 20px; justify-content: flex-end; align-items: center; position: relative;">
        <!-- Two images side by side overlapping slightly for a modern look -->
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero1.png" alt="Hero 1" style="width: 250px; height: 350px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); z-index: 2; transform: translateY(-20px);">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero2.png" alt="Hero 2" style="width: 220px; height: 300px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); z-index: 1; transform: translateX(-40px) translateY(20px);">
    </div>
</div>

<div class="categories-row" style="margin-top: 40px;">
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg></div>
            Laptops
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect><rect x="9" y="9" width="6" height="6"></rect><line x1="9" y1="1" x2="9" y2="4"></line><line x1="15" y1="1" x2="15" y2="4"></line><line x1="9" y1="20" x2="9" y2="23"></line><line x1="15" y1="20" x2="15" y2="23"></line><line x1="20" y1="9" x2="23" y2="9"></line><line x1="20" y1="14" x2="23" y2="14"></line><line x1="1" y1="9" x2="4" y2="9"></line><line x1="1" y1="14" x2="4" y2="14"></line></svg></div>
            Components
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 18v-6a9 9 0 0 1 18 0v6"></path><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path></svg></div>
            Audio
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg></div>
            Phones
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"></rect><polyline points="17 2 12 7 7 2"></polyline></svg></div>
            Gaming
        </a>
    </div>
    <div class="cat-item">
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center; gap:8px;">
            <div class="cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg></div>
            More
        </a>
    </div>
</div>

<div class="promo-row">
    <div class="promo-card" style="border-left: 4px solid var(--red);">
        <h3>Flash Sale</h3>
        <p>Limited time deals. Up to 70% Off</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1">Shop now →</a>
    </div>
    <div class="promo-card" style="border-left: 4px solid #10b981;">
        <h3>Free Shipping</h3>
        <p>On orders over $50</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Shop now →</a>
    </div>
    <div class="promo-card" style="border-left: 4px solid var(--accent);">
        <h3>New Arrivals</h3>
        <p>Check out the latest tech trends</p>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date">Shop now →</a>
    </div>
</div>

<div class="section-header">
    <h2>Best Deals for You</h2>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1">View All</a>
</div>

<!-- Use WooCommerce native shortcode for guaranteed 6x6 grid rendering -->
<?php echo do_shortcode('[products limit="36" columns="6" on_sale="true"]'); ?>

<div class="section-header" style="margin-top: 40px;">
    <h2>Recommended for You</h2>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">View All</a>
</div>

<!-- Use WooCommerce native shortcode for guaranteed 6x6 grid rendering -->
<?php echo do_shortcode('[products limit="36" columns="6" orderby="popularity"]'); ?>

<?php
get_footer();
