<?php
/**
 * Template Name: Admin Panel
 * GamTech — Full product management panel
 */
defined( 'ABSPATH' ) || exit;

header( 'Cache-Control: no-cache, no-store, must-revalidate' );

$admin_key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
if ( $admin_key !== 'gamtech2026admin' ) {
    wp_die( 'Access denied.' );
}

$user_key = $admin_key;

// ─── AJAX HANDLERS ─────────────────────────────────────────────
if ( isset( $_POST['gs_action'] ) && $_POST['gs_action'] === 'add_product' && wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'gs_admin_add' ) ) {
    $name     = sanitize_text_field( $_POST['name'] );
    $cat_id   = (int) ( $_POST['category'] ?? 0 );
    $reg      = sanitize_text_field( $_POST['regular_price'] );
    $sale     = sanitize_text_field( $_POST['sale_price'] ?? '' );
    $sku      = sanitize_text_field( $_POST['sku'] ?? '' );
    $desc     = wp_kses_post( $_POST['description'] ?? '' );
    $short    = sanitize_text_field( $_POST['short_description'] ?? '' );

    $pid = wp_insert_post( array(
        'post_title'   => $name,
        'post_type'    => 'product',
        'post_status'  => 'publish',
        'post_content' => $desc,
    ) );

    if ( $pid && ! is_wp_error( $pid ) ) {
        $product = wc_get_product( $pid );
        $product->set_regular_price( $reg );
        if ( $sale !== '' ) $product->set_sale_price( $sale );
        if ( $sku !== '' ) $product->set_sku( $sku );
        $product->set_short_description( $short );
        $product->set_catalog_visibility( 'visible' );
        $product->set_manage_stock( false );
        $product->set_status( 'publish' );
        $product->save();

        if ( $cat_id > 0 ) {
            wp_set_object_terms( $pid, array( (int) $cat_id ), 'product_cat' );
        }

        // Handle image upload
        if ( ! empty( $_FILES['image']['name'] ) && $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $attach_id = media_handle_upload( 'image', $pid );
            if ( ! is_wp_error( $attach_id ) ) {
                set_post_thumbnail( $pid, $attach_id );
            }
        }
    }

    wp_safe_redirect( add_query_arg( array( 'key' => $user_key, 'msg' => 'added', 't' => time() ), get_permalink() ) );
    exit;
}

if ( isset( $_POST['gs_action'] ) && $_POST['gs_action'] === 'edit_product' && wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'gs_admin_edit' ) ) {
    $pid      = (int) ( $_POST['product_id'] ?? 0 );
    $name     = sanitize_text_field( $_POST['name'] );
    $cat_id   = (int) ( $_POST['category'] ?? 0 );
    $reg      = sanitize_text_field( $_POST['regular_price'] );
    $sale     = sanitize_text_field( $_POST['sale_price'] ?? '' );
    $sku      = sanitize_text_field( $_POST['sku'] ?? '' );
    $desc     = wp_kses_post( $_POST['description'] ?? '' );
    $short    = sanitize_text_field( $_POST['short_description'] ?? '' );
    $status   = sanitize_text_field( $_POST['status'] ?? 'publish' );

    if ( $pid > 0 ) {
        wp_update_post( array(
            'ID'           => $pid,
            'post_title'   => $name,
            'post_content' => $desc,
            'post_status'  => $status,
        ) );

        $product = wc_get_product( $pid );
        if ( $product ) {
            $product->set_regular_price( $reg );
            $product->set_sale_price( $sale );
            $product->set_sku( $sku );
            $product->set_short_description( $short );
            $product->set_status( $status );
            $product->save();
        }

        wp_set_object_terms( $pid, $cat_id > 0 ? array( (int) $cat_id ) : array(), 'product_cat' );

        if ( ! empty( $_FILES['image']['name'] ) && $_FILES['image']['error'] === UPLOAD_ERR_OK ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $attach_id = media_handle_upload( 'image', $pid );
            if ( ! is_wp_error( $attach_id ) ) {
                set_post_thumbnail( $pid, $attach_id );
            }
        }
    }

    wp_safe_redirect( add_query_arg( array( 'key' => $user_key, 'msg' => 'updated', 't' => time() ), get_permalink() ) );
    exit;
}

