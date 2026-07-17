<?php
/**
 * Template Name: Order Planning Page
 * Description: Displays customer order details from WhatsApp checkout
 */

get_header();
?>

<style>
body{background:#0a0a0a;color:#e0e0e0;font-family:'Poppins',sans-serif;margin:0;padding:20px;}
.order-container{max-width:800px;margin:0 auto;background:#1a1a2e;border-radius:16px;padding:30px;box-shadow:0 8px 32px rgba(0,0,0,0.4);}
.order-header{border-bottom:2px solid #7c3aed;padding-bottom:20px;margin-bottom:30px;}
.order-header h1{color:#7c3aed;margin:0 0 10px 0;font-size:28px;}
.order-id{color:#a78bfa;font-size:14px;font-family:monospace;}
.order-item{display:flex;gap:20px;background:#16213e;border-radius:12px;padding:20px;margin-bottom:15px;align-items:center;}
.item-img{width:100px;height:100px;object-fit:cover;border-radius:8px;background:#2e2e2e;}
.item-info{flex:1;}
.item-name{font-size:18px;font-weight:600;color:#fff;margin-bottom:5px;}
.item-details{color:#a0a0a0;font-size:14px;margin:5px 0;}
.item-total{font-size:20px;font-weight:700;color:#7c3aed;}
.order-summary{background:#16213e;border-radius:12px;padding:25px;margin-top:30px;}
.summary-row{display:flex;justify-content:space-between;padding:10px 0;font-size:18px;}
.summary-total{border-top:2px solid #7c3aed;margin-top:15px;padding-top:15px;font-size:24px;font-weight:700;color:#7c3aed;}
.no-order{text-align:center;padding:60px 20px;color:#a0a0a0;}
.no-order svg{margin-bottom:20px;}
@media print{body{background:#fff;color:#000;}.order-container{box-shadow:none;}}
</style>

<div class="order-container">
<?php
// Get order ID from URL
$order_id = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';

if (!$order_id):
?>
    <div class="no-order">
        <svg width="80" height="80" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        <h2>No Order Found</h2>
        <p>This order link is invalid or has expired.</p>
        <p><a href="<?php echo home_url('/'); ?>" style="color:#7c3aed;">Return to Shop</a></p>
    </div>
<?php else: ?>
    <div class="order-header">
        <h1>🛒 Order Details</h1>
        <div class="order-id">Order ID: <?php echo esc_html($order_id); ?></div>
        <div style="color:#a0a0a0;font-size:12px;margin-top:5px;">
            Note: Order data is stored temporarily. Seller should save this information.
        </div>
    </div>

    <div id="order-content">
        <p style="text-align:center;color:#a0a0a0;padding:40px 0;">
            <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;">
                <circle cx="12" cy="12" r="10" stroke-dasharray="60" stroke-dashoffset="20"/>
            </svg><br>Loading order details...
        </p>
    </div>
<?php endif; ?>
</div>

<script>
(function(){
    var orderId='<?php echo esc_js($order_id); ?>';
    if(!orderId)return;
    
    // Try to get order from sessionStorage
    var orderData=sessionStorage.getItem(orderId);
    if(!orderData){
        // Try localStorage as fallback
        orderData=localStorage.getItem(orderId);
    }
    
    var container=document.getElementById('order-content');
    if(!container)return;
    
    if(!orderData){
        container.innerHTML='<div class="no-order"><h3>Order data not found</h3><p>The order information is no longer available in your browser.</p><p style="font-size:12px;color:#666;">This usually happens when:</p><ul style="text-align:left;max-width:400px;margin:20px auto;color:#888;"><li>The link was opened on a different device</li><li>Browser data was cleared</li><li>The order is older than 24 hours</li></ul></div>';
        return;
    }
    
    try{
        var order=JSON.parse(orderData);
        var html='';
        
        order.items.forEach(function(item){
            html+='<div class="order-item">';
            if(item.img){
                html+='<img src="'+item.img+'" alt="'+item.name+'" class="item-img">';
            }else{
                html+='<div class="item-img" style="display:flex;align-items:center;justify-content:center;color:#666;"><svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/></svg></div>';
            }
            html+='<div class="item-info">';
            html+='<div class="item-name">'+item.name+'</div>';
            html+='<div class="item-details">Price: $'+item.price.toFixed(2)+' each</div>';
            html+='<div class="item-details">Quantity: '+item.qty+'</div>';
            html+='</div>';
            html+='<div class="item-total">$'+item.total.toFixed(2)+'</div>';
            html+='</div>';
        });
        
        html+='<div class="order-summary">';
        html+='<div class="summary-row">Items: <span>'+order.items.length+'</span></div>';
        html+='<div class="summary-row summary-total">Total: <span>$'+order.total.toFixed(2)+'</span></div>';
        if(order.timestamp){
            var date=new Date(order.timestamp);
            html+='<div style="text-align:center;margin-top:20px;color:#666;font-size:12px;">Order created: '+date.toLocaleString()+'</div>';
        }
        html+='</div>';
        
        container.innerHTML=html;
        
        // Also save to localStorage for persistence
        localStorage.setItem(orderId,orderData);
        
    }catch(e){
        container.innerHTML='<div class="no-order"><h3>Error loading order</h3><p>There was a problem displaying the order details.</p></div>';
    }
})();
</script>

<?php
get_footer();
?>
