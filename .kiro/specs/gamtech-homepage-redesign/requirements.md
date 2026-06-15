# Requirements Document

## Introduction

This document specifies the functional and non-functional requirements for the Gamtech Electronic homepage redesign. The redesign replaces the bare Woodmart child theme with a complete Ogo-style electronics marketplace layout implemented across four child-theme files: `header.php`, `front-page.php`, `style.css`, and `functions.php`. All requirements are derived from the approved design document.

---

## Glossary

- **Child_Theme**: The `woodmart-child` WordPress theme located at `wp-content/themes/woodmart-child/`
- **Front_Page**: The WordPress `front-page.php` template rendered when the site homepage is loaded
- **Header**: The `header.php` template override rendered by `get_header()` from `front-page.php`
- **Hero_Slider**: The full-width image carousel at the top of the homepage body
- **Slider_JS**: The vanilla JavaScript module responsible for hero slider behaviour
- **Tab_JS**: The vanilla JavaScript module responsible for product tab switching behaviour
- **Topbar**: The dismissible promotional strip rendered at the very top of the page
- **Topbar_JS**: The vanilla JavaScript module responsible for topbar dismiss behaviour
- **WooCommerce**: The WooCommerce plugin providing product, category, and cart data
- **Data_Helper**: Any PHP function in `functions.php` that retrieves product or category data (e.g. `gamtech_get_products`, `gamtech_get_product_categories`)
- **ProductCard**: The uniform data structure representing a single product as defined in the design document
- **CategoryTile**: The uniform data structure representing a product category as defined in the design document
- **Static_Fallback**: Pre-defined PHP arrays that mirror the live WooCommerce data structure, used when WooCommerce is inactive
- **Poppins**: The Google Fonts typeface used throughout the redesign
- **Primary_Menu**: The WordPress menu registered at the `primary` nav location
- **Trust_Badges**: The five-column row of shipping/support/guarantee icons below the hero slider
- **Category_Strip**: The horizontally-scrollable row of product category tiles
- **Promo_Banners**: The three-column grid of promotional image/text banners
- **Product_Tabs**: The tabbed section containing New Arrivals, Featured, and On Sale product grids
- **Best_Sellers**: The section containing a large featured product card and a supporting product grid

---

## Requirements

---

### Requirement 1: Promo Topbar

**User Story:** As a site visitor, I want to see a promotional message at the top of the homepage, so that I am aware of current deals.

#### Acceptance Criteria

1. THE Header SHALL render a Topbar containing promotional text and a close (×) button above the main header row.
2. WHEN the close button is clicked, THE Topbar_JS SHALL hide the Topbar immediately and store a dismissal flag in `localStorage` under the key `gt_topbar_dismissed`.
3. WHEN the page loads and `localStorage` contains the key `gt_topbar_dismissed` with value `'true'`, THE Topbar_JS SHALL hide the Topbar before it becomes visible.
4. IF JavaScript is disabled, THEN THE Topbar SHALL remain visible and the close button SHALL be inert without causing a page error.

---

### Requirement 2: Main Header Row

**User Story:** As a site visitor, I want a clear header with a logo, search bar, contact information, and action icons, so that I can navigate and search the store efficiently.

#### Acceptance Criteria

1. THE Header SHALL render a main header row containing: a site logo (or site name), a search form, a phone/contact number, and icon links for Compare, Wishlist, and Cart.
2. WHEN WooCommerce is active, THE Header SHALL display the current cart item count as a numeric badge on the Cart icon using `WC()->cart->get_cart_contents_count()`.
3. IF WooCommerce is inactive, THEN THE Header SHALL display a static badge value of `0` on the Cart icon.
4. THE Header SHALL render the search form with a `GET` method targeting the site's root URL with parameter `s`, compatible with the WordPress native search.
5. THE Header SHALL include a "My Account" link pointing to the WooCommerce account page when WooCommerce is active, or to `wp-login.php` when WooCommerce is inactive.

---

### Requirement 3: Navigation Bar

**User Story:** As a site visitor, I want a navigation bar with category browsing and page links, so that I can find products and content easily.

#### Acceptance Criteria

1. THE Header SHALL render a navigation bar below the main header row containing a "Browse Categories" button and primary navigation links.
2. WHEN the Primary_Menu is assigned in WordPress, THE Header SHALL render it using `wp_nav_menu()` at the `primary` menu location.
3. IF no menu is assigned to the `primary` location, THEN THE Header SHALL render a fallback list of WordPress pages via `wp_nav_menu()`'s `fallback_cb`.
4. THE Header SHALL include a Login/Register link on the right side of the navigation bar.
5. THE Header SHALL output a `<div class="main-page-wrapper">` opening tag that is closed by the parent theme's `footer.php`.

