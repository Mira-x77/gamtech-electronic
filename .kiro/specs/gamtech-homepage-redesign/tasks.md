# Implementation Plan: Gamtech Electronic Homepage Redesign (Ogo-Style)

## Overview

Implement the Ogo-style homepage redesign by creating/modifying four files in the `woodmart-child` theme: `functions.php`, `style.css`, `header.php`, and `front-page.php`. The implementation follows the design document's component breakdown, builds incrementally from core helpers through to the full page assembly, and integrates WooCommerce data with static fallbacks throughout.

---

## Tasks

- [x] 1. Set up `functions.php` — theme registration, asset enqueueing, and helper scaffolding
  - Replace the current `functions.php` with the full Gamtech child theme setup
  - Register `primary` nav menu location via `register_nav_menus()` in `after_setup_theme`
  - Register `gamtech-product` image size (300×300, hard-cropped)
  - Add `add_theme_support('woocommerce')` declaration
  - Enqueue `style.css` with `woodmart-style` as dependency (keep existing `woodmart_child_enqueue_styles` but rename/update)
  - Enqueue Poppins from Google Fonts (weights 300,400,500,600,700) with `display=swap` via `wp_enqueue_style()`
  - Stub out all helper function signatures with empty bodies: `gamtech_woo_active()`, `gamtech_get_product_categories()`, `gamtech_get_products()`, `gamtech_get_star_html()`
  - Do NOT enqueue slider/tab JS yet (done in task 3)
  - _Requirements: 13.1, 13.3, 13.4, 13.5, 13.6, 10.1_

- [x] 2. Implement PHP data helpers in `functions.php`
  - [x] 2.1 Implement `gamtech_woo_active() : bool`
    - Return `class_exists('WooCommerce')` or `function_exists('WC')`
    - _Requirements: 12.1_

  - [-] 2.2 Write unit tests for `gamtech_woo_active()`
    - Test returns `true` when WooCommerce class exists (mock or actual active)
    - Test returns `false` when WooCommerce class absent

  - [x] 2.3 Implement `gamtech_get_product_categories(int $limit = 8) : array`
    - When `gamtech_woo_active()`: call `get_terms(['taxonomy' => 'product_cat', 'number' => $limit, 'hide_empty' => false])`
    - Map each `WP_Term` to CategoryTile array: `slug`, `name`, `count`, `image_url` (from term meta `thumbnail_id` → `wp_get_attachment_image_url`, fallback to inline SVG placeholder), `link` via `get_term_link()`
    - Wrap in `get_transient` / `set_transient` with key `'gamtech_categories_' . $limit` and TTL 3600
    - When WooCommerce inactive: return static fallback array of 8 category tiles (TV, AC, Washing Machine, Audio, Office, Car, Gaming, Cameras) with identical keys
    - _Requirements: 6.2, 6.3, 6.4, 6.5, 12.2, 12.4, 12.7_

  - [~] 2.4 Write property test for `gamtech_get_product_categories()`
    - **Property 4: Category Array Is Complete**
    - For any `$limit ∈ [1, 20]`, assert `count(result) <= $limit` and every element has non-empty `name`, `slug`, `link`, `image_url`
    - Test both WC-active and WC-inactive code paths
    - **Validates: Requirements 6.2, 6.4, 6.5, 12.2**

  - [x] 2.5 Implement `gamtech_get_products(string $tab, int $limit = 6) : array`
    - When `gamtech_woo_active()`: build `wc_get_products()` args based on `$tab`:
      - `'new_arrivals'` → `orderby: 'date', order: 'DESC'`
      - `'featured'` → tax query on `product_visibility` `featured`
      - `'on_sale'` → `post__in: wc_get_product_ids_on_sale()`
      - Invalid `$tab` → return static fallback (no PHP error)
    - Map each `WC_Product` to ProductCard array (all fields per design)
    - Wrap in `get_transient` / `set_transient` with key `'gamtech_products_' . $tab . '_' . $limit` and TTL 3600
    - When WooCommerce inactive: return static fallback of 6 product cards with identical keys
    - Clamp `rating` to `[0, 5]` using `max(0, min(5, $rating))`
    - Use `wc_placeholder_img_src()` when product image is absent
    - _Requirements: 8.4, 8.5, 12.3, 12.4, 12.6, 12.7_

  - [~] 2.6 Write property tests for `gamtech_get_products()`
    - **Property 1: Product Fetch Respects Limit**
    - For any `$tab ∈ {'new_arrivals','featured','on_sale'}` and `$limit ∈ [1, 12]`, assert `count(result) <= $limit`
    - **Validates: Requirements 12.3**
    - **Property 2: Product Cards Are Complete**
    - For any product in the returned array, assert `image_url` is non-empty string and `rating ∈ [0, 5]`
    - **Validates: Requirements 8.7, 12.3**
    - **Property 5: Static Fallback Structural Equivalence**
    - Assert that static fallback array keys are identical to live WC product array keys
    - **Validates: Requirements 6.3, 8.5, 12.4**

  - [x] 2.7 Implement `gamtech_get_star_html(float $rating) : string`
    - Loop 1 to 5: output `<span class="star filled">★</span>` when `$i <= floor($rating)`, else `<span class="star empty">☆</span>`
    - Wrap in `<span class="gt-stars">` container
    - Return escaped HTML string (use `ob_start` / `ob_get_clean` or string concat)
    - _Requirements: 12.5_

  - [~] 2.8 Write property test for `gamtech_get_star_html()`
    - **Property 3: Star HTML Is Well-Formed**
    - For any `$rating ∈ [0.0, 5.0]` (test with values 0, 1, 2.5, 3, 4.7, 5), assert output contains exactly 5 star `<span>` elements with `floor($rating)` having class `filled`
    - **Validates: Requirements 12.5**

