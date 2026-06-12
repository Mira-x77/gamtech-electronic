<?php
/**
 * Template Name: Gamtech Custom Homepage
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamtech - Dashboard</title>
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

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Layout Grid */
        .dashboard {
            display: flex;
            width: 100%;
            height: 100%;
            padding: 20px;
            gap: 20px;
        }

        /* --- Left Sidebar --- */
        .sidebar-left {
            width: 250px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .logo {
            font-size: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
        }
        .logo img { width: 30px; height: 30px; }

        .nav-menu { list-style: none; display: flex; flex-direction: column; gap: 10px; flex-grow: 1; }
        .nav-item {
            padding: 12px 16px;
            border-radius: 10px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            transition: 0.2s;
        }
        .nav-item.active {
            background: var(--purple);
            color: var(--white);
        }
        .nav-item:hover:not(.active) {
            background: var(--bg-color);
        }
        .badge {
            background: var(--red);
            color: white;
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
        }

        .special-offer {
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            border-radius: var(--radius);
            padding: 20px;
            color: white;
            text-align: center;
            margin-top: 20px;
        }
        .special-offer h4 { font-size: 14px; opacity: 0.9; }
        .special-offer h2 { font-size: 24px; margin: 8px 0; }
        .special-offer button {
            background: white;
            color: var(--purple);
            border: none;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 10px;
            cursor: pointer;
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
        }
        .search-bar {
            flex: 1;
            background: var(--white);
            padding: 12px 20px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
        }
        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
        }

        /* Hero Banner */
        .hero-banner {
            background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%);
            border-radius: var(--radius);
            padding: 40px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }
        .hero-text { max-width: 50%; position: relative; z-index: 2; }
        .hero-text span { background: rgba(255,255,255,0.3); padding: 4px 12px; border-radius: 20px; font-size: 12px; }
        .hero-text h1 { font-size: 36px; color: var(--text-main); margin: 16px 0; line-height: 1.2; }
        .hero-text p { color: var(--text-muted); margin-bottom: 24px; }
        .hero-btn { background: var(--white); color: var(--purple); padding: 12px 24px; border-radius: 24px; text-decoration: none; font-weight: 600; display: inline-block; }
        
        /* Categories Row */
        .categories-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .cat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
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
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        /* Promo Cards Row */
        .promo-row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .promo-card {
            flex: 1;
            padding: 20px;
            border-radius: var(--radius);
            background: var(--white);
            position: relative;
        }
        .promo-card.pink { background: #ffe4e6; }
        .promo-card.green { background: #dcfce7; }
        .promo-card.orange { background: #ffedd5; }
        .promo-card h3 { font-size: 16px; margin-bottom: 8px; }
        .promo-card p { font-size: 12px; color: var(--text-muted); margin-bottom: 12px; }
        .promo-card a { font-size: 12px; font-weight: 600; color: var(--text-main); text-decoration: none; }

        /* Products Section */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .section-header h2 { font-size: 18px; }
        .section-header a { font-size: 13px; color: var(--text-muted); text-decoration: none; font-weight: 500; background: var(--white); padding: 6px 12px; border-radius: 12px;}

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .product-card {
            background: var(--white);
            border-radius: var(--radius);
            padding: 16px;
            position: relative;
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
        }
        .product-card .heart {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--bg-color);
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 12px;
        }
        .product-image {
            width: 100%;
            height: 140px;
            object-fit: contain;
            margin-bottom: 16px;
            margin-top: 20px;
        }
        .product-card h4 { font-size: 13px; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-card .cat { font-size: 11px; color: var(--text-muted); margin-bottom: 8px; }
        .product-price { display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .product-price .current { font-weight: 700; font-size: 16px; }
        .product-price .old { font-size: 12px; color: var(--text-muted); text-decoration: line-through; }
        .product-rating { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        .product-rating span { color: #fbbf24; }

        /* --- Right Sidebar --- */
        .sidebar-right {
            width: 320px;
            background: var(--white);
            border-radius: var(--radius);
            padding: 24px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }
        .sidebar-right::-webkit-scrollbar { width: 4px; }

        .profile-top {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 16px;
            margin-bottom: 30px;
        }
        .profile-top .icon { color: var(--text-main); font-size: 18px; }
        .profile-top .avatar { width: 36px; height: 36px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        .profile-top .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .profile-top span { font-weight: 600; font-size: 14px; }

        .cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .cart-header h3 { font-size: 16px; }
        .cart-header .close { color: var(--text-muted); }

        .cart-item { display: flex; gap: 12px; margin-bottom: 16px; }
        .cart-item img { width: 60px; height: 60px; background: var(--bg-color); border-radius: 8px; object-fit: contain; padding: 4px; }
        .cart-item-info { flex: 1; }
        .cart-item-info h4 { font-size: 12px; margin-bottom: 2px; }
        .cart-item-info p { font-size: 11px; color: var(--text-muted); margin-bottom: 6px; }
        .cart-item-price { font-weight: 700; font-size: 14px; margin-bottom: 4px; }
        .qty-controls { display: flex; align-items: center; gap: 10px; font-size: 12px; }
        .qty-controls span { cursor: pointer; color: var(--text-muted); }

        .cart-summary { margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px; }
        .promo-code { display: flex; gap: 10px; margin-bottom: 20px; }
        .promo-code input { flex: 1; padding: 10px 14px; border: 1px solid var(--border); border-radius: 8px; font-size: 12px; }
        .promo-code button { background: var(--purple); color: white; border: none; padding: 0 16px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; color: var(--text-muted); }
        .summary-row.total { font-weight: 700; font-size: 16px; color: var(--text-main); margin-top: 10px; }
        .summary-row .discount-text { color: var(--red); }

        .checkout-btn { width: 100%; background: var(--purple); color: white; border: none; padding: 14px; border-radius: 12px; font-weight: 600; font-size: 14px; margin-top: 20px; display: flex; justify-content: space-between; }

        /* Club Banner */
        .club-banner {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border-radius: var(--radius);
            padding: 20px;
            color: white;
            margin-top: 30px;
            position: relative;
        }
        .club-banner h3 { font-size: 16px; margin-bottom: 8px; }
        .club-banner p { font-size: 12px; opacity: 0.9; margin-bottom: 16px; }
        .club-banner button { background: white; color: var(--purple); border: none; padding: 6px 16px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar-left">
        <div class="logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/favicon.ico" alt="Logo">
            Gamtech
        </div>
        
        <ul class="nav-menu">
            <a href="#" class="nav-item active">🏠 Home</a>
            <a href="/shop" class="nav-item">🗂️ Categories</a>
            <a href="/sale" class="nav-item">🔥 Deals <span class="badge">Hot</span></a>
            <a href="#" class="nav-item">✨ New Arrivals</a>
            <a href="#" class="nav-item">👍 Best Sellers</a>
            <a href="#" class="nav-item">💎 Brands</a>
        </ul>

        <div style="margin: 20px 0; border-top: 1px solid var(--border);"></div>

        <ul class="nav-menu">
            <a href="/my-account/orders" class="nav-item">📦 My Orders</a>
            <a href="#" class="nav-item">❤️ Wishlist</a>
            <a href="#" class="nav-item">🎟️ Coupons</a>
            <a href="#" class="nav-item">⚙️ Settings</a>
        </ul>

        <div class="special-offer">
            <h4>Special Offer</h4>
            <h2>Summer Sale<br>Up to 50% Off</h2>
            <button>Shop Now</button>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <div class="top-bar">
            <div class="search-bar">
                🔍 <input type="text" placeholder="Search for products, brands and more...">
            </div>
        </div>

        <div class="hero-banner">
            <div class="hero-text">
                <span>New Collection</span>
                <h1>Find Your Style,<br>Love Your Tech ✨</h1>
                <p>Discover the latest trends in electronics, gaming, and lifestyle.</p>
                <a href="/shop" class="hero-btn">Shop Now →</a>
            </div>
        </div>

        <div class="categories-row">
            <div class="cat-item"><div class="cat-icon">👕</div> Fashion</div>
            <div class="cat-item"><div class="cat-icon">💄</div> Beauty</div>
            <div class="cat-item"><div class="cat-icon">🎧</div> Electronics</div>
            <div class="cat-item"><div class="cat-icon">🪑</div> Home</div>
            <div class="cat-item"><div class="cat-icon">⚽</div> Sports</div>
            <div class="cat-item"><div class="cat-icon">grid</div> More</div>
        </div>

        <div class="promo-row">
            <div class="promo-card pink">
                <h3>Flash Sale</h3>
                <p>Limited time deals. Up to 70% Off</p>
                <a href="#">Shop now →</a>
            </div>
            <div class="promo-card green">
                <h3>Free Shipping</h3>
                <p>On orders over $50</p>
                <a href="#">Shop now →</a>
            </div>
            <div class="promo-card orange">
                <h3>New Arrivals</h3>
                <p>Check out the latest trends</p>
                <a href="#">Shop now →</a>
            </div>
        </div>

        <?php
        // Fetch real products to match user request "use the already images I had in the site before"
        $args = array('limit' => 8, 'status' => 'publish');
        $products = wc_get_products($args);
        $best_deals = array_slice($products, 0, 4);
        $recommended = array_slice($products, 4, 4);
        ?>

        <div class="section-header">
            <h2>Best Deals for You</h2>
            <a href="/shop">View All</a>
        </div>
        <div class="product-grid">
            <?php foreach($best_deals as $product): ?>
            <div class="product-card">
                <div class="discount">-20%</div>
                <div class="heart">♡</div>
                <?php $img_url = wp_get_attachment_url( $product->get_image_id() ); ?>
                <img src="<?php echo $img_url ? $img_url : 'https://via.placeholder.com/150'; ?>" alt="" class="product-image">
                <h4><?php echo $product->get_name(); ?></h4>
                <div class="cat">Gamtech Electronics</div>
                <div class="product-price">
                    <span class="current"><?php echo wc_price($product->get_price()); ?></span>
                    <span class="old"><?php echo wc_price((float)$product->get_price() * 1.2); ?></span>
                </div>
                <div class="product-rating"><span>★</span> 4.8 (124)</div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="section-header">
            <h2>Recommended for You</h2>
            <a href="/shop">View All</a>
        </div>
        <div class="product-grid">
            <?php foreach($recommended as $product): ?>
            <div class="product-card">
                <div class="discount">-15%</div>
                <div class="heart">♡</div>
                <?php $img_url = wp_get_attachment_url( $product->get_image_id() ); ?>
                <img src="<?php echo $img_url ? $img_url : 'https://via.placeholder.com/150'; ?>" alt="" class="product-image">
                <h4><?php echo $product->get_name(); ?></h4>
                <div class="cat">Gamtech Electronics</div>
                <div class="product-price">
                    <span class="current"><?php echo wc_price($product->get_price()); ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- RIGHT SIDEBAR -->
    <aside class="sidebar-right">
        <div class="profile-top">
            <div class="icon">♡</div>
            <div class="icon">🔔</div>
            <div class="avatar"><img src="https://ui-avatars.com/api/?name=User&background=random" alt=""></div>
            <span>User Profile ⌄</span>
        </div>

        <div class="cart-header">
            <h3>My Cart (<?php echo count($best_deals); ?>)</h3>
            <span class="close">✕</span>
        </div>

        <div class="cart-items">
            <?php 
            $subtotal = 0;
            foreach($best_deals as $product): 
                $subtotal += (float)$product->get_price();
                $img_url = wp_get_attachment_url( $product->get_image_id() );
            ?>
            <div class="cart-item">
                <img src="<?php echo $img_url ? $img_url : 'https://via.placeholder.com/150'; ?>" alt="">
                <div class="cart-item-info">
                    <h4><?php echo substr($product->get_name(), 0, 30); ?>...</h4>
                    <p>Size: Universal</p>
                    <div class="cart-item-price"><?php echo wc_price($product->get_price()); ?></div>
                    <div class="qty-controls">
                        <span>-</span> 1 <span>+</span> <span>🗑️</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="promo-code">
                <input type="text" placeholder="Promo Code">
                <button>Apply</button>
            </div>
            <div class="summary-row">
                <span>Subtotal</span>
                <span><?php echo wc_price($subtotal); ?></span>
            </div>
            <div class="summary-row">
                <span>Discount</span>
                <span class="discount-text">-<?php echo wc_price($subtotal * 0.1); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Free</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span><?php echo wc_price($subtotal * 0.9); ?></span>
            </div>
            <button class="checkout-btn">
                🔒 Checkout (<?php echo count($best_deals); ?>) <span>→</span>
            </button>
        </div>

        <div class="club-banner">
            <h3>Join Gamtech Club</h3>
            <p>Get exclusive offers, early access and more!</p>
            <button>Join Now</button>
        </div>
    </aside>
</div>

</body>
</html>
