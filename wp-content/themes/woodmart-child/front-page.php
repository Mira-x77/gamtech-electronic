<?php
/**
 * Front Page Template — Gamtech Electronic Ogo-Style Homepage
 *
 * @package woodmart-child
 */

get_header();

// =============================================================================
// Hero Slides Data
// =============================================================================
$gt_slides = array(
	array(
		'badge_text'   => 'Save $7.99',
		'headline'     => 'Galaxy C9 Pro',
		'sub_headline' => 'Big Performance. Sleek Design. Unbeatable Price.',
		'cta_text'     => 'Shop Now',
		'cta_url'      => home_url( '/shop/' ),
		'image_url'    => esc_url( get_stylesheet_directory_uri() . '/images/hero-phone.png' ),
		'bg_color'     => '#eaf3fb',
	),
	array(
		'badge_text'   => 'Up to 40% Off',
		'headline'     => 'Smart TV 4K OLED',
		'sub_headline' => 'Immersive viewing experience for your living room.',
		'cta_text'     => 'View Deals',
		'cta_url'      => home_url( '/shop/' ),
		'image_url'    => esc_url( get_stylesheet_directory_uri() . '/images/hero-tv.png' ),
		'bg_color'     => '#f5eaf3',
	),
	array(
		'badge_text'   => 'New Arrival',
		'headline'     => 'Wireless Earbuds Pro',
		'sub_headline' => 'Crystal clear sound with 30-hour battery life.',
		'cta_text'     => 'Explore',
		'cta_url'      => home_url( '/shop/' ),
		'image_url'    => esc_url( get_stylesheet_directory_uri() . '/images/hero-earbuds.png' ),
		'bg_color'     => '#eafaf1',
	),
);

// =============================================================================
// Section 1: Hero Slider
// =============================================================================
?>
<section class="gt-hero-slider" aria-label="Featured promotions">
  <?php foreach ( $gt_slides as $idx => $slide ) : ?>
    <div class="gt-slide<?php echo $idx === 0 ? ' active' : ''; ?>"
         style="background-color: <?php echo esc_attr( $slide['bg_color'] ); ?>;">
      <div class="gt-slide__content">
        <span class="gt-save-badge">
          <span class="gt-save-badge__label">SAVE</span>
          <span class="gt-save-badge__price"><?php echo esc_html( $slide['badge_text'] ); ?></span>
        </span>
        <h1 class="gt-slide__headline"><?php echo esc_html( $slide['headline'] ); ?></h1>
        <p class="gt-slide__sub"><?php echo esc_html( $slide['sub_headline'] ); ?></p>
        <a class="gt-slide__cta" href="<?php echo esc_url( $slide['cta_url'] ); ?>"><?php echo esc_html( $slide['cta_text'] ); ?></a>
      </div>
      <?php if ( ! empty( $slide['image_url'] ) ) : ?>
        <img class="gt-slide__image" src="<?php echo esc_url( $slide['image_url'] ); ?>" alt="<?php echo esc_attr( $slide['headline'] ); ?>">
      <?php endif; ?>
    </div>
  <?php endforeach; ?>

  <button class="gt-slider-prev" aria-label="Previous slide">&#8592;</button>
  <button class="gt-slider-next" aria-label="Next slide">&#8594;</button>

  <div class="gt-slider-dots" aria-hidden="true">
    <?php foreach ( $gt_slides as $idx => $slide ) : ?>
      <button class="gt-slider-dot<?php echo $idx === 0 ? ' active' : ''; ?>" aria-label="Go to slide <?php echo esc_attr( $idx + 1 ); ?>"></button>
    <?php endforeach; ?>
  </div>
</section>