if ( isset( $_POST['gs_action'] ) && $_POST['gs_action'] === 'delete_product' && wp_verify_nonce( $_POST['_wpnonce'] ?? '', 'gs_admin_delete' ) ) {
    $pid = (int) ( $_POST['product_id'] ?? 0 );
    if ( $pid > 0 ) {
        wp_delete_post( $pid, true );
    }
    wp_safe_redirect( add_query_arg( array( 'key' => $user_key, 'msg' => 'deleted', 't' => time() ), get_permalink() ) );
    exit;
}

$msg = isset( $_GET['msg'] ) ? $_GET['msg'] : '';

// Get all categories
$categories = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC' ) );
$cat_options = array();
if ( ! is_wp_error( $categories ) ) {
    foreach ( $categories as $cat ) {
        if ( $cat->slug === 'uncategorized' ) continue;
        $cat_options[ $cat->term_id ] = $cat->name;
    }
}

// Pagination
$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$per_page = 25;
$search = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
$filter_cat = isset( $_GET['cat'] ) ? (int) $_GET['cat'] : 0;

$query_args = array(
    'post_type'      => 'product',
    'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
    'posts_per_page' => $per_page,
    'paged'          => $paged,
    'orderby'        => 'title',
    'order'          => 'ASC',
);

if ( $search ) {
    $query_args['s'] = $search;
}
if ( $filter_cat > 0 ) {
    $query_args['tax_query'] = array( array(
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $filter_cat,
    ) );
}

$products_q = new WP_Query( $query_args );
$total = $products_q->found_posts;
$total_pages = $products_q->max_num_pages;

// Stats
$all_q = new WP_Query( array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'fields'         => 'ids',
) );
$total_products = $all_q->found_posts;
$priced = 0;
$unpriced = 0;
foreach ( $all_q->posts as $pid ) {
    $p = wc_get_product( $pid );
    if ( $p && $p->get_regular_price() !== '' ) {
        $priced++;
    } else {
        $unpriced++;
    }
}
wp_reset_postdata();

// Edit mode
$edit_product = null;
if ( isset( $_GET['edit'] ) ) {
    $edit_product = wc_get_product( (int) $_GET['edit'] );
}

get_header();
?>