- [x] 3. Checkpoint — Verify data helpers
  - Ensure all tests pass, ask the user if questions arise.
  - Manually verify in a browser (or via `wp eval`) that `gamtech_get_product_categories(8)` and `gamtech_get_products('new_arrivals', 6)` return correct data structures with WooCommerce active and inactive

- [-] 4. Implement `style.css` — full Ogo-style stylesheet
  - [-] 4.1 Write CSS reset, custom properties, and base typography
    - Define all `:root` CSS custom properties from the design (`--gt-red`, `--gt-text`, `--gt-border`, etc.)
    - Apply `font-family: var(--gt-font)` to `body` and headings
    - Basic reset: `box-sizing: border-box`, margin/padding resets
    - _Requirements: 10.2, 10.3, 10.4_

  - [-] 4.2 Write header styles (topbar, main header row, navbar)
    - Topbar: full-width, red background, white text, flex layout with close button right-aligned
    - Main header: white background, three-column flex (logo | search | actions), search button red
    - Cart badge: red circle, white text, positioned absolute on cart icon
    - Navbar: light grey background, flex row, `Browse Categories` button with red left-border accent
    - Dropdown menus: hidden by default, shown on hover/focus
    - _Requirements: 1.1, 2.1, 3.1_

  - [-] 4.3 Write hero slider styles
    - `.gt-hero-slider`: `position: relative; overflow: hidden; height: 500px`
    - `.gt-slide`: `position: absolute; width: 100%; height: 100%; opacity: 0; transition: opacity 0.5s`
    - `.gt-slide.active`: `opacity: 1; position: relative`
    - Prev/next arrows: white circles, positioned absolute left/right center
    - Dot indicators: small circles, bottom center, red when active
    - Save badge: red circle, white text, positioned absolute
    - _Requirements: 4.1, 4.6_

  - [x] 4.4 Write trust badges, category strip, and promo banner styles
    - Trust badges: 5-column flex row, icon above text, grey border separators
    - Category strip: `display: flex; overflow-x: auto; gap: 16px`, each tile `min-width: 120px`, active tile red border
    - Promo banners: `display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px`, each banner relative positioned with overlay text
    - _Requirements: 5.1, 5.2, 6.1, 7.1, 7.2_

  - [x] 4.5 Write product card, product tabs, and best-seller styles
    - Product card: white background, subtle shadow on hover, image zoom `transform: scale(1.05)`, price red
    - Product tabs: tab links row, active link with red underline, panels hidden/shown via `.active` class
    - Best-seller: CSS grid `1fr 3fr` (large card left, grid right)
    - Star rating: inline flex, filled stars gold/yellow, empty stars grey
    - Sale badge: red background, white text, positioned on product image
    - _Requirements: 8.7, 9.2, 9.3_

  - [x] 4.6 Write responsive breakpoints
    - At `@media (max-width: 768px)`:
      - Hero height: `300px`
      - Trust badges: `grid-template-columns: repeat(2, 1fr)`
      - Promo banners: `grid-template-columns: 1fr`
      - Product grid: `grid-template-columns: repeat(2, 1fr)`
      - Header: stack to single column, search bar full-width
      - Best-seller: single column
    - _Requirements: 11.1, 11.2, 11.3, 11.4, 11.5_

