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
            /* True Dark Theme for Tech Store */
            --bg-color: #09090b; 
            --white: #18181b; /* Card background */
            --accent: #eab308; /* Yellow/Gold for creative tech feel */
            --accent-light: rgba(234, 179, 8, 0.15);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --border: #27272a;
            --red: #ef4444;
            --radius: 16px;
        }

        body {
            background-color: var(--bg-color) !important;
            color: var(--text-main);
            font-family: 'Inter', sans-serif !important;
            overflow: hidden !important; 
            margin: 0;
            padding: 0;
        }

        /* Override Woodmart Header completely */
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
            width: 320px;
            min-width: 320px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            border: 1px solid var(--border);
        }
        .sidebar-left::-webkit-scrollbar { width: 4px; }
        .sidebar-left::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .logo-wrap {
            margin-bottom: 40px;
            display: flex;
            justify-content: center;
        }
        .logo-wrap img { 
            max-width: 280px !important; 
            width: 100%;
            height: auto; 
            display: block;
            margin: 0 auto;
            /* Using the light logo because the background is now dark */
            content: url("<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-light.png") !important;
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
            background: var(--accent);
            color: #000; /* Dark text on yellow background for high contrast */
        }
        .nav-item.active svg, .nav-item:hover svg { stroke: #000; }
        
        .nav-item:hover:not(.active) {
            background: var(--accent-light);
            color: var(--accent);
        }
        .nav-item:hover:not(.active) svg { stroke: var(--accent); }

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
            background: linear-gradient(135deg, #422006, #713f12); /* Techy gold/brown gradient */
            border: 1px solid #854d0e;
            border-radius: var(--radius);
            padding: 20px;
            color: var(--text-main);
            text-align: center;
            margin-top: 30px;
        }
        .special-offer h4 { font-size: 14px; opacity: 0.9; margin:0; color: var(--accent); }
        .special-offer h2 { font-size: 22px; margin: 8px 0; font-weight: 700; line-height: 1.2; }
        .special-offer a {
            background: var(--accent);
            color: #000;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            transition: 0.2s;
        }
        .special-offer a:hover { transform: translateY(-2px); }

        /* --- Main Content --- */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            border-radius: var(--radius);
            padding-right: 10px;
        }
        .main-content::-webkit-scrollbar { width: 6px; }
        .main-content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }

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
            border: 1px solid var(--border);
        }
        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            background: transparent;
            color: var(--text-main);
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
            border: 1px solid var(--border);
        }
        .sidebar-right::-webkit-scrollbar { width: 4px; }
        .sidebar-right::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        .profile-top {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
            margin-bottom: 30px;
        }
        .profile-top .icon { color: var(--text-main); }
        .profile-top .icon:hover { color: var(--accent); }
        .profile-top .avatar { width: 40px; height: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .profile-top .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-top span { font-weight: 600; font-size: 14px; }

        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cart-header h3 { font-size: 18px; font-weight: 700; margin: 0; }
        .cart-header .close { color: var(--text-muted); cursor: pointer; }
        .cart-header .close:hover { color: var(--accent); }

        .cart-item { display: flex; gap: 12px; margin-bottom: 20px; align-items: center; }
        .cart-item img { width: 64px; height: 64px; background: var(--bg-color); border-radius: 12px; object-fit: contain; padding: 4px; border: 1px solid var(--border); }
        .cart-item-info { flex: 1; }
        .cart-item-info h4 { font-size: 13px; margin: 0 0 4px 0; font-weight: 600; }
        .cart-item-info p { font-size: 11px; color: var(--text-muted); margin: 0 0 6px 0; }
        .cart-item-price { font-weight: 700; font-size: 14px; color: var(--accent); }
        .qty-controls { display: flex; align-items: center; gap: 12px; font-size: 12px; font-weight: 600; }
        .qty-controls span { color: var(--text-muted); }
        .qty-controls .remove { color: var(--red); margin-left: auto; }

        .cart-summary { margin-top: auto; border-top: 1px solid var(--border); padding-top: 24px; }
        .promo-code { display: flex; gap: 10px; margin-bottom: 20px; }
        .promo-code input { flex: 1; padding: 12px 16px; border: 1px solid var(--border); border-radius: 10px; font-size: 13px; outline: none; background: var(--bg-color); color: var(--text-main); }
        .promo-code button { background: var(--accent); color: #000; border: none; padding: 0 20px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .promo-code button:hover { background: #ca8a04; }
        
        .summary-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted); }
        .summary-row.total { font-weight: 700; font-size: 18px; color: var(--text-main); margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border); }
        .summary-row .discount-text { color: var(--red); }

        .checkout-btn { width: 100%; background: var(--accent); color: #000; border: none; padding: 16px; border-radius: 14px; font-weight: 600; font-size: 15px; margin-top: 24px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; text-decoration: none; transition: 0.2s;}
        .checkout-btn:hover { transform: translateY(-2px); }

        /* Club Banner */
        .club-banner {
            background: linear-gradient(135deg, #422006, #713f12);
            border: 1px solid #854d0e;
            border-radius: var(--radius);
            padding: 20px;
            color: white;
            margin-top: 24px;
            position: relative;
        }
        .club-banner h3 { font-size: 16px; margin-bottom: 8px; font-weight: 700; color: var(--accent); }
        .club-banner p { font-size: 12px; opacity: 0.9; margin-bottom: 16px; }
        .club-banner a { background: var(--accent); color: #000; border: none; padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; text-decoration: none; display: inline-block; transition: 0.2s; }
        .club-banner a:hover { transform: translateY(-2px); }

        /* --- CSS FOR HOMEPAGE COMPONENTS --- */
        .hero-banner {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); /* Deep tech blue/purple */
            border-radius: var(--radius);
            padding: 40px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
            border: 1px solid var(--border);
        }
        .hero-text { max-width: 60%; position: relative; z-index: 2; }
        .hero-text span { background: rgba(234, 179, 8, 0.2); color: var(--accent); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .hero-text h1 { font-size: 36px; color: var(--text-main); margin: 16px 0; line-height: 1.2; font-weight: 800; }
        .hero-text p { color: var(--text-muted); margin-bottom: 24px; }
        .hero-btn { background: var(--accent); color: #000; padding: 12px 24px; border-radius: 24px; text-decoration: none; font-weight: 600; display: inline-block; transition: 0.2s; }
        .hero-btn:hover { transform: translateY(-2px); }
        
        .categories-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .cat-item {
            flex: 1;
            min-width: 80px;
        }
        .cat-icon {
            width: 60px;
            height: 60px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            border: 1px solid var(--border);
            color: var(--accent);
            transition: 0.2s;
        }
        .cat-item a:hover .cat-icon {
            background: var(--accent-light);
            transform: translateY(-4px);
        }

        .promo-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .promo-card {
            padding: 20px;
            border-radius: var(--radius);
            background: var(--white);
            border: 1px solid var(--border);
            transition: 0.2s;
        }
        .promo-card:hover { transform: translateY(-4px); border-color: var(--accent); }
        .promo-card h3 { font-size: 16px; margin-bottom: 8px; color: var(--text-main); }
        .promo-card p { font-size: 12px; color: var(--text-muted); margin-bottom: 12px; }
        .promo-card a { font-size: 12px; font-weight: 600; color: var(--accent); text-decoration: none; }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .section-header h2 { font-size: 18px; color: var(--text-main); }
        .section-header a { font-size: 13px; color: var(--accent); text-decoration: none; font-weight: 600; background: var(--accent-light); padding: 6px 12px; border-radius: 12px;}

        .product-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 16px;
            position: relative;
            border: 1px solid var(--border);
            transition: 0.2s;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            border-color: var(--accent);
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .product-card .discount {
            position: absolute;
            top: 16px;
            left: 16px;
            background: var(--red);
            color: white;
            font-size: 10px;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 700;
            z-index: 2;
        }
        .product-card .heart {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0,0,0,0.5);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            z-index: 2;
            transition: 0.2s;
        }
        .product-card .heart:hover { color: var(--red); background: rgba(239,68,68,0.2); }
        .product-image {
            width: 100%;
            height: 140px;
            object-fit: contain;
            margin-bottom: 16px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .product-card h4 { font-size: 13px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-main); }
        .product-card .cat { font-size: 11px; color: var(--text-muted); margin-bottom: 8px; }
        .product-price { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .product-price .current { font-weight: 700; font-size: 16px; color: var(--accent); }
        .product-price .old { font-size: 12px; color: var(--text-muted); text-decoration: line-through; }
        .product-rating { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; margin-top: auto; }

        /* Mobile specific fixes */
        @media (max-width: 1200px) {
            .sidebar-right { display: none; }
            .product-grid { grid-template-columns: repeat(4, 1fr); }
            .promo-row { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 900px) {
            body { overflow: auto !important; }
            .website-wrapper.dashboard { display: block !important; height: auto !important; min-height: 100vh; padding: 0 !important; gap: 0 !important; }
            .sidebar-left { display: none; }
            .main-content { display: block !important; overflow: visible !important; height: auto !important; border-radius: 0; padding: 16px; padding-bottom: 80px; }
            .top-bar { margin-top: 10px; position: static; }
            .product-grid { grid-template-columns: repeat(2, 1fr) !important; gap: 12px; }
            .promo-row { grid-template-columns: 1fr; }
            .categories-row { padding-bottom: 15px; }
            .hero-images { display: none !important; }
            .hero-banner { padding: 24px; flex-direction: column; text-align: center; }
            .hero-text { max-width: 100%; }
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
                    <svg width="20" height="20" fill="none" stroke="var(--accent)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="search" name="s" placeholder="Search for products, brands and more..." value="<?php echo get_search_query(); ?>">
                    <input type="hidden" name="post_type" value="product" />
                </form>
            </div>
            
            <?php woodmart_page_top_part(); ?>
            <!-- WooCommerce content / Main page content begins here -->
