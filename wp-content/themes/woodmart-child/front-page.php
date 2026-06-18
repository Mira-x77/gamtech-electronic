<?php
/**
 * Homepage — GamTech Electronics
 * Black / Purple / White layout. Self-contained (no header.php / footer.php).
 */
defined( 'ABSPATH' ) || exit;

/* ── helpers ─────────────────────────────────────────── */
function gs_q( $a = [] ) {
    return new WP_Query( array_merge([
        'post_type'=>'product','post_status'=>'publish','posts_per_page'=>8
    ], $a ) );
}

function gs_card( $p, $badge='' ) {
    if(!$p) return;
    $id   = $p->get_id();
    $link = get_permalink($id);
    $img  = $p->get_image_id() ? wp_get_attachment_image_url($p->get_image_id(),'woocommerce_thumbnail') : wc_placeholder_img_src();
    $name = $p->get_name();
    $rat  = (float)$p->get_average_rating();
    $rev  = (int)$p->get_review_count();
    $reg  = (float)$p->get_regular_price();
    $sal  = (float)$p->get_sale_price();
    $sale = $p->is_on_sale();
    $prc  = (float)$p->get_price();
    $add  = ($p->is_purchasable()&&$p->is_in_stock()) ? $p->add_to_cart_url() : $link;
    if(!$badge && $sale) $badge='sale';
    $pct  = ($sale&&$reg>0) ? round(($reg-$sal)/$reg*100) : 0;
    ?>
<div class="gs-card">
    <?php if($badge):?><span class="gs-bdg gs-bdg-<?=esc_attr($badge)?>"><?=$badge==='sale'?'Sale':esc_html(ucfirst($badge))?></span><?php endif;?>
    <a href="<?=esc_url($link)?>" class="gs-card-img">
        <img src="<?=esc_url($img)?>" alt="<?=esc_attr($name)?>" loading="lazy">
    </a>
    <div class="gs-card-body">
        <span class="gs-card-brand">GamTech</span>
        <a href="<?=esc_url($link)?>" class="gs-card-name"><?=esc_html($name)?></a>
        <?php if($rat>0):?>
        <div class="gs-stars">
            <span class="gs-st-f"><?php for($i=0;$i<min(5,round($rat));$i++) echo '★';?></span>
            <span class="gs-st-e"><?php for($i=round($rat);$i<5;$i++) echo '★';?></span>
            <span class="gs-st-c">(<?=esc_html($rev)?>)</span>
        </div>
        <?php endif;?>
        <div class="gs-card-price">
            <?php if($sale&&$reg>0):?>
            <span class="gs-pnow"><?=wp_kses_post(wc_price($sal))?></span>
            <span class="gs-pwas"><?=wp_kses_post(wc_price($reg))?></span>
            <span class="gs-psave">-<?=esc_html($pct)?>%</span>
            <?php else:?>
            <span class="gs-pnow"><?=wp_kses_post($p->get_price_html())?></span>
            <?php endif;?>
        </div>
    </div>
    <a href="<?=esc_url($add)?>" class="gs-add-btn"
       data-id="<?=esc_attr($id)?>" data-price="<?=esc_attr($prc)?>"
       data-name="<?=esc_attr($name)?>" data-img="<?=esc_attr($img)?>">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        Add to Cart
    </a>
</div>
    <?php
}

/* ── queries ──────────────────────────────────────────── */
$q_deals = gs_q(['posts_per_page'=>8,'meta_query'=>[['key'=>'_sale_price','value'=>'','compare'=>'!=']],'orderby'=>'rand']);
if(!$q_deals->have_posts()) $q_deals = gs_q(['posts_per_page'=>8,'orderby'=>'date']);
$q_rec = gs_q(['posts_per_page'=>4,'meta_key'=>'total_sales','orderby'=>'meta_value_num','order'=>'DESC']);

/* ── WC cart ──────────────────────────────────────────── */
$wc_items=[]; $wc_total=0; $wc_count=0;
if(function_exists('WC')&&WC()->cart){
    foreach(WC()->cart->get_cart() as $v){
        $cp=wc_get_product($v['product_id']); if(!$cp) continue;
        $wc_items[]=['name'=>$cp->get_name(),'qty'=>$v['quantity'],'price'=>(float)$cp->get_price(),
            'img'=>$cp->get_image_id()?wp_get_attachment_image_url($cp->get_image_id(),'thumbnail'):wc_placeholder_img_src('thumbnail')];
        $wc_total+=(float)$cp->get_price()*(int)$v['quantity'];
    }
    $wc_count=WC()->cart->get_cart_contents_count();
}

/* ── categories (hardcoded to match store) ────────────── */
$store_cats = [
    'Mice'               => '<path d="M12 2a6 6 0 0 1 6 6v8a6 6 0 0 1-12 0V8a6 6 0 0 1 6-6z"/><line x1="12" y1="2" x2="12" y2="8"/>',
    'Keyboards'          => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 11h.01M10 11h.01M14 11h.01M18 11h.01M8 15h8"/>',
    'Headphones & Audio' => '<path d="M3 18v-6a9 9 0 0 1 18 0v6"/><path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>',
    'Storage'            => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
    'RAM & Memory'       => '<rect x="4" y="8" width="16" height="8" rx="1"/><path d="M8 8V6M12 8V6M16 8V6M8 16v2M12 16v2M16 16v2"/>',
    'Networking'         => '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
    'Cables & Converters'=> '<path d="M18 4l3 3-3 3"/><path d="M3 7h18M6 20l-3-3 3-3"/><path d="M21 17H3"/>',
    'Laptop Accessories' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>',
    'Computer Accessories'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
    'Tools & Repair'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
    'Adapters & Hubs'    => '<circle cx="12" cy="12" r="3"/><path d="M12 2v4M12 18v4M4.22 4.22l2.83 2.83M16.95 16.95l2.83 2.83M2 12h4M18 12h4M4.22 19.78l2.83-2.83M16.95 7.05l2.83-2.83"/>',
];
?>
<!DOCTYPE html>
<html <?php language_attributes();?>>
<head>
<meta charset="<?php bloginfo('charset');?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php wp_title('|',true,'right');bloginfo('name');?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<?php wp_head();?>
<style>
/* ═══ DESIGN TOKENS ═══════════════════════════════════════ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0f0f0f;--bg2:#161616;--bg3:#1e1e1e;--card:#181818;
  --b1:#252525;--b2:#2e2e2e;
  --pu:#7c3aed;--pud:#6d28d9;--pul:#a78bfa;--pug:rgba(124,58,237,.22);
  --wh:#fff;--of:#f4f4f5;--tx:#d4d4d8;--mu:#71717a;--di:#52525b;
  --re:#ef4444;--gr:#22c55e;--ye:#f59e0b;
  --r:12px;--rs:8px;--tr:all .2s ease;
  --font:'Poppins','Segoe UI',Arial,sans-serif;
  --sw:244px;--cw:296px;
}
html,body{height:100%;margin:0;padding:0}
body{font-family:var(--font);background:var(--bg);color:var(--tx)}
a{color:inherit;text-decoration:none;transition:var(--tr)}
img{max-width:100%;height:auto;display:block}
button{font-family:var(--font);cursor:pointer;border:none;outline:none;background:none}

/* ═══ OUTER WRAPPER — fixed viewport container ═══════════
   position:fixed + overflow:hidden keeps BOTH sidebars
   locked regardless of what WP/Woodmart does to <body>    */