- [~] 5. Implement `header.php` — complete Ogo-style header override
  - Begin with `<!DOCTYPE html>`, `<html>`, `<head>`, `<?php wp_head(); ?>`, `<body <?php body_class(); ?>>` boilerplate
  - Render Topbar: promo message text + close button with class `gt-topbar-close`
  - Render main header row: `bloginfo('name')` logo, search form (`method="get"` action `home_url('/')`), phone placeholder, Compare/Wishlist/Cart icons
  - Render cart badge: WC conditional — `WC()->cart->get_cart_contents_count()` or `0`
  - Render "My Account" link: `wc_get_page_permalink('myaccount')` when WC active, else `wp_login_url()`
  - Render navbar: "Browse Categories" button + `wp_nav_menu(['theme_location' => 'primary', 'fallback_cb' => 'wp_page_menu', ...])` + Login/Register link
  - Open `<div class="main-page-wrapper">` (closed by parent footer.php)
  - Escape all output: `esc_html()`, `esc_url()`, `esc_attr()`
  - _Requirements: 1.1, 2.1, 2.2, 2.3, 2.4, 2.5, 3.1, 3.2, 3.3, 3.4, 3.5, 14.1, 14.4_

- [ ] 6. Implement `front-page.php` — full homepage template
  - [~] 6.1 Scaffold front-page.php with get_header() / get_footer() and section function calls
    - Call `get_header()`, then each section render function in order, then `get_footer()`
    - Define static hero slides array (2–3 slides with placeholder image paths)
    - _Requirements: 4.1_

  - [~] 6.2 Implement Hero Slider section
    - Render `.gt-hero-slider` wrapper with prev/next arrows and dot indicators
    - Loop through slides array outputting `.gt-slide` divs with badge, headline, sub-headline, CTA button, and image
    - First slide gets `active` class by default (CSS fallback for no-JS)
    - _Requirements: 4.1, 4.6_

  - [~] 6.3 Implement Trust Badges section
    - Render 5 hardcoded trust badge items with SVG icons inline
    - Each badge: icon + title + subtext
    - _Requirements: 5.1, 5.2_

  - [~] 6.4 Implement Top Categories section
    - Call `gamtech_get_product_categories(8)`, loop to render category tiles
    - First tile gets `active` CSS class
    - Image, name, and `<a>` link for each tile
    - Escape: `esc_url($cat['link'])`, `esc_html($cat['name'])`, `esc_url($cat['image_url'])`
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.6, 14.1_

  - [~] 6.5 Implement Promotional Banners section
    - Render 3 hardcoded banner items with background image/colour, text overlay, and Shop Now button
    - Use `esc_url()` on all CTA URLs
    - _Requirements: 7.1, 7.2, 7.3_

  - [~] 6.6 Implement Product Tabs section
    - Render 3 tab links (`data-target="gt-tab-new"`, `gt-tab-featured`, `gt-tab-sale`) with first marked `active`
    - Fetch: `$new = gamtech_get_products('new_arrivals', 6)`, `$featured = gamtech_get_products('featured', 6)`, `$sale = gamtech_get_products('on_sale', 6)`
    - For each tab panel: loop products to render product cards, or render "No products found" if array is empty
    - Product card: image, category, name, star HTML via `gamtech_get_star_html($p['rating'])`, price HTML
    - Escape all output per 14.1
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7, 14.1_

  - [~] 6.7 Implement Best Sellers section
    - Render heading + category filter tabs (hardcoded labels)
    - Fetch `$bestsellers = gamtech_get_products('featured', 5)`
    - First product → large featured card (left column)
    - Remaining 4 products → right-side product grid
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [ ] 7. Implement vanilla JS — slider, tabs, and topbar dismiss in `functions.php`
  - [~] 7.1 Write `initHeroSlider()` JavaScript function
    - Implement per the algorithmic pseudocode in the design document
    - Handle `slides.length < 2` guard (return early)
    - Bind prev/next click handlers with modular wrap-around arithmetic
    - `setInterval` auto-advance every 5000 ms
    - Update dot `active` class on every transition
    - _Requirements: 4.2, 4.3, 4.4, 4.5_

  - [~] 7.2 Write property test for Slider_JS index invariant
    - **Property 6: Slider Index Invariant**
    - Using jsdom or a simple JS test harness, for any N (2–10) slides and any random sequence of 20 prev/next clicks, assert `currentIndex` is always in `[0, N-1]` and exactly one dot is `active`
    - **Validates: Requirements 4.3, 4.4, 4.5**

  - [~] 7.3 Write `initProductTabs()` JavaScript function
    - Implement per the algorithmic pseudocode in the design document
    - Bind click handler to each `.gt-tab-link`
    - On click: remove `active` from all tabs/panels, add `active` to clicked tab and its target panel
    - Activate first tab on DOMContentLoaded
    - _Requirements: 8.2, 8.3_

  - [~] 7.4 Write property test for Tab_JS mutual exclusivity
    - **Property 7: Tab Mutual Exclusivity**
    - For any number of tabs T (2–6) and any sequence of tab clicks, assert exactly 1 tab link and exactly 1 panel have `active` class
    - **Validates: Requirements 8.2**

  - [~] 7.5 Write `initTopbarDismiss()` JavaScript function
    - Implement per the algorithmic pseudocode in the design document
    - On load: check `localStorage.getItem('gt_topbar_dismissed') === 'true'` and hide topbar if true
    - On close button click: hide topbar, set `localStorage` flag
    - _Requirements: 1.2, 1.3, 14.3_

  - [~] 7.6 Enqueue the JS in `functions.php`
    - Add `wp_add_inline_script()` or `wp_enqueue_script()` to load the three JS functions
    - Wrap all three init calls in a `DOMContentLoaded` listener in the inline script
    - Use `wp_enqueue_scripts` hook with priority after Woodmart parent scripts
    - _Requirements: 13.2, 13.6_

