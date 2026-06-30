<?php
/**
 * Template Name: Price Editor
 * GamTech — bulk price editor for all products
 */
defined( 'ABSPATH' ) || exit;

// Access control: only with secret key
$editor_key = isset( $_GET['key'] ) ? sanitize_text_field( $_GET['key'] ) : '';
if ( $editor_key !== 'gamtech2026prices' ) {
    wp_die( 'Access denied.' );
}

// Handle AJAX save
if ( isset( $_POST['gs_save_prices'] ) && wp_verify_nonce( $_POST['gs_price_nonce'] ?? '', 'gs_save_prices' ) ) {
    $saved = 0;
    $prices = $_POST['prices'] ?? array();
    foreach ( $prices as $pid => $data ) {
        $product = wc_get_product( (int) $pid );
        if ( ! $product ) continue;
        $reg = sanitize_text_field( $data['regular_price'] ?? '' );
        $sale = sanitize_text_field( $data['sale_price'] ?? '' );
        $product->set_regular_price( $reg !== '' ? $reg : '' );
        $product->set_sale_price( $sale !== '' ? $sale : '' );
        $product->save();
        $saved++;
    }
    $redirect = add_query_arg( array(
        'key'   => $editor_key,
        'saved' => $saved,
        'time'  => time(),
    ), get_permalink() );
    wp_safe_redirect( $redirect );
    exit;
}

$saved_count = isset( $_GET['saved'] ) ? (int) $_GET['saved'] : 0;

get_header();
?>

