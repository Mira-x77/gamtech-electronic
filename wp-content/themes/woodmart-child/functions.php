<?php
/**
 * Gamtech Electronic - Woodmart Child Theme Functions
 *
 * Handles theme setup, asset enqueueing, and helper function scaffolding.
 *
 * @package woodmart-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// =============================================================================
// Theme Setup
// =============================================================================

/**
 * Sets up child theme defaults and registers support for various WordPress features.
 *
 * Hooked into the `after_setup_theme` action.
 */
function gamtech_setup() {
	// Register primary navigation menu location.
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'woodmart-child' ),
		)
	);

	// Register custom image size for product thumbnails (hard-cropped 300×300).
	add_image_size( 'gamtech-product', 300, 300, true );

	// Declare WooCommerce compatibility.
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'gamtech_setup' );

// =============================================================================
// Asset Enqueueing
// =============================================================================

/**
 * Enqueues stylesheets for the Gamtech child theme.
 *
 * - Enqueues the child theme's style.css with woodmart-style as a dependency.
 * - Enqueues Poppins from Google Fonts.
 *
 * Hooked into `wp_enqueue_scripts` at priority 10010 (after parent theme).
 */
function gamtech_enqueue_styles() {
	// Enqueue child theme stylesheet, depending on the parent Woodmart stylesheet.
	wp_enqueue_style(
		'gamtech-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'woodmart-style' ),
		wp_get_theme()->get( 'Version' )
	);

	// Enqueue Poppins from Google Fonts with requested weights and display=swap.
	wp_enqueue_style(
		'gamtech-google-fonts',
		'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
		array(),
		null
	);
}
add_action( 'wp_enqueue_scripts', 'gamtech_enqueue_styles', 10010 );

// =============================================================================
// Helper Functions (Scaffolding — implementations added in Task 2)
// =============================================================================

/**
 * Checks whether WooCommerce is currently active.
 *
 * @return bool True if WooCommerce is active, false otherwise.
 */
function gamtech_woo_active(): bool {
	return class_exists( 'WooCommerce' ) || function_exists( 'WC' );
}

/**
 * Retrieves an array of product category tiles.
 *
 * Returns live WooCommerce categories when WooCommerce is active,
 * or static fallback data when WooCommerce is inactive.
 *
 * @param int $limit Maximum number of categories to return. Default 8.
 * @return array Array of CategoryTile associative arrays.
 */
function gamtech_get_product_categories( int $limit = 8 ): array {
	// 1. Check transient cache.
	$cached = get_transient( 'gamtech_categories_' . $limit );
	if ( false !== $cached ) {
		return $cached;
	}

	// SVG placeholder for categories with no image.
	$placeholder_svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='120' viewBox='0 0 120 120'%3E%3Crect width='120' height='120' fill='%23f0f0f0'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' font-size='12' fill='%23999'%3ENo Image%3C/text%3E%3C/svg%3E";

	$result = array();

	// 2. Attempt live WooCommerce data.
	if ( gamtech_woo_active() ) {
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'number'     => $limit,
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
				$image_url    = $thumbnail_id ? wp_get_attachment_image_url( $thumbnail_id, 'medium' ) : '';
				if ( empty( $image_url ) ) {
					$image_url = $placeholder_svg;
				}

				$result[] = array(
					'slug'      => $term->slug,
					'name'      => $term->name,
					'count'     => $term->count,
					'image_url' => $image_url,
					'link'      => get_term_link( $term ),
				);
			}
		}
	}

	// 3. Static fallback when WooCommerce is inactive or returned no categories.
	if ( empty( $result ) ) {
		$fallback_categories = array(
			array( 'slug' => 'tv-televisions',   'name' => 'TV Televisions' ),
			array( 'slug' => 'air-conditioners',  'name' => 'Air Conditioners' ),
			array( 'slug' => 'washing-machine',   'name' => 'Washing Machine' ),
			array( 'slug' => 'audios-theaters',   'name' => 'Audios & Theaters' ),
			array( 'slug' => 'office-electronics','name' => 'Office Electronics' ),
			array( 'slug' => 'car-electronics',   'name' => 'Car Electronics' ),
			array( 'slug' => 'game-controller',   'name' => 'Game Controller' ),
			array( 'slug' => 'cameras',           'name' => 'Cameras' ),
		);

		foreach ( array_slice( $fallback_categories, 0, $limit ) as $cat ) {
			$result[] = array(
				'slug'      => $cat['slug'],
				'name'      => $cat['name'],
				'count'     => 0,
				'image_url' => $placeholder_svg,
				'link'      => home_url( '/shop' ),
			);
		}
	}

	// 4. Cache the result.
	set_transient( 'gamtech_categories_' . $limit, $result, 3600 );

	// 5. Return.
	return $result;
}