- [~] 8. Checkpoint — End-to-end integration
  - Ensure all tests pass, ask the user if questions arise.
  - Load the homepage in a browser and verify:
    - Header renders with topbar, main row, and navbar
    - Hero slider auto-advances and arrows work
    - Category strip scrolls horizontally
    - Product tabs switch correctly
    - Best sellers section displays
    - No JS console errors
    - No PHP errors or notices in debug log

- [ ] 9. Final wiring and polish
  - [~] 9.1 Add `wp_body_open()` call after `<body>` tag in `header.php` for plugin compatibility
    - Required for WooCommerce and other plugins that hook into `wp_body_open`
    - _Requirements: 13.5_

  - [~] 9.2 Verify all output escaping in `header.php` and `front-page.php`
    - Audit every `echo` statement for correct escaping function
    - Confirm no raw `$_GET`/`$_POST` variables are echoed
    - _Requirements: 14.1, 14.2_

  - [~] 9.3 Add no-JS CSS fallback rules to `style.css`
    - `.gt-tab-panel { display: none }` → `.gt-tab-panel.active { display: grid }`
    - `noscript` fallback: `.gt-tab-panel:first-of-type { display: grid }` in a `<noscript>` style block
    - _Requirements: 4.6, 8.2_

  - [~] 9.4 Wire hero slide images to use `get_stylesheet_directory_uri()` for asset paths
    - Update static slide array in `front-page.php` to reference child theme image paths
    - Ensure paths use `esc_url()` before output
    - _Requirements: 4.1, 14.1_

- [~] 10. Final checkpoint — All tests pass and site is ready
  - Ensure all tests pass, ask the user if questions arise.

---

## Notes

- Tasks marked with `*` are optional and can be skipped for a faster MVP
- All four files (`functions.php`, `style.css`, `header.php`, `front-page.php`) must exist for the homepage to render correctly
- The parent Woodmart `footer.php` closes the `<div class="main-page-wrapper">` opened in `header.php` — this coupling must not be broken
- Hero slide images are placeholders until real product photography is provided; the design uses CSS background colours as fallbacks
- WooCommerce transient caches should be cleared after WooCommerce data changes (handled automatically by WC hooks)
- The `primary` nav menu location must be assigned in WordPress admin (Appearance → Menus) for navigation to render dynamically

## Task Dependency Graph

```json
{
  "waves": [
    {"wave": 1, "tasks": ["1"]},
    {"wave": 2, "tasks": ["2.1", "2.3", "2.5", "2.7"]},
    {"wave": 3, "tasks": ["3"]},
    {"wave": 4, "tasks": ["4.1", "4.2", "4.3", "4.4", "4.5", "4.6"]},
    {"wave": 5, "tasks": ["5"]},
    {"wave": 6, "tasks": ["6.1", "6.2", "6.3", "6.4", "6.5", "6.6", "6.7"]},
    {"wave": 7, "tasks": ["7.1", "7.3", "7.5", "7.6"]},
    {"wave": 8, "tasks": ["8"]},
    {"wave": 9, "tasks": ["9.1", "9.2", "9.3", "9.4"]},
    {"wave": 10, "tasks": ["10"]}
  ]
}
```
