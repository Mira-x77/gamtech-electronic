<?php
/**
 * GamTech Admin Panel
 * Accessed via /admin
 */
require_once dirname(__FILE__) . '/wp-blog-header.php';

$admin_url = home_url( '/admin' );

function gs_admin_flush() {
    if ( function_exists( 'wp_cache_flush' ) ) { wp_cache_flush(); }
    global $wp_object_cache;
    if ( $wp_object_cache ) { $wp_object_cache->flush(); }
    if ( function_exists( 'wc_delete_product_transients' ) ) { wc_delete_product_transients(); }
}

function gs_admin_redirect( $msg ) {
    $admin_url = home_url( '/admin' );
    wp_safe_redirect( add_query_arg( array( 'msg' => $msg, 't' => time() ), $admin_url ) );
    exit;
}

// ─── DELETE ────────────────────────────────────────────────────
if ( isset( $_POST['gs_del'] ) && wp_verify_nonce( $_POST['_n'] ?? '', 'gs_del' ) ) {
    $pid = (int) $_POST['pid'];
    if ( $pid > 0 ) wp_delete_post( $pid, true );
    gs_admin_flush();
    gs_admin_redirect( 'deleted' );
}

// ─── ADD ───────────────────────────────────────────────────────
if ( isset( $_POST['gs_add'] ) && wp_verify_nonce( $_POST['_n'] ?? '', 'gs_add' ) ) {
    $pid = wp_insert_post( array(
        'post_title'   => sanitize_text_field( $_POST['name'] ),
        'post_type'    => 'product',
        'post_status'  => 'publish',
    ));
    if ( $pid && ! is_wp_error( $pid ) ) {
        $p = wc_get_product( $pid );
        $p->set_regular_price( sanitize_text_field( $_POST['reg'] ) );
        $p->set_sale_price( sanitize_text_field( $_POST['sale'] ) );
        $p->set_sku( sanitize_text_field( $_POST['sku'] ) );
        $p->set_description( wp_kses_post( $_POST['description'] ?? '' ) );
        $p->set_short_description( wp_kses_post( $_POST['short_description'] ?? '' ) );
        $p->set_stock_status( ! empty( $_POST['out_of_stock'] ) ? 'outofstock' : 'instock' );
        $p->save();
        $cat = (int) ( $_POST['cat'] ?? 0 );
        if ( $cat > 0 ) wp_set_object_terms( $pid, $cat, 'product_cat' );
        if ( ! empty( $_FILES['img']['tmp_name'] ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $aid = media_handle_upload( 'img', $pid );
            if ( ! is_wp_error( $aid ) ) set_post_thumbnail( $pid, $aid );
        }
    }
    gs_admin_flush();
    gs_admin_redirect( 'added' );
}

// ─── EDIT ──────────────────────────────────────────────────────
if ( isset( $_POST['gs_edit'] ) && wp_verify_nonce( $_POST['_n'] ?? '', 'gs_edit' ) ) {
    $pid = (int) $_POST['pid'];
    wp_update_post( array(
        'ID'          => $pid,
        'post_title'  => sanitize_text_field( $_POST['name'] ),
        'post_status' => sanitize_text_field( $_POST['status'] ),
    ));
    $p = wc_get_product( $pid );
    if ( $p ) {
        $p->set_regular_price( sanitize_text_field( $_POST['reg'] ) );
        $p->set_sale_price( sanitize_text_field( $_POST['sale'] ) );
        $p->set_sku( sanitize_text_field( $_POST['sku'] ) );
        $p->set_status( sanitize_text_field( $_POST['status'] ) );
        $p->set_description( wp_kses_post( $_POST['description'] ?? '' ) );
        $p->set_short_description( wp_kses_post( $_POST['short_description'] ?? '' ) );
        $p->set_stock_status( ! empty( $_POST['out_of_stock'] ) ? 'outofstock' : 'instock' );
        $p->save();
    }
    $cat = (int) ( $_POST['cat'] ?? 0 );
    wp_set_object_terms( $pid, $cat > 0 ? $cat : array(), 'product_cat' );
    if ( ! empty( $_FILES['img']['tmp_name'] ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $aid = media_handle_upload( 'img', $pid );
        if ( ! is_wp_error( $aid ) ) set_post_thumbnail( $pid, $aid );
    }
    gs_admin_flush();
    gs_admin_redirect( 'updated' );
}

// ─── COPY ──────────────────────────────────────────────────────
if ( isset( $_POST['gs_copy'] ) && wp_verify_nonce( $_POST['_n'] ?? '', 'gs_copy' ) ) {
    $orig = wc_get_product( (int) $_POST['pid'] );
    if ( $orig ) {
        $new_id = wp_insert_post( array(
            'post_title'   => $orig->get_name() . ' (Copy)',
            'post_type'    => 'product',
            'post_status'  => 'publish',
            'post_content' => $orig->get_description(),
        ));
        if ( $new_id && ! is_wp_error( $new_id ) ) {
            $np = wc_get_product( $new_id );
            $np->set_short_description( $orig->get_short_description() );
            $np->set_description( $orig->get_description() );
            $np->set_regular_price( '' );
            $np->set_sale_price( '' );
            $np->set_sku( $orig->get_sku() ? $orig->get_sku() . '-copy' : '' );
            $np->set_stock_status( $orig->get_stock_status() );
            $np->save();
            $orig_cats = wp_get_post_terms( $orig->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
            if ( ! is_wp_error( $orig_cats ) && ! empty( $orig_cats ) ) {
                wp_set_object_terms( $new_id, $orig_cats, 'product_cat' );
            }
            if ( $orig->get_image_id() ) {
                set_post_thumbnail( $new_id, $orig->get_image_id() );
            }
        }
    }
    gs_admin_flush();
    gs_admin_redirect( 'copied' );
}

// ─── DATA ──────────────────────────────────────────────────────
$msg = $_GET['msg'] ?? '';
$s   = $_GET['s'] ?? '';
$fcat = (int) ( $_GET['cat'] ?? 0 );
$page = max( 1, (int) ( $_GET['paged'] ?? 1 ) );

$cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name' ) );
$cat_opts = array();
if ( ! is_wp_error( $cats ) ) {
    foreach ( $cats as $c ) {
        if ( $c->slug === 'uncategorized' ) continue;
        $cat_opts[ $c->term_id ] = $c->name;
    }
}

$args = array(
    'post_type'      => 'product',
    'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
    'posts_per_page' => 48,
    'paged'          => $page,
    'orderby'        => 'title',
    'order'          => 'ASC',
);
if ( $s ) $args['s'] = $s;
if ( $fcat ) $args['tax_query'] = array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $fcat ) );

$q = new WP_Query( $args );
$total = $q->found_posts;
$total_pages = $q->max_num_pages;

$all = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids' ) );
$total_products = $all->found_posts;
$priced = $unpriced = 0;
foreach ( $all->posts as $id ) {
    $pp = wc_get_product( $id );
    if ( $pp && $pp->get_regular_price() !== '' ) $priced++; else $unpriced++;
}
wp_reset_postdata();

function gs_admin_link( $params = array() ) {
    $base = home_url( '/admin' );
    return esc_url( add_query_arg( $params, $base ) );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>GamTech Admin</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0a0a12;color:#e2e8f0;min-height:100vh}
.wrap{max-width:1400px;margin:0 auto;padding:20px}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px}
.top h1{font-size:24px;color:#fff;font-weight:800;letter-spacing:-0.5px}
.top h1 span{color:#7c3aed}
.stats{display:flex;gap:8px}
.stat{background:#14141f;padding:6px 14px;border-radius:8px;border:1px solid #1e1e30;font-size:12px;color:#94a3b8;display:flex;align-items:center;gap:6px}
.stat b{color:#fff;font-size:14px}
.msg{padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px}
.msg.ok{background:rgba(34,197,94,.1);color:#22c55e;border:1px solid rgba(34,197,94,.15)}
.toolbar{display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center}
.toolbar form{display:flex;gap:8px;flex:1;flex-wrap:wrap}
.inp{padding:10px 14px;background:#14141f;border:1px solid #1e1e30;border-radius:8px;color:#fff;font-size:13px;outline:none;transition:border .2s}
.inp:focus{border-color:#7c3aed}
.inp::placeholder{color:#4a4a6a}
select.inp{appearance:auto}
.btn{padding:10px 20px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s}
.btn-add{background:#7c3aed;color:#fff}.btn-add:hover{background:#6d28d9}
.btn-sm{padding:6px 12px;font-size:11px;border-radius:6px}
.btn-edit{background:rgba(59,130,246,.12);color:#3b82f6}.btn-edit:hover{background:rgba(59,130,246,.2)}
.btn-del{background:rgba(239,68,68,.1);color:#ef4444}.btn-del:hover{background:rgba(239,68,68,.2)}
.btn-view{background:rgba(34,197,94,.1);color:#22c55e}.btn-view:hover{background:rgba(34,197,94,.2)}
.btn-copy{background:rgba(251,191,36,.1);color:#f59e0b}.btn-copy:hover{background:rgba(251,191,36,.2)}

.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
.card{background:#14141f;border:1px solid #1e1e30;border-radius:12px;overflow:hidden;transition:all .2s;position:relative}
.card:hover{border-color:#7c3aed;transform:translateY(-2px);box-shadow:0 8px 24px rgba(124,58,237,.15)}
.card-img{width:100%;height:200px;object-fit:cover;background:#1a1a2a;display:block}
.card-body{padding:14px}
.card-cat{font-size:10px;color:#7c3aed;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}
.card-name{font-size:14px;font-weight:700;color:#fff;margin-bottom:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.card-sku{font-size:11px;color:#4a4a6a;margin-bottom:8px}
.card-desc{font-size:11px;color:#888;line-height:1.4;margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;white-space:pre-line}
.card-price{display:flex;align-items:baseline;gap:8px;margin-bottom:10px}
.card-price .reg{font-size:16px;font-weight:800;color:#fff}
.card-price .sale{font-size:13px;color:#22c55e;font-weight:600}
.card-price .no-price{font-size:13px;color:#ef4444;font-weight:600}
.card-badge{position:absolute;top:10px;right:10px;font-size:9px;font-weight:700;padding:3px 8px;border-radius:4px;text-transform:uppercase}
.badge-oos{background:rgba(239,68,68,.9);color:#fff}
.badge-draft{background:rgba(251,191,36,.9);color:#000}
.card-actions{display:flex;gap:6px;padding:0 14px 14px}
.card-actions .btn{flex:1;text-align:center}

.pg{display:flex;gap:6px;justify-content:center;margin-top:20px;flex-wrap:wrap}
.pg a,.pg span{padding:6px 14px;border-radius:8px;font-size:12px;text-decoration:none;color:#fff;background:#14141f;border:1px solid #1e1e30;transition:all .15s}
.pg a:hover{border-color:#7c3aed;background:#1a1a2e}
.pg .cur{background:#7c3aed;border-color:#7c3aed}

.ov{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:999;display:none;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
.ov.on{display:flex}
.modal{background:#0f0f18;border:1px solid #1e1e30;border-radius:14px;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;padding:28px}
.modal h2{font-size:20px;color:#fff;margin-bottom:20px;font-weight:800}
.modal label{display:block;font-size:12px;color:#94a3b8;font-weight:600;margin-bottom:5px;margin-top:14px}
.modal label:first-of-type{margin-top:0}
.modal input[type="text"],.modal input[type="number"],.modal select,.modal textarea{width:100%;padding:10px 14px;background:#14141f;border:1px solid #1e1e30;border-radius:8px;color:#fff;font-size:13px;outline:none;transition:border .2s;font-family:inherit}
.modal input:focus,.modal select:focus,.modal textarea:focus{border-color:#7c3aed}
.modal textarea{min-height:60px;resize:vertical}
.row{display:flex;gap:12px}.row>div{flex:1}
.acts2{display:flex;gap:8px;margin-top:20px;justify-content:flex-end}
.cancel{background:#1e1e30;color:#fff}.cancel:hover{background:#2a2a40}

@media(max-width:768px){
    .grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px}
    .card-img{height:140px}
    .card-body{padding:10px}
    .card-name{font-size:12px}
    .card-desc{-webkit-line-clamp:2}
    .row{flex-direction:column;gap:0}
    .modal{padding:18px}
}
</style>
</head>
<body>
<div class="wrap">

<div class="top">
  <h1>Gam<span>Tech</span> Admin</h1>
  <div class="stats">
    <span class="stat"><b><?= $total_products ?></b> products</span>
    <span class="stat"><b style="color:#22c55e"><?= $priced ?></b> priced</span>
    <span class="stat"><b style="color:#ef4444"><?= $unpriced ?></b> no price</span>
  </div>
</div>

<?php if ($msg==='added'): ?><div class="msg ok">✓ Product added successfully</div>
<?php elseif ($msg==='updated'): ?><div class="msg ok">✓ Product updated</div>
<?php elseif ($msg==='copied'): ?><div class="msg ok">✓ Product copied! Edit the copy to set a price.</div>
<?php elseif ($msg==='deleted'): ?><div class="msg ok">✓ Product deleted.</div><?php endif; ?>

<div class="toolbar">
  <form action="<?= gs_admin_link() ?>" method="get">
    <input class="inp" name="s" placeholder="Search products..." value="<?= esc_attr($s) ?>">
    <select class="inp" name="cat" onchange="this.form.submit()">
      <option value="0">All categories</option>
      <?php foreach ($cat_opts as $cid=>$cn): ?>
        <option value="<?= $cid ?>" <?= $fcat==$cid?'selected':'' ?>><?= esc_html($cn) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-add" type="submit">Search</button>
  </form>
  <button class="btn btn-add" onclick="openAdd()">+ Add Product</button>
</div>

<?php if ($total===0): ?>
<p style="text-align:center;padding:60px;color:#4a4a6a">No products found.</p>
<?php else: ?>

<div class="grid">
<?php while ($q->have_posts()): $q->the_post();
  $pr = wc_get_product(get_the_ID());
  if (!$pr) continue;
  $pid  = $pr->get_id();
  $nm   = $pr->get_name();
  $im   = $pr->get_image_id() ? wp_get_attachment_image_url($pr->get_image_id(),'woocommerce_thumbnail') : wc_placeholder_img_src('woocommerce_thumbnail');
  $tc   = get_the_terms($pid,'product_cat');
  $cn   = (!empty($tc) && !is_wp_error($tc)) ? $tc[0]->name : '';
  $cid  = (!empty($tc) && !is_wp_error($tc)) ? $tc[0]->term_id : 0;
  $rg   = $pr->get_regular_price();
  $sl   = $pr->get_sale_price();
  $st   = $pr->get_status();
  $sku  = $pr->get_sku();
  $desc = $pr->get_description();
  $sh   = $pr->get_short_description();
  $oos  = $pr->get_stock_status() === 'outofstock';
?>
<div class="card">
  <?php if ($oos): ?><span class="card-badge badge-oos">Out of Stock</span>
  <?php elseif ($st==='draft'): ?><span class="card-badge badge-draft">Draft</span><?php endif; ?>
  <img class="card-img" src="<?= esc_url($im) ?>" alt="<?= esc_attr($nm) ?>">
  <div class="card-body">
    <?php if ($cn): ?><div class="card-cat"><?= esc_html($cn) ?></div><?php endif; ?>
    <div class="card-name" title="<?= esc_attr($nm) ?>"><?= esc_html($nm) ?></div>
    <?php if ($sku): ?><div class="card-sku">SKU: <?= esc_html($sku) ?></div><?php endif; ?>
    <?php if ($sh): ?><div class="card-desc"><?= wp_strip_all_tags($sh) ?></div>
    <?php elseif ($desc): ?><div class="card-desc"><?= wp_strip_all_tags($desc) ?></div><?php endif; ?>
    <div class="card-price">
      <?php if ($rg !== ''): ?>
        <span class="reg"><?= wc_price($rg) ?></span>
        <?php if ($sl !== ''): ?><span class="sale"><?= wc_price($sl) ?></span><?php endif; ?>
      <?php else: ?>
        <span class="no-price">No price set</span>
      <?php endif; ?>
    </div>
  </div>
  <div class="card-actions">
    <button class="btn btn-sm btn-edit" onclick='openEdit(<?= json_encode(array(
        'id'=>$pid,'nm'=>$nm,'rg'=>$rg,'sl'=>$sl,'sku'=>$sku,'st'=>$st,'cat'=>$cid,
        'img'=>$im,'desc'=>$desc,'sh'=>$sh,'oos'=>$oos?1:0
    ), JSON_HEX_APOS|JSON_HEX_TAG) ?>)' >Edit</button>
    <form method="post" style="display:inline;flex:1" onsubmit="return confirm('Copy this product?')">
      <?= wp_nonce_field('gs_copy','_n',false) ?>
      <input type="hidden" name="gs_copy" value="1">
      <input type="hidden" name="pid" value="<?=$pid?>">
      <button class="btn btn-sm btn-copy" type="submit" style="width:100%">Copy</button>
    </form>
    <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?')">
      <?= wp_nonce_field('gs_del','_n',false) ?>
      <input type="hidden" name="gs_del" value="1">
      <input type="hidden" name="pid" value="<?=$pid?>">
      <button class="btn btn-sm btn-del" type="submit">Del</button>
    </form>
    <a href="<?= get_permalink($pid) ?>" target="_blank" class="btn btn-sm btn-view">View</a>
  </div>
</div>
<?php endwhile; wp_reset_postdata(); ?>
</div>

<?php if ($total_pages>1): ?>
<div class="pg">
  <?php if ($page>1): ?><a href="<?= gs_admin_link(array('paged'=>$page-1,'s'=>$s,'cat'=>$fcat)) ?>">← Prev</a><?php endif; ?>
  <?php
  $start = max(1, $page - 3);
  $end = min($total_pages, $page + 3);
  if ($start > 1): ?><a href="<?= gs_admin_link(array('paged'=>1,'s'=>$s,'cat'=>$fcat)) ?>">1</a><?php if ($start > 2): ?><span style="background:none;border:none;color:#4a4a6a">...</span><?php endif; ?><?php endif; ?>
  <?php for($i=$start;$i<=$end;$i++):?>
    <?php if($i===$page):?><span class="cur"><?=$i?></span>
    <?php else:?><a href="<?= gs_admin_link(array('paged'=>$i,'s'=>$s,'cat'=>$fcat)) ?>"><?=$i?></a><?php endif;?>
  <?php endfor;?>
  <?php if ($end < $total_pages): ?><?php if ($end < $total_pages - 1): ?><span style="background:none;border:none;color:#4a4a6a">...</span><?php endif; ?><a href="<?= gs_admin_link(array('paged'=>$total_pages,'s'=>$s,'cat'=>$fcat)) ?>"><?=$total_pages?></a><?php endif; ?>
  <?php if ($page<$total_pages):?><a href="<?= gs_admin_link(array('paged'=>$page+1,'s'=>$s,'cat'=>$fcat)) ?>">Next →</a><?php endif;?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>

<!-- ADD -->
<div class="ov" id="m-add">
<div class="modal">
<h2>Add Product</h2>
<form method="post" enctype="multipart/form-data" action="<?= gs_admin_link() ?>">
<?= wp_nonce_field('gs_add','_n',false) ?>
<input type="hidden" name="gs_add" value="1">
<label>Product Name *</label>
<input type="text" name="name" required placeholder="e.g. HDMI Cable 3m">
<div class="row"><div><label>Category</label><select name="cat"><option value="0">— Select —</option><?php foreach($cat_opts as $cid=>$cn):?><option value="<?=$cid?>"><?=esc_html($cn)?></option><?php endforeach;?></select></div><div><label>SKU</label><input type="text" name="sku" placeholder="e.g. HDMI-3M"></div></div>
<div class="row"><div><label>Regular Price (CFA) *</label><input type="number" name="reg" required step="1" min="0"></div><div><label>Sale Price (CFA)</label><input type="number" name="sale" step="1" min="0"></div></div>
<label>Short Description (wire lengths, pack sizes)</label>
<textarea name="short_description" rows="3" placeholder="e.g. Available in 1m, 3m, 5m lengths. Packs of 5."></textarea>
<label>Description (full product details)</label>
<textarea name="description" rows="5" placeholder="Full specs, features, etc."></textarea>
<label style="margin-top:12px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
  <input type="checkbox" name="out_of_stock" value="1" style="width:16px;height:16px"> Mark as Out of Stock
</label>
<label>Product Image</label>
<input type="file" name="img" accept="image/*">
<div class="acts2">
  <button type="button" class="btn cancel" onclick="closeM()">Cancel</button>
  <button type="submit" class="btn btn-add">Add Product</button>
</div>
</form>
</div>
</div>

<!-- EDIT -->
<div class="ov" id="m-edit">
<div class="modal">
<h2>Edit Product</h2>
<form method="post" enctype="multipart/form-data" action="<?= gs_admin_link() ?>">
<?= wp_nonce_field('gs_edit','_n',false) ?>
<input type="hidden" name="pid" id="eid">
<label>Product Name *</label>
<input type="text" name="name" id="ename" required>
<div class="row"><div><label>Category</label><select name="cat" id="ecat"><option value="0">— Select —</option><?php foreach($cat_opts as $cid=>$cn):?><option value="<?=$cid?>"><?=esc_html($cn)?></option><?php endforeach;?></select></div><div><label>SKU</label><input type="text" name="sku" id="esku"></div></div>
<div class="row"><div><label>Regular Price (CFA) *</label><input type="number" name="reg" id="ereg" required step="1" min="0"></div><div><label>Sale Price (CFA)</label><input type="number" name="sale" id="esale" step="1" min="0"></div></div>
<label>Short Description</label>
<textarea name="short_description" id="eshort" rows="3" placeholder="Wire lengths, pack sizes..."></textarea>
<label>Description</label>
<textarea name="description" id="edesc" rows="5" placeholder="Full product details..."></textarea>
<label style="margin-top:12px;display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
  <input type="checkbox" name="out_of_stock" id="estock" value="1" style="width:16px;height:16px"> Mark as Out of Stock
</label>
<label>Status</label>
<select name="status" id="estat"><option value="publish">Published</option><option value="draft">Draft</option></select>
<label>Current Image</label>
<div id="eimg-preview" style="margin-bottom:8px"></div>
<label>Replace Image (empty = keep current)</label>
<input type="file" name="img" accept="image/*" onchange="gsPreviewEdit(this)">
<div class="acts2">
  <button type="button" class="btn cancel" onclick="closeM()">Cancel</button>
  <button type="submit" name="gs_copy" value="1" formnovalidate class="btn btn-copy" onclick="return confirm('Copy this product? Price will be cleared.')">Save as Copy</button>
  <button type="submit" name="gs_edit" value="1" class="btn btn-add">Save Changes</button>
</div>
</form>
</div>
</div>

<script>
function openAdd(){document.getElementById('m-add').classList.add('on')}

function openEdit(d){
  document.getElementById('eid').value=d.id;
  document.getElementById('ename').value=d.nm||'';
  document.getElementById('ereg').value=d.rg||'';
  document.getElementById('esale').value=d.sl||'';
  document.getElementById('esku').value=d.sku||'';
  document.getElementById('estat').value=d.st||'publish';
  document.getElementById('ecat').value=d.cat||0;
  document.getElementById('edesc').value=d.desc||'';
  document.getElementById('eshort').value=d.sh||'';
  document.getElementById('estock').checked=d.oos==1;
  var pv=document.getElementById('eimg-preview');
  if(d.img){pv.innerHTML='<img src="'+d.img+'" style="width:100px;height:100px;border-radius:10px;object-fit:cover;background:#1a1a2a">'}
  else{pv.innerHTML='<span style="color:#4a4a6a;font-size:12px">No image</span>'}
  document.getElementById('m-edit').classList.add('on');
}

function gsPreviewEdit(input){
  if(input.files&&input.files[0]){
    var r=new FileReader();
    r.onload=function(e){
      document.getElementById('eimg-preview').innerHTML='<img src="'+e.target.result+'" style="width:100px;height:100px;border-radius:10px;object-fit:cover;background:#1a1a2a">';
    };
    r.readAsDataURL(input.files[0]);
  }
}

function closeM(){document.querySelectorAll('.ov').forEach(function(m){m.classList.remove('on')})}
document.querySelectorAll('.ov').forEach(function(o){o.addEventListener('click',function(e){if(e.target===o)closeM()})});
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeM()});
</script>
</body></html>