/**
 * Retrieves an array of product cards for the given tab.
 *
 * Returns live WooCommerce products when WooCommerce is active,
 * or static fallback data when WooCommerce is inactive.
 *
 * @param string $tab   Tab identifier: 'new_arrivals', 'featured', or 'on_sale'. Default 'new_arrivals'.
 * @param int    $limit Maximum number of products to return. Default 6.
 * @return array Array of ProductCard associative arrays.
 */
function gamtech_get_products( string $tab = 'new_arrivals', int $limit = 6 ): array {
	// 1. Check transient cache.
	$cache_key = 'gamtech_products_' . $tab . '_' . $limit;
	$cached    = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	// SVG placeholder for products with no image.
	$placeholder_svg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='300' viewBox='0 0 300 300'%3E%3Crect width='300' height='300' fill='%23f8f8f8'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' font-size='14' fill='%23ccc'%3EProduct%3C/text%3E%3C/svg%3E";

	// 2. Static fallback definition.
	$static_fallback = array(
		array(
			'id'            => 1,
			'name'          => 'Bose SoundLink Micro Bluetooth Speaker',
			'category'      => 'AUDIO',
			'price_html'    => '<del>$76.00</del> <ins>$59.99</ins>',
			'sale_price'    => '59.99',
			'regular_price' => '76.00',
			'on_sale'       => true,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.5,
			'review_count'  => 3,
		),
		array(
			'id'            => 2,
			'name'          => 'Apple iPad with Retina Display M0510LL/A',
			'category'      => 'TELEPHONES',
			'price_html'    => '$49.99',
			'sale_price'    => '',
			'regular_price' => '49.99',
			'on_sale'       => false,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.0,
			'review_count'  => 1,
		),
		array(
			'id'            => 3,
			'name'          => 'Beats Solo3 Wireless On-Ear Headphones',
			'category'      => 'HEADPHONES',
			'price_html'    => '<del>$35.00</del> <ins>$89.99</ins>',
			'sale_price'    => '89.99',
			'regular_price' => '35.00',
			'on_sale'       => true,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.0,
			'review_count'  => 2,
		),
		array(
			'id'            => 4,
			'name'          => 'Knox KHF7 Lightweight Portable Headphone',
			'category'      => 'HEADPHONES',
			'price_html'    => '$49.99',
			'sale_price'    => '',
			'regular_price' => '49.99',
			'on_sale'       => false,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.5,
			'review_count'  => 2,
		),
		array(
			'id'            => 5,
			'name'          => 'JBL Flip 3 Splashproof Portable Bluetooth',
			'category'      => 'AUDIO',
			'price_html'    => '$69.59',
			'sale_price'    => '',
			'regular_price' => '69.59',
			'on_sale'       => false,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.0,
			'review_count'  => 0,
		),
		array(
			'id'            => 6,
			'name'          => 'Sony X850 Portable Wireless Speaker',
			'category'      => 'HEADPHONES',
			'price_html'    => '<del>$56.00</del> <ins>$39.99</ins>',
			'sale_price'    => '39.99',
			'regular_price' => '56.00',
			'on_sale'       => true,
			'image_url'     => $placeholder_svg,
			'permalink'     => home_url( '/shop' ),
			'rating'        => 4.0,
			'review_count'  => 2,
		),
	);

	$valid_tabs = array( 'new_arrivals', 'featured', 'on_sale' );

	// 3. Attempt live WooCommerce data when active and tab is valid.
	if ( gamtech_woo_active() && in_array( $tab, $valid_tabs, true ) ) {
		$args = array(
			'limit'  => $limit,
			'status' => 'publish',
			'return' => 'objects',
		);

		switch ( $tab ) {
			case 'new_arrivals':
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
			case 'featured':
				$args['featured'] = true;
				break;
			case 'on_sale':
				$args['on_sale'] = true;
				break;
		}

		$wc_products = wc_get_products( $args );

		if ( ! empty( $wc_products ) ) {
			$result = array();

			foreach ( $wc_products as $p ) {
				// Resolve category name.
				$category      = '';
				$product_terms = get_the_terms( $p->get_id(), 'product_cat' );
				if ( ! empty( $product_terms ) && ! is_wp_error( $product_terms ) ) {
					$category = $product_terms[0]->name;
				}

				// Resolve image URL.
				$image_url = wp_get_attachment_image_url( $p->get_image_id(), 'medium' );
				if ( empty( $image_url ) ) {
					$image_url = wc_placeholder_img_src( 'medium' );
				}

				$result[] = array(
					'id'            => $p->get_id(),
					'name'          => $p->get_name() ?: 'Unnamed Product',
					'category'      => $category,
					'price_html'    => $p->get_price_html(),
					'sale_price'    => $p->get_sale_price(),
					'regular_price' => $p->get_regular_price(),
					'on_sale'       => $p->is_on_sale(),
					'image_url'     => $image_url,
					'permalink'     => get_permalink( $p->get_id() ),
					'rating'        => max( 0, min( 5, (float) $p->get_average_rating() ) ),
					'review_count'  => $p->get_review_count(),
				);
			}

			set_transient( $cache_key, $result, 3600 );
			return $result;
		}
	}

	// 4. Return static fallback when WooCommerce is inactive, tab is invalid, or no products found.
	$result = array_slice( $static_fallback, 0, $limit );

	// 5. Cache and return.
	set_transient( $cache_key, $result, 3600 );
	return $result;
}

