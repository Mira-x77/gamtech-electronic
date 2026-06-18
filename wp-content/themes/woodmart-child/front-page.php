<?php
/**
 * Homepage template — GamTech Electronics
 * Black / Purple / White layout — NovaShop reference design
 * Self-contained: renders full HTML, no header.php / footer.php dependency
 */
defined( 'ABSPATH' ) || exit;

/* ============================================================
   PRODUCT HELPERS
   ============================================================ */
function gs_query( $args = [] ) {
    return new WP_Query( array_merge( [
        'post_type'   => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 8,
    ], $args ) );
}

function gs_card( $product, $badge = '' ) {
    if ( ! $product ) return;
    $id      = $product->get_id();
    $link    = get_permalink( $id );
    $img_id  = $product->get_image_id();
    $img     = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : wc_placeholder_img_src();
    $name    = $product->get_name();
    $rating  = (float) $product->get_average_rating();
    $reviews = (int)   $product->get_review_count();
    $reg     = (float) $product->get_regular_price();
    $sale    = (float) $product->get_sale_price();
    $on_sale = $product->is_on_sale();
    $price   = (float) $product->get_price();
    $add_url = ( $product->is_purchasable() && $product->is_in_stock() ) ? $product->add_to_cart_url() : $link;
    if ( ! $badge && $on_sale ) $badge = 'sale';
    $pct = ( $on_sale && $reg > 0 ) ? round( ( $reg - $sale ) / $reg * 100 ) : 0;
    ?>
<div class="gs-card">
    <?php if ( $badge ) : ?><span class="gs-badge gs-badge-<?php echo esc_attr($badge); ?>"><?php echo $badge==='sale'?'Sale':esc_html(ucfirst($badge)); ?></span><?php endif; ?>
    <div class="gs-card-actions">
        <button class="gs-act-btn gs-wish" data-action="wish" aria-label="Wishlist">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </button>
        <a href="<?php echo esc_url($link); ?>" class="gs-act-btn" aria-label="View">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </a>
    </div>
    <a href="<?php echo esc_url($link); ?>" class="gs-card-img-wrap">
        <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name); ?>" loading="lazy">
    </a>
    <div class="gs-card-body">
        <span class="gs-card-brand">GamTech</span>
        <a href="<?php echo esc_url($link); ?>" class="gs-card-name"><?php echo esc_html($name); ?></a>
        <?php if ( $rating > 0 ) : ?>
        <div class="gs-stars">
            <span class="gs-stars-filled"><?php for($i=0;$i<min(5,round($rating));$i++) echo '★'; ?></span>
            <span class="gs-stars-empty"><?php for($i=round($rating);$i<5;$i++) echo '★'; ?></span>
            <span class="gs-stars-count">(<?php echo esc_html($reviews); ?>)</span>
        </div>
        <?php endif; ?>
        <div class="gs-card-price">
            <?php if ( $on_sale && $reg > 0 ) : ?>
            <span class="gs-price-now"><?php echo wp_kses_post( wc_price($sale) ); ?></span>
            <span class="gs-price-was"><?php echo wp_kses_post( wc_price($reg) ); ?></span>
            <span class="gs-price-save">-<?php echo esc_html($pct); ?>%</span>
            <?php else : ?>
            <span class="gs-price-now"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <a href="<?php echo esc_url($add_url); ?>" class="gs-add-btn" data-id="<?php echo esc_attr($id); ?>" data-price="<?php echo esc_attr($price); ?>" data-name="<?php echo esc_attr($name); ?>" data-img="<?php echo esc_attr($img); ?>">
        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        Add to Cart
    </a>
</div>
    <?php
}

/* queries */
$q_deals = gs_query(['posts_per_page'=>8,'meta_query'=>[['key'=>'_sale_price','value'=>'','compare'=>'!=']],'orderby'=>'rand']);
if ( ! $q_deals->have_posts() ) $q_deals = gs_query(['posts_per_page'=>8,'orderby'=>'date']);
$q_rec   = gs_query(['posts_per_page'=>4,'meta_key'=>'total_sales','orderby'=>'meta_value_num','order'=>'DESC']);

/* cart */
$wc_items=[]; $wc_total=0; $wc_count=0;
if ( function_exists('WC') && WC()->cart ) {
    foreach( WC()->cart->get_cart() as $v ) {
        $p = wc_get_product($v['product_id']); if(!$p) continue;
        $wc_items[]=['name'=>$p->get_name(),'qty'=>$v['quantity'],'price'=>(float)$p->get_price(),
            'img'=>$p->get_image_id()?wp_get_attachment_image_url($p->get_image_id(),'thumbnail'):wc_placeholder_img_src('thumbnail')];
        $wc_total += (float)$p->get_price() * (int)$v['quantity'];
    }
    $wc_count = WC()->cart->get_cart_contents_count();
}

/* categories */
$cats = get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'number'=>7,'parent'=>0,
    'orderby'=>'count','order'=>'DESC','exclude'=>[absint(get_option('default_product_cat'))]]);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php wp_title('|',true,'right'); ?><?php bloginfo('name'); ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<?php wp_head(); ?>
<style>
/* ============================================================
   GAMTECH STORE — DESIGN SYSTEM
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f0f0f;--bg2:#161616;--bg3:#1e1e1e;--card:#181818;
  --border:#252525;--border2:#2e2e2e;
  --purple:#7c3aed;--purple-d:#6d28d9;--purple-l:#a78bfa;
  --purple-glow:rgba(124,58,237,.22);
  --white:#ffffff;--off:#f4f4f5;
  --text:#d4d4d8;--muted:#71717a;--dim:#52525b;
  --red:#ef4444;--green:#22c55e;--yellow:#f59e0b;
  --r:12px;--rs:8px;
  --shadow:0 4px 24px rgba(0,0,0,.45);
  --tr:all .2s ease;
  --font:'Poppins','Segoe UI',Arial,sans-serif;
  --sw:240px;--cw:294px;
}
html,body{height:100%;overflow:hidden}
body{font-family:var(--font);background:var(--bg);color:var(--text)}
a{color:inherit;text-decoration:none;transition:var(--tr)}
img{max-width:100%;height:auto;display:block}
button{font-family:var(--font);cursor:pointer;border:none;background:none;outline:none}

/* ── LAYOUT: fixed left + right sidebars, only center scrolls ── */
.gs-page{
  display:block;          /* sidebars are fixed, no grid needed for them */
  height:100vh;
  overflow:hidden;
}

