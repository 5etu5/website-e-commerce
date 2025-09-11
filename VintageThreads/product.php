<?php
require_once 'includes/header.php';

// Get product by slug
$product_slug = $_GET['slug'] ?? '';
$product = get_product_by_slug($pdo, $product_slug);

if (!$product) {
    header('Location: index.php');
    exit;
}

$page_title = $product['name'];

// Get related products from same category
$related_products = get_products($pdo, $product['category_id'], 4);
$related_products = array_filter($related_products, function($p) use ($product) {
    return $p['id'] !== $product['id'];
});
?>

<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="category.php?slug=<?php echo $product['category_name']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($product['name']); ?></li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <!-- Product Image -->
            <div class="product-image-large mb-3" style="height: 500px;">
                <?php if ($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="img-fluid rounded">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded">
                        <i class="fas fa-tshirt fa-5x text-muted"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="h2 text-vintage-brown"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="product-price fs-2 fw-bold text-vintage-brown mb-3">
                    <?php echo format_price($product['price']); ?>
                </div>

                <?php if ($product['condition_notes']): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Condition:</strong> <?php echo htmlspecialchars($product['condition_notes']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($product['description']): ?>
                    <div class="mb-4">
                        <h5>Description</h5>
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Size Selection -->
                <?php if ($product['sizes']): ?>
                    <div class="mb-4">
                        <h5>Size</h5>
                        <div class="btn-group" role="group" id="sizeGroup">
                            <?php 
                            $sizes = explode(',', $product['sizes']);
                            foreach ($sizes as $size): 
                                $size = trim($size);
                                $stock_col = 'stock_' . strtoupper($size); // Database column: stock_S, stock_M, etc.
                                $size_stock = $product[$stock_col] ?? 0;
                            ?>
                                <input type="radio" class="btn-check size-radio" name="size" 
                                       id="size_<?php echo $size; ?>" value="<?php echo $size; ?>" data-stock="<?php echo $size_stock; ?>">
                                <label class="btn btn-outline-secondary" for="size_<?php echo $size; ?>">
                                    <?php echo htmlspecialchars($size); ?>
                                    <?php if ($size_stock == 0) echo ' (Sold Out)'; ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Quantity -->
                <div class="mb-4" id="quantityContainer">
                    <h5>Quantity</h5>
                    <div class="input-group" style="width: 150px;">
                        <button class="btn btn-outline-secondary quantity-control decrement" type="button">-</button>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1">
                        <button class="btn btn-outline-secondary quantity-control increment" type="button">+</button>
                    </div>
                    <small class="text-muted" id="stockInfo"><?php echo $product['stock_quantity']; ?> available</small>
                </div>

                <!-- Add to Cart -->
                <div class="d-grid gap-2 mb-4">
                    <button class="btn btn-vintage btn-lg" id="addToCartBtn" data-product-id="<?php echo $product['id']; ?>">
                        <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="text-vintage-brown mb-4">Related Products</h3>
            <div class="row g-4">
                <?php foreach (array_slice($related_products, 0, 4) as $related): ?>
                    <div class="col-lg-3 col-md-6">
                        <div class="card product-card h-100">
                            <div class="product-image">
                                <?php if ($related['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($related['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($related['name']); ?>"
                                         class="img-fluid">
                                <?php else: ?>
                                    <i class="fas fa-tshirt"></i>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h5 class="product-title">
                                    <a href="product.php?slug=<?php echo $related['slug']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($related['name']); ?>
                                    </a>
                                </h5>
                                <div class="product-price">
                                    <?php echo format_price($related['price']); ?>
                                </div>
                                <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $related['id']; ?>">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- AJAX Add to Cart & Size Stock Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sizeRadios = document.querySelectorAll('.size-radio');
    const quantityInput = document.getElementById('quantity');
    const stockInfo = document.getElementById('stockInfo');
    const addToCartBtn = document.getElementById('addToCartBtn');

    // Update stock based on selected size
    function updateStock() {
        const selected = document.querySelector('.size-radio:checked');
        if (!selected) {
            stockInfo.textContent = '';
            quantityInput.disabled = true;
            addToCartBtn.disabled = true;
            return;
        }

        const stock = parseInt(selected.dataset.stock);
        if (stock <= 0) {
            quantityInput.value = 0;
            quantityInput.disabled = true;
            addToCartBtn.disabled = true;
            stockInfo.textContent = 'Sold Out';
        } else {
            quantityInput.value = 1;
            quantityInput.disabled = false;
            addToCartBtn.disabled = false;
            quantityInput.max = stock;
            stockInfo.textContent = stock + ' available';
        }
    }

    sizeRadios.forEach(radio => radio.addEventListener('change', updateStock));
    updateStock(); // initialize on page load

    // AJAX Add to Cart
    addToCartBtn.addEventListener('click', function() {
        const productId = this.dataset.productId;
        const selectedSize = document.querySelector('.size-radio:checked')?.value;
        const quantity = parseInt(quantityInput.value);

        if (!selectedSize) {
            alert('Please select a size.');
            return;
        }

        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({product_id: productId, size: selectedSize, quantity: quantity})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
            } else {
                alert('Error: ' + (data.message || 'Unable to add to cart.'));
            }
        });
    });

    // Quantity buttons
    document.querySelector('.quantity-control.decrement').addEventListener('click', () => {
        if (quantityInput.value > 1) quantityInput.value--;
    });
    document.querySelector('.quantity-control.increment').addEventListener('click', () => {
        if (quantityInput.value < parseInt(quantityInput.max)) quantityInput.value++;
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