<?php
// =============================================================================
// Section 2: Trust Badges
// =============================================================================
?>
<section class="gt-trust-badges">
  <div class="gt-container">
    <div class="gt-trust-badges__inner">

      <div class="gt-trust-badge">
        <div class="gt-trust-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 5v3h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
        </div>
        <div class="gt-trust-text">
          <h4>Free Shipping</h4>
          <p>On all orders over $50</p>
        </div>
      </div>

      <div class="gt-trust-badge">
        <div class="gt-trust-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12 19.79 19.79 0 0 1 1.95 3.26 2 2 0 0 1 3.93 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 8.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <div class="gt-trust-text">
          <h4>Support 24/7</h4>
          <p>Contact us anytime</p>
        </div>
      </div>

      <div class="gt-trust-badge">
        <div class="gt-trust-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="gt-trust-text">
          <h4>100% Money Back</h4>
          <p>30-day guarantee</p>
        </div>
      </div>

      <div class="gt-trust-badge">
        <div class="gt-trust-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.54"/></svg>
        </div>
        <div class="gt-trust-text">
          <h4>90 Days Return</h4>
          <p>Hassle-free returns</p>
        </div>
      </div>

      <div class="gt-trust-badge">
        <div class="gt-trust-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        </div>
        <div class="gt-trust-text">
          <h4>Payment Secure</h4>
          <p>100% secure payments</p>
        </div>
      </div>

    </div>
  </div>
</section>

<?php
// =============================================================================
// Section 3: Top Categories
// =============================================================================
$gt_categories = gamtech_get_product_categories( 8 );
?>
<section class="gt-top-categories">
  <div class="gt-container">
    <h2 class="gt-section-title">Top Categories</h2>
    <div class="gt-category-strip">
      <?php foreach ( $gt_categories as $idx => $cat ) : ?>
        <a class="gt-category-tile<?php echo $idx === 0 ? ' active' : ''; ?>"
           href="<?php echo esc_url( $cat['link'] ); ?>"
           title="<?php echo esc_attr( $cat['name'] ); ?>">
          <img src="<?php echo esc_url( $cat['image_url'] ); ?>" alt="<?php echo esc_attr( $cat['name'] ); ?>" loading="lazy">
          <span><?php echo esc_html( $cat['name'] ); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// =============================================================================
// Section 4: Promotional Banners
// =============================================================================
$gt_banners = array(
	array(
		'sub'      => 'Best Deal',
		'title'    => 'Smart Speakers',
		'price'    => 'From $29.99',
		'cta_url'  => home_url( '/shop/' ),
		'bg_color' => '#fff8e1',
	),
	array(
		'sub'      => 'New Arrival',
		'title'    => 'Laptop Pro Series',
		'price'    => 'Starting at $699',
		'cta_url'  => home_url( '/shop/' ),
		'bg_color' => '#e8f5e9',
	),
	array(
		'sub'      => 'Limited Offer',
		'title'    => '4K Action Camera',
		'price'    => 'Only $149.99',
		'cta_url'  => home_url( '/shop/' ),
		'bg_color' => '#fce4ec',
	),
);
?>
<section class="gt-promo-banners">
  <div class="gt-container">
    <div class="gt-promo-banners__grid">
      <?php foreach ( $gt_banners as $banner ) : ?>
        <div class="gt-promo-banner" style="background-color: <?php echo esc_attr( $banner['bg_color'] ); ?>;">
          <div class="gt-promo-banner__content">
            <p class="gt-promo-banner__sub"><?php echo esc_html( $banner['sub'] ); ?></p>
            <h3 class="gt-promo-banner__title"><?php echo esc_html( $banner['title'] ); ?></h3>
            <p class="gt-promo-banner__price"><?php echo esc_html( $banner['price'] ); ?></p>
            <a class="gt-promo-banner__btn" href="<?php echo esc_url( $banner['cta_url'] ); ?>">Shop Now</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// =============================================================================
