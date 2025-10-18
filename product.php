<?php
session_start();
require_once 'db.php';
require_once 'functions.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get product data
$id = isset($_GET['id']) ? filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) : 0;

if (!$id) {
    header("Location: 404.php");
    exit();
}

$product = getProductById($id);
if (!$product) {
    header("Location: 404.php");
    exit();
}

$product = array_merge([
    'additional_images' => [],
    'specifications' => [],
    'full_description' => $product['description'] ?? '', // Fallback to main description
    'category_name' => 'Uncategorized', // Default category name
    'sku' => 'N/A', // Default SKU
    'stock' => 0 // Default stock
], $product);

// Get related products
$relatedProducts = getRelatedProducts($product['category_id'], $id, 4);

// Page title and meta
$pageTitle = htmlspecialchars($product['name']) . ' - Agri E-Commerce';
$metaDescription = substr(strip_tags($product['description']), 0, 160);

// Include header
include 'header.php';
?>

<style>
/* Product Page Styles */
.product-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.breadcrumb {
    margin-bottom: 20px;
    padding: 10px 0;
    font-size: 14px;
    color: #666;
}

.breadcrumb a {
    color: #333;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.product-view {
    display: flex;
    flex-wrap: wrap;
    gap: 30px;
    margin-bottom: 40px;
}

.product-gallery {
    flex: 1;
    min-width: 300px;
}

#main-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.thumbnail-gallery {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.thumbnail {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.2s;
}

.thumbnail:hover, .thumbnail.active {
    border-color: #3498db;
}

.product-details {
    flex: 1;
    min-width: 300px;
}

.product-details h1 {
    font-size: 24px;
    margin-bottom: 15px;
    color: #333;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 14px;
}

.stock-status {
    padding: 3px 10px;
    border-radius: 3px;
    font-weight: bold;
}