/**
 * Generates an HTML star-rating string for a given numeric rating.
 *
 * Outputs exactly 5 star elements with floor($rating) filled stars
 * and the remainder as empty stars, wrapped in a container span.
 *
 * @param float $rating Numeric rating value between 0.0 and 5.0.
 * @return string HTML string containing the star elements.
 */
function gamtech_get_star_html( float $rating ): string {
	// 1. Clamp rating to [0, 5].
	$rating = max( 0, min( 5, $rating ) );

	// 2. Number of filled stars.
	$filled = (int) floor( $rating );

	// 3. Build HTML string.
	$html = '<span class="gt-stars" aria-label="' . esc_attr( number_format( $rating, 1 ) ) . ' out of 5 stars">';

	for ( $i = 1; $i <= 5; $i++ ) {
		if ( $i <= $filled ) {
			$html .= '<span class="gt-star gt-star--filled" aria-hidden="true">★</span>';
		} else {
			$html .= '<span class="gt-star gt-star--empty" aria-hidden="true">☆</span>';
		}
	}

	$html .= '</span>';

	// 4. Return the HTML string.
	return $html;
}

// =============================================================================
// Product Card Render Helper
// =============================================================================

/**
 * Renders a single product card HTML.
 *
 * @param array $p ProductCard associative array.
 */
function gamtech_render_product_card( array $p ): void {
	?>
	<div class="gt-product-card">
		<?php if ( ! empty( $p['on_sale'] ) ) : ?>
			<span class="gt-sale-badge">Sale</span>
		<?php endif; ?>
		<a href="<?php echo esc_url( $p['permalink'] ); ?>" class="gt-product-card__img-wrap">
			<img src="<?php echo esc_url( $p['image_url'] ); ?>"
			     alt="<?php echo esc_attr( $p['name'] ); ?>"
			     loading="lazy">
		</a>
		<div class="gt-product-card__body">
			<?php if ( ! empty( $p['category'] ) ) : ?>
				<p class="gt-product-card__cat"><?php echo esc_html( $p['category'] ); ?></p>
			<?php endif; ?>
			<h3 class="gt-product-card__name">
				<a href="<?php echo esc_url( $p['permalink'] ); ?>"><?php echo esc_html( $p['name'] ); ?></a>
			</h3>
			<div class="gt-product-card__rating">
				<?php echo wp_kses_post( gamtech_get_star_html( $p['rating'] ) ); ?>
				<span class="gt-review-count">(<?php echo esc_html( $p['review_count'] ); ?>)</span>
			</div>
			<div class="gt-product-card__price"><?php echo wp_kses_post( $p['price_html'] ); ?></div>
		</div>
	</div>
	<?php
}

