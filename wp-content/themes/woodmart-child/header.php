<?php
/**
 * The custom dashboard Header template for Gamtech theme
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #f7f8fc;
            --white: #ffffff;
            --purple: #7c3aed;
            --purple-light: #f0eaff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --red: #ef4444;
            --radius: 16px;
        }

        body {
            background-color: var(--bg-color) !important;
            color: var(--text-main);
            font-family: 'Inter', sans-serif !important;
            overflow: hidden !important; /* The dashboard is fixed, columns scroll */
        }

        /* Override Woodmart Header completely if it tries to render */
        .whb-general-header, .whb-top-bar, .woodmart-prefooter, .footer-container { display: none !important; }

        /* Layout Grid */
        .website-wrapper.dashboard {
            display: flex;
            width: 100vw;
            height: 100vh;
            padding: 20px;
            gap: 20px;
            box-sizing: border-box;
            background-color: var(--bg-color);
        }

        /* --- Left Sidebar --- */
        .sidebar-left {
            width: 250px;
            min-width: 250px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .sidebar-left::-webkit-scrollbar { width: 4px; }

        .logo-wrap {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }
        .logo-wrap img { 
            max-width: 180px !important; 
            height: auto; 
            /* Since background is white, use the dark logo */
            content: url("<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-dark.png") !important;
        }

        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 10px; flex-grow: 1; padding: 0; margin: 0; }
        .nav-item {
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            transition: 0.2s;
            font-size: 14px;
        }
        .nav-item svg { width: 20px; height: 20px; }
        
        /* Make current page active */
        .nav-item.active, .nav-item:hover {
            background: var(--purple);
            color: var(--white);
        }
        .nav-item:hover:not(.active) {
            background: var(--purple-light);
            color: var(--purple);
        }

        .badge {
            background: var(--red);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
            font-weight: 700;
        }

        .special-offer {
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            border-radius: var(--radius);
            padding: 20px;
            color: white;
            text-align: center;
            margin-top: 30px;
        }
        .special-offer h4 { font-size: 14px; opacity: 0.9; margin:0; }
        .special-offer h2 { font-size: 22px; margin: 8px 0; font-weight: 700; line-height: 1.2; }
        .special-offer a {
            background: white;
            color: var(--purple);
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
        }

        /* --- Main Content --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            border-radius: var(--radius);
            padding-right: 10px; /* scrollbar spacing */
        }
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }

        /* Top Bar */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            background: var(--bg-color);
            padding-bottom: 10px;
        }
        .search-bar {
            flex: 1;
            background: var(--white);
            padding: 14px 24px;
            border-radius: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            background: transparent;
        }

        /* --- Right Sidebar --- */
        .sidebar-right {
            width: 320px;
            min-width: 320px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .sidebar-right::-webkit-scrollbar { width: 4px; }

        .profile-top {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
            margin-bottom: 30px;
        }
        .profile-top .icon { color: var(--text-main); }
        .profile-top .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .profile-top .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-top span { font-weight: 600; font-size: 14px; }

        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cart-header h3 { font-size: 18px; font-weight: 700; margin: 0; }
        .cart-header .close { color: var(--text-muted); cursor: pointer; }

        .cart-item { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
        .cart-item img { width: 64px; height: 64px; background: var(--bg-color); border-radius: 12px; object-fit: contain; padding: 4px; }
        .cart-item-info { flex: 1; }
        .cart-item-info h4 { font-size: 13px; margin: 0 0 4px 0; font-weight: 600; }
        .cart-item-info p { font-size: 11px; color: var(--text-muted); margin: 0 0 6px 0; }
        .cart-item-price { font-weight: 700; font-size: 14px; color: var(--purple); }
        .qty-controls { display: flex; align-items: center; gap: 12px; font-size: 12px; font-weight: 600; }
        .qty-controls span { cursor: pointer; color: var(--text-muted); }
        .qty-controls .remove { color: var(--red); margin-left: auto; }

        .cart-summary { margin-top: auto; border-top: 1px solid var(--border); padding-top: 24px; }
        .promo-code { display: flex; gap: 10px; margin-bottom: 20px; }
        .promo-code input { flex: 1; padding: 12px 16px; border: 1px solid var(--border); border-radius: 10px; font-size: 13px; outline: none; }
        .promo-code button { background: var(--purple); color: white; border: none; padding: 0 20px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; }
        
        .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted); }
        .summary-row.total { font-weight: 700; font-size: 18px; color: var(--text-main); margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border); }
        .summary-row .discount-text { color: var(--red); }

        .checkout-btn { width: 100%; background: var(--purple); color: white; border: none; padding: 16px; border-radius: 14px; font-weight: 600; font-size: 15px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; text-decoration: none; }
        .checkout-btn:hover { background: #6d28d9; }

        /* Club Banner */
        .club-banner {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border-radius: var(--radius);
            padding: 20px;
            color: white;
            margin-top: 24px;
            position: relative;
        }
        .club-banner h3 { font-size: 16px; margin-bottom: 8px; font-weight: 700; }
        .club-banner p { font-size: 12px; opacity: 0.9; margin-bottom: 16px; }
        .club-banner a { background: white; color: var(--purple); border: none; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; }

        /* Mobile specific fixes */
        @media (max-width: 1200px) {
            .sidebar-right { display: none; }
        }
        @media (max-width: 900px) {
            .sidebar-left { display: none; }
            .website-wrapper.dashboard { padding: 10px; }
        }
    </style>
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'woodmart_after_body_open' ); ?>
	
	<div class="website-wrapper dashboard">
        
        <!-- LEFT SIDEBAR -->
        <aside class="sidebar-left">
            <div class="logo-wrap">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <img src="" alt="Gamtech">
                </a>
            </div>
            
            <ul class="nav-menu">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="nav-item <?php echo is_front_page() ? 'active' : ''; ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    Home
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="nav-item <?php echo is_shop() ? 'active' : ''; ?>">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Categories
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8.5 14.5A2.5 2.5 0 0011 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 11-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 002.5 2.5z"></path></svg>
                    Deals <span class="badge">Hot</span>
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=date" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    New Arrivals
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?orderby=popularity" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                    Best Sellers
                </a>
            </ul>

            <div style="margin: 24px 0; border-top: 1px solid var(--border);"></div>

            <ul class="nav-menu">
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    My Orders
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    Wishlist
                </a>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="nav-item">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Settings
                </a>
            </ul>

            <div class="special-offer">
                <h4>Special Offer</h4>
                <h2>Gamtech<br>Mega Sale</h2>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>?on_sale=1">Shop Now</a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="top-bar">
                <form role="search" method="get" class="search-bar" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="search" name="s" placeholder="Search for products, brands and more..." value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="post_type" value="product" />
                </form>
            </div>
            
            <?php woodmart_page_top_part(); ?>
            <!-- WooCommerce content / Main page content begins here -->