.gs-page{
  position:fixed;inset:0;
  display:flex;flex-direction:row;
  overflow:hidden;
  z-index:1;
}

/* ═══ LEFT SIDEBAR ════════════════════════════════════════ */
.gs-sb{
  width:var(--sw);flex-shrink:0;height:100%;
  background:var(--bg2);border-right:1px solid var(--b1);
  display:flex;flex-direction:column;
  overflow-y:auto;overflow-x:hidden;
  scrollbar-width:thin;scrollbar-color:var(--b2) transparent;
}
.gs-sb::-webkit-scrollbar{width:3px}
.gs-sb::-webkit-scrollbar-thumb{background:var(--b2)}

/* logo */
.gs-logo{display:flex;align-items:center;gap:10px;padding:20px 16px 16px;border-bottom:1px solid var(--b1);flex-shrink:0}
.gs-logo-ico{width:34px;height:34px;background:linear-gradient(135deg,#7c3aed,#5b21b6);
  border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-logo-txt{font-size:17px;font-weight:800;color:var(--wh)}
.gs-logo-txt span{color:var(--pul)}

/* nav */
.gs-nav-sec{padding:12px 8px 4px}
.gs-nav-lbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1.3px;
  color:var(--di);padding:0 8px;margin-bottom:4px}
.gs-nav-a{display:flex;align-items:center;gap:10px;padding:9px 10px;
  border-radius:var(--rs);font-size:12.5px;font-weight:500;
  color:var(--mu);transition:var(--tr);position:relative;white-space:nowrap}
.gs-nav-a svg{flex-shrink:0;opacity:.7;transition:var(--tr)}
.gs-nav-a:hover{background:var(--bg3);color:var(--wh)}
.gs-nav-a:hover svg{opacity:1}
.gs-nav-a.active{background:rgba(124,58,237,.14);color:var(--pul)}
.gs-nav-a.active svg{opacity:1}
.gs-nav-a.active::before{content:'';position:absolute;left:0;top:50%;
  transform:translateY(-50%);width:3px;height:58%;
  background:var(--pu);border-radius:0 3px 3px 0}

/* sidebar cats (collapsible) */
.gs-sb-cats{padding:12px 8px 8px}
.gs-sb-cats-title{display:flex;align-items:center;justify-content:space-between;
  font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1.3px;
  color:var(--di);padding:0 8px;margin-bottom:6px;cursor:pointer}
.gs-sb-cats-title svg{transition:transform .2s}
.gs-sb-cats-title.open svg{transform:rotate(180deg)}
.gs-sb-cats-list{display:flex;flex-direction:column;gap:1px}
.gs-sb-cat{display:flex;align-items:center;gap:9px;padding:8px 10px;
  border-radius:var(--rs);font-size:12px;font-weight:500;color:var(--mu);
  transition:var(--tr);cursor:pointer}
.gs-sb-cat:hover{background:var(--bg3);color:var(--wh)}
.gs-sb-cat svg{flex-shrink:0;opacity:.6}
.gs-sb-cat:hover svg{opacity:1}

/* sidebar promo card */
.gs-sb-promo{margin:8px;border-radius:var(--r);
  background:linear-gradient(135deg,#7c3aed,#5b21b6);
  padding:16px 14px;position:relative;overflow:hidden;flex-shrink:0}
.gs-sb-promo::before{content:'';position:absolute;top:-20px;right:-20px;
  width:100px;height:100px;background:rgba(255,255,255,.07);border-radius:50%}
.gs-sb-promo::after{content:'';position:absolute;bottom:-18px;left:-18px;
  width:70px;height:70px;background:rgba(255,255,255,.05);border-radius:50%}
.gs-sb-promo .sptag{font-size:9px;font-weight:700;text-transform:uppercase;
  letter-spacing:1px;color:rgba(255,255,255,.7);margin-bottom:5px;position:relative;z-index:1}
.gs-sb-promo h4{font-size:14px;font-weight:800;color:var(--wh);
  line-height:1.25;margin-bottom:3px;position:relative;z-index:1}
.gs-sb-promo p{font-size:10px;color:rgba(255,255,255,.7);
  margin-bottom:12px;position:relative;z-index:1}
.gs-sb-promo a{display:inline-block;background:var(--wh);color:var(--pu);
  font-size:11px;font-weight:700;padding:6px 14px;border-radius:16px;
  position:relative;z-index:1;transition:var(--tr)}
.gs-sb-promo a:hover{background:var(--of);transform:scale(1.02)}

/* support */
.gs-sb-support{margin-top:auto;padding:12px 16px;border-top:1px solid var(--b1);
  display:flex;align-items:center;gap:9px;font-size:11px;color:var(--mu);flex-shrink:0}
.gs-sb-support .sdot{width:7px;height:7px;background:var(--gr);border-radius:50%;
  flex-shrink:0;box-shadow:0 0 5px var(--gr);animation:sbpulse 2s infinite}
@keyframes sbpulse{0%,100%{opacity:1}50%{opacity:.4}}
.gs-sb-support strong{display:block;font-size:11.5px;color:var(--tx)}
</style>
<style>
/* ═══ CENTER COLUMN ═══════════════════════════════════════ */
.gs-center{flex:1;min-width:0;display:flex;flex-direction:column;overflow:hidden;height:100%}

/* header */
.gs-hd{flex-shrink:0;background:var(--bg2);border-bottom:1px solid var(--b1);
  display:flex;align-items:center;gap:12px;padding:12px 20px;z-index:90}
.gs-search{flex:1;max-width:520px;position:relative}
.gs-search input{width:100%;background:var(--bg3);border:1.5px solid var(--b2);
  border-radius:22px;color:var(--tx);font-family:var(--font);font-size:13px;
  padding:10px 44px 10px 18px;transition:var(--tr)}
.gs-search input::placeholder{color:var(--di)}
.gs-search input:focus{border-color:var(--pu);outline:none;box-shadow:0 0 0 3px var(--pug);background:var(--bg2)}
.gs-search-btn{position:absolute;right:5px;top:50%;transform:translateY(-50%);
  width:32px;height:32px;background:var(--pu);border-radius:50%;
  display:flex;align-items:center;justify-content:center;transition:var(--tr)}
.gs-search-btn:hover{background:var(--pud)}
.gs-search-btn svg{color:var(--wh)}
.gs-hd-icons{display:flex;align-items:center;gap:5px;margin-left:auto}
.gs-hd-btn{position:relative;width:38px;height:38px;border-radius:9px;background:var(--bg3);
  border:1px solid var(--b2);display:flex;align-items:center;justify-content:center;
  color:var(--mu);transition:var(--tr);cursor:pointer}
.gs-hd-btn:hover{color:var(--wh);background:var(--bg)}
.gs-hd-btn .bdg{position:absolute;top:-4px;right:-4px;min-width:17px;height:17px;
  background:var(--pu);color:var(--wh);font-size:9.5px;font-weight:700;border-radius:8px;
  display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid var(--bg2)}
.gs-avatar{display:flex;align-items:center;gap:8px;padding:5px 12px 5px 5px;
  background:var(--bg3);border:1px solid var(--b2);border-radius:22px;cursor:pointer;
  transition:var(--tr);margin-left:3px}
.gs-avatar:hover{border-color:var(--pu)}
.gs-avatar .av-img{width:28px;height:28px;border-radius:50%;
  background:linear-gradient(135deg,#7c3aed,#5b21b6);display:flex;align-items:center;
  justify-content:center;font-size:12px;font-weight:700;color:var(--wh)}
.gs-avatar .av-nm{font-size:12.5px;font-weight:600;color:var(--tx);white-space:nowrap}

/* scrollable main */
.gs-mn{flex:1;overflow-y:auto;overflow-x:hidden;background:var(--bg);padding:20px;
  scrollbar-width:thin;scrollbar-color:var(--b2) transparent}
.gs-mn::-webkit-scrollbar{width:5px}
.gs-mn::-webkit-scrollbar-thumb{background:var(--b2);border-radius:3px}

/* footer inside scroll */
.gs-ft{background:var(--bg2);border-top:1px solid var(--b1);padding:16px 20px;flex-shrink:0}

/* section headings */
.gs-sh{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:14px;gap:10px}
.gs-sh h2{font-size:16px;font-weight:700;color:var(--wh)}
.gs-sh p{font-size:11px;color:var(--mu);margin-top:2px}
.gs-viewall{font-size:11.5px;font-weight:600;color:var(--pul);white-space:nowrap;
  padding:5px 12px;border-radius:16px;border:1px solid rgba(124,58,237,.3);transition:var(--tr)}
.gs-viewall:hover{background:rgba(124,58,237,.14);border-color:var(--pu);color:var(--wh)}
</style>
<style>
/* ═══ HERO ════════════════════════════════════════════════ */
.gs-hero{background:linear-gradient(135deg,#1a0533 0%,#3b0764 38%,#5b21b6 72%,#7c3aed 100%);
  border-radius:14px;padding:36px 42px;display:grid;grid-template-columns:1fr 1fr;
  align-items:center;gap:24px;position:relative;overflow:hidden;margin-bottom:20px;min-height:200px}
.gs-hero::before{content:'';position:absolute;top:-50px;right:28%;width:190px;height:190px;
  background:rgba(255,255,255,.04);border-radius:50%}
.gs-hero::after{content:'';position:absolute;bottom:-65px;right:-30px;width:250px;height:250px;
  background:rgba(124,58,237,.12);border-radius:50%}
.gs-hero-tag{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.11);
  border:1px solid rgba(255,255,255,.14);border-radius:16px;padding:4px 12px;
  font-size:10px;font-weight:600;color:rgba(255,255,255,.82);text-transform:uppercase;
  letter-spacing:.9px;margin-bottom:11px}
.gs-hero-tag .dot{width:5px;height:5px;background:var(--ye);border-radius:50%}
.gs-hero h1{font-size:clamp(22px,2.6vw,34px);font-weight:900;color:var(--wh);
  line-height:1.15;margin-bottom:9px}
.gs-hero h1 span{color:#c4b5fd}
.gs-hero p{font-size:13px;color:rgba(255,255,255,.68);line-height:1.65;margin-bottom:24px;max-width:320px}
.gs-hero-cta{display:inline-flex;align-items:center;gap:7px;background:var(--wh);
  color:var(--pu);font-size:13px;font-weight:700;padding:10px 24px;border-radius:20px;
  transition:var(--tr);box-shadow:0 4px 18px rgba(255,255,255,.14)}
.gs-hero-cta:hover{background:var(--of);transform:translateY(-2px);color:var(--pud)}
.gs-hero-img{display:flex;align-items:center;justify-content:center;position:relative;z-index:1}
.gs-hero-img img{max-height:180px;object-fit:contain;
  filter:drop-shadow(0 14px 36px rgba(0,0,0,.5));animation:gsfloat 3.5s ease-in-out infinite}
.gs-hero-ph{width:170px;height:170px;margin:0 auto;background:rgba(255,255,255,.05);
  border-radius:50%;display:flex;align-items:center;justify-content:center;
  animation:gsfloat 3.5s ease-in-out infinite}
@keyframes gsfloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

/* ═══ CATEGORY ICONS ROW ═══════════════════════════════════ */
.gs-cats{display:flex;gap:10px;overflow-x:auto;padding-bottom:4px;
  margin-bottom:24px;scrollbar-width:none}
.gs-cats::-webkit-scrollbar{display:none}
.gs-cat{display:flex;flex-direction:column;align-items:center;gap:7px;
  flex-shrink:0;cursor:pointer;transition:var(--tr)}
.gs-cat:hover{transform:translateY(-3px)}
.gs-cat-ico{width:60px;height:60px;background:var(--card);border:1.5px solid var(--b1);
  border-radius:50%;display:flex;align-items:center;justify-content:center;transition:var(--tr)}
.gs-cat:hover .gs-cat-ico,.gs-cat.active .gs-cat-ico{
  border-color:var(--pu);background:rgba(124,58,237,.12);box-shadow:0 0 0 3px var(--pug)}
.gs-cat-ico svg{color:var(--mu);transition:var(--tr)}
.gs-cat:hover .gs-cat-ico svg,.gs-cat.active .gs-cat-ico svg{color:var(--pul)}
.gs-cat-lbl{font-size:10.5px;font-weight:500;color:var(--mu);text-align:center;
  white-space:nowrap;max-width:72px;overflow:hidden;text-overflow:ellipsis;transition:var(--tr)}
.gs-cat:hover .gs-cat-lbl,.gs-cat.active .gs-cat-lbl{color:var(--wh)}

/* ═══ PROMO CARDS ══════════════════════════════════════════ */
.gs-promos{display:grid;grid-template-columns:repeat(4,1fr);gap:11px;margin-bottom:26px}
.gs-promo{background:var(--card);border:1px solid var(--b1);border-radius:var(--r);
  padding:14px;display:flex;align-items:center;gap:12px;transition:var(--tr);
  cursor:pointer;position:relative;overflow:hidden}
.gs-promo::before{content:'';position:absolute;inset:0;
  background:linear-gradient(135deg,rgba(124,58,237,.07) 0%,transparent 60%);
  opacity:0;transition:var(--tr)}
.gs-promo:hover::before{opacity:1}
.gs-promo:hover{border-color:var(--pu);transform:translateY(-2px);
  box-shadow:0 8px 22px rgba(124,58,237,.14)}
.gs-pi{width:44px;height:44px;border-radius:10px;display:flex;
  align-items:center;justify-content:center;flex-shrink:0}
.gs-pi.r{background:rgba(239,68,68,.14);color:#f87171}
.gs-pi.g{background:rgba(34,197,94,.14);color:#4ade80}
.gs-pi.p{background:rgba(124,58,237,.18);color:var(--pul)}
.gs-pi.y{background:rgba(245,158,11,.14);color:#fbbf24}
.gs-promo-b h4{font-size:12.5px;font-weight:700;color:var(--wh);margin-bottom:2px}
.gs-promo-b p{font-size:10.5px;color:var(--mu);line-height:1.35}
.gs-promo-b .pcta{font-size:10px;font-weight:600;color:var(--pul);
  margin-top:3px;display:inline-flex;align-items:center;gap:3px}

/* ═══ PRODUCT GRID ════════════════════════════════════════ */
.gs-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:26px}

/* ═══ PRODUCT CARD ════════════════════════════════════════ */
.gs-card{background:var(--card);border:1px solid var(--b1);border-radius:var(--r);
  overflow:hidden;transition:var(--tr);position:relative;display:flex;flex-direction:column}
.gs-card:hover{border-color:var(--b2);transform:translateY(-3px);box-shadow:0 12px 30px rgba(0,0,0,.5)}
.gs-bdg{position:absolute;top:9px;left:9px;font-size:9.5px;font-weight:700;
  padding:2px 7px;border-radius:5px;text-transform:uppercase;z-index:2}
.gs-bdg-sale{background:var(--re);color:var(--wh)}
.gs-bdg-new{background:var(--pu);color:var(--wh)}
.gs-card-img{background:var(--bg3);aspect-ratio:1/1;overflow:hidden;display:block;position:relative}
.gs-card-img img{width:100%;height:100%;object-fit:cover;transition:transform .3s ease}
.gs-card:hover .gs-card-img img{transform:scale(1.05)}
.gs-card-body{padding:12px;display:flex;flex-direction:column;gap:5px;flex:1}
.gs-card-brand{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.7px;color:var(--pul)}
.gs-card-name{font-size:12.5px;font-weight:600;color:var(--tx);line-height:1.38;
  display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;transition:var(--tr)}
.gs-card:hover .gs-card-name{color:var(--wh)}
.gs-stars{display:flex;align-items:center;gap:3px;font-size:10.5px}
.gs-st-f{color:var(--ye)}.gs-st-e{color:var(--b2)}.gs-st-c{color:var(--di)}
.gs-card-price{display:flex;align-items:center;gap:7px;flex-wrap:wrap;margin-top:auto}
.gs-pnow{font-size:14px;font-weight:800;color:var(--wh)}
.gs-pwas{font-size:11px;color:var(--di);text-decoration:line-through}
.gs-psave{font-size:10px;font-weight:700;color:var(--gr)}
.gs-add-btn{display:flex;align-items:center;justify-content:center;gap:6px;width:100%;
  background:var(--bg3);border-top:1px solid var(--b2);color:var(--mu);
  font-size:12px;font-weight:600;padding:9px;transition:var(--tr)}
.gs-add-btn:hover{background:var(--pu);color:var(--wh)}
</style>
<style>
/* ═══ CART PANEL ══════════════════════════════════════════ */
.gs-ct{width:var(--cw);flex-shrink:0;height:100%;background:var(--bg2);
  border-left:1px solid var(--b1);display:flex;flex-direction:column;
  overflow-y:auto;scrollbar-width:thin;scrollbar-color:var(--b2) transparent}
.gs-ct::-webkit-scrollbar{width:3px}
.gs-ct::-webkit-scrollbar-thumb{background:var(--b2)}
.gs-ct-hd{display:flex;align-items:center;justify-content:space-between;
  padding:16px 14px 12px;border-bottom:1px solid var(--b1);
  position:sticky;top:0;background:var(--bg2);z-index:2;flex-shrink:0}
.gs-ct-hd h3{font-size:14px;font-weight:700;color:var(--wh)}
.gs-ct-cnt{background:var(--pu);color:var(--wh);font-size:10px;font-weight:700;
  padding:2px 8px;border-radius:9px;margin-left:6px}
.gs-ct-close{width:26px;height:26px;border-radius:6px;background:var(--bg3);
  border:1px solid var(--b2);display:flex;align-items:center;justify-content:center;
  color:var(--mu);cursor:pointer;transition:var(--tr)}
.gs-ct-close:hover{background:var(--re);border-color:var(--re);color:var(--wh)}
.gs-ct-items{padding:8px;flex:1}
.gs-ct-item{display:flex;align-items:flex-start;gap:9px;padding:10px 0;border-bottom:1px solid var(--b1)}
.gs-ct-item:last-child{border-bottom:none}
.gs-ct-thumb{width:52px;height:52px;border-radius:7px;object-fit:cover;background:var(--bg3);flex-shrink:0}
.gs-ct-thumb-ph{width:52px;height:52px;border-radius:7px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-ct-info{flex:1;min-width:0}
.gs-ct-name{font-size:11.5px;font-weight:600;color:var(--tx);
  white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px}
.gs-ct-sub{font-size:10px;color:var(--di);margin-bottom:4px}
.gs-ct-price{font-size:12px;font-weight:700;color:var(--wh)}
.gs-qty{display:flex;align-items:center;gap:5px;margin-top:5px}
.gs-qty-btn{width:22px;height:22px;border-radius:5px;background:var(--bg3);
  border:1px solid var(--b2);color:var(--mu);font-size:13px;
  display:flex;align-items:center;justify-content:center;cursor:pointer;line-height:1;transition:var(--tr)}
.gs-qty-btn:hover{background:var(--pu);border-color:var(--pu);color:var(--wh)}
.gs-qty-n{font-size:11.5px;font-weight:600;color:var(--tx);min-width:18px;text-align:center}
.gs-ct-del{color:var(--di);cursor:pointer;margin-left:auto;align-self:flex-start;
  transition:var(--tr);background:none;border:none}
.gs-ct-del:hover{color:var(--re)}
.gs-ct-promo{padding:10px;border-top:1px solid var(--b1)}
.gs-prom-row{display:flex;gap:6px}
.gs-prom-row input{flex:1;background:var(--bg3);border:1.5px solid var(--b2);border-radius:7px;
  color:var(--tx);font-family:var(--font);font-size:11px;padding:8px 10px;transition:var(--tr)}
.gs-prom-row input::placeholder{color:var(--di)}
.gs-prom-row input:focus{border-color:var(--pu);outline:none}
.gs-prom-row button{background:var(--pu);color:var(--wh);font-size:11px;font-weight:700;
  padding:8px 13px;border-radius:7px;cursor:pointer;border:none;transition:var(--tr)}
.gs-prom-row button:hover{background:var(--pud)}
.gs-prom-msg{font-size:10px;margin-top:5px}
.gs-ct-sum{padding:12px 14px;border-top:1px solid var(--b1)}
.gs-srow{display:flex;justify-content:space-between;align-items:center;
  margin-bottom:6px;font-size:12px}
.gs-srow .l{color:var(--mu)}.gs-srow .v{font-weight:600;color:var(--tx)}
.gs-srow.disc .v{color:var(--gr)}.gs-srow.ship .v{color:var(--gr)}
.gs-srow.tot{font-size:15px;font-weight:800;margin-top:10px;padding-top:10px;
  border-top:1px solid var(--b2);margin-bottom:0}
.gs-srow.tot .v{color:var(--wh);font-size:17px}
.gs-checkout-btn{display:flex;align-items:center;justify-content:center;gap:7px;
  width:100%;background:linear-gradient(135deg,#7c3aed,#5b21b6);color:var(--wh);
  font-size:13px;font-weight:700;padding:12px;border-radius:10px;margin-top:12px;
  transition:var(--tr);box-shadow:0 4px 18px var(--pug);cursor:pointer;border:none}
.gs-checkout-btn:hover{transform:translateY(-2px);filter:brightness(1.1)}
.gs-pay-ico{display:flex;align-items:center;justify-content:center;gap:5px;margin-top:10px}
.gs-pay-ico span{background:var(--bg3);border:1px solid var(--b2);border-radius:3px;
  padding:2px 6px;font-size:9px;font-weight:700;color:var(--mu)}
.gs-ct-sugg{padding:12px 14px;border-top:1px solid var(--b1)}
.gs-ct-sugg h4{font-size:11.5px;font-weight:700;color:var(--wh);margin-bottom:9px}
.gs-sugg-item{display:flex;align-items:center;gap:9px;padding:7px 0;
  border-bottom:1px solid var(--b1);cursor:pointer;transition:var(--tr)}
.gs-sugg-item:last-child{border-bottom:none}
.gs-sugg-item:hover{opacity:.78}
.gs-sugg-img{width:40px;height:40px;border-radius:7px;object-fit:cover;background:var(--bg3);flex-shrink:0}
.gs-sugg-img-ph{width:40px;height:40px;border-radius:7px;background:var(--bg3);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-sugg-nm{flex:1;font-size:11px;color:var(--tx);font-weight:500;line-height:1.3}
.gs-sugg-pr{font-size:11px;font-weight:700;color:var(--wh);white-space:nowrap}
.gs-sugg-add{width:24px;height:24px;background:var(--pu);border-radius:50%;
  display:flex;align-items:center;justify-content:center;color:var(--wh);
  font-size:15px;line-height:1;cursor:pointer;transition:var(--tr);flex-shrink:0;border:none}
.gs-sugg-add:hover{background:var(--pud);transform:scale(1.1)}

/* ═══ FOOTER FEATURES ═════════════════════════════════════ */
.gs-ft-features{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.gs-ft-card{background:var(--card);border:1px solid var(--b1);border-radius:var(--r);
  padding:14px;display:flex;align-items:center;gap:10px;transition:var(--tr)}
.gs-ft-card:hover{border-color:var(--pu);background:rgba(124,58,237,.05)}
.gs-ft-ico{width:40px;height:40px;border-radius:10px;background:rgba(124,58,237,.14);
  display:flex;align-items:center;justify-content:center;flex-shrink:0}
.gs-ft-ico svg{color:var(--pul)}
.gs-ft-b h4{font-size:12px;font-weight:700;color:var(--wh);margin-bottom:2px}
.gs-ft-b p{font-size:10px;color:var(--mu)}
</style>
<style>
/* ═══ MOBILE BAR ══════════════════════════════════════════ */
.gs-mob-bar{
  display:none;align-items:center;justify-content:space-between;
  padding:0 14px;height:56px;
  background:var(--bg2);border-bottom:1px solid var(--b1);
  position:absolute;top:0;left:0;right:0;z-index:200;flex-shrink:0;
}
.gs-mob-btn{background:var(--bg3);border:1px solid var(--b2);color:var(--tx);
  width:44px;height:44px;border-radius:10px;
  display:flex;align-items:center;justify-content:center;
  cursor:pointer;transition:var(--tr);flex-shrink:0;
  -webkit-tap-highlight-color:transparent}
.gs-mob-btn:active{background:var(--pu);border-color:var(--pu)}
.gs-mob-logo{font-size:17px;font-weight:800;color:var(--wh)}
.gs-mob-logo span{color:var(--pul)}
.gs-mob-cart-wrap{position:relative}
.gs-mob-cart-bdg{position:absolute;top:-5px;right:-5px;min-width:18px;height:18px;
  background:var(--pu);color:var(--wh);font-size:10px;font-weight:700;border-radius:9px;
  display:flex;align-items:center;justify-content:center;
  padding:0 4px;border:2px solid var(--bg2)}

/* overlay */
.gs-overlay{display:none;position:absolute;inset:0;
  background:rgba(0,0,0,.7);z-index:150;
  backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px)}
.gs-overlay.on{display:block}

/* ═══ RESPONSIVE ══════════════════════════════════════════
   Strategy: .gs-page is always position:fixed and overflow:hidden.
   Sidebars are flex children — so they NEVER scroll with page.
   On ≤1024px the cart becomes position:absolute inside .gs-page.
   On ≤768px BOTH sidebars become position:absolute drawers.
   ═══════════════════════════════════════════════════════ */

/* ── 1280px: tighten cart ── */
@media(max-width:1280px){
  :root{--cw:272px}
  .gs-grid{grid-template-columns:repeat(3,1fr)}
  .gs-promos{grid-template-columns:repeat(2,1fr)}
}

/* ── 1024px: cart becomes slide-over ── */
@media(max-width:1024px){
  :root{--sw:220px}
  .gs-ct{
    position:absolute;top:0;right:0;bottom:0;
    width:300px;
    transform:translateX(100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;height:100%;
  }
  .gs-ct.open{transform:translateX(0)}
  .gs-grid{grid-template-columns:repeat(3,1fr)}
  .gs-promos{grid-template-columns:repeat(2,1fr)}
}

/* ── 768px: both sidebars become drawers ── */
@media(max-width:768px){
  .gs-sb{
    position:absolute;top:0;left:0;bottom:0;
    width:280px;
    transform:translateX(-100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;height:100%;
  }
  .gs-sb.open{transform:translateX(0)}
  .gs-ct{
    position:absolute;top:0;right:0;bottom:0;
    width:300px;
    transform:translateX(100%);
    transition:transform .28s cubic-bezier(.4,0,.2,1);
    z-index:160;height:100%;
  }
  .gs-ct.open{transform:translateX(0)}
  /* center takes full width, offset for mobile bar */
  .gs-center{padding-top:56px}
  .gs-mob-bar{display:flex}
  /* header: compact */
  .gs-hd{padding:10px 14px;gap:8px}
  .gs-search{max-width:none;flex:1}
  .gs-avatar .av-nm{display:none}
  .gs-avatar{padding:5px}
  /* hero */
  .gs-hero{grid-template-columns:1fr;padding:26px 22px;min-height:auto}
  .gs-hero-img{display:none}
  .gs-hero h1{font-size:26px}
  .gs-hero p{max-width:100%;font-size:13px}
  /* cats */
  .gs-cat-ico{width:66px;height:66px}
  .gs-cat-ico svg{width:27px;height:27px}
  .gs-cats{gap:14px}
  /* promos */
  .gs-promos{grid-template-columns:repeat(2,1fr);gap:9px}
  /* grid */
  .gs-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  /* footer */
  .gs-ft-features{grid-template-columns:repeat(2,1fr);gap:9px}
}

/* ── 480px: true mobile-first ── */
@media(max-width:480px){
  :root{--r:10px;--rs:7px}
  .gs-mn{padding:12px}
  /* hero */
  .gs-hero{padding:22px 18px;border-radius:12px;margin-bottom:16px}
  .gs-hero h1{font-size:24px;margin-bottom:8px}
  .gs-hero p{font-size:13px;margin-bottom:18px}
  .gs-hero-cta{font-size:15px;padding:13px 26px;width:100%;justify-content:center}
  /* cats: bigger for thumb */
  .gs-cats{gap:14px;padding-bottom:8px;margin-bottom:20px}
  .gs-cat-ico{width:70px;height:70px}
  .gs-cat-ico svg{width:28px;height:28px}
  .gs-cat-lbl{font-size:11.5px;font-weight:600;max-width:72px}
  /* promos: stack */
  .gs-promos{grid-template-columns:1fr;gap:9px;margin-bottom:20px}
  .gs-pi{width:46px;height:46px}
  .gs-promo-b h4{font-size:13.5px}
  .gs-promo-b p{font-size:11.5px}
  /* section titles */
  .gs-sh h2{font-size:17px}
  /* product grid: 2 col comfortable */
  .gs-grid{grid-template-columns:repeat(2,1fr);gap:9px;margin-bottom:20px}
  /* card: bigger tap area */
  .gs-card-body{padding:11px}
  .gs-card-name{font-size:12.5px}
  .gs-pnow{font-size:14.5px}
  .gs-add-btn{font-size:13px;font-weight:700;padding:11px}
  /* footer */
  .gs-ft-features{grid-template-columns:1fr;gap:8px}
  .gs-ft{padding:12px}
  /* cart full-width */
  .gs-ct{width:100% !important}
}

/* ── 360px: smallest Androids ── */
@media(max-width:360px){
  .gs-hero h1{font-size:21px}
  .gs-grid{grid-template-columns:1fr}
  .gs-cat-ico{width:62px;height:62px}
}

/* admin bar */
body.admin-bar .gs-page{top:32px}
@media screen and (max-width:782px){body.admin-bar .gs-page{top:46px}}

@keyframes gsfade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
.gs-fade{animation:gsfade .3s ease both}
</style>
</head>
<body <?php body_class('gs-body');?>>
<?php wp_body_open();?>

<div class="gs-page">

<!-- OVERLAY -->
<div id="gs-ov" class="gs-overlay"></div>

<!-- MOBILE BAR -->
<div class="gs-mob-bar">
  <button class="gs-mob-btn" id="gs-sb-tog" aria-label="Menu">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
  </button>
  <a href="<?=esc_url(home_url('/'))?>" class="gs-mob-logo">Gam<span>Tech</span></a>
  <div class="gs-mob-cart-wrap">
    <button class="gs-mob-btn" id="gs-ct-tog-mob" aria-label="Cart">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
    </button>
    <span class="gs-mob-cart-bdg" id="gs-badge-mob"><?=esc_html($wc_count?:2)?></span>
  </div>
</div>

<!-- ════════════════ LEFT SIDEBAR ════════════════ -->
<aside class="gs-sb" id="gs-sb">
  <div class="gs-logo">
    <div class="gs-logo-ico">
      <svg width="18" height="18" fill="none" stroke="#fff" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
    </div>
    <span class="gs-logo-txt">Gam<span>Tech</span></span>
  </div>

  <div class="gs-nav-sec">
    <p class="gs-nav-lbl">Menu</p>
    <a href="<?=esc_url(home_url('/'))?>" class="gs-nav-a active">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Home
    </a>
    <a href="<?=esc_url(wc_get_page_permalink('shop'))?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>Shop All
    </a>
    <a href="<?=esc_url(wc_get_page_permalink('shop').'?orderby=date')?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>New Arrivals
    </a>
    <a href="<?=esc_url(wc_get_page_permalink('shop').'?orderby=popularity')?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>Best Sellers
    </a>
    <a href="<?=esc_url(wc_get_page_permalink('myaccount'))?>" class="gs-nav-a">
      <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>My Account
    </a>
  </div>

  <div class="gs-sb-cats">
    <div class="gs-sb-cats-title open" id="gs-cats-tog">
      <span>Categories</span>
      <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </div>
    <div class="gs-sb-cats-list" id="gs-cats-list">
      <?php foreach($store_cats as $cname => $cico):
        $term = get_term_by('name',$cname,'product_cat');
        $curl = $term ? get_term_link($term) : wc_get_page_permalink('shop').'?s='.urlencode($cname);
      ?>
      <a href="<?=esc_url($curl)?>" class="gs-sb-cat">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?=$cico?></svg>
        <?=esc_html($cname)?>
      </a>
      <?php endforeach;?>
    </div>
  </div>

  <div class="gs-sb-promo">
    <p class="sptag">Special Offer</p>
    <h4>Tech Sale<br>Up to 60% Off</h4>
    <p>Limited-time deals on top gadgets</p>
    <a href="<?=esc_url(wc_get_page_permalink('shop').'?on_sale=1')?>">Shop Now</a>
  </div>

  <a href="<?=esc_url(get_permalink(get_page_by_path('contact')))?>" class="gs-sb-support">
    <span class="sdot"></span>
    <div><strong>Need Help?</strong>24/7 Support Center</div>
  </a>
</aside>

<!-- ════════════════ CENTER ════════════════ -->
<div class="gs-center">

  <!-- HEADER -->
  <header class="gs-hd">
    <div class="gs-search">
      <form method="get" action="<?=esc_url(home_url('/'))?>">
        <input type="search" name="s" placeholder="Search products, brands..." value="<?=esc_attr(get_search_query())?>">
        <input type="hidden" name="post_type" value="product">
        <button type="submit" class="gs-search-btn" aria-label="Search">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </form>
    </div>
    <div class="gs-hd-icons">
      <button class="gs-hd-btn" id="gs-ct-tog" aria-label="Cart">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <span class="bdg" id="gs-badge-hd"><?=esc_html($wc_count?:2)?></span>
      </button>
      <a href="<?=esc_url(wc_get_page_permalink('myaccount'))?>" class="gs-avatar">
        <div class="av-img"><?=esc_html(strtoupper(substr(wp_get_current_user()->display_name?:'G',0,1)))?></div>
        <span class="av-nm"><?=esc_html(wp_get_current_user()->display_name?:'Guest')?></span>
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
      </a>
    </div>
  </header>

  <!-- SCROLLABLE CONTENT -->
  <main class="gs-mn">
    <!-- HERO -->
    <section class="gs-hero">
      <div>
        <div class="gs-hero-tag"><span class="dot"></span>Tech Store 2026</div>
        <h1>Power Up Your<br><span>Workspace ✦</span></h1>
        <p>Mice, keyboards, headphones, storage, networking and more — everything for your tech setup.</p>
        <a href="<?=esc_url(wc_get_page_permalink('shop'))?>" class="gs-hero-cta">
          Shop Now
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
      <div class="gs-hero-img">
        <?php $hi=get_theme_mod('gamtech_hero_image',''); if($hi):?>
          <img src="<?=esc_url($hi)?>" alt="Hero product">
        <?php else:?>
          <div class="gs-hero-ph">
            <svg width="80" height="80" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
          </div>
        <?php endif;?>
      </div>
    </section>

    <!-- CATEGORY ICON ROW -->
    <div class="gs-cats">
      <?php foreach($store_cats as $cname => $cico):
        $term=get_term_by('name',$cname,'product_cat');
        $curl=$term?get_term_link($term):wc_get_page_permalink('shop').'?s='.urlencode($cname);
        $short=strlen($cname)>12?substr($cname,0,11).'…':$cname;
      ?>
      <a href="<?=esc_url($curl)?>" class="gs-cat">
        <div class="gs-cat-ico">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><?=$cico?></svg>
        </div>
        <span class="gs-cat-lbl"><?=esc_html($short)?></span>
      </a>
      <?php endforeach;?>
    </div>

    <!-- PROMO CARDS -->
    <div class="gs-promos">
      <a href="<?=esc_url(wc_get_page_permalink('shop').'?on_sale=1')?>" class="gs-promo">
        <div class="gs-pi r"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
        <div class="gs-promo-b"><h4>Flash Sale</h4><p>Up to 70% Off</p><span class="pcta">Shop now →</span></div>
      </a>
      <a href="<?=esc_url(wc_get_page_permalink('shop'))?>" class="gs-promo">
        <div class="gs-pi g"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="1"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
        <div class="gs-promo-b"><h4>Free Shipping</h4><p>On orders over $350</p><span class="pcta">Shop now →</span></div>
      </a>
      <a href="<?=esc_url(wc_get_page_permalink('shop').'?orderby=date')?>" class="gs-promo">
        <div class="gs-pi p"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
        <div class="gs-promo-b"><h4>New Arrivals</h4><p>Latest tech just dropped</p><span class="pcta">Shop now →</span></div>
      </a>
      <a href="<?=esc_url(wc_get_page_permalink('shop').'?on_sale=1')?>" class="gs-promo">
        <div class="gs-pi y"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg></div>
        <div class="gs-promo-b"><h4>Limited Offers</h4><p>Exclusive discounts</p><span class="pcta">Shop now →</span></div>
      </a>
    </div>

    <!-- BEST DEALS -->
    <div class="gs-sh">
      <div><h2>Best Deals for You</h2><p>Hand-picked at unbeatable prices</p></div>
      <a href="<?=esc_url(wc_get_page_permalink('shop').'?on_sale=1')?>" class="gs-viewall">View All</a>
    </div>
    <div class="gs-grid">
      <?php if($q_deals->have_posts()):
        while($q_deals->have_posts()){$q_deals->the_post();global $product;gs_card($product);}
        wp_reset_postdata();
      else: for($i=1;$i<=4;$i++):?>
      <div class="gs-card gs-fade">
        <div class="gs-card-img" style="display:flex;align-items:center;justify-content:center;">
          <svg width="44" height="44" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        </div>
        <div class="gs-card-body"><span class="gs-card-brand">GamTech</span>
          <span class="gs-card-name">Product <?=$i?></span>
          <div class="gs-card-price"><span class="gs-pnow">$0.00</span></div>
        </div>
        <a href="<?=esc_url(wc_get_page_permalink('shop'))?>" class="gs-add-btn">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Add to Cart
        </a>
      </div>
      <?php endfor; endif;?>
    </div>

    <!-- RECOMMENDED -->
    <div class="gs-sh">
      <div><h2>Recommended for You</h2><p>Based on popular picks</p></div>
      <a href="<?=esc_url(wc_get_page_permalink('shop').'?orderby=popularity')?>" class="gs-viewall">View All</a>
    </div>
    <div class="gs-grid">
      <?php if($q_rec->have_posts()):
        while($q_rec->have_posts()){$q_rec->the_post();global $product;gs_card($product);}
        wp_reset_postdata();
      else: for($i=1;$i<=4;$i++):?>
      <div class="gs-card gs-fade">
        <div class="gs-card-img" style="display:flex;align-items:center;justify-content:center;">
          <svg width="44" height="44" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="15" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>
        </div>
        <div class="gs-card-body"><span class="gs-card-brand">GamTech</span>
          <span class="gs-card-name">Top Pick <?=$i?></span>
          <div class="gs-stars"><span class="gs-st-f">★★★★★</span><span class="gs-st-c">(0)</span></div>
          <div class="gs-card-price"><span class="gs-pnow">$0.00</span></div>
        </div>
        <a href="<?=esc_url(wc_get_page_permalink('shop'))?>" class="gs-add-btn">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
          Add to Cart
        </a>
      </div>
      <?php endfor; endif;?>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="gs-ft">
    <div class="gs-ft-features">
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
        <div class="gs-ft-b"><h4>Secure Payment</h4><p>100% secure checkout</p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg></div>
        <div class="gs-ft-b"><h4>Easy Returns</h4><p>30-day return policy</p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.09 6.09l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg></div>
        <div class="gs-ft-b"><h4>24/7 Support</h4><p>Dedicated support team</p></div>
      </div>
      <div class="gs-ft-card">
        <div class="gs-ft-ico"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></div>
        <div class="gs-ft-b"><h4>Trusted Store</h4><p>4.8 average rating</p></div>
      </div>
    </div>
  </footer>

</div><!-- /.gs-center -->

<!-- ════════════════ RIGHT CART ════════════════ -->
<aside class="gs-ct" id="gs-ct">
  <div class="gs-ct-hd">
    <h3>My Cart <span class="gs-ct-cnt" id="gs-ct-cnt"><?=esc_html($wc_count?:2)?></span></h3>
    <button class="gs-ct-close" id="gs-ct-close" aria-label="Close">
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <div class="gs-ct-items" id="gs-ct-items">
    <?php if(!empty($wc_items)):foreach($wc_items as $ci):?>
    <div class="gs-ct-item">
      <img class="gs-ct-thumb" src="<?=esc_url($ci['img'])?>" alt="<?=esc_attr($ci['name'])?>" loading="lazy">
      <div class="gs-ct-info">
        <div class="gs-ct-name"><?=esc_html($ci['name'])?></div>
        <div class="gs-ct-sub">GamTech</div>
        <div class="gs-ct-price" data-price="<?=esc_attr($ci['price'])?>"><?=wp_kses_post(wc_price($ci['price']))?></div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n"><?=esc_html($ci['qty'])?></span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <?php endforeach;else:?>
    <div class="gs-ct-item">
      <div class="gs-ct-thumb-ph"><svg width="20" height="20" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>
      <div class="gs-ct-info"><div class="gs-ct-name">Wireless Headphones</div><div class="gs-ct-sub">GamTech Audio</div>
        <div class="gs-ct-price" data-price="129.99">$129.99</div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <div class="gs-ct-item">
      <div class="gs-ct-thumb-ph"><svg width="20" height="20" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M6 11h.01M10 11h.01M14 11h.01"/></svg></div>
      <div class="gs-ct-info"><div class="gs-ct-name">Mechanical Keyboard RGB</div><div class="gs-ct-sub">GamTech</div>
        <div class="gs-ct-price" data-price="89.99">$89.99</div>
        <div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">2</span><button class="gs-qty-btn" data-action="plus">+</button></div>
      </div>
      <button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
    </div>
    <?php endif;?>
  </div>
  <div class="gs-ct-promo">
    <div class="gs-prom-row">
      <input type="text" id="gs-promo-in" placeholder="Promo Code">
      <button id="gs-promo-btn">Apply</button>
    </div>
    <p class="gs-prom-msg" id="gs-promo-msg"></p>
  </div>
  <div class="gs-ct-sum">
    <div class="gs-srow"><span class="l">Subtotal</span><span class="v" id="gs-sub"><?=wp_kses_post(wc_price($wc_total?:309.97))?></span></div>
    <div class="gs-srow disc"><span class="l">Discount</span><span class="v" id="gs-disc">$0.00</span></div>
    <div class="gs-srow ship"><span class="l">Shipping</span><span class="v">Free</span></div>
    <div class="gs-srow tot"><span class="l">Total</span><span class="v" id="gs-tot"><?=wp_kses_post(wc_price($wc_total?:309.97))?></span></div>
    <a href="<?=esc_url(wc_get_checkout_url())?>" class="gs-checkout-btn">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
      Checkout (<?=esc_html($wc_count?:2)?>)
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
    <div class="gs-pay-ico"><span>VISA</span><span>MC</span><span>PayPal</span><span>Apple Pay</span></div>
  </div>
  <div class="gs-ct-sugg">
    <h4>You might also like</h4>
    <?php $sq=gs_q(['posts_per_page'=>3,'orderby'=>'rand']);
    if($sq->have_posts()):while($sq->have_posts()){$sq->the_post();global $product;
      $si=$product->get_image_id()?wp_get_attachment_image_url($product->get_image_id(),'thumbnail'):wc_placeholder_img_src('thumbnail');
      echo '<div class="gs-sugg-item"><img class="gs-sugg-img" src="'.esc_url($si).'" alt="'.esc_attr($product->get_name()).'" loading="lazy"><span class="gs-sugg-nm">'.esc_html($product->get_name()).'</span><span class="gs-sugg-pr">'.wp_kses_post($product->get_price_html()).'</span><a href="'.esc_url($product->add_to_cart_url()).'" class="gs-sugg-add">+</a></div>';
    }wp_reset_postdata();
    else:$ss=[['USB-C Hub 7-in-1','$49.99'],['Webcam HD3000','$39.99'],['NVMe SSD 1TB','$109.99']];
    foreach($ss as $s):echo '<div class="gs-sugg-item"><div class="gs-sugg-img-ph"><svg width="18" height="18" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div><span class="gs-sugg-nm">'.esc_html($s[0]).'</span><span class="gs-sugg-pr">'.esc_html($s[1]).'</span><button class="gs-sugg-add">+</button></div>';endforeach;endif;?>
  </div>
</aside>

</div><!-- /.gs-page -->

<script>
(function(){
'use strict';
var qs=function(s,c){return(c||document).querySelector(s)};
var qa=function(s,c){return Array.from((c||document).querySelectorAll(s))};

document.addEventListener('DOMContentLoaded',function(){
  var sb=qs('#gs-sb'), ct=qs('#gs-ct'), ov=qs('#gs-ov');

  function openPanel(el){el.classList.add('open');ov&&ov.classList.add('on');}
  function closeAll(){sb&&sb.classList.remove('open');ct&&ct.classList.remove('open');ov&&ov.classList.remove('on');}

  var sbTog=qs('#gs-sb-tog'), ctTog=qs('#gs-ct-tog'), ctTogM=qs('#gs-ct-tog-mob'), ctClose=qs('#gs-ct-close');
  sbTog&&sbTog.addEventListener('click',function(){openPanel(sb);});
  ctTog&&ctTog.addEventListener('click',function(){openPanel(ct);});
  ctTogM&&ctTogM.addEventListener('click',function(){openPanel(ct);});
  ctClose&&ctClose.addEventListener('click',closeAll);
  ov&&ov.addEventListener('click',closeAll);

  /* categories toggle in sidebar */
  var catsTog=qs('#gs-cats-tog'), catsList=qs('#gs-cats-list');
  if(catsTog&&catsList){
    catsTog.addEventListener('click',function(){
      var open=catsTog.classList.toggle('open');
      catsList.style.display=open?'flex':'none';
    });
  }

  /* qty + delete */
  var ctItems=qs('#gs-ct-items');
  ctItems&&ctItems.addEventListener('click',function(e){
    var btn=e.target.closest('.gs-qty-btn');
    if(btn){var n=btn.closest('.gs-qty').querySelector('.gs-qty-n');var v=parseInt(n.textContent)||1;
      n.textContent=btn.dataset.action==='minus'?Math.max(1,v-1):v+1;recalc();return;}
    var del=e.target.closest('.gs-ct-del');
    if(del){var item=del.closest('.gs-ct-item');
      item.style.cssText='opacity:0;transform:translateX(16px);transition:all .2s';
      setTimeout(function(){item.remove();recalc();updateBadge();},220);}
  });

  function recalc(){
    var sub=0,hasPromo=document.getElementById('gs-promo-applied');
    qa('.gs-ct-item').forEach(function(item){
      var prEl=item.querySelector('.gs-ct-price');var qEl=item.querySelector('.gs-qty-n');
      if(!prEl||!qEl)return;
      sub+=(parseFloat(prEl.dataset.price)||0)*(parseInt(qEl.textContent)||1);
    });
    var disc=hasPromo?sub*0.1:0, tot=sub-disc;
    var s=qs('#gs-sub');if(s)s.textContent='$'+sub.toFixed(2);
    var d=qs('#gs-disc');if(d)d.textContent=disc>0?'-$'+disc.toFixed(2):'$0.00';
    var t=qs('#gs-tot');if(t)t.textContent='$'+tot.toFixed(2);
  }

  function updateBadge(){
    var n=qa('.gs-ct-item').length;
    var b1=qs('#gs-badge-hd');if(b1)b1.textContent=n;
    var b2=qs('#gs-badge-mob');if(b2)b2.textContent=n;
    var bc=qs('#gs-ct-cnt');if(bc)bc.textContent=n;
  }

  /* add to cart */
  document.addEventListener('click',function(e){
    var btn=e.target.closest('.gs-add-btn');
    if(!btn)return;
    var card=btn.closest('.gs-card');if(!card)return;
    var name=(card.querySelector('.gs-card-name')||{}).textContent||'Product';
    var pn=parseFloat(btn.dataset.price)||0;
    var img=btn.dataset.img||'';
    var html='<div class="gs-ct-item" style="animation:gsfade .3s ease">'
      +(img?'<img class="gs-ct-thumb" src="'+img+'" alt="">':'<div class="gs-ct-thumb-ph"><svg width="18" height="18" fill="none" stroke="var(--b2)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/></svg></div>')
      +'<div class="gs-ct-info"><div class="gs-ct-name">'+name.replace(/</g,'&lt;')+'</div><div class="gs-ct-sub">GamTech</div>'
      +'<div class="gs-ct-price" data-price="'+pn.toFixed(2)+'">$'+pn.toFixed(2)+'</div>'
      +'<div class="gs-qty"><button class="gs-qty-btn" data-action="minus">−</button><span class="gs-qty-n">1</span><button class="gs-qty-btn" data-action="plus">+</button></div></div>'
      +'<button class="gs-ct-del" aria-label="Remove"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button></div>';
    ctItems&&ctItems.insertAdjacentHTML('beforeend',html);
    recalc();updateBadge();
    if(window.innerWidth>768)openPanel(ct);
    var orig=btn.innerHTML;
    btn.innerHTML='<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Added!';
    btn.style.cssText='background:var(--gr);color:#fff';
    setTimeout(function(){btn.innerHTML=orig;btn.style.cssText='';},1200);
  });

  /* promo */
  var applyBtn=qs('#gs-promo-btn');
  if(applyBtn){
    applyBtn.addEventListener('click',function(){
      var code=(qs('#gs-promo-in')||{}).value||'';
      code=code.trim().toUpperCase();
      var msg=qs('#gs-promo-msg');
      if(['TECH10','GAMTECH','SAVE10'].indexOf(code)!==-1){
        if(msg){msg.textContent='✓ 10% discount applied!';msg.style.color='var(--gr)';}
        var m=document.createElement('span');m.id='gs-promo-applied';m.hidden=true;document.body.appendChild(m);
        recalc();applyBtn.textContent='Applied';applyBtn.style.background='var(--gr)';applyBtn.disabled=true;
      } else {
        if(msg){msg.textContent=code?'Invalid code. Try TECH10':'Enter a code.';msg.style.color='var(--re)';}
      }
    });
    var pi=qs('#gs-promo-in');pi&&pi.addEventListener('keydown',function(e){if(e.key==='Enter')applyBtn.click();});
  }

  /* category active state */
  qa('.gs-cat').forEach(function(c){
    c.addEventListener('click',function(){qa('.gs-cat').forEach(function(x){x.classList.remove('active')});c.classList.add('active');});
  });

  /* scroll-in fade */
  if('IntersectionObserver' in window){
    var io=new IntersectionObserver(function(entries){
      entries.forEach(function(en){if(en.isIntersecting){en.target.classList.add('gs-fade');io.unobserve(en.target);}});
    },{threshold:.08});
    qa('.gs-card,.gs-promo,.gs-ft-card').forEach(function(el){io.observe(el);});
  }
});
})();
</script>

<?php wp_footer();?>
</body>
</html>