---

### Requirement 4: Hero Slider

**User Story:** As a site visitor, I want a visually engaging hero slider at the top of the page, so that I can see featured products and promotions.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Hero_Slider containing at least one slide with a headline, sub-headline, call-to-action button, product image, and a promotional badge.
2. WHEN the Hero_Slider contains more than one slide, THE Slider_JS SHALL auto-advance to the next slide every 5 seconds.
3. WHEN a user clicks the previous arrow, THE Slider_JS SHALL display the preceding slide, wrapping from the first slide to the last.
4. WHEN a user clicks the next arrow, THE Slider_JS SHALL display the following slide, wrapping from the last slide to the first.
5. THE Slider_JS SHALL update dot navigation indicators to reflect the currently active slide index.
6. IF JavaScript is disabled, THEN THE Hero_Slider SHALL display the first slide via CSS default styles without JavaScript errors.

---

### Requirement 5: Trust Badges Row

**User Story:** As a site visitor, I want to see trust signals below the hero, so that I feel confident shopping on the site.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Trust_Badges row containing exactly five badges: Free Shipping, Support 24/7, 100% Money Back, 90 Days Return, and Payment Secure.
2. THE Front_Page SHALL render each badge with an icon and a short descriptive subtext.

---

### Requirement 6: Top Categories Section

**User Story:** As a site visitor, I want to browse product categories visually, so that I can quickly navigate to the type of product I want.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Category_Strip section with a heading and horizontally-scrollable category tiles.
2. WHEN WooCommerce is active, THE Front_Page SHALL populate the Category_Strip using `gamtech_get_product_categories(8)` to fetch up to 8 product categories.
3. IF WooCommerce is inactive, THEN THE Front_Page SHALL populate the Category_Strip using Static_Fallback category data with identical structure.
4. THE Front_Page SHALL render each category tile with a category image, label, and a link to the category archive page.
5. IF a category has no assigned thumbnail image, THEN THE Data_Helper SHALL substitute a generic placeholder image so no broken image is shown.
6. THE Front_Page SHALL visually highlight the first category tile as the active/selected state using a red border accent.

---

### Requirement 7: Promotional Banners

**User Story:** As a site visitor, I want to see featured promotional banners, so that I can discover highlighted deals and products.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Promo_Banners section containing exactly three promotional banners displayed in a three-column grid.
2. THE Front_Page SHALL render each banner with a background image or colour, promotional text, and a "Shop Now" call-to-action button.
3. THE Front_Page SHALL link each banner's "Shop Now" button to a relevant shop or category URL.

---

### Requirement 8: Product Tabs Section

**User Story:** As a site visitor, I want to browse products organised by New Arrivals, Featured, and On Sale tabs, so that I can discover relevant products quickly.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Product_Tabs section with three tab links: "New Arrivals", "Featured", and "On Sale".
2. WHEN a tab link is clicked, THE Tab_JS SHALL display the corresponding product panel and hide all other panels.
3. THE Tab_JS SHALL activate the "New Arrivals" tab by default on page load.
4. WHEN WooCommerce is active, THE Front_Page SHALL populate each tab panel by calling `gamtech_get_products($tab, 6)` with the appropriate tab identifier.
5. IF WooCommerce is inactive, THEN THE Front_Page SHALL populate tab panels using Static_Fallback product data.
6. IF no products are found for a given tab, THEN THE Front_Page SHALL render a "No products found" message inside that tab panel.
7. THE Front_Page SHALL render each product card with: product image, category label, product name, star rating with review count, and price (showing sale price and original price when on sale).

---

### Requirement 9: Best Sellers Section

**User Story:** As a site visitor, I want to see best-selling products with category filters, so that I can find popular products by type.

#### Acceptance Criteria

1. THE Front_Page SHALL render a Best_Sellers section with a heading and category filter tabs (e.g. Tv Televisions, Speakers, Air Conditions, Cameras & Videos, View All).
2. THE Front_Page SHALL render a large featured product card on the left side of the Best_Sellers section.
3. THE Front_Page SHALL render a supporting product grid on the right side of the Best_Sellers section.
4. WHEN WooCommerce is active, THE Front_Page SHALL populate Best_Sellers using `gamtech_get_products('featured', 5)`.
5. IF WooCommerce is inactive, THEN THE Front_Page SHALL use Static_Fallback product data for the Best_Sellers section.

---

### Requirement 10: Typography and Visual Theme

**User Story:** As a site visitor, I want a consistent, modern visual design, so that the site feels professional and trustworthy.

#### Acceptance Criteria