/* Centre column sits between the two fixed sidebars */
.gs-center{
  position:fixed;
  left:var(--sw);
  right:var(--cw);
  top:0;
  bottom:0;
  display:flex;
  flex-direction:column;
  overflow:hidden;
}
</style>
<style>
/* ── SIDEBAR — fixed left ── */
.gs-sb{
  position:fixed;
  left:0;top:0;bottom:0;
  width:var(--sw);
  background:var(--bg2);
  border-right:1px solid var(--border);
  display:flex;flex-direction:column;
  overflow-y:auto;overflow-x:hidden;
  scrollbar-width:thin;scrollbar-color:var(--border2) transparent;
  z-index:100;
}
.gs-sb::-webkit-scrollbar{width:3px}.gs-sb::-webkit-scrollbar-thumb{background:var(--border2)}
.gs-logo{display:flex;align-items:center;gap:10px;padding:22px 18px 18px;border-bottom:1px solid var(--border)}
.gs-logo-icon{width:34px;height:34px;background:linear-gradient(135deg,#7c3aed,#5b21b6);border-radius:9px;
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-logo-text{font-size:17px;font-weight:800;color:var(--white)}
.gs-logo-text span{color:var(--purple-l)}
.gs-nav-section{padding:14px 10px 6px}
.gs-nav-label{font-size:9.5px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;
  color:var(--dim);padding:0 8px;margin-bottom:5px}
.gs-nav-a{display:flex;align-items:center;gap:11px;padding:9px 10px;border-radius:var(--rs);
  font-size:13px;font-weight:500;color:var(--muted);transition:var(--tr);position:relative}
.gs-nav-a:hover{background:var(--bg3);color:var(--white)}
.gs-nav-a.active{background:rgba(124,58,237,.14);color:var(--purple-l)}
.gs-nav-a.active::before{content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
  width:3px;height:58%;background:var(--purple);border-radius:0 3px 3px 0}
.gs-nav-a svg{flex-shrink:0;opacity:.7}
.gs-nav-a:hover svg,.gs-nav-a.active svg{opacity:1}
.gs-nav-badge{margin-left:auto;background:var(--red);color:var(--white);font-size:9.5px;
  font-weight:700;padding:2px 6px;border-radius:8px;line-height:1.4}
.gs-nav-badge.p{background:var(--purple)}

/* sidebar promo */
.gs-sb-promo{margin:10px;border-radius:var(--r);
  background:linear-gradient(135deg,#7c3aed,#5b21b6);padding:18px 14px;position:relative;overflow:hidden}
.gs-sb-promo::before{content:'';position:absolute;top:-25px;right:-25px;width:110px;height:110px;
  background:rgba(255,255,255,.07);border-radius:50%}
.gs-sb-promo::after{content:'';position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;
  background:rgba(255,255,255,.05);border-radius:50%}
.gs-sb-promo .sp-tag{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1px;
  color:rgba(255,255,255,.7);margin-bottom:5px;position:relative;z-index:1}
.gs-sb-promo h4{font-size:15px;font-weight:800;color:var(--white);line-height:1.25;
  margin-bottom:3px;position:relative;z-index:1}
.gs-sb-promo p{font-size:10.5px;color:rgba(255,255,255,.72);margin-bottom:13px;position:relative;z-index:1}
.gs-sb-promo a{display:inline-block;background:var(--white);color:var(--purple);font-size:11.5px;
  font-weight:700;padding:7px 15px;border-radius:18px;position:relative;z-index:1;transition:var(--tr)}
.gs-sb-promo a:hover{background:var(--off);transform:scale(1.03)}

/* sidebar support */
.gs-sb-support{margin-top:auto;padding:14px 18px;border-top:1px solid var(--border);
  display:flex;align-items:center;gap:9px;font-size:11.5px;color:var(--muted);cursor:pointer}
.gs-sb-support .dot{width:7px;height:7px;background:var(--green);border-radius:50%;
  flex-shrink:0;box-shadow:0 0 5px var(--green);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.gs-sb-support strong{display:block;font-size:12px;color:var(--text)}
</style>
<style>
/* ── HEADER — fixed top of center column ── */
.gs-hd{
  flex-shrink:0;
  background:var(--bg2);
  border-bottom:1px solid var(--border);
  display:flex;align-items:center;gap:14px;
  padding:12px 22px;
  z-index:90;
}
.gs-search{flex:1;max-width:540px;position:relative}
.gs-search input{width:100%;background:var(--bg3);border:1.5px solid var(--border2);border-radius:22px;
  color:var(--text);font-family:var(--font);font-size:13px;padding:10px 46px 10px 18px;transition:var(--tr)}
.gs-search input::placeholder{color:var(--dim)}
.gs-search input:focus{border-color:var(--purple);outline:none;box-shadow:0 0 0 3px var(--purple-glow);background:var(--bg2)}
.gs-search-btn{position:absolute;right:5px;top:50%;transform:translateY(-50%);
  width:32px;height:32px;background:var(--purple);border-radius:50%;
  display:flex;align-items:center;justify-content:center;transition:var(--tr)}
.gs-search-btn:hover{background:var(--purple-d)}
.gs-search-btn svg{color:var(--white)}
.gs-hd-icons{display:flex;align-items:center;gap:5px;margin-left:auto}
.gs-hd-btn{position:relative;width:38px;height:38px;border-radius:9px;background:var(--bg3);
  border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;
  color:var(--muted);transition:var(--tr);cursor:pointer}
.gs-hd-btn:hover{background:var(--bg);color:var(--white)}
.gs-hd-btn .bdg{position:absolute;top:-4px;right:-4px;min-width:17px;height:17px;
  background:var(--purple);color:var(--white);font-size:9.5px;font-weight:700;border-radius:8px;
  display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid var(--bg2)}
.gs-avatar{display:flex;align-items:center;gap:9px;padding:5px 12px 5px 5px;background:var(--bg3);
  border:1px solid var(--border2);border-radius:22px;cursor:pointer;transition:var(--tr);margin-left:3px}
.gs-avatar:hover{border-color:var(--purple)}
.gs-avatar .av-img{width:28px;height:28px;border-radius:50%;
  background:linear-gradient(135deg,#7c3aed,#5b21b6);display:flex;align-items:center;justify-content:center;
  font-size:12px;font-weight:700;color:var(--white)}
.gs-avatar .av-name{font-size:12.5px;font-weight:600;color:var(--text);white-space:nowrap}

/* ── CONTENT — scrollable center ── */
.gs-mn{
  flex:1;
  overflow-y:auto;
  overflow-x:hidden;
  background:var(--bg);
  padding:22px;
  scrollbar-width:thin;
  scrollbar-color:var(--border2) transparent;
}
.gs-mn::-webkit-scrollbar{width:5px}
.gs-mn::-webkit-scrollbar-thumb{background:var(--border2);border-radius:3px}

/* footer lives inside the scroll area */
.gs-ft{
  background:var(--bg2);
  border-top:1px solid var(--border);
  padding:18px 22px;
  flex-shrink:0;
}

/* section headers */
.gs-sec-hd{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:16px;gap:10px}
.gs-sec-title{font-size:17px;font-weight:700;color:var(--white)}
.gs-sec-sub{font-size:11.5px;color:var(--muted);margin-top:2px}
.gs-view-all{font-size:12px;font-weight:600;color:var(--purple-l);white-space:nowrap;
  padding:5px 13px;border-radius:18px;border:1px solid rgba(124,58,237,.3);transition:var(--tr)}
.gs-view-all:hover{background:rgba(124,58,237,.14);border-color:var(--purple);color:var(--white)}

/* ── HERO ── */
.gs-hero{background:linear-gradient(135deg,#1a0533 0%,#3b0764 38%,#5b21b6 72%,#7c3aed 100%);
  border-radius:16px;padding:38px 44px;display:grid;grid-template-columns:1fr 1fr;
  align-items:center;gap:28px;position:relative;overflow:hidden;margin-bottom:22px;min-height:210px}
.gs-hero::before{content:'';position:absolute;top:-55px;right:28%;width:200px;height:200px;
  background:rgba(255,255,255,.04);border-radius:50%}
.gs-hero::after{content:'';position:absolute;bottom:-70px;right:-35px;width:260px;height:260px;
  background:rgba(124,58,237,.13);border-radius:50%}
.gs-hero-tag{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.11);
  border:1px solid rgba(255,255,255,.14);border-radius:18px;padding:4px 12px;
  font-size:10.5px;font-weight:600;color:rgba(255,255,255,.82);text-transform:uppercase;
  letter-spacing:.9px;margin-bottom:12px}
.gs-hero-tag .dot{width:5px;height:5px;background:var(--yellow);border-radius:50%}
.gs-hero h1{font-size:clamp(24px,2.8vw,36px);font-weight:900;color:var(--white);
  line-height:1.15;margin-bottom:10px}
.gs-hero h1 span{color:#c4b5fd}
.gs-hero p{font-size:13.5px;color:rgba(255,255,255,.68);line-height:1.65;margin-bottom:26px;max-width:340px}
.gs-hero-cta{display:inline-flex;align-items:center;gap:7px;background:var(--white);color:var(--purple);
  font-size:13.5px;font-weight:700;padding:11px 26px;border-radius:22px;transition:var(--tr);
  box-shadow:0 4px 18px rgba(255,255,255,.14)}
.gs-hero-cta:hover{background:var(--off);transform:translateY(-2px);
  box-shadow:0 8px 28px rgba(255,255,255,.18);color:var(--purple-d)}
.gs-hero-img{display:flex;align-items:center;justify-content:center;position:relative;z-index:1}
.gs-hero-img img{max-height:190px;object-fit:contain;filter:drop-shadow(0 14px 36px rgba(0,0,0,.5));
  animation:float 3.5s ease-in-out infinite}
.gs-hero-placeholder{width:180px;height:180px;margin:0 auto;background:rgba(255,255,255,.05);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  animation:float 3.5s ease-in-out infinite}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-11px)}}
</style>
<style>
/* ── CATEGORIES ── */
.gs-cats{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;margin-bottom:26px;scrollbar-width:none}
.gs-cats::-webkit-scrollbar{display:none}
.gs-cat{display:flex;flex-direction:column;align-items:center;gap:7px;flex-shrink:0;cursor:pointer;transition:var(--tr)}
.gs-cat:hover{transform:translateY(-3px)}
.gs-cat-icon{width:60px;height:60px;background:var(--card);border:1.5px solid var(--border);
  border-radius:50%;display:flex;align-items:center;justify-content:center;transition:var(--tr)}
.gs-cat:hover .gs-cat-icon,.gs-cat.active .gs-cat-icon{border-color:var(--purple);
  background:rgba(124,58,237,.12);box-shadow:0 0 0 3px var(--purple-glow)}
.gs-cat-icon svg{color:var(--muted);transition:var(--tr)}
.gs-cat:hover .gs-cat-icon svg,.gs-cat.active .gs-cat-icon svg{color:var(--purple-l)}
.gs-cat-lbl{font-size:11px;font-weight:500;color:var(--muted);text-align:center;
  white-space:nowrap;transition:var(--tr)}
.gs-cat:hover .gs-cat-lbl,.gs-cat.active .gs-cat-lbl{color:var(--white)}

/* ── PROMO CARDS ── */
.gs-promos{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px}
.gs-promo{background:var(--card);border:1px solid var(--border);border-radius:var(--r);
  padding:16px;display:flex;align-items:center;gap:13px;transition:var(--tr);cursor:pointer;position:relative;overflow:hidden}
.gs-promo::before{content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(124,58,237,.07) 0%,transparent 60%);opacity:0;transition:var(--tr)}
.gs-promo:hover::before{opacity:1}
.gs-promo:hover{border-color:var(--purple);transform:translateY(-2px);
  box-shadow:0 8px 22px rgba(124,58,237,.14)}
.gs-promo-ico{width:46px;height:46px;border-radius:11px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-promo-ico.red   {background:rgba(239,68,68,.14);color:#f87171}
.gs-promo-ico.green {background:rgba(34,197,94,.14);color:#4ade80}
.gs-promo-ico.pur   {background:rgba(124,58,237,.18);color:var(--purple-l)}
.gs-promo-ico.yel   {background:rgba(245,158,11,.14);color:#fbbf24}
.gs-promo-body h4{font-size:13px;font-weight:700;color:var(--white);margin-bottom:2px}
.gs-promo-body p{font-size:11px;color:var(--muted);line-height:1.38}
.gs-promo-body .pcta{font-size:10.5px;font-weight:600;color:var(--purple-l);
  margin-top:3px;display:inline-flex;align-items:center;gap:3px}

/* ── PRODUCT GRID ── */
.gs-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:13px;margin-bottom:28px}
.gs-grid-4{grid-template-columns:repeat(4,1fr)}

/* ── PRODUCT CARD ── */
.gs-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);
  overflow:hidden;transition:var(--tr);position:relative;display:flex;flex-direction:column}
.gs-card:hover{border-color:var(--border2);transform:translateY(-3px);box-shadow:0 12px 30px rgba(0,0,0,.5)}
.gs-badge{position:absolute;top:9px;left:9px;font-size:9.5px;font-weight:700;
  padding:2px 7px;border-radius:5px;text-transform:uppercase;letter-spacing:.4px;z-index:2}
.gs-badge-sale{background:var(--red);color:var(--white)}
.gs-badge-new {background:var(--purple);color:var(--white)}
.gs-badge-hot {background:var(--yellow);color:#111}
.gs-card-actions{position:absolute;top:9px;right:9px;display:flex;flex-direction:column;gap:5px;
  opacity:0;transform:translateX(7px);transition:var(--tr);z-index:2}
.gs-card:hover .gs-card-actions{opacity:1;transform:translateX(0)}
.gs-act-btn{width:30px;height:30px;border-radius:7px;background:var(--bg2);
  border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;
  color:var(--muted);transition:var(--tr);cursor:pointer}
.gs-act-btn:hover{background:var(--purple);border-color:var(--purple);color:var(--white)}
.gs-act-btn.wishlisted{color:var(--red);border-color:var(--red)}
.gs-card-img-wrap{position:relative;background:var(--bg3);aspect-ratio:1/1;overflow:hidden;display:block}
.gs-card-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .32s ease}
.gs-card:hover .gs-card-img-wrap img{transform:scale(1.06)}
.gs-card-body{padding:12px;display:flex;flex-direction:column;gap:5px;flex:1}
.gs-card-brand{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--purple-l)}
.gs-card-name{font-size:12.5px;font-weight:600;color:var(--text);line-height:1.38;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;transition:var(--tr)}
.gs-card:hover .gs-card-name{color:var(--white)}
.gs-stars{display:flex;align-items:center;gap:3px;font-size:10.5px}
.gs-stars-filled{color:var(--yellow)}
.gs-stars-empty{color:var(--border2)}
.gs-stars-count{color:var(--dim)}
.gs-card-price{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-top:auto}
.gs-price-now{font-size:14.5px;font-weight:800;color:var(--white)}
.gs-price-was{font-size:11.5px;color:var(--dim);text-decoration:line-through}
.gs-price-save{font-size:10px;font-weight:700;color:var(--green)}
.gs-add-btn{display:flex;align-items:center;justify-content:center;gap:6px;
  width:100%;background:var(--bg3);border-top:1px solid var(--border2);color:var(--muted);
  font-size:12px;font-weight:600;padding:9px;transition:var(--tr)}
.gs-add-btn:hover{background:var(--purple);border-color:var(--purple);color:var(--white)}
</style>
<style>
/* ── CART PANEL — fixed right ── */
.gs-ct{
  position:fixed;
  right:0;top:0;bottom:0;
  width:var(--cw);
  background:var(--bg2);
  border-left:1px solid var(--border);
  display:flex;flex-direction:column;
  overflow-y:auto;
  scrollbar-width:thin;scrollbar-color:var(--border2) transparent;
  z-index:100;
}
.gs-ct::-webkit-scrollbar{width:3px}.gs-ct::-webkit-scrollbar-thumb{background:var(--border2)}
.gs-ct-hd{display:flex;align-items:center;justify-content:space-between;
  padding:18px 16px 14px;border-bottom:1px solid var(--border);
  position:sticky;top:0;background:var(--bg2);z-index:2}
.gs-ct-hd h3{font-size:14.5px;font-weight:700;color:var(--white)}
.gs-ct-cnt{background:var(--purple);color:var(--white);font-size:10.5px;font-weight:700;
  padding:2px 8px;border-radius:9px;margin-left:7px}
.gs-ct-close{width:26px;height:26px;border-radius:6px;background:var(--bg3);
  border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;
  color:var(--muted);cursor:pointer;transition:var(--tr)}
.gs-ct-close:hover{background:var(--red);border-color:var(--red);color:var(--white)}
.gs-ct-items{padding:10px;flex:1}
.gs-ct-item{display:flex;align-items:flex-start;gap:9px;padding:11px 0;border-bottom:1px solid var(--border)}
.gs-ct-item:last-child{border-bottom:none}
.gs-ct-thumb{width:52px;height:52px;border-radius:7px;object-fit:cover;background:var(--bg3);flex-shrink:0}
.gs-ct-thumb-ph{width:52px;height:52px;border-radius:7px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-ct-info{flex:1;min-width:0}
.gs-ct-name{font-size:11.5px;font-weight:600;color:var(--text);white-space:nowrap;
  overflow:hidden;text-overflow:ellipsis;margin-bottom:3px}
.gs-ct-sub{font-size:10.5px;color:var(--dim);margin-bottom:5px}
.gs-ct-price{font-size:12.5px;font-weight:700;color:var(--white)}
.gs-qty{display:flex;align-items:center;gap:5px;margin-top:5px}
.gs-qty-btn{width:22px;height:22px;border-radius:5px;background:var(--bg3);
  border:1px solid var(--border2);color:var(--muted);font-size:13px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;line-height:1;transition:var(--tr)}
.gs-qty-btn:hover{background:var(--purple);border-color:var(--purple);color:var(--white)}
.gs-qty-n{font-size:11.5px;font-weight:600;color:var(--text);min-width:18px;text-align:center}
.gs-ct-del{color:var(--dim);cursor:pointer;margin-left:auto;align-self:flex-start;
  transition:var(--tr);background:none;border:none}
.gs-ct-del:hover{color:var(--red)}

/* promo code */
.gs-ct-promo{padding:11px;border-top:1px solid var(--border)}
.gs-promo-row{display:flex;gap:7px}
.gs-promo-row input{flex:1;background:var(--bg3);border:1.5px solid var(--border2);border-radius:7px;
  color:var(--text);font-family:var(--font);font-size:11.5px;padding:8px 10px;transition:var(--tr)}
.gs-promo-row input::placeholder{color:var(--dim)}
.gs-promo-row input:focus{border-color:var(--purple);outline:none}
.gs-promo-row button{background:var(--purple);color:var(--white);font-size:11.5px;font-weight:700;
  padding:8px 14px;border-radius:7px;transition:var(--tr);cursor:pointer;border:none}
.gs-promo-row button:hover{background:var(--purple-d)}
.gs-promo-msg{font-size:10.5px;margin-top:5px}

/* summary */
.gs-ct-sum{padding:14px 16px;border-top:1px solid var(--border)}
.gs-sum-row{display:flex;justify-content:space-between;align-items:center;
  margin-bottom:7px;font-size:12.5px}
.gs-sum-row .lbl{color:var(--muted)}.gs-sum-row .val{font-weight:600;color:var(--text)}
.gs-sum-row.disc .val{color:var(--green)}.gs-sum-row.ship .val{color:var(--green)}
.gs-sum-row.total{font-size:15px;font-weight:800;color:var(--white);
  margin-top:10px;padding-top:10px;border-top:1px solid var(--border2);margin-bottom:0}
.gs-sum-row.total .val{color:var(--white);font-size:17px}
.gs-checkout{display:flex;align-items:center;justify-content:center;gap:7px;width:100%;
  background:linear-gradient(135deg,#7c3aed,#5b21b6);color:var(--white);
  font-size:13.5px;font-weight:700;padding:13px;border-radius:11px;margin-top:14px;
  transition:var(--tr);box-shadow:0 4px 18px var(--purple-glow);cursor:pointer;border:none}
.gs-checkout:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(124,58,237,.38);filter:brightness(1.09)}
.gs-pay-icons{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:10px}
.gs-pay-icons span{background:var(--bg3);border:1px solid var(--border2);border-radius:3px;
  padding:2px 7px;font-size:9.5px;font-weight:700;color:var(--muted)}

/* suggestions */
.gs-ct-sugg{padding:14px 16px;border-top:1px solid var(--border)}
.gs-ct-sugg h4{font-size:12px;font-weight:700;color:var(--white);margin-bottom:10px}
.gs-sugg-item{display:flex;align-items:center;gap:9px;padding:7px 0;
  border-bottom:1px solid var(--border);cursor:pointer;transition:var(--tr)}
.gs-sugg-item:last-child{border-bottom:none}
.gs-sugg-item:hover{opacity:.78}
.gs-sugg-img{width:40px;height:40px;border-radius:7px;object-fit:cover;background:var(--bg3);flex-shrink:0}
.gs-sugg-img-ph{width:40px;height:40px;border-radius:7px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-sugg-name{flex:1;font-size:11px;color:var(--text);font-weight:500;line-height:1.3}
.gs-sugg-price{font-size:11.5px;font-weight:700;color:var(--white);white-space:nowrap}
.gs-sugg-add{width:24px;height:24px;background:var(--purple);border-radius:50%;
  display:flex;align-items:center;justify-content:center;color:var(--white);
  font-size:15px;line-height:1;cursor:pointer;transition:var(--tr);flex-shrink:0;border:none}
.gs-sugg-add:hover{background:var(--purple-d);transform:scale(1.1)}

/* ── FOOTER FEATURES ── */
.gs-ft-features{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}
.gs-ft-card{background:var(--card);border:1px solid var(--border);border-radius:var(--r);
  padding:16px 14px;display:flex;align-items:center;gap:11px;transition:var(--tr)}
.gs-ft-card:hover{border-color:var(--purple);background:rgba(124,58,237,.05)}
.gs-ft-icon{width:42px;height:42px;border-radius:11px;background:rgba(124,58,237,.14);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-ft-icon svg{color:var(--purple-l)}
.gs-ft-body h4{font-size:12.5px;font-weight:700;color:var(--white);margin-bottom:2px}
.gs-ft-body p{font-size:10.5px;color:var(--muted)}

/* ── MOBILE TOP BAR ── */
.gs-mob-bar{
  display:none;
  align-items:center;justify-content:space-between;
  padding:0 16px;
  height:58px;
  background:var(--bg2);
  border-bottom:1px solid var(--border);
  position:fixed;top:0;left:0;right:0;
  z-index:200;
}
.gs-mob-btn{
  background:var(--bg3);border:1px solid var(--border2);color:var(--text);
  width:44px;height:44px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:var(--tr);flex-shrink:0;
  -webkit-tap-highlight-color:transparent;
}
.gs-mob-btn:active{background:var(--purple);border-color:var(--purple)}
.gs-mob-btn svg{pointer-events:none}
.gs-mob-logo{font-size:17px;font-weight:800;color:var(--white)}
.gs-mob-logo span{color:var(--purple-l)}
.gs-mob-cart-wrap{position:relative}
.gs-mob-cart-bdg{
  position:absolute;top:-5px;right:-5px;
  min-width:18px;height:18px;
  background:var(--purple);color:var(--white);
  font-size:10px;font-weight:700;border-radius:9px;
  display:flex;align-items:center;justify-content:center;
  padding:0 4px;border:2px solid var(--bg2);
}

.gs-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(0,0,0,.72);z-index:150;
  backdrop-filter:blur(3px);
  -webkit-backdrop-filter:blur(3px);
}
.gs-overlay.on{display:block}

/* ============================================================
   RESPONSIVE — TABLET  (≤ 1280px)
   ============================================================ */
@media(max-width:1280px){
  :root{--cw:272px}
  .gs-grid{grid-template-columns:repeat(3,1fr)}
  .gs-promos{grid-template-columns:repeat(2,1fr)}
}

/* ============================================================
   RESPONSIVE — SMALL LAPTOP / LARGE TABLET  (≤ 1024px)
   Cart panel becomes a slide-in overlay, sidebar stays fixed
   ============================================================ */
@media(max-width:1024px){
  :root{--sw:220px}
  .gs-center{right:0}           /* reclaim cart width */
  .gs-ct{
    transform:translateX(100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;
  }
  .gs-ct.open{transform:translateX(0)}
  .gs-grid{grid-template-columns:repeat(3,1fr)}
}

/* ============================================================
   RESPONSIVE — TABLET PORTRAIT  (≤ 768px)
   Both sidebars become drawers, mobile bar appears
   ============================================================ */
@media(max-width:768px){
  html,body{overflow:hidden}

  /* hide both fixed sidebars off-screen by default */
  .gs-sb{
    transform:translateX(-100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;
    width:280px;
  }
  .gs-sb.open{transform:translateX(0)}

  .gs-ct{
    transform:translateX(100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;
    width:300px;
  }
  .gs-ct.open{transform:translateX(0)}

  /* center column spans full width, shifted down for mobile bar */
  .gs-center{
    left:0;right:0;
    top:58px;          /* mobile bar height */
  }

  /* show mobile top bar */
  .gs-mob-bar{display:flex}

  /* header: simplified on tablet */
  .gs-hd{padding:10px 16px;gap:10px}
  .gs-search{max-width:none;flex:1}
  .gs-avatar .av-name{display:none}
  .gs-avatar{padding:5px}

  /* hero: single column */
  .gs-hero{
    grid-template-columns:1fr;
    padding:28px 24px;
    min-height:auto;
  }
  .gs-hero-img{display:none}
  .gs-hero h1{font-size:28px}
  .gs-hero p{max-width:100%;font-size:14px}

  /* categories: larger touch targets */
  .gs-cat-icon{width:68px;height:68px}
  .gs-cat-icon svg{width:28px;height:28px}
  .gs-cat-lbl{font-size:12px}
  .gs-cats{gap:14px}

  /* promo cards: 2 cols */
  .gs-promos{grid-template-columns:repeat(2,1fr);gap:10px}
  .gs-promo{padding:14px}
  .gs-promo-ico{width:44px;height:44px}
  .gs-promo-body h4{font-size:13px}

  /* product grid: 2 cols */
  .gs-grid{grid-template-columns:repeat(2,1fr);gap:12px}

  /* footer features: 2 cols */
  .gs-ft-features{grid-template-columns:repeat(2,1fr);gap:10px}
  .gs-ft{padding:16px}
}

/* ============================================================
   RESPONSIVE — MOBILE  (≤ 480px)
   True single-column mobile-first layout
   ============================================================ */
@media(max-width:480px){
  :root{--r:10px;--rs:7px}

  .gs-mn{padding:14px}

  /* hero — full mobile */
  .gs-hero{
    padding:24px 20px;
    border-radius:14px;
    margin-bottom:18px;
  }
  .gs-hero-tag{font-size:10px;padding:4px 10px}
  .gs-hero h1{font-size:26px;margin-bottom:10px}
  .gs-hero p{font-size:13px;margin-bottom:20px}
  .gs-hero-cta{
    font-size:15px;
    padding:13px 28px;
    border-radius:24px;
    width:100%;
    justify-content:center;
  }

  /* categories: big icons, scrollable row */
  .gs-cats{gap:16px;padding-bottom:8px;margin-bottom:22px}
  .gs-cat-icon{width:72px;height:72px}
  .gs-cat-icon svg{width:30px;height:30px}
  .gs-cat-lbl{font-size:12.5px;font-weight:600}

  /* promo cards: full width stacked */
  .gs-promos{grid-template-columns:1fr;gap:10px;margin-bottom:22px}
  .gs-promo{padding:16px}
  .gs-promo-ico{width:48px;height:48px;border-radius:12px}
  .gs-promo-body h4{font-size:14px}
  .gs-promo-body p{font-size:12px}

  /* section headers */
  .gs-sec-title{font-size:18px}
  .gs-sec-sub{font-size:12px}
  .gs-sec-hd{margin-bottom:14px}

  /* product grid: 2 cols (comfortable on Android) */
  .gs-grid{grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:22px}

  /* product card: bigger tap targets */
  .gs-card-body{padding:12px}
  .gs-card-brand{font-size:10.5px}
  .gs-card-name{font-size:13px}
  .gs-stars{font-size:12px}
  .gs-price-now{font-size:15px}
  .gs-price-was{font-size:12px}
  .gs-add-btn{
    font-size:13px;
    font-weight:700;
    padding:12px;
    letter-spacing:.2px;
  }
  .gs-add-btn svg{width:15px;height:15px}

  /* always show wishlist/view buttons on mobile (no hover) */
  .gs-card-actions{
    opacity:1;
    transform:translateX(0);
  }
  .gs-act-btn{width:34px;height:34px;border-radius:9px}

  /* footer */
  .gs-ft-features{grid-template-columns:1fr;gap:8px}
  .gs-ft{padding:14px}
  .gs-ft-card{padding:14px}
  .gs-ft-body h4{font-size:13.5px}
  .gs-ft-body p{font-size:11.5px}

  /* cart panel: full width on small phones */
  .gs-ct{width:100% !important}

  /* mobile bar buttons: bigger */
  .gs-mob-btn{width:46px;height:46px}
}

/* ============================================================
   RESPONSIVE — TINY PHONES  (≤ 360px)
   ============================================================ */
@media(max-width:360px){
  .gs-hero h1{font-size:22px}
  .gs-grid{grid-template-columns:1fr}   /* single column on very small phones */
  .gs-cat-icon{width:64px;height:64px}
}

/* ============================================================
   ADMIN BAR OFFSETS
   ============================================================ */
body.admin-bar .gs-sb,
body.admin-bar .gs-ct{top:32px}
body.admin-bar .gs-mob-bar{top:32px}
body.admin-bar .gs-center{top:32px}
@media screen and (max-width:782px){
  body.admin-bar .gs-sb,
  body.admin-bar .gs-ct{top:46px}
  body.admin-bar .gs-mob-bar{top:46px}
  body.admin-bar .gs-center{top:calc(58px + 46px)}
}
</style>
</head>
<body <?php body_class('gs-body'); ?>>
<?php wp_body_open(); ?>

<!-- MOBILE BAR -->
<div class="gs-mob-bar">
  <button class="gs-mob-btn" id="gs-sb-toggle" aria-label="Menu">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
  <a href="<?php echo esc_url(home_url('/')); ?>" class="gs-mob-logo">Gam<span>Tech</span></a>
  <div class="gs-mob-cart-wrap">
    <button class="gs-mob-btn" id="gs-ct-toggle-mob" aria-label="Cart">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
    </button>
    <span class="gs-mob-cart-bdg" id="gs-cart-badge-mob"><?php echo esc_html($wc_count ?: 2); ?></span>
  </div>
</div>
<div id="gs-overlay" class="gs-overlay"></div>

<div class="gs-page">

<!-- ============================================================
     LEFT SIDEBAR
     ============================================================ -->
<aside class="gs-sb" id="gs-sb">

  <div class="gs-logo">
    <div class="gs-logo-icon">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    </div>
    <span class="gs-logo-text">Gam<span>Tech</span></span>
  </div>

  <div class="gs-nav-section">
    <p class="gs-nav-label">Main</p>
    <a href="<?php echo esc_url(home_url('/')); ?>" class="gs-nav-a active">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Home
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>Categories
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?on_sale=1'); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>Deals
      <span class="gs-nav-badge">Hot</span>
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?orderby=date'); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>New Arrivals
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?orderby=popularity'); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>Best Sellers
    </a>
  </div>

  <div class="gs-nav-section">
    <p class="gs-nav-label">Account</p>
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>My Orders
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>Wishlist
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Account
    </a>
  </div>

  <div class="gs-sb-promo">
    <p class="sp-tag">Special Offer</p>
    <h4>Tech Sale<br>Up to 60% Off</h4>
    <p>Limited-time deals on top gadgets</p>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?on_sale=1'); ?>">Shop Now</a>
  </div>

  <a href="<?php echo esc_url(get_permalink(get_page_by_path('contact'))); ?>" class="gs-sb-support">
    <span class="dot"></span>
    <div><strong>Need Help?</strong><span>24/7 Support Center</span></div>
  </a>

</aside>

<!-- ============================================================
     TOP HEADER
     ============================================================ -->
<div class="gs-center">
<header class="gs-hd">
  <div class="gs-search">
    <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <input type="search" name="s" placeholder="Search for products, brands and more..." value="<?php echo esc_attr(get_search_query()); ?>">
      <input type="hidden" name="post_type" value="product">
      <button type="submit" class="gs-search-btn" aria-label="Search">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </button>
    </form>
  </div>
  <div class="gs-hd-icons">
    <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="gs-hd-btn" title="Wishlist">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      <span class="bdg">0</span>
    </a>
    <button class="gs-hd-btn" title="Notifications">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      <span class="bdg">3</span>
    </button>
    <button class="gs-hd-btn" id="gs-ct-toggle" title="Cart">
      <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
      <span class="bdg" id="gs-cart-badge"><?php echo esc_html($wc_count ?: 2); ?></span>
    </button>
    <div class="gs-avatar">
      <div class="av-img"><?php echo esc_html(strtoupper(substr(wp_get_current_user()->display_name ?: 'G', 0, 1))); ?></div>
      <span class="av-name"><?php echo esc_html(wp_get_current_user()->display_name ?: 'Guest'); ?></span>
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
  </div>
</header>

<!-- ============================================================
     MAIN CONTENT
     ============================================================ -->
<main class="gs-mn">

  <!-- HERO -->
  <section class="gs-hero">
    <div>
      <div class="gs-hero-tag"><span class="dot"></span>New Collection 2026</div>
      <h1>Upgrade Your<br><span>Tech Setup ✦</span></h1>
      <p>Explore the latest laptops, gaming gear, smart devices, and accessories — built for performance-driven people.</p>
      <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gs-hero-cta">
        Shop Now
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
    <div class="gs-hero-img">
      <?php $hi = get_theme_mod('gamtech_hero_image',''); if($hi): ?>
        <img src="<?php echo esc_url($hi); ?>" alt="Hero product">
      <?php else: ?>
        <div class="gs-hero-placeholder">
          <svg width="80" height="80" fill="none" stroke="rgba(255,255,255,.55)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- CATEGORIES -->
  <div class="gs-cats">
    <?php
    $icons = [
      'Laptops'    =>'<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
      'Phones'     =>'<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>',
      'Headphones' =>'<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
      'Gaming'     =>'<line x1="6" y1="11" x2="10" y2="11"/><line x1="8" y1="9" x2="8" y2="13"/><line x1="15" y1="12" x2="15.01" y2="12"/><line x1="17" y1="10" x2="17.01" y2="10"/><path d="M6 3h12l4 8-2 8H4L2 11z"/>',
      'Smart Watch'=>'<circle cx="12" cy="12" r="7"/><polyline points="12 9 12 12 13.5 13.5"/><path d="M16.51 17.35l-.35 3.83a2 2 0 0 1-2 1.82H9.83a2 2 0 0 1-2-1.82l-.35-3.83m.01-10.7l.35-3.83A2 2 0 0 1 9.83 1h4.35a2 2 0 0 1 2 1.82l.35 3.83"/>',
      'Cameras'    =>'<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/>',
      'Networking' =>'<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
      'More'       =>'<circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/>',
    ];
    if ( ! empty($cats) && ! is_wp_error($cats) ) {
      foreach ($cats as $cat) {
        $url = get_term_link($cat);
        $ico = $icons[$cat->name] ?? $icons['More'];
        echo '<a href="'.esc_url($url).'" class="gs-cat"><div class="gs-cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">'.$ico.'</svg></div><span class="gs-cat-lbl">'.esc_html($cat->name).'</span></a>';
      }
    } else {
      foreach ($icons as $lbl => $ico) {
        echo '<a href="'.esc_url(wc_get_page_permalink('shop')).'" class="gs-cat"><div class="gs-cat-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">'.$ico.'</svg></div><span class="gs-cat-lbl">'.esc_html($lbl).'</span></a>';
      }
    }
    ?>
  </div>

  <!-- PROMO CARDS -->
  <div class="gs-promos">
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?on_sale=1'); ?>" class="gs-promo">
      <div class="gs-promo-ico red"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
      <div class="gs-promo-body"><h4>Flash Sale</h4><p>Limited time. Up to 70% Off</p><span class="pcta">Shop now →</span></div>
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gs-promo">
      <div class="gs-promo-ico green"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
      <div class="gs-promo-body"><h4>Free Shipping</h4><p>On orders over $350</p><span class="pcta">Shop now →</span></div>
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?orderby=date'); ?>" class="gs-promo">
      <div class="gs-promo-ico pur"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
      <div class="gs-promo-body"><h4>New Arrivals</h4><p>Latest tech just dropped</p><span class="pcta">Shop now →</span></div>
    </a>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?on_sale=1'); ?>" class="gs-promo">
      <div class="gs-promo-ico yel"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg></div>
      <div class="gs-promo-body"><h4>Limited Offers</h4><p>Exclusive member discounts</p><span class="pcta">Shop now →</span></div>
    </a>
  </div>

  <!-- BEST DEALS -->
  <div class="gs-sec-hd">
    <div><h2 class="gs-sec-title">Best Deals for You</h2><p class="gs-sec-sub">Hand-picked products at unbeatable prices</p></div>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?on_sale=1'); ?>" class="gs-view-all">View All</a>
  </div>
  <div class="gs-grid gs-grid-4">
    <?php
    if ($q_deals->have_posts()) {
      while ($q_deals->have_posts()) { $q_deals->the_post(); global $product; gs_card($product); }
      wp_reset_postdata();
    } else {
      for ($i=1;$i<=4;$i++) { ?>
      <div class="gs-card">
        <div class="gs-card-img-wrap" style="aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;">
          <svg width="44" height="44" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <div class="gs-card-body"><span class="gs-card-brand">GamTech</span><span class="gs-card-name">Sample Product <?php echo $i; ?></span>
        <div class="gs-card-price"><span class="gs-price-now">$0.00</span></div></div>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gs-add-btn"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>Add to Cart</a>
      </div>
    <?php } }
    ?>
  </div>

  <!-- RECOMMENDED -->
  <div class="gs-sec-hd">
    <div><h2 class="gs-sec-title">Recommended for You</h2><p class="gs-sec-sub">Based on popular picks</p></div>
    <a href="<?php echo esc_url(wc_get_page_permalink('shop').'?orderby=popularity'); ?>" class="gs-view-all">View All</a>
  </div>
  <div class="gs-grid gs-grid-4">
    <?php
    if ($q_rec->have_posts()) {
      while ($q_rec->have_posts()) { $q_rec->the_post(); global $product; gs_card($product); }
      wp_reset_postdata();
    } else {
      for ($i=1;$i<=4;$i++) { ?>
      <div class="gs-card">
        <div class="gs-card-img-wrap" style="aspect-ratio:1/1;display:flex;align-items:center;justify-content:center;">
          <svg width="44" height="44" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>
        </div>
        <div class="gs-card-body"><span class="gs-card-brand">GamTech</span><span class="gs-card-name">Top Pick <?php echo $i; ?></span>
        <div class="gs-stars"><span class="gs-stars-filled">★★★★★</span><span class="gs-stars-count">(0)</span></div>
        <div class="gs-card-price"><span class="gs-price-now">$0.00</span></div></div>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="gs-add-btn"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>Add to Cart</a>
      </div>
    <?php } }
    ?>
  </div>

</main>

<!-- FOOTER FEATURES BAR -->
<footer class="gs-ft">
  <div class="gs-ft-features">
    <div class="gs-ft-card"><div class="gs-ft-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div><div class="gs-ft-body"><h4>Secure Payment</h4><p>100% secure checkout</p></div></div>
    <div class="gs-ft-card"><div class="gs-ft-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div><div class="gs-ft-body"><h4>Easy Returns</h4><p>30-day return policy</p></div></div>
    <div class="gs-ft-card"><div class="gs-ft-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div><div class="gs-ft-body"><h4>24/7 Support</h4><p>Dedicated support team</p></div></div>
    <div class="gs-ft-card"><div class="gs-ft-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div><div class="gs-ft-body"><h4>Trusted by Customers</h4><p>4.8 average rating</p></div></div>
  </div>
</footer>

</div><!-- /.gs-center -->

<!-- ============================================================
     RIGHT CART PANEL
     ============================================================ -->
<aside class="gs-ct" id="gs-ct">
  <div class="gs-ct-hd">
    <h3>My Cart <span class="gs-ct-cnt" id="gs-ct-cnt"><?php echo esc_html($wc_count ?: 2); ?></span></h3>
    <button class="gs-ct-close" id="gs-ct-close" aria-label="Close cart">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>

  <div class="gs-ct-items" id="gs-ct-items">
    <?php if ( ! empty($wc_items) ) :
      foreach ($wc_items as $ci) : ?>
    <div class="gs-ct-item">
      <img class="gs-ct-thumb" src="<?php echo esc_url($ci['img']); ?>" alt="<?php echo esc_attr($ci['name']); ?>" loading="lazy">
      <div class="gs-ct-info">
        <div class="gs-ct-name"><?php echo esc_html($ci['name']); ?></div>
        <div class="gs-ct-sub">GamTech</div>
        <div class="gs-ct-price" data-price="<?php echo esc_attr($ci['price']); ?>"><?php echo wp_kses_post(wc_price($ci['price'])); ?></div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n"><?php echo esc_html($ci['qty']); ?></span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <?php endforeach;
    else : ?>
    <div class="gs-ct-item">
      <div class="gs-ct-thumb-ph"><svg width="20" height="20" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>
      <div class="gs-ct-info">
        <div class="gs-ct-name">Wireless Headphones Pro</div><div class="gs-ct-sub">GamTech Audio</div>
        <div class="gs-ct-price" data-price="129.99">$129.99</div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <div class="gs-ct-item">
      <div class="gs-ct-thumb-ph"><svg width="20" height="20" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/></svg></div>
      <div class="gs-ct-info">
        <div class="gs-ct-name">Mechanical Keyboard RGB</div><div class="gs-ct-sub">GamTech Gaming</div>
        <div class="gs-ct-price" data-price="89.99">$89.99</div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">2</span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <?php endif; ?>
  </div>

  <div class="gs-ct-promo">
    <div class="gs-promo-row">
      <input type="text" id="gs-promo-input" placeholder="Promo Code">
      <button id="gs-promo-apply">Apply</button>
    </div>
    <p class="gs-promo-msg" id="gs-promo-msg"></p>
  </div>

  <div class="gs-ct-sum">
    <div class="gs-sum-row"><span class="lbl">Subtotal</span><span class="val" id="gs-subtotal"><?php echo wp_kses_post(wc_price($wc_total ?: 309.97)); ?></span></div>
    <div class="gs-sum-row disc"><span class="lbl">Discount</span><span class="val" id="gs-discount">$0.00</span></div>
    <div class="gs-sum-row ship"><span class="lbl">Shipping</span><span class="val">Free</span></div>
    <div class="gs-sum-row total"><span class="lbl">Total</span><span class="val" id="gs-total"><?php echo wp_kses_post(wc_price($wc_total ?: 309.97)); ?></span></div>
    <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="gs-checkout">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Checkout (<?php echo esc_html($wc_count ?: 2); ?>)
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <div class="gs-pay-icons"><span>VISA</span><span>MC</span><span>PayPal</span><span>Apple Pay</span></div>
  </div>

  <div class="gs-ct-sugg">
    <h4>You might also like</h4>
    <?php
    $sq = gs_query(['posts_per_page'=>3,'orderby'=>'rand']);
    if ($sq->have_posts()) {
      while ($sq->have_posts()) { $sq->the_post(); global $product;
        $si = $product->get_image_id() ? wp_get_attachment_image_url($product->get_image_id(),'thumbnail') : wc_placeholder_img_src('thumbnail');
        echo '<div class="gs-sugg-item"><img class="gs-sugg-img" src="'.esc_url($si).'" alt="'.esc_attr($product->get_name()).'" loading="lazy"><span class="gs-sugg-name">'.esc_html($product->get_name()).'</span><span class="gs-sugg-price">'.wp_kses_post($product->get_price_html()).'</span><a href="'.esc_url($product->add_to_cart_url()).'" class="gs-sugg-add">+</a></div>';
      }
      wp_reset_postdata();
    } else {
      $suggs=[['USB-C Hub 7-in-1','$49.99'],['Webcam 4K Ultra HD','$79.99'],['NVMe SSD 1TB','$109.99']];
      foreach($suggs as $s) {
        echo '<div class="gs-sugg-item"><div class="gs-sugg-img-ph"><svg width="18" height="18" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div><span class="gs-sugg-name">'.esc_html($s[0]).'</span><span class="gs-sugg-price">'.esc_html($s[1]).'</span><button class="gs-sugg-add">+</button></div>';
      }
    }
    ?>
  </div>

</aside>

</div><!-- .gs-page -->

<script>
(function(){
  'use strict';
  var $ = function(s,c){return (c||document).querySelector(s);};
  var $$ = function(s,c){return Array.from((c||document).querySelectorAll(s));};

  document.addEventListener('DOMContentLoaded', function(){
    /* --- sidebar & cart toggles --- */
    var sb   = $('#gs-sb');
    var ct   = $('#gs-ct');
    var ov   = $('#gs-overlay');

    function openPanel(el){ el.classList.add('open'); ov && ov.classList.add('on'); }
    function closeAll(){ sb&&sb.classList.remove('open'); ct&&ct.classList.remove('open'); ov&&ov.classList.remove('on'); }

    var sbTog = $('#gs-sb-toggle');
    var ctTog = $('#gs-ct-toggle');
    var ctTog2= $('#gs-ct-toggle-mob');
    var ctClose=$('#gs-ct-close');

    sbTog  && sbTog.addEventListener('click',   function(){ openPanel(sb); });
    ctTog  && ctTog.addEventListener('click',   function(){ openPanel(ct); });
    ctTog2 && ctTog2.addEventListener('click',  function(){ openPanel(ct); });
    ctClose&& ctClose.addEventListener('click', closeAll);
    ov     && ov.addEventListener('click',      closeAll);

    /* --- qty controls & delete --- */
    var ctItems = $('#gs-ct-items');
    ctItems && ctItems.addEventListener('click', function(e){
      var btn = e.target.closest('.gs-qty-btn');
      if (btn) {
        var n = btn.closest('.gs-qty').querySelector('.gs-qty-n');
        var v = parseInt(n.textContent)||1;
        n.textContent = btn.dataset.action==='minus' ? Math.max(1,v-1) : v+1;
        recalc();
        return;
      }
      var del = e.target.closest('.gs-ct-del');
      if (del) {
        var item = del.closest('.gs-ct-item');
        item.style.cssText='opacity:0;transform:translateX(18px);transition:all .22s';
        setTimeout(function(){ item.remove(); recalc(); updateBadge(); }, 230);
      }
    });

    /* --- recalc totals --- */
    function recalc(){
      var sub=0, hasPromo=document.getElementById('gs-promo-applied');
      $$('.gs-ct-item').forEach(function(item){
        var pr=parseFloat((item.querySelector('.gs-ct-price')||{}).dataset&&item.querySelector('.gs-ct-price').dataset.price)||0;
        var qty=parseInt((item.querySelector('.gs-qty-n')||{}).textContent)||1;
        sub+=pr*qty;
      });
      var disc= hasPromo ? sub*0.1 : 0;
      var tot = sub - disc;
      var el=document.getElementById('gs-subtotal'); if(el) el.textContent='$'+sub.toFixed(2);
      var el2=document.getElementById('gs-discount'); if(el2) el2.textContent=disc>0?'-$'+disc.toFixed(2):'$0.00';
      var el3=document.getElementById('gs-total');   if(el3) el3.textContent='$'+tot.toFixed(2);
    }

    function updateBadge(){
      var n=$$('.gs-ct-item').length;
      var b=document.getElementById('gs-cart-badge'); if(b) b.textContent=n;
      var b2=document.getElementById('gs-cart-badge-mob'); if(b2) b2.textContent=n;
      var c=document.getElementById('gs-ct-cnt');     if(c) c.textContent=n;
    }

    /* --- add to cart (inject demo item) --- */
    document.addEventListener('click', function(e){
      var btn = e.target.closest('.gs-add-btn');
      if (!btn) return;
      var card = btn.closest('.gs-card');
      if (!card) return;
      var name  = (card.querySelector('.gs-card-name')||{}).textContent||'Product';
      var price = btn.dataset.price||'0';
      var img   = btn.dataset.img||'';
      var pn    = parseFloat(price)||0;
      var html  =
        '<div class="gs-ct-item" style="animation:fadeIn .3s ease">' +
        (img ? '<img class="gs-ct-thumb" src="'+img+'" alt="">' : '<div class="gs-ct-thumb-ph"><svg width="18" height="18" fill="none" stroke="var(--border2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>') +
        '<div class="gs-ct-info"><div class="gs-ct-name">'+name.replace(/</g,'&lt;')+'</div><div class="gs-ct-sub">GamTech</div>' +
        '<div class="gs-ct-price" data-price="'+pn.toFixed(2)+'">$'+pn.toFixed(2)+'</div>' +
        '<div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div></div>' +
        '<button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button></div>';
      ctItems && ctItems.insertAdjacentHTML('beforeend', html);
      recalc(); updateBadge();
      if (window.innerWidth>1024) openPanel(ct);
      /* feedback on button */
      var orig=btn.innerHTML;
      btn.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Added!';
      btn.style.cssText='background:var(--green);color:#fff';
      setTimeout(function(){ btn.innerHTML=orig; btn.style.cssText=''; }, 1200);
    });

    /* --- wishlist toggle --- */
    document.addEventListener('click', function(e){
      var btn=e.target.closest('.gs-wish');
      if(!btn) return;
      btn.classList.toggle('wishlisted');
      btn.style.transform='scale(1.35)';
      setTimeout(function(){btn.style.transform='';},200);
    });

    /* --- promo code --- */
    var applyBtn=document.getElementById('gs-promo-apply');
    var promoMsg=document.getElementById('gs-promo-msg');
    if(applyBtn){
      applyBtn.addEventListener('click',function(){
        var code=(document.getElementById('gs-promo-input')||{}).value;
        code=(code||'').trim().toUpperCase();
        if(['TECH10','GAMTECH','SAVE10'].indexOf(code)!==-1){
          if(promoMsg){promoMsg.textContent='✓ 10% discount applied!';promoMsg.style.color='var(--green)';}
          var m=document.createElement('span');m.id='gs-promo-applied';m.hidden=true;document.body.appendChild(m);
          recalc(); applyBtn.textContent='Applied'; applyBtn.style.background='var(--green)'; applyBtn.disabled=true;
        } else {
          if(promoMsg){promoMsg.textContent=code?'Invalid code. Try TECH10':'Enter a code.';promoMsg.style.color='var(--red)';}
        }
      });
      var pi=document.getElementById('gs-promo-input');
      pi&&pi.addEventListener('keydown',function(e){if(e.key==='Enter')applyBtn.click();});
    }

    /* --- category active state --- */
    $$('.gs-cat').forEach(function(c){
      c.addEventListener('click',function(){$$('.gs-cat').forEach(function(x){x.classList.remove('active')});c.classList.add('active');});
    });

    /* --- scroll-in animation --- */
    if('IntersectionObserver' in window){
      var io=new IntersectionObserver(function(entries){
        entries.forEach(function(en){if(en.isIntersecting){en.target.style.animation='fadeIn .35s ease both';io.unobserve(en.target);}});
      },{threshold:.1});
      $$('.gs-card,.gs-promo,.gs-ft-card').forEach(function(el){io.observe(el);});
    }
  });
})();
</script>
<style>@keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}</style>

<?php wp_footer(); ?>
</body>
</html>
