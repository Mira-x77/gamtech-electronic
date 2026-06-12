<?php
/**
 * Template Name: Gamtech Custom Homepage
 */

get_header(); ?>

<div class="gt-custom-homepage">
    <!-- Hero Section -->
    <section class="gt-hero-section">
        <div class="gt-hero-container">
            <div class="gt-hero-content">
                <h1 class="gt-hero-title">Welcome to <span class="gt-highlight">Gamtech</span></h1>
                <p class="gt-hero-subtitle">Discover the future of electronics and gaming gear. Premium quality, unparalleled performance.</p>
                <div class="gt-hero-actions">
                    <a href="/shop" class="gt-btn gt-btn-primary">Shop Now</a>
                    <a href="/about" class="gt-btn gt-btn-secondary">Learn More</a>
                </div>
            </div>
            <div class="gt-hero-images">
                <div class="gt-hero-image-wrapper gt-floating">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero1.png" alt="Electronics Showcase" class="gt-hero-img">
                </div>
                <div class="gt-hero-image-wrapper gt-hero-img-2 gt-floating-delay">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero2.png" alt="Gamtech Gear" class="gt-hero-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="gt-section gt-categories-section">
        <div class="container">
            <h2 class="gt-section-title">Shop by Category</h2>
            <div class="gt-categories-grid">
                <?php echo do_shortcode('[product_categories number="4" columns="4" hide_empty="0" orderby="count" order="DESC"]'); ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="gt-section gt-featured-section">
        <div class="container">
            <h2 class="gt-section-title">Trending Now</h2>
            <div class="gt-products-grid">
                <?php echo do_shortcode('[featured_products per_page="4" columns="4"]'); ?>
            </div>
        </div>
    </section>

    <!-- Deal of the Day / Countdown -->
    <section class="gt-section gt-deal-section">
        <div class="container">
            <div class="gt-deal-box">
                <div class="gt-deal-content">
                    <h2>Deal of the Week</h2>
                    <p>Get up to 50% off on selected gaming accessories. Limited time offer.</p>
                    <div class="gt-countdown" id="gt-countdown">
                        <div class="gt-time-box"><span id="days">00</span><small>Days</small></div>
                        <div class="gt-time-box"><span id="hours">00</span><small>Hours</small></div>
                        <div class="gt-time-box"><span id="mins">00</span><small>Mins</small></div>
                        <div class="gt-time-box"><span id="secs">00</span><small>Secs</small></div>
                    </div>
                    <a href="/sale" class="gt-btn gt-btn-primary mt-4">Shop the Sale</a>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Scoped CSS for Custom Homepage */
.gt-custom-homepage {
    background: var(--gt-bg-primary);
    color: var(--gt-text-primary);
    overflow: hidden;
}

/* Hero Section */
.gt-hero-section {
    position: relative;
    padding: 100px 0;
    min-height: 80vh;
    display: flex;
    align-items: center;
    background: radial-gradient(circle at top right, rgba(0, 242, 254, 0.1), transparent 50%),
                radial-gradient(circle at bottom left, rgba(124, 58, 237, 0.1), transparent 50%);
}

.gt-hero-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
}

.gt-hero-content {
    flex: 1;
    max-width: 600px;
    z-index: 2;
}

.gt-hero-title {
    font-size: 4.5rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
    background: linear-gradient(to right, #ffffff, #b3b3b3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.gt-highlight {
    background: linear-gradient(to right, #00f2fe, #4facfe);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.gt-hero-subtitle {
    font-size: 1.25rem;
    color: var(--gt-text-secondary);
    margin-bottom: 40px;
    line-height: 1.6;
}

.gt-hero-actions {
    display: flex;
    gap: 20px;
}

.gt-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 14px 32px;
    border-radius: 30px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--gt-transition);
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 0.9rem;
}

.gt-btn-primary {
    background: linear-gradient(45deg, #00f2fe, #4facfe);
    color: #fff !important;
    box-shadow: 0 4px 15px rgba(0, 242, 254, 0.3);
}

.gt-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 242, 254, 0.5);
    color: #fff;
}

.gt-btn-secondary {
    background: rgba(255,255,255,0.05);
    color: #fff !important;
    border: 1px solid var(--gt-border);
    backdrop-filter: blur(10px);
}

.gt-btn-secondary:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
    transform: translateY(-2px);
}

.gt-hero-images {
    flex: 1;
    position: relative;
    height: 500px;
}

.gt-hero-image-wrapper {
    position: absolute;
    width: 70%;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--gt-shadow);
    border: 1px solid var(--gt-border);
    right: 0;
    top: 0;
    z-index: 1;
}

.gt-hero-img-2 {
    width: 60%;
    left: 0;
    top: 150px;
    z-index: 2;
}

.gt-hero-img {
    width: 100%;
    height: auto;
    display: block;
}

/* Animations */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
    100% { transform: translateY(0px); }
}

.gt-floating {
    animation: float 6s ease-in-out infinite;
}

.gt-floating-delay {
    animation: float 6s ease-in-out infinite;
    animation-delay: -3s;
}

/* Sections */
.gt-section {
    padding: 80px 0;
    position: relative;
}

.gt-section-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 40px;
    text-align: center;
}

/* Deal Box */
.gt-deal-box {
    background: var(--gt-bg-card);
    backdrop-filter: blur(12px);
    border: 1px solid var(--gt-border);
    border-radius: 24px;
    padding: 60px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.gt-deal-box::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(0,242,254,0.1) 0%, transparent 60%);
    pointer-events: none;
    z-index: 0;
}

.gt-deal-content {
    position: relative;
    z-index: 1;
}

.gt-countdown {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin: 30px 0;
}

.gt-time-box {
    background: rgba(0,0,0,0.4);
    border: 1px solid var(--gt-border);
    border-radius: 12px;
    width: 80px;
    height: 80px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.gt-time-box span {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--gt-accent);
}

.gt-time-box small {
    font-size: 0.8rem;
    color: var(--gt-text-secondary);
    text-transform: uppercase;
}

@media (max-width: 992px) {
    .gt-hero-container {
        flex-direction: column;
        text-align: center;
    }
    .gt-hero-actions {
        justify-content: center;
    }
    .gt-hero-images {
        width: 100%;
        height: 400px;
        margin-top: 40px;
    }
}
</style>

<script>
// Simple countdown timer
document.addEventListener('DOMContentLoaded', function() {
    const countDownDate = new Date().getTime() + (7 * 24 * 60 * 60 * 1000); // 7 days from now
    
    const x = setInterval(function() {
        const now = new Date().getTime();
        const distance = countDownDate - now;
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        document.getElementById("days").innerHTML = days.toString().padStart(2, '0');
        document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
        document.getElementById("mins").innerHTML = minutes.toString().padStart(2, '0');
        document.getElementById("secs").innerHTML = seconds.toString().padStart(2, '0');
        
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("gt-countdown").innerHTML = "EXPIRED";
        }
    }, 1000);
});
</script>

<?php get_footer(); ?>