1. THE Child_Theme SHALL load the Poppins typeface from Google Fonts with weights 300, 400, 500, 600, and 700 via `wp_enqueue_style()` with `display=swap`.
2. THE Child_Theme SHALL define CSS custom properties for the primary red accent colour (`#e74c3c`), text colour (`#333333`), border colour (`#e0e0e0`), and background light colour (`#f5f5f5`).
3. THE Child_Theme SHALL apply the Poppins typeface as the default `font-family` for all body text and headings.
4. THE Child_Theme SHALL use the red accent colour (`#e74c3c`) for all interactive elements including buttons, active states, badges, and hover effects.

---

### Requirement 11: Responsive Layout

**User Story:** As a mobile site visitor, I want the homepage to be usable on small screens, so that I can browse the store on my phone.

#### Acceptance Criteria

1. THE Child_Theme SHALL apply a single responsive breakpoint at `768px` max-width where multi-column layouts collapse to a single column.
2. THE Child_Theme SHALL ensure the Hero_Slider height reduces from `500px` on desktop to `300px` on mobile.
3. THE Child_Theme SHALL ensure the Trust_Badges row stacks to a two-column grid on screens ≤ 768px.
4. THE Child_Theme SHALL ensure the Promo_Banners grid stacks to a single column on screens ≤ 768px.
5. THE Child_Theme SHALL ensure product grids reduce from six columns to two columns on screens ≤ 768px.

---

### Requirement 12: Data Helpers and WooCommerce Integration

**User Story:** As a developer, I want reliable data-fetching helpers with WooCommerce integration and static fallbacks, so that the page renders correctly regardless of plugin state.

#### Acceptance Criteria

1. THE Child_Theme SHALL implement `gamtech_woo_active()` that returns `true` if and only if the WooCommerce plugin is active.
2. THE Child_Theme SHALL implement `gamtech_get_product_categories(int $limit)` that returns an array of CategoryTile objects with length ≤ `$limit`.
3. THE Child_Theme SHALL implement `gamtech_get_products(string $tab, int $limit)` that accepts `$tab ∈ {'new_arrivals', 'featured', 'on_sale'}` and returns an array of ProductCard objects with length ≤ `$limit`.
4. WHEN WooCommerce is inactive, THE Data_Helper SHALL return Static_Fallback data with the same array keys as the live WooCommerce data.
5. THE Child_Theme SHALL implement `gamtech_get_star_html(float $rating)` that returns an HTML string containing exactly 5 star elements with `floor($rating)` filled stars.
6. IF `gamtech_get_products()` is called with an invalid `$tab` value, THEN THE Data_Helper SHALL return the Static_Fallback product data without triggering a PHP error.
7. THE Child_Theme SHALL implement transient caching for `gamtech_get_products()` and `gamtech_get_product_categories()` with a TTL of 3600 seconds to reduce database queries.

---

### Requirement 13: Asset Enqueueing and Theme Setup

**User Story:** As a developer, I want the child theme's assets properly enqueued and the theme properly configured, so that styles and scripts load correctly without conflicts.

#### Acceptance Criteria

1. THE Child_Theme SHALL enqueue `style.css` as a child stylesheet with `woodmart-style` as a dependency via `wp_enqueue_style()`.
2. THE Child_Theme SHALL enqueue the hero slider, product tab, and topbar dismiss JavaScript as a single file or inline script after the DOM is ready.
3. THE Child_Theme SHALL register a `primary` navigation menu location via `register_nav_menus()` in a `after_setup_theme` action hook.
4. THE Child_Theme SHALL register an image size named `gamtech-product` at 300×300 pixels (hard-cropped) for product thumbnails.
5. THE Child_Theme SHALL add `add_theme_support('woocommerce')` to declare WooCommerce compatibility.
6. THE Child_Theme SHALL NOT introduce any jQuery dependency; all JavaScript SHALL use vanilla ES6+.

---

### Requirement 14: Output Safety and Code Quality

**User Story:** As a developer, I want all template output to be properly escaped, so that the site is protected against XSS vulnerabilities.

#### Acceptance Criteria

1. THE Child_Theme SHALL escape all dynamic string output using the appropriate WordPress escaping function: `esc_html()` for text nodes, `esc_url()` for URLs, `esc_attr()` for HTML attributes, and `wp_kses_post()` for trusted HTML content.
2. THE Child_Theme SHALL NOT execute any raw SQL queries; all data retrieval SHALL use WordPress or WooCommerce API functions.
3. THE Child_Theme SHALL NOT store any personally identifiable information in `localStorage`; only UI state flags SHALL be stored client-side.
4. WHEN `wp_nav_menu()` is called in the Header, THE Header SHALL provide a `fallback_cb` parameter so navigation is always rendered.