<style>
.gsap-wrap{max-width:1300px;margin:0 auto;padding:16px 20px}
.gsap-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px}
.gsap-top h1{font-size:22px;color:var(--wh);margin:0}
.gsap-stats{display:flex;gap:8px;flex-wrap:wrap}
.gsap-stat{background:var(--card);padding:5px 12px;border-radius:6px;border:1px solid var(--b1);font-size:12px;color:var(--di)}
.gsap-stat b{color:var(--wh)}
.gsap-msg{padding:10px 14px;border-radius:8px;margin-bottom:14px;font-size:13px;font-weight:600}
.gsap-msg.ok{background:rgba(34,197,94,.12);color:var(--gr);border:1px solid rgba(34,197,94,.2)}
.gsap-msg.err{background:rgba(239,68,68,.12);color:var(--re);border:1px solid rgba(239,68,68,.2)}
.gsap-toolbar{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;align-items:center}
.gsap-search{flex:1;min-width:200px;padding:9px 12px;background:var(--card);border:1px solid var(--b1);border-radius:8px;color:var(--wh);font-size:13px;outline:none}
.gsap-search:focus{border-color:var(--pu)}
.gsap-search::placeholder{color:var(--di)}
.gsap-select{padding:9px 12px;background:var(--card);border:1px solid var(--b1);border-radius:8px;color:var(--wh);font-size:13px;outline:none}
.gsap-btn{padding:9px 18px;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s}
.gsap-btn-add{background:var(--pu);color:#fff}
.gsap-btn-add:hover{background:#6d28d9}
.gsap-btn-sm{padding:5px 10px;font-size:11px;border-radius:5px}
.gsap-btn-edit{background:rgba(59,130,246,.15);color:#3b82f6;border:1px solid rgba(59,130,246,.2)}
.gsap-btn-edit:hover{background:rgba(59,130,246,.25)}
.gsap-btn-del{background:rgba(239,68,68,.12);color:var(--re);border:1px solid rgba(239,68,68,.2)}
.gsap-btn-del:hover{background:rgba(239,68,68,.25)}
.gsap-btn-view{background:rgba(34,197,94,.12);color:var(--gr);border:1px solid rgba(34,197,94,.2)}
.gsap-btn-view:hover{background:rgba(34,197,94,.25)}
.gsap-table{width:100%;border-collapse:collapse;font-size:12px}
.gsap-table th{background:var(--card);color:var(--di);padding:8px 10px;text-align:left;font-weight:600;font-size:10px;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--b1);position:sticky;top:0;z-index:2}
.gsap-table td{padding:8px 10px;border-bottom:1px solid var(--b1);vertical-align:middle}
.gsap-table tr:hover{background:rgba(124,58,237,.05)}
.gsap-img{width:40px;height:40px;border-radius:5px;object-fit:cover;background:var(--b1)}
.gsap-pname{color:var(--wh);font-weight:600;font-size:12px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.gsap-pcat{color:var(--di);font-size:11px}
.gsap-pprice{color:var(--wh);font-weight:700;font-size:13px}
.gsap-psale{color:var(--gr);font-size:11px}
.gsap-status{font-size:10px;font-weight:600;padding:2px 6px;border-radius:3px;display:inline-block}
.gsap-status.pub{background:rgba(34,197,94,.12);color:var(--gr)}
.gsap-status.draft{background:rgba(251,191,36,.12);color:#f59e0b}
.gsap-act{display:flex;gap:4px}
/* Modal */
.gsap-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.6);z-index:999;display:none;align-items:center;justify-content:center;padding:20px}
.gsap-overlay.open{display:flex}
.gsap-modal{background:var(--bg);border:1px solid var(--b1);border-radius:12px;width:100%;max-width:620px;max-height:90vh;overflow-y:auto;padding:24px}
.gsap-modal h2{font-size:18px;color:var(--wh);margin:0 0 16px}
.gsap-modal label{display:block;font-size:12px;color:var(--di);font-weight:600;margin-bottom:4px;margin-top:12px}
.gsap-modal label:first-of-type{margin-top:0}
.gsap-modal input[type="text"],.gsap-modal input[type="number"],.gsap-modal select,.gsap-modal textarea{width:100%;padding:9px 12px;background:var(--card);border:1px solid var(--b1);border-radius:8px;color:var(--wh);font-size:13px;outline:none;box-sizing:border-box}
.gsap-modal input:focus,.gsap-modal select:focus,.gsap-modal textarea:focus{border-color:var(--pu)}
.gsap-modal textarea{min-height:60px;resize:vertical}
.gsap-modal .row{display:flex;gap:12px}
.gsap-modal .row>div{flex:1}
.gsap-modal .actions{display:flex;gap:8px;margin-top:18px;justify-content:flex-end}
.gsap-modal .preview-img{width:60px;height:60px;border-radius:6px;object-fit:cover;background:var(--b1);margin-top:6px}
.gsap-pagination{display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap}
.gsap-pagination a,.gsap-pagination span{padding:5px 10px;border-radius:6px;font-size:12px;text-decoration:none;color:var(--wh);background:var(--card);border:1px solid var(--b1)}
.gsap-pagination a:hover{border-color:var(--pu)}
.gsap-pagination .current{background:var(--pu);border-color:var(--pu)}
.gsap-empty{text-align:center;padding:40px;color:var(--di);font-size:14px}
.gsap-link{color:var(--pu);text-decoration:none;font-weight:600}
.gsap-link:hover{text-decoration:underline}
@media(max-width:768px){
  .gsap-table{font-size:11px}
  .gsap-img{width:32px;height:32px}
  .gsap-table th:nth-child(4),.gsap-table td:nth-child(4),
  .gsap-table th:nth-child(5),.gsap-table td:nth-child(5){display:none}
  .gsap-modal .row{flex-direction:column;gap:0}
}
</style>

<div class="gsap-wrap">

  <div class="gsap-top">
    <h1>GamTech Admin</h1>
    <div class="gsap-stats">
      <span class="gsap-stat"><b><?php echo $total_products; ?></b> products</span>
      <span class="gsap-stat"><b style="color:var(--gr)"><?php echo $priced; ?></b> priced</span>
      <span class="gsap-stat"><b style="color:var(--re)"><?php echo $unpriced; ?></b> unpriced</span>
    </div>
  </div>

  <?php if ( $msg === 'added' ) : ?>
    <div class="gsap-msg ok">Product added successfully!</div>
  <?php elseif ( $msg === 'updated' ) : ?>
    <div class="gsap-msg ok">Product updated successfully!</div>
  <?php elseif ( $msg === 'deleted' ) : ?>
    <div class="gsap-msg ok">Product deleted.</div>
  <?php endif; ?>

  <div class="gsap-toolbar">
    <form method="get" style="display:flex;gap:8px;flex:1;flex-wrap:wrap;align-items:center">
      <input type="hidden" name="key" value="<?php echo esc_attr( $user_key ); ?>">
      <input type="text" class="gsap-search" name="s" placeholder="Search products..." value="<?php echo esc_attr( $search ); ?>">
      <select class="gsap-select" name="cat" onchange="this.form.submit()">
        <option value="0">All categories</option>
        <?php foreach ( $cat_options as $cid => $cname ) : ?>
          <option value="<?php echo esc_attr( $cid ); ?>" <?php selected( $filter_cat, $cid ); ?>><?php echo esc_html( $cname ); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="gsap-btn gsap-btn-add" style="font-size:12px">Search</button>
    </form>
    <button class="gsap-btn gsap-btn-add" onclick="gsapOpenAdd()">+ Add Product</button>
  </div>

  <?php if ( $total === 0 ) : ?>
    <div class="gsap-empty">No products found.</div>
  <?php else : ?>
  <table class="gsap-table">
    <thead>
      <tr>
        <th>Image</th>
        <th>Product</th>
        <th>Category</th>
        <th>Regular Price</th>
        <th>Sale Price</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ( $products_q->have_posts() ) : $products_q->the_post();
        $product = wc_get_product( get_the_ID() );
        if ( ! $product ) continue;
        $pid   = $product->get_id();
        $name  = $product->get_name();
        $img   = $product->get_image_id()
            ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
            : wc_placeholder_img_src( 'thumbnail' );
        $cats  = get_the_terms( $pid, 'product_cat' );
        $cat   = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : '—';
        $reg   = $product->get_regular_price();
        $sale  = $product->get_sale_price();
        $stat  = $product->get_status();
        $sku   = $product->get_sku();
        $desc  = $product->get_description();
        $short = $product->get_short_description();
        $link  = get_permalink( $pid );
      ?>
      <tr>
        <td><img class="gsap-img" src="<?php echo esc_url( $img ); ?>" alt=""></td>
        <td>
          <div class="gsap-pname"><?php echo esc_html( $name ); ?></div>
          <?php if ( $sku ) : ?><div class="gsap-pcat">SKU: <?php echo esc_html( $sku ); ?></div><?php endif; ?>
        </td>
        <td class="gsap-pcat"><?php echo esc_html( $cat ); ?></td>
        <td class="gsap-pprice"><?php echo $reg !== '' ? wc_price( $reg ) : '<span style="color:var(--re)">—</span>'; ?></td>
        <td class="gsap-psale"><?php echo $sale !== '' ? wc_price( $sale ) : ''; ?></td>
        <td><span class="gsap-status <?php echo $stat === 'publish' ? 'pub' : 'draft'; ?>"><?php echo esc_html( $stat ); ?></span></td>
        <td>
          <div class="gsap-act">
            <button class="gsap-btn gsap-btn-sm gsap-btn-edit" onclick="gsapOpenEdit(<?php echo esc_attr( $pid ); ?>,'<?php echo esc_js( $name ); ?>','<?php echo esc_js( $reg ); ?>','<?php echo esc_js( $sale ); ?>','<?php echo esc_js( $sku ); ?>','<?php echo esc_js( $stat ); ?>',<?php echo esc_attr( ( $cats && ! is_wp_error( $cats ) ) ? $cats[0]->term_id : 0 ); ?>,<?php echo esc_attr( $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : 0 ); ?>,'<?php echo esc_js( $desc ); ?>','<?php echo esc_js( $short ); ?>')">Edit</button>
            <form method="post" style="display:inline" onsubmit="return confirm('Delete this product?')">
              <?php wp_nonce_field( 'gs_admin_delete' ); ?>
              <input type="hidden" name="gs_action" value="delete_product">
              <input type="hidden" name="product_id" value="<?php echo esc_attr( $pid ); ?>">
              <button type="submit" class="gsap-btn gsap-btn-sm gsap-btn-del">Delete</button>
            </form>
            <a href="<?php echo esc_url( $link ); ?>" target="_blank" class="gsap-btn gsap-btn-sm gsap-btn-view">View</a>
          </div>
        </td>
      </tr>
      <?php endwhile; wp_reset_postdata(); ?>
    </tbody>
  </table>

  <?php if ( $total_pages > 1 ) : ?>
    <div class="gsap-pagination">
      <?php if ( $paged > 1 ) : ?>
        <a href="<?php echo esc_url( add_query_arg( array( 'key' => $user_key, 'paged' => $paged - 1, 's' => $search, 'cat' => $filter_cat ) ) ); ?>">← Prev</a>
      <?php endif; ?>
      <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
        <?php if ( $i === $paged ) : ?>
          <span class="current"><?php echo $i; ?></span>
        <?php else : ?>
          <a href="<?php echo esc_url( add_query_arg( array( 'key' => $user_key, 'paged' => $i, 's' => $search, 'cat' => $filter_cat ) ) ); ?>"><?php echo $i; ?></a>
        <?php endif; ?>
      <?php endfor; ?>
      <?php if ( $paged < $total_pages ) : ?>
        <a href="<?php echo esc_url( add_query_arg( array( 'key' => $user_key, 'paged' => $paged + 1, 's' => $search, 'cat' => $filter_cat ) ) ); ?>">Next →</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php endif; ?>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="gsap-overlay" id="gsap-add-modal">
  <div class="gsap-modal">
    <h2>Add New Product</h2>
    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field( 'gs_admin_add' ); ?>
      <input type="hidden" name="gs_action" value="add_product">

      <label>Product Name *</label>
      <input type="text" name="name" required placeholder="e.g. Logitech MX Master 3S">

      <div class="row">
        <div>
          <label>Category</label>
          <select name="category">
            <option value="0">— Select —</option>
            <?php foreach ( $cat_options as $cid => $cname ) : ?>
              <option value="<?php echo esc_attr( $cid ); ?>"><?php echo esc_html( $cname ); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>SKU</label>
          <input type="text" name="sku" placeholder="e.g. GMX-MS3S">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Regular Price (CFA) *</label>
          <input type="number" name="regular_price" required placeholder="0" step="1" min="0">
        </div>
        <div>
          <label>Sale Price (CFA)</label>
          <input type="number" name="sale_price" placeholder="—" step="1" min="0">
        </div>
      </div>

      <label>Short Description</label>
      <input type="text" name="short_description" placeholder="Brief product summary">

      <label>Full Description</label>
      <textarea name="description" placeholder="Detailed product description"></textarea>

      <label>Product Image</label>
      <input type="file" name="image" accept="image/*" onchange="gsapPreviewAdd(this)">

      <div class="actions">
        <button type="button" class="gsap-btn" style="background:var(--b1);color:var(--wh)" onclick="gsapCloseModals()">Cancel</button>
        <button type="submit" class="gsap-btn gsap-btn-add">Add Product</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="gsap-overlay" id="gsap-edit-modal">
  <div class="gsap-modal">
    <h2>Edit Product</h2>
    <form method="post" enctype="multipart/form-data">
      <?php wp_nonce_field( 'gs_admin_edit' ); ?>
      <input type="hidden" name="gs_action" value="edit_product">
      <input type="hidden" name="product_id" id="gsap-edit-id">

      <label>Product Name *</label>
      <input type="text" name="name" id="gsap-edit-name" required>

      <div class="row">
        <div>
          <label>Category</label>
          <select name="category" id="gsap-edit-cat">
            <option value="0">— Select —</option>
            <?php foreach ( $cat_options as $cid => $cname ) : ?>
              <option value="<?php echo esc_attr( $cid ); ?>"><?php echo esc_html( $cname ); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>SKU</label>
          <input type="text" name="sku" id="gsap-edit-sku">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Regular Price (CFA) *</label>
          <input type="number" name="regular_price" id="gsap-edit-reg" required step="1" min="0">
        </div>
        <div>
          <label>Sale Price (CFA)</label>
          <input type="number" name="sale_price" id="gsap-edit-sale" step="1" min="0">
        </div>
      </div>

      <label>Status</label>
      <select name="status" id="gsap-edit-status">
        <option value="publish">Published</option>
        <option value="draft">Draft</option>
        <option value="pending">Pending</option>
        <option value="private">Private</option>
      </select>

      <label>Short Description</label>
      <input type="text" name="short_description" id="gsap-edit-short">

      <label>Full Description</label>
      <textarea name="description" id="gsap-edit-desc"></textarea>

      <label>Replace Image (leave empty to keep current)</label>
      <input type="file" name="image" accept="image/*" onchange="gsapPreviewEdit(this)">

      <div class="actions">
        <button type="button" class="gsap-btn" style="background:var(--b1);color:var(--wh)" onclick="gsapCloseModals()">Cancel</button>
        <button type="submit" class="gsap-btn gsap-btn-add">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script>
function gsapOpenAdd(){
  document.getElementById('gsap-add-modal').classList.add('open');
}
function gsapOpenEdit(id,name,reg,sale,sku,stat,catId,imgId,desc,short){
  document.getElementById('gsap-edit-id').value=id;
  document.getElementById('gsap-edit-name').value=name;
  document.getElementById('gsap-edit-reg').value=reg;
  document.getElementById('gsap-edit-sale').value=sale;
  document.getElementById('gsap-edit-sku').value=sku;
  document.getElementById('gsap-edit-status').value=stat;
  document.getElementById('gsap-edit-cat').value=catId;
  document.getElementById('gsap-edit-desc').value=desc||'';
  document.getElementById('gsap-edit-short').value=short||'';
  document.getElementById('gsap-edit-modal').classList.add('open');
}
function gsapCloseModals(){
  document.querySelectorAll('.gsap-overlay').forEach(function(m){m.classList.remove('open')});
}
document.querySelectorAll('.gsap-overlay').forEach(function(o){
  o.addEventListener('click',function(e){if(e.target===o)gsapCloseModals()});
});
function gsapPreviewAdd(input){
  if(input.files&&input.files[0]){
    var r=new FileReader();
    r.onload=function(e){
      var img=document.querySelector('#gsap-add-modal .preview-img');
      if(!img){var i=document.createElement('img');i.className='preview-img';i.src=e.target.result;input.parentNode.appendChild(i)}
      else{img.src=e.target.result}
    };
    r.readAsDataURL(input.files[0]);
  }
}
function gsapPreviewEdit(input){
  if(input.files&&input.files[0]){
    var r=new FileReader();
    r.onload=function(e){
      var img=document.querySelector('#gsap-edit-modal .preview-img');
      if(!img){var i=document.createElement('img');i.className='preview-img';i.src=e.target.result;input.parentNode.appendChild(i)}
      else{img.src=e.target.result}
    };
    r.readAsDataURL(input.files[0]);
  }
}
document.addEventListener('keydown',function(e){if(e.key==='Escape')gsapCloseModals()});
</script>

<?php get_footer(); ?>