.in-stock {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.out-of-stock {
    background-color: #ffebee;
    color: #c62828;
}

.price {
    font-size: 24px;
    font-weight: bold;
    color: #e74c3c;
    margin-bottom: 15px;
}

.product-description {
    margin-bottom: 20px;
    line-height: 1.6;
    color: #555;
}

.add-to-cart-form {
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.quantity-selector {
    display: none; /* Hide quantity selector */
}

.price-calculation {
    margin: 10px 0;
    font-size: 18px;
    font-weight: bold;
}

.btn {
    padding: 10px 20px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s;
}

.btn:hover {
    background-color: #2980b9;
}

.add-to-cart-btn {
    width: 100%;
    max-width: 300px;
    padding: 12px;
    margin-top: 15px;
    background-color: #27ae60;
}

.add-to-cart-btn:hover {
    background-color: #219653;
}

.product-meta-info {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.delivery-info, .returns-info {
    margin: 10px 0;
    display: flex;
    align-items: center;
    color: #666;
}

.delivery-info i, .returns-info i {
    margin-right: 10px;
    color: #3498db;
}

/* Related Products Section */
.related-products {
    margin: 40px 0;
}

.related-products h2 {
    margin-bottom: 20px;
    font-size: 24px;
    color: #333;
    text-align: center;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
}

.product-card {
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.product-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-card h3 {
    padding: 10px;
    margin: 0;
    font-size: 16px;
    color: #333;
}

.product-price {
    padding: 0 10px 10px;
    font-weight: bold;
    color: #e74c3c;
}

.quick-add-btn {
    width: 100%;
    padding: 10px;
    background-color: #f5f5f5;
    border: none;
    border-top: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.quick-add-btn:hover {
    background-color: #e0e0e0;
}

/* Product Tabs Styles */
.product-tabs {
    margin: 40px 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tabs-header {
    display: flex;
    border-bottom: 1px solid #eee;
    padding: 0 20px;
}

.tab-btn {
    padding: 15px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-weight: 600;
    color: #666;
    transition: all 0.3s;
}

.tab-btn:hover {
    color: #333;
}

.tab-btn.active {
    color: #27ae60;
    border-bottom: 2px solid #27ae60;
}

.tab-content {
    padding: 20px;
    display: none;
}

.tab-content.active {
    display: block;
}

/* Description Styles */
.description-content {
    line-height: 1.6;
}

.description-content h3 {
    margin-bottom: 15px;
    color: #333;
}

/* Specifications Styles */
.specifications-content h3 {
    margin-bottom: 20px;
    color: #333;
}

.specs-table {
    width: 100%;
    border-collapse: collapse;
}

.specs-table th, .specs-table td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.specs-table th {
    width: 30%;
    color: #666;
    font-weight: 600;
}

.no-specs {
    color: #666;
    text-align: center;
    padding: 20px;
}

/* Reviews Styles */
.reviews-content h3 {
    margin-bottom: 20px;
    color: #333;
}

.review-item {
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
}

.rating {
    color: #f39c12;
}

.review-date {
    color: #666;
    font-size: 0.9em;
}

.review-content {
    line-height: 1.6;
    color: #444;
}

.write-review {
    margin-top: 30px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
}

.review-form .form-group {
    margin-bottom: 20px;
}

.rating-selector {
    display: flex;
    gap: 10px;
}

.rating-selector input[type="radio"] {
    display: none;
}

.rating-selector label {
    cursor: pointer;
    color: #ddd;
    transition: color 0.3s;
}

.rating-selector input[type="radio"]:checked ~ label,
.rating-selector label:hover,
.rating-selector label:hover ~ label {
    color: #f39c12;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: vertical;
}

.no-reviews, .login-prompt {
    text-align: center;
    color: #666;
    padding: 20px;
}

.login-prompt a {
    color: #27ae60;
    text-decoration: none;
}

.login-prompt a:hover {
    text-decoration: underline;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .product-view {
        flex-direction: column;
    }
    
    .tabs-header {
        flex-wrap: wrap;
    }
    
    .review-header {
        flex-direction: column;
    }
    
    .review-date {
        margin-top: 5px;
    }
}
</style>

<div class="container product-container">
    <div class="breadcrumb">
        <a href="home.php">Home</a> &gt; 
        <a href="category.php?id=<?= $product['category_id'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> &gt; 
        <?= htmlspecialchars($product['name']) ?>
    </div>
    
    <div class="product-view">
        <div class="product-gallery">
            <img id="main-image" src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-product-image">
            <?php if (!empty($product['additional_images'])): ?>
                <div class="thumbnail-gallery">
                    <img class="thumbnail active" src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         data-image="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>">
                    <?php foreach ($product['additional_images'] as $img): ?>
                        <img class="thumbnail" src="assets/images/products/<?php echo htmlspecialchars($img); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                             data-image="assets/images/products/<?php echo htmlspecialchars($img); ?>">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="product-details">
            <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
            <div class="product-meta">
                <span class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                <span class="stock-status <?= $product['stock'] > 0 ? 'in-stock' : 'out-of-stock' ?>">
                    <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                </span>
            </div>
            
            <div class="price">₹<?= number_format($product['price'], 2) ?></div>
            
            <div class="product-description">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
            
            <?php if ($product['stock'] > 0): ?>
                <form method="post" action="cart.php" class="add-to-cart-form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="quantity" value="1">
                    
                    <button class="btn add-to-cart-btn" type="submit" name="add_to_cart">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                </form>
            <?php else: ?>
                <div class="out-of-stock-message">
                    This product is currently out of stock. Please check back later.
                </div>
            <?php endif; ?>
            
            <div class="product-meta-info">
                <div class="delivery-info">
                    <i class="fa fa-truck"></i> Free delivery for orders over ₹500
                </div>
                <div class="returns-info">
                    <i class="fa fa-refresh"></i> 30-day returns policy
                </div>
            </div>
        </div>
    </div>
    
    <?php if (!empty($relatedProducts)): ?>
    <div class="related-products">
        <h2>You may also like</h2>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $rProduct): ?>
                <div class="product-card">
                    <a href="product.php?id=<?= $rProduct['id'] ?>">
                        <img src="assets/images/products/<?php echo htmlspecialchars($rProduct['image']); ?>" 
                             alt="<?php echo htmlspecialchars($rProduct['name']); ?>">
                        <h3><?php echo htmlspecialchars($rProduct['name']); ?></h3>
                        <div class="product-price">₹<?php echo number_format($rProduct['price'], 2); ?></div>
                    </a>
                    <button class="quick-add-btn" data-product-id="<?php echo $rProduct['id']; ?>">Quick Add</button>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="product-tabs">
        <div class="tabs-header">
            <button class="tab-btn active" data-tab="description">Description</button>
            <button class="tab-btn" data-tab="specifications">Specifications</button>
            <button class="tab-btn" data-tab="reviews">Reviews</button>
        </div>
        
        <div class="tab-content active" id="description-tab">
            <div class="description-content">
                <h3>Product Description</h3>
                <div class="description-text">
                    <?= nl2br(htmlspecialchars($product['full_description'] ?? $product['description'])) ?>
                </div>
            </div>
        </div>
        
        <div class="tab-content" id="specifications-tab">
            <div class="specifications-content">
                <h3>Product Specifications</h3>
                <?php 
                $specifications = getProductSpecifications($id);
                if (!empty($specifications)): ?>
                    <table class="specs-table">
                        <?php foreach ($specifications as $spec): ?>
                            <tr>
                                <th><?php echo htmlspecialchars($spec['spec_name']); ?></th>
                                <td><?php echo htmlspecialchars($spec['spec_value']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p class="no-specs">No specifications available for this product.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="tab-content" id="reviews-tab">
            <div class="reviews-content">
                <h3>Customer Reviews</h3>
                <?php
                $reviews = getProductReviews($id);
                if (!empty($reviews)): ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="review-header">
                                    <span class="reviewer-name"><?php echo htmlspecialchars($review['user_name']); ?></span>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="review-date"><?= date('M d, Y', strtotime($review['date_added'])); ?></span>
                                </div>
                                <div class="review-content"><?php echo nl2br(htmlspecialchars($review['review'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-reviews">No reviews yet. Be the first to review this product!</p>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="write-review">
                        <h3>Write a Review</h3>
                        <form method="post" action="submit_review.php" class="review-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            
                            <div class="form-group">
                                <label for="rating">Rating:</label>
                                <div class="rating-selector">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                        <label for="star<?php echo $i; ?>"><i class="fa fa-star"></i></label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="review">Your Review:</label>
                                <textarea id="review" name="review" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn">Submit Review</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p class="login-prompt">Please <a href="login.php">log in</a> to write a review.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity selector functionality
    const quantityInput = document.getElementById('quantity');
    const decrementBtn = document.querySelector('[data-qty-action="decrement"]');
    const incrementBtn = document.querySelector('[data-qty-action="increment"]');
    const totalPriceElement = document.getElementById('total-price');
    const pricePerUnit = parseFloat(quantityInput.dataset.price);
    
    function updateTotalPrice() {
        const quantity = parseInt(quantityInput.value);
        const totalPrice = quantity * pricePerUnit;
        totalPriceElement.textContent = '₹' + totalPrice.toFixed(2);
    }
    
    decrementBtn.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
            updateTotalPrice();
        }
    });
    
    incrementBtn.addEventListener('click', function() {
        let currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.max);
        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
            updateTotalPrice();
        }
    });
    
    quantityInput.addEventListener('change', function() {
        let value = parseInt(this.value);
        const maxValue = parseInt(this.max);
        if (value < 1) value = 1;
        if (value > maxValue) value = maxValue;
        this.value = value;
        updateTotalPrice();
    });
    
    // Show cart message if exists
    <?php if (isset($_SESSION['cart_message'])): ?>
        const notification = document.createElement('div');
        notification.className = 'notification show';
        notification.textContent = '<?php echo $_SESSION['cart_message']; ?>';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
        <?php unset($_SESSION['cart_message']); ?>
    <?php endif; ?>

    // Image gallery functionality
    const mainImage = document.getElementById('main-image');
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            // Update main image
            if (mainImage) {
                mainImage.src = this.getAttribute('data-image');
            }
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button
            button.classList.add('active');
            
            // Show corresponding content
            const tabId = button.getAttribute('data-tab') + '-tab';
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Quick add functionality
    document.querySelectorAll('.quick-add-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = button.getAttribute('data-product-id');
            
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            
            fetch('quick_add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                },
                body: 'product_id=' + productId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                button.disabled = false;
                button.textContent = 'Quick Add';
                
                if (data.success) {
                    showNotification('Product added to cart');
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                button.disabled = false;
                button.textContent = 'Quick Add';
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
        });
    });

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = 'notification ' + type;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    function updateCartCount(count) {
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }
});
</script>

<?php include 'footer.php'; ?>

</body>
</html>

