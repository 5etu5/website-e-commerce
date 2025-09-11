<?php
$page_title = 'Shopping Cart';
require_once 'includes/header.php';

$cart_items = get_cart_items($pdo);
$cart_total = get_cart_total($pdo);

$currency = '$';          // Fixed currency symbol
$shipping_rate = 5.00;    // Fixed shipping cost
?>

<div class="container py-5">
    <h1 class="text-vintage-brown mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>
    
    <?php if (empty($cart_items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Discover our vintage collection and add some unique pieces!</p>
            <a href="index.php" class="btn btn-vintage">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="cart-item mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <div class="product-image" style="height: 80px; width: 80px;">
                                            <?php if ($item['image_url']): ?>
                                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                     class="img-fluid rounded">
                                            <?php else: ?>
                                                <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded">
                                                    <i class="fas fa-tshirt text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($item['condition_notes']); ?></small>
                                        <?php if (!empty($item['size'])): ?>
                                            <br><small>Size: <?php echo htmlspecialchars($item['size']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                                            <input type="number" class="form-control text-center" value="<?php echo $item['quantity']; ?>" readonly>
                                            <button class="btn btn-outline-secondary btn-sm" onclick="updateCartQuantity(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <strong><?php echo format_price($item['price'] * $item['quantity'], $currency); ?></strong>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                            <i class="fas fa-trash"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h5 class="text-vintage-brown mb-3">Order Summary</h5>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo format_price($cart_total, $currency); ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span><?php echo format_price($shipping_rate, $currency); ?></span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong><?php echo format_price($cart_total + $shipping_rate, $currency); ?></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="checkout.php" class="btn btn-vintage btn-lg">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                        <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function removeFromCart(id){
    fetch('cart_actions.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({action:'remove',id:id})
    }).then(r=>r.json()).then(d=>{
        if(d.success) location.reload();
    });
}

function updateCartQuantity(id, qty){
    if(qty < 1) return;
    fetch('cart_actions.php',{
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({action:'update',id:id,quantity:qty})
    }).then(r=>r.json()).then(d=>{
        if(d.success) location.reload();
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