// Section 5: Product Tabs
// =============================================================================
$gt_new      = gamtech_get_products( 'new_arrivals', 6 );
$gt_featured = gamtech_get_products( 'featured', 6 );
$gt_sale     = gamtech_get_products( 'on_sale', 6 );
?>
<section class="gt-product-tabs">
  <div class="gt-container">
    <div class="gt-tabs-header">
      <h2 class="gt-section-title" style="margin:0 20px 0 0;">Products</h2>
      <button class="gt-tab-link active" data-target="gt-tab-new">New Arrivals</button>
      <span class="gt-tabs-sep">|</span>
      <button class="gt-tab-link" data-target="gt-tab-featured">Featured</button>
      <span class="gt-tabs-sep">|</span>
      <button class="gt-tab-link" data-target="gt-tab-sale">On Sale</button>
    </div>

    <!-- New Arrivals Panel -->
    <div class="gt-tab-panel active" id="gt-tab-new">
      <?php if ( ! empty( $gt_new ) ) : ?>
        <?php foreach ( $gt_new as $p ) : ?>
          <?php gamtech_render_product_card( $p ); ?>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="gt-no-products">No products found.</p>
      <?php endif; ?>
    </div>

    <!-- Featured Panel -->
    <div class="gt-tab-panel" id="gt-tab-featured">
      <?php if ( ! empty( $gt_featured ) ) : ?>
        <?php foreach ( $gt_featured as $p ) : ?>
          <?php gamtech_render_product_card( $p ); ?>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="gt-no-products">No products found.</p>
      <?php endif; ?>
    </div>

    <!-- On Sale Panel -->
    <div class="gt-tab-panel" id="gt-tab-sale">
      <?php if ( ! empty( $gt_sale ) ) : ?>
        <?php foreach ( $gt_sale as $p ) : ?>
          <?php gamtech_render_product_card( $p ); ?>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="gt-no-products">No products found.</p>
      <?php endif; ?>
    </div>

  </div>
</section>

<?php
// =============================================================================
// Section 6: Best Sellers
// =============================================================================
$gt_bestsellers = gamtech_get_products( 'featured', 5 );
$gt_bs_featured = ! empty( $gt_bestsellers ) ? array_shift( $gt_bestsellers ) : null;
?>
<section class="gt-best-sellers">
  <div class="gt-container">
    <div class="gt-best-sellers__header">
      <h2 class="gt-section-title" style="margin:0;">Best Sellers</h2>
      <div class="gt-bs-filter-tabs">
        <button class="gt-bs-tab active">All</button>
        <button class="gt-bs-tab">TV &amp; Video</button>
        <button class="gt-bs-tab">Speakers</button>
        <button class="gt-bs-tab">Air Conditions</button>
        <button class="gt-bs-tab">Cameras</button>
      </div>
    </div>

    <div class="gt-best-sellers__layout">

      <!-- Large featured card -->
      <?php if ( $gt_bs_featured ) : ?>
        <div class="gt-bs-featured">
          <img src="<?php echo esc_url( $gt_bs_featured['image_url'] ); ?>" alt="<?php echo esc_attr( $gt_bs_featured['name'] ); ?>" loading="lazy">
          <div class="gt-bs-featured__body">
            <?php if ( ! empty( $gt_bs_featured['category'] ) ) : ?>
              <p class="gt-product-card__cat"><?php echo esc_html( $gt_bs_featured['category'] ); ?></p>
            <?php endif; ?>
            <h3 class="gt-bs-featured__name">
              <a href="<?php echo esc_url( $gt_bs_featured['permalink'] ); ?>"><?php echo esc_html( $gt_bs_featured['name'] ); ?></a>
            </h3>
            <div class="gt-product-card__rating">
              <?php echo wp_kses_post( gamtech_get_star_html( $gt_bs_featured['rating'] ) ); ?>
              <span class="gt-review-count">(<?php echo esc_html( $gt_bs_featured['review_count'] ); ?>)</span>
            </div>
            <div class="gt-bs-featured__price"><?php echo wp_kses_post( $gt_bs_featured['price_html'] ); ?></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Right grid of remaining products -->
      <div class="gt-bs-grid">
        <?php foreach ( $gt_bestsellers as $p ) : ?>
          <?php gamtech_render_product_card( $p ); ?>
        <?php endforeach; ?>
      </div>

    </div>
  </div>
</section>

<?php get_footer(); ?>