<style>
/* Price Editor Styles */
.gs-pe-wrap{max-width:1200px;margin:0 auto;padding:20px}
.gs-pe-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px}
.gs-pe-header h1{font-size:22px;color:var(--wh);margin:0}
.gs-pe-stats{display:flex;gap:12px;font-size:13px;color:var(--di)}
.gs-pe-stats span{background:var(--card);padding:4px 10px;border-radius:6px;border:1px solid var(--b1)}
.gs-pe-stats .priced{color:var(--gr)}
.gs-pe-stats .unpriced{color:var(--re)}
.gs-pe-search{width:100%;padding:10px 14px;background:var(--card);border:1px solid var(--b1);border-radius:8px;color:var(--wh);font-size:14px;outline:none;margin-bottom:16px}
.gs-pe-search:focus{border-color:var(--pu)}
.gs-pe-search::placeholder{color:var(--di)}
.gs-pe-table{width:100%;border-collapse:collapse;font-size:13px}
.gs-pe-table th{background:var(--card);color:var(--di);padding:10px 12px;text-align:left;font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid var(--b1);position:sticky;top:0;z-index:2}
.gs-pe-table td{padding:10px 12px;border-bottom:1px solid var(--b1);vertical-align:middle}
.gs-pe-table tr{transition:background .15s}
.gs-pe-table tr:hover{background:rgba(124,58,237,.06)}
.gs-pe-img{width:44px;height:44px;border-radius:6px;object-fit:cover;background:var(--b1)}
.gs-pe-name{color:var(--wh);font-weight:600;font-size:13px;max-width:250px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.gs-pe-cat{color:var(--di);font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.gs-pe-input{width:120px;padding:8px 10px;background:var(--b1);border:1px solid transparent;border-radius:6px;color:var(--wh);font-size:13px;font-weight:600;outline:none;transition:border-color .15s}
.gs-pe-input:focus{border-color:var(--pu)}
.gs-pe-input::placeholder{color:var(--di);font-weight:400}
.gs-pe-input.has-price{border-left:3px solid var(--gr)}
.gs-pe-input.no-price{border-left:3px solid var(--re)}
.gs-pe-status{font-size:11px;font-weight:600;padding:3px 8px;border-radius:4px;display:inline-block}
.gs-pe-status.set{background:rgba(34,197,94,.15);color:var(--gr)}
.gs-pe-status.none{background:rgba(239,68,68,.15);color:var(--re)}
.gs-pe-save-row{position:sticky;bottom:0;background:var(--bg);padding:16px 0;border-top:1px solid var(--b1);display:flex;justify-content:space-between;align-items:center;gap:12px;z-index:2}
.gs-pe-save-btn{padding:12px 32px;background:var(--pu);color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:700;cursor:pointer;transition:all .15s}
.gs-pe-save-btn:hover{background:#6d28d9;transform:translateY(-1px)}
.gs-pe-save-btn:active{transform:translateY(0)}
.gs-pe-save-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
.gs-pe-saved-msg{color:var(--gr);font-weight:600;font-size:14px;animation:gsPeFade 3s ease forwards}
@keyframes gsPeFade{0%,70%{opacity:1}100%{opacity:0}}
.gs-pe-count{color:var(--di);font-size:13px}
.gs-pe-pagination{display:flex;gap:8px;justify-content:center;margin-top:16px}
.gs-pe-pagination a,.gs-pe-pagination span{padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;color:var(--wh);background:var(--card);border:1px solid var(--b1);transition:all .15s}
.gs-pe-pagination a:hover{border-color:var(--pu)}
.gs-pe-pagination .current{background:var(--pu);border-color:var(--pu)}
@media(max-width:768px){
  .gs-pe-table{font-size:12px}
  .gs-pe-input{width:90px;padding:6px 8px}
  .gs-pe-img{width:36px;height:36px}
  .gs-pe-table th:nth-child(4),.gs-pe-table td:nth-child(4),
  .gs-pe-table th:nth-child(5),.gs-pe-table td:nth-child(5){min-width:100px}
}
</style>

<div class="gs-pe-wrap">
  <div class="gs-pe-header">
    <h1>Price Editor</h1>
    <div class="gs-pe-stats">
      <span class="priced"><?php echo esc_html( $priced_count ); ?> priced</span>
      <span class="unpriced"><?php echo esc_html( $unpriced_count ); ?> to price</span>
    </div>
  </div>

  <?php if ( $saved_count > 0 ) : ?>
    <p class="gs-pe-saved-msg">Saved <?php echo esc_html( $saved_count ); ?> products!</p>
  <?php endif; ?>

  <input type="text" class="gs-pe-search" id="gs-pe-search" placeholder="Search products by name or category...">

  <form method="post" id="gs-pe-form">
    <?php wp_nonce_field( 'gs_save_prices', 'gs_price_nonce' ); ?>
    <input type="hidden" name="gs_save_prices" value="1">
    <table class="gs-pe-table">
      <thead>
        <tr>
          <th>Image</th>
          <th>Product</th>
          <th>Category</th>
          <th>Regular Price (CFA)</th>
          <th>Sale Price (CFA)</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
        $per_page = 50;
        $products_q = new WP_Query( array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $per_page,
            'paged'          => $paged,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );
        $total = $products_q->found_posts;
        $priced_count = 0;
        $unpriced_count = 0;
        $all_q = new WP_Query( array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        ) );
        foreach ( $all_q->posts as $pid ) {
            $p = wc_get_product( $pid );
            if ( $p && $p->get_regular_price() !== '' ) {
                $priced_count++;
            } else {
                $unpriced_count++;
            }
        }
        wp_reset_postdata();
        $total_pages = $products_q->max_num_pages;

        while ( $products_q->have_posts() ) : $products_q->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;
            $pid    = $product->get_id();
            $name   = $product->get_name();
            $img    = $product->get_image_id()
                ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' )
                : wc_placeholder_img_src( 'thumbnail' );
            $cats   = get_the_terms( $pid, 'product_cat' );
            $cat    = ( ! empty( $cats ) && ! is_wp_error( $cats ) ) ? $cats[0]->name : '';
            $reg    = $product->get_regular_price();
            $sale   = $product->get_sale_price();
            $has_p  = $reg !== '';
            ?>
            <tr data-search="<?php echo esc_attr( strtolower( $name . ' ' . $cat ) ); ?>">
              <td><img class="gs-pe-img" src="<?php echo esc_url( $img ); ?>" alt=""></td>
              <td class="gs-pe-name"><?php echo esc_html( $name ); ?></td>
              <td class="gs-pe-cat"><?php echo esc_html( $cat ); ?></td>
              <td>
                <input type="number" class="gs-pe-input <?php echo $has_p ? 'has-price' : 'no-price'; ?>"
                       name="prices[<?php echo esc_attr( $pid ); ?>][regular_price]"
                       value="<?php echo esc_attr( $reg ); ?>"
                       placeholder="0" step="1" min="0">
              </td>
              <td>
                <input type="number" class="gs-pe-input"
                       name="prices[<?php echo esc_attr( $pid ); ?>][sale_price]"
                       value="<?php echo esc_attr( $sale ); ?>"
                       placeholder="-" step="1" min="0">
              </td>
              <td>
                <?php if ( $has_p ) : ?>
                  <span class="gs-pe-status set">Set</span>
                <?php else : ?>
                  <span class="gs-pe-status none">Not priced</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; wp_reset_postdata(); ?>
      </tbody>
    </table>

    <div class="gs-pe-save-row">
      <span class="gs-pe-count"><?php echo esc_html( $total ); ?> products total</span>
      <button type="submit" class="gs-pe-save-btn" id="gs-pe-save">Save All Prices</button>
    </div>

    <?php if ( $total_pages > 1 ) : ?>
      <div class="gs-pe-pagination">
        <?php if ( $paged > 1 ) : ?>
          <a href="<?php echo esc_url( add_query_arg( array( 'paged' => $paged - 1, 'key' => $editor_key ) ) ); ?>">← Prev</a>
        <?php endif; ?>
        <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
          <?php if ( $i === $paged ) : ?>
            <span class="current"><?php echo $i; ?></span>
          <?php else : ?>
            <a href="<?php echo esc_url( add_query_arg( array( 'paged' => $i, 'key' => $editor_key ) ) ); ?>"><?php echo $i; ?></a>
          <?php endif; ?>
        <?php endfor; ?>
        <?php if ( $paged < $total_pages ) : ?>
          <a href="<?php echo esc_url( add_query_arg( array( 'paged' => $paged + 1, 'key' => $editor_key ) ) ); ?>">Next →</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var search = document.getElementById('gs-pe-search');
  if (search) {
    search.addEventListener('input', function() {
      var q = this.value.toLowerCase();
      var rows = document.querySelectorAll('.gs-pe-table tbody tr');
      rows.forEach(function(row) {
        var text = row.getAttribute('data-search') || '';
        row.style.display = text.indexOf(q) !== -1 ? '' : 'none';
      });
    });
  }
  var form = document.getElementById('gs-pe-form');
  var btn = document.getElementById('gs-pe-save');
  if (form && btn) {
    form.addEventListener('submit', function() {
      btn.disabled = true;
      btn.textContent = 'Saving...';
    });
  }
});
</script>

<?php get_footer(); ?>