// =============================================================================
// Vanilla JS — Hero Slider, Product Tabs, Topbar Dismiss
// =============================================================================

/**
 * Enqueues the inline JavaScript for UI interactions.
 * Hooked into wp_enqueue_scripts at priority 10020 (after parent scripts).
 */
function gamtech_enqueue_inline_js(): void {
	// Register a dummy script handle so we can attach inline JS to it.
	wp_register_script( 'gamtech-ui', false, array(), null, true );
	wp_enqueue_script( 'gamtech-ui' );

	$js = <<<'JS'
(function () {
  'use strict';

  /* ── Hero Slider ─────────────────────────────────────────── */
  function initHeroSlider() {
    var slider = document.querySelector('.gt-hero-slider');
    if (!slider) return;

    var slides = slider.querySelectorAll('.gt-slide');
    var dots   = slider.querySelectorAll('.gt-slider-dot');
    var prev   = slider.querySelector('.gt-slider-prev');
    var next   = slider.querySelector('.gt-slider-next');

    if (slides.length < 2) return;

    var currentIdx = 0;
    var total      = slides.length;

    function showSlide(idx) {
      slides.forEach(function (s) { s.classList.remove('active'); });
      dots.forEach(function (d)   { d.classList.remove('active'); });
      slides[idx].classList.add('active');
      if (dots[idx]) dots[idx].classList.add('active');
    }

    prev && prev.addEventListener('click', function () {
      currentIdx = (currentIdx - 1 + total) % total;
      showSlide(currentIdx);
    });

    next && next.addEventListener('click', function () {
      currentIdx = (currentIdx + 1) % total;
      showSlide(currentIdx);
    });

    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () {
        currentIdx = i;
        showSlide(currentIdx);
      });
    });

    setInterval(function () {
      currentIdx = (currentIdx + 1) % total;
      showSlide(currentIdx);
    }, 5000);

    showSlide(0);
  }

  /* ── Product Tabs ────────────────────────────────────────── */
  function initProductTabs() {
    var tabLinks = document.querySelectorAll('.gt-tab-link');
    var panels   = document.querySelectorAll('.gt-tab-panel');

    if (!tabLinks.length) return;

    function activateTab(targetId) {
      tabLinks.forEach(function (t) {
        t.classList.toggle('active', t.dataset.target === targetId);
      });
      panels.forEach(function (p) {
        p.classList.toggle('active', p.id === targetId);
      });
    }

    tabLinks.forEach(function (tab) {
      tab.addEventListener('click', function () {
        activateTab(tab.dataset.target);
      });
    });

    // Activate first tab on load
    if (tabLinks[0]) activateTab(tabLinks[0].dataset.target);
  }

  /* ── Topbar Dismiss ──────────────────────────────────────── */
  function initTopbarDismiss() {
    var topbar   = document.querySelector('.gt-topbar');
    var closeBtn = document.querySelector('.gt-topbar-close');

    if (!topbar) return;

    if (localStorage.getItem('gt_topbar_dismissed') === 'true') {
      topbar.style.display = 'none';
      return;
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', function () {
        topbar.style.display = 'none';
        localStorage.setItem('gt_topbar_dismissed', 'true');
      });
    }
  }

  /* ── Boot ────────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    initHeroSlider();
    initProductTabs();
    initTopbarDismiss();
  });

}());
JS;

	wp_add_inline_script( 'gamtech-ui', $js );
}
add_action( 'wp_enqueue_scripts', 'gamtech_enqueue_inline_js', 10020 );

// =============================================================================
// No-JS CSS Fallback (noscript block for tab panels)
// =============================================================================

/**
 * Outputs a <noscript> block so the first tab panel is visible when JS is off.
 */
function gamtech_noscript_fallback(): void {
	echo '<noscript><style>.gt-tab-panel:first-of-type { display: grid; grid-template-columns: repeat(6, 1fr); gap: 15px; }</style></noscript>' . "\n";
}
add_action( 'wp_head', 'gamtech_noscript_fallback' );
