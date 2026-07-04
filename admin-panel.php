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
    @shell_exec( 'curl -s -X PURGE http://localhost/ 2>/dev/null' );
    @shell_exec( 'curl -s -X PURGE http://127.0.0.1/ 2>/dev/null' );
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
        'post_title'  => sanitize_text_field( $_POST['name'] ),
        'post_type'   => 'product',
        'post_status' => 'publish',
    ));
    if ( $pid && ! is_wp_error( $pid ) ) {
        $p = wc_get_product( $pid );
        $p->set_regular_price( sanitize_text_field( $_POST['reg'] ) );
        $p->set_sale_price( sanitize_text_field( $_POST['sale'] ) );
        $p->set_sku( sanitize_text_field( $_POST['sku'] ) );
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
    'posts_per_page' => 25,
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
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#0f0f13;color:#e2e8f0;padding:16px}
.wrap{max-width:1280px;margin:0 auto}
.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px}
.top h1{font-size:22px;color:#fff}
.stats{display:flex;gap:8px}
.stat{background:#1a1a24;padding:5px 12px;border-radius:6px;border:1px solid #2a2a3a;font-size:12px;color:#94a3b8}
.stat b{color:#fff}
.msg{padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:13px;font-weight:600}
.msg.ok{background:rgba(34,197,94,.12);color:#22c55e;border:1px solid rgba(34,197,94,.2)}
.toolbar{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center}
.toolbar form{display:flex;gap:8px;flex:1;flex-wrap:wrap}
.inp{padding:9px 12px;background:#1a1a24;border:1px solid #2a2a3a;border-radius:8px;color:#fff;font-size:13px;outline:none}
.inp:focus{border-color:#7c3aed}
.inp::placeholder{color:#64748b}
select.inp{appearance:auto}
.btn{padding:9px 18px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer}
.btn-add{background:#7c3aed;color:#fff}.btn-add:hover{background:#6d28d9}
.btn-sm{padding:5px 10px;font-size:11px;border-radius:5px}
.btn-edit{background:rgba(59,130,246,.15);color:#3b82f6}.btn-del{background:rgba(239,68,68,.12);color:#ef4444}
.btn-view{background:rgba(34,197,94,.12);color:#22c55e}
table{width:100%;border-collapse:collapse;font-size:12px}
th{background:#1a1a24;color:#94a3b8;padding:8px 10px;text-align:left;font-weight:600;font-size:10px;text-transform:uppercase;border-bottom:1px solid #2a2a3a;position:sticky;top:0}
td{padding:8px 10px;border-bottom:1px solid #1e1e2e;vertical-align:middle}
tr:hover{background:rgba(124,58,237,.05)}
.pimg{width:40px;height:40px;border-radius:5px;object-fit:cover;background:#2a2a3a}
.pname{font-weight:600;font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.pcat{color:#94a3b8;font-size:11px}
.pprice{font-weight:700;font-size:13px}
.psale{color:#22c55e;font-size:11px}
.st{font-size:10px;font-weight:600;padding:2px 6px;border-radius:3px;display:inline-block}
.st.pub{background:rgba(34,197,94,.12);color:#22c55e}
.st.draft{background:rgba(251,191,36,.12);color:#f59e0b}
.acts{display:flex;gap:4px}
.pg{display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap}
.pg a,.pg span{padding:5px 10px;border-radius:6px;font-size:12px;text-decoration:none;color:#fff;background:#1a1a24;border:1px solid #2a2a3a}
.pg a:hover{border-color:#7c3aed}
.pg .cur{background:#7c3aed;border-color:#7c3aed}
.ov{position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:999;display:none;align-items:center;justify-content:center;padding:20px}
.ov.on{display:flex}
.modal{background:#0f0f13;border:1px solid #2a2a3a;border-radius:12px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;padding:24px}
.modal h2{font-size:18px;color:#fff;margin-bottom:16px}
.modal label{display:block;font-size:12px;color:#94a3b8;font-weight:600;margin-bottom:4px;margin-top:12px}
.modal label:first-of-type{margin-top:0}
.modal input[type="text"],.modal input[type="number"],.modal select,.modal textarea{width:100%;padding:9px 12px;background:#1a1a24;border:1px solid #2a2a3a;border-radius:8px;color:#fff;font-size:13px;outline:none}
.modal input:focus,.modal select:focus,.modal textarea:focus{border-color:#7c3aed}
.modal textarea{min-height:50px;resize:vertical}
.row{display:flex;gap:12px}.row>div{flex:1}
.acts2{display:flex;gap:8px;margin-top:18px;justify-content:flex-end}
.cancel{background:#2a2a3a;color:#fff}
@media(max-width:768px){.pimg{width:32px;height:32px}th:nth-child(4),td:nth-child(4),th:nth-child(5),td:nth-child(5){display:none}.row{flex-direction:column;gap:0}}
</style>
</head>
<body>
<div class="wrap">

<div class="top">
  <h1>GamTech Admin</h1>
  <div class="stats">
    <span class="stat"><b><?= $total_products ?></b> products</span>
    <span class="stat"><b style="color:#22c55e"><?= $priced ?></b> priced</span>
    <span class="stat"><b style="color:#ef4444"><?= $unpriced ?></b> unpriced</span>
  </div>
</div>

<?php if ($msg==='added'): ?><div class="msg ok">Product added!</div>
<?php elseif ($msg==='updated'): ?><div class="msg ok">Product updated!</div>
<?php elseif ($msg==='deleted'): ?><div class="msg ok">Product deleted.</div><?php endif; ?>

<div class="toolbar">
  <form action="<?= gs_admin_link() ?>" method="get">
    <input class="inp" name="s" placeholder="Search products..." value="<?= esc_attr($s) ?>">
    <select class="inp" name="cat" onchange="this.form.submit()">
      <option value="0">All categories</option>
      <?php foreach ($cat_opts as $cid=>$cn): ?>
        <option value="<?= $cid ?>" <?= $fcat==$cid?'selected':'' ?>><?= esc_html($cn) ?></option>
      <?php endforeach; ?>
    </select>
    <button class="btn btn-add" type="submit" style="font-size:12px">Search</button>
  </form>
  <button class="btn btn-add" onclick="openAdd()">+ Add Product</button>
</div>

<?php if ($total===0): ?>
<p style="text-align:center;padding:40px;color:#64748b">No products found.</p>
<?php else: ?>
<table>
<thead><tr><th>Image</th><th>Product</th><th>Category</th><th>Price</th><th>Sale</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php while ($q->have_posts()): $q->the_post();
  $pr = wc_get_product(get_the_ID());
  if (!$pr) continue;
  $pid  = $pr->get_id();
  $nm   = $pr->get_name();
  $im   = $pr->get_image_id() ? wp_get_attachment_image_url($pr->get_image_id(),'thumbnail') : wc_placeholder_img_src('thumbnail');
  $tc   = get_the_terms($pid,'product_cat');
  $cn   = (!empty($tc) && !is_wp_error($tc)) ? $tc[0]->name : '—';
  $cid  = (!empty($tc) && !is_wp_error($tc)) ? $tc[0]->term_id : 0;
  $rg   = $pr->get_regular_price();
  $sl   = $pr->get_sale_price();
  $st   = $pr->get_status();
  $sku  = $pr->get_sku();
  $desc = esc_js($pr->get_description());
  $sh   = esc_js($pr->get_short_description());
?>
<tr>
  <td><img class="pimg" src="<?= esc_url($im) ?>"></td>
  <td><div class="pname"><?= esc_html($nm) ?></div><?php if($sku):?><div class="pcat">SKU: <?= esc_html($sku) ?></div><?php endif;?></td>
  <td class="pcat"><?= esc_html($cn) ?></td>
  <td class="pprice"><?= $rg!=='' ? wc_price($rg) : '<span style="color:#ef4444">—</span>' ?></td>
  <td class="psale"><?= $sl!=='' ? wc_price($sl) : '' ?></td>
  <td><span class="st <?= $st==='publish'?'pub':'draft' ?>"><?= $st ?></span></td>
  <td><div class="acts">
    <button class="btn btn-sm btn-edit" onclick="openEdit(<?=$pid?>,'<?=esc_js($nm)?>','<?=esc_js($rg)?>','<?=esc_js($sl)?>','<?=esc_js($sku)?>','<?=$st?>',<?=$cid?>,'<?=esc_js($im)?>','<?=$desc?>','<?=$sh?>')">Edit</button>
    <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?')">
      <?= wp_nonce_field('gs_del','_n',false) ?>
      <input type="hidden" name="gs_del" value="1">
      <input type="hidden" name="pid" value="<?=$pid?>">
      <button class="btn btn-sm btn-del" type="submit">Del</button>
    </form>
    <a href="<?= get_permalink($pid) ?>" target="_blank" class="btn btn-sm btn-view">View</a>
  </div></td>
</tr>
<?php endwhile; wp_reset_postdata(); ?>
</tbody></table>

<?php if ($total_pages>1): ?>
<div class="pg">
  <?php if ($page>1): ?><a href="<?= gs_admin_link(array('paged'=>$page-1,'s'=>$s,'cat'=>$fcat)) ?>">← Prev</a><?php endif; ?>
  <?php for($i=1;$i<=$total_pages;$i++):?>
    <?php if($i===$page):?><span class="cur"><?=$i?></span>
    <?php else:?><a href="<?= gs_admin_link(array('paged'=>$i,'s'=>$s,'cat'=>$fcat)) ?>"><?=$i?></a><?php endif;?>
  <?php endfor;?>
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
<input type="text" name="name" required placeholder="e.g. Logitech MX Master 3S">
<div class="row"><div><label>Category</label><select name="cat"><option value="0">— Select —</option><?php foreach($cat_opts as $cid=>$cn):?><option value="<?=$cid?>"><?=esc_html($cn)?></option><?php endforeach;?></select></div><div><label>SKU</label><input type="text" name="sku" placeholder="e.g. GMX-MS3S"></div></div>
<div class="row"><div><label>Regular Price (CFA) *</label><input type="number" name="reg" required step="1" min="0"></div><div><label>Sale Price (CFA)</label><input type="number" name="sale" step="1" min="0"></div></div>
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
<input type="hidden" name="gs_edit" value="1">
<input type="hidden" name="pid" id="eid">
<label>Product Name *</label>
<input type="text" name="name" id="ename" required>
<div class="row"><div><label>Category</label><select name="cat" id="ecat"><option value="0">— Select —</option><?php foreach($cat_opts as $cid=>$cn):?><option value="<?=$cid?>"><?=esc_html($cn)?></option><?php endforeach;?></select></div><div><label>SKU</label><input type="text" name="sku" id="esku"></div></div>
<div class="row"><div><label>Regular Price (CFA) *</label><input type="number" name="reg" id="ereg" required step="1" min="0"></div><div><label>Sale Price (CFA)</label><input type="number" name="sale" id="esale" step="1" min="0"></div></div>
<label>Status</label>
<select name="status" id="estat"><option value="publish">Published</option><option value="draft">Draft</option></select>
<label>Current Image</label>
<div id="eimg-preview" style="margin-bottom:8px"></div>
<label>Replace Image (empty = keep current)</label>
<input type="file" name="img" accept="image/*" onchange="gsPreviewEdit(this)">
<div class="acts2">
  <button type="button" class="btn cancel" onclick="closeM()">Cancel</button>
  <button type="submit" class="btn btn-add">Save Changes</button>
</div>
</form>
</div>
</div>

<script>
function openAdd(){document.getElementById('m-add').classList.add('on')}
function openEdit(id,nm,rg,sl,sku,st,cat,img,desc,sh){
  document.getElementById('eid').value=id;
  document.getElementById('ename').value=nm;
  document.getElementById('ereg').value=rg;
  document.getElementById('esale').value=sl;
  document.getElementById('esku').value=sku;
  document.getElementById('estat').value=st;
  document.getElementById('ecat').value=cat;
  var pv=document.getElementById('eimg-preview');
  if(img){pv.innerHTML='<img src="'+img+'" style="width:80px;height:80px;border-radius:8px;object-fit:cover;background:#2a2a3a">'}
  else{pv.innerHTML='<span style="color:#64748b;font-size:12px">No image</span>'}
  document.getElementById('m-edit').classList.add('on');
}
function gsPreviewEdit(input){
  if(input.files&&input.files[0]){
    var r=new FileReader();
    r.onload=function(e){
      document.getElementById('eimg-preview').innerHTML='<img src="'+e.target.result+'" style="width:80px;height:80px;border-radius:8px;object-fit:cover;background:#2a2a3a">';
    };
    r.readAsDataURL(input.files[0]);
  }
}
function closeM(){document.querySelectorAll('.ov').forEach(function(m){m.classList.remove('on')})}
document.querySelectorAll('.ov').forEach(function(o){o.addEventListener('click',function(e){if(e.target===o)closeM()})});
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeM()});
</script>
</body></html>
