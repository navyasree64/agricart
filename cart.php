<?php
session_start();
include('db.php');

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get user_id (0 for guests)
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }
    
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));
    
    // Check if user is logged in
    if ($user_id > 0) {
        // Check if product exists in cart
        $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing item
            $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
            $stmt->bind_param("iii", $quantity, $user_id, $product_id);
        } else {
            // Add new item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        }
        $stmt->execute();
    }
    
    // Update session cart regardless of login status
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    // Redirect back to the referring page with success message
    $_SESSION['success'] = "Product added to cart successfully!";
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'category.php';
    header("Location: " . $referer);
    exit();
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    
    if ($user_id > 0) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $remove_id);
        $stmt->execute();
    }
    
    // Update session cart regardless of login status
    unset($_SESSION['cart'][$remove_id]);
    
    header("Location: cart.php");
    exit();
}

// Include header after all redirects
include 'header.php';

// Handle Update Quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        $product_id = intval($product_id);
        $quantity = max(1, intval($quantity));
        
        if ($user_id > 0) {
            if ($quantity > 0) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
                $stmt->bind_param("ii", $user_id, $product_id);
                $stmt->execute();
            }
        }
        
        // Update session cart regardless of login status
        if ($quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    echo "<script>window.location.href = 'cart.php';</script>";
    exit();
}

// Fetch cart products from database
$products = [];
$cartItems = [];

if ($user_id > 0) {
    // Get cart items for logged-in user from database
    $stmt = $conn->prepare("SELECT c.product_id, c.quantity, p.*, cat.name as category_name 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           LEFT JOIN categories cat ON p.category_id = cat.id 
                           WHERE c.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $products[$row['product_id']] = $row;
        $cartItems[$row['product_id']] = $row['quantity'];
    }
} else {
    // For guest users, get product details from session
    if (!empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        if (!empty($product_ids)) {
            $placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
            $stmt = $conn->prepare("SELECT p.*, cat.name as category_name 
                                  FROM products p 
                                  LEFT JOIN categories cat ON p.category_id = cat.id 
                                  WHERE p.id IN ($placeholders)");
            $stmt->bind_param(str_repeat('i', count($product_ids)), ...$product_ids);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $products[$row['id']] = $row;
                $cartItems[$row['id']] = $_SESSION['cart'][$row['id']];
            }
        }
    }
}

// Store cart items in session for quick access
$_SESSION['cart'] = $cartItems;

// Base URL for assets
$baseUrl = "./"; // Update this to match your project structure
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart - Agri E-Commerce</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    :root {
      --primary-color: #28a745;
      --primary-hover: #218838;
      --secondary-color: #6c757d;
      --accent-color: #ffc107;
      --danger-color: #dc3545;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2f2f2;
      color: #333;
      line-height: 1.6;
    }
    
    header {
      background-color: var(--primary-color);
      padding: 1rem;
      color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .header-title {
      font-size: 1.5rem;
      font-weight: bold;
    }
    
    nav {
      display: flex;
      align-items: center;
    }
    
    nav a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    nav a:hover {
      color: var(--accent-color);
    }
    
    .container {
      max-width: 1200px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    
    .page-title {
      font-size: 2rem;
      margin-bottom: 1.5rem;
      color: #333;
      border-bottom: 2px solid var(--primary-color);
      padding-bottom: 0.5rem;
    }
    
    .cart-summary {
      background-color: var(--light-bg);
      padding: 1.5rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }
    
    .cart-summary h3 {
      margin-bottom: 1rem;
      font-size: 1.2rem;
    }
    
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--border-color);
    }
    
    .summary-row.total {
      border-top: 2px solid var(--border-color);
      border-bottom: none;
      font-weight: bold;
      font-size: 1.2rem;
      margin-top: 0.5rem;
      padding-top: 1rem;
    }
    
    .cart-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 2rem;
    }
    
    .cart-table th,
    .cart-table td {
      padding: 1rem;
      text-align: left;
      vertical-align: middle;
      border-bottom: 1px solid var(--border-color);
    }
    
    .cart-table th {
      background-color: var(--light-bg);
      font-weight: 600;
      color: #555;
    }
    
    .product-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .product-image {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 4px;
      border: 1px solid var(--border-color);
    }
    
    .product-details {
      flex: 1;
    }
    
    .product-name {
      font-weight: 600;
      margin-bottom: 0.25rem;
      color: #333;
    }
    
    .product-category {
      font-size: 0.85rem;
      color: var(--secondary-color);
    }
    
    .quantity-input {
      width: 70px;
      padding: 0.5rem;
      text-align: center;
      border: 1px solid var(--border-color);
      border-radius: 4px;
    }
    
    .btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.3s ease;
      text-align: center;
    }
    
    .btn:hover {
      background: var(--primary-hover);
    }
    
    .btn-secondary {
      background: var(--secondary-color);
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .btn-danger {
      background: var(--danger-color);
    }
    
    .btn-danger:hover {
      background: #c82333;
    }
    
    .btn-sm {
      padding: 0.4rem 0.75rem;
      font-size: 0.875rem;
    }
    
    .btn-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    
    .actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin: 2rem 0;
    }
    
    .empty-cart {
      text-align: center;
      padding: 3rem;
    }
    
    .empty-cart i {
      font-size: 4rem;
      color: var(--secondary-color);
      margin-bottom: 1rem;
    }
    
    .empty-cart h3 {
      font-size: 1.5rem;
      margin-bottom: 1rem;
      color: #555;
    }
    
    .recommended-products {
      margin-top: 3rem;
    }
    
    .section-title {
      font-size: 1.5rem;
      margin-bottom: 1.5rem;
      color: #333;
      position: relative;
      padding-bottom: 0.5rem;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      left: 0;
      bottom: 0;
      width: 50px;
      height: 3px;
      background-color: var(--primary-color);
    }
    
    .product-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    
    .product-card {
      border: 1px solid var(--border-color);
      border-radius: 8px;
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .product-card-image {
      height: 180px;
      width: 100%;
      object-fit: cover;
    }
    
    .product-card-info {
      padding: 1rem;
    }
    
    .product-card-name {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .product-card-price {
      color: var(--primary-color);
      font-weight: 600;
      margin-bottom: 0.75rem;
    }
    
    .product-card-actions {
      display: flex;
      justify-content: space-between;
    }
    
    footer {
      background: #333;
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }
    
    @media (max-width: 768px) {
      .product-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      }
      
      .cart-table {
        display: block;
        overflow-x: auto;
      }
      
      .actions {
        flex-direction: column;
        gap: 1rem;
      }
      
      .actions .btn {
        width: 100%;
      }
    }
  </style>
</head>
<body>



<div class="container">
  <h1 class="page-title">Your Shopping Cart</h1>

  <?php if (empty($products)): ?>
    <div class="empty-cart">
      <i class="fas fa-shopping-cart"></i>
      <h3>Your cart is empty</h3>
      <p>Looks like you haven't added any products to your cart yet.</p>
      <a href="category.php" class="btn" style="margin-top: 1.5rem;">Start Shopping</a>
    </div>
  <?php else: ?>
    <form action="cart.php" method="post">
      <table class="cart-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Subtotal</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php 
            $total = 0;
            $itemCount = 0;
          ?>
          <?php foreach ($products as $product): ?>
            <?php
              $pid = $product['id'];
              $qty = $_SESSION['cart'][$pid];
              $itemCount += $qty;
              $price = isset($product['discount_price']) && $product['discount_price'] > 0 ? $product['discount_price'] : $product['price'];
              $subtotal = $qty * $price;
              $total += $subtotal;
            ?>
            <tr class="cart-item" data-price="<?= $price ?>">
              <td>
                <div class="product-info">
                  <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                  <div class="product-details">
                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                    <?php if (isset($product['category_name'])): ?>
                      <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>
              <td class="product-price">₹<?= number_format($price, 2) ?></td>
              <td>
                <input type="number" name="quantity[<?= $pid ?>]" value="<?= $qty ?>" min="1" class="quantity-input" data-product-id="<?= $pid ?>">
              </td>
              <td class="product-subtotal">₹<?= number_format($subtotal, 2) ?></td>
              <td>
                <a href="cart.php?remove=<?= $pid ?>" class="btn btn-danger btn-sm">
                  <i class="fas fa-trash"></i> Remove
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
      <div class="actions">
        <button type="submit" name="update_cart" class="btn btn-secondary btn-icon">
          <i class="fas fa-sync-alt"></i> Update Cart
        </button>
        <a href="category.php" class="btn btn-secondary btn-icon">
          <i class="fas fa-shopping-basket"></i> Continue Shopping
        </a>
      </div>
    </form>
    
    <div class="cart-summary">
      <h3>Cart Summary</h3>
      
      <div class="summary-row">
        <span>Items (<span id="item-count"><?= $itemCount ?></span>)</span>
        <span id="cart-subtotal">₹<?= number_format($total, 2) ?></span>
      </div>
      <div class="summary-row">
        <span>Shipping</span>
        <span id="shipping-cost">
          <?php
            $shipping = ($total < 500) ? 50 : 0;
            echo $shipping > 0 ? '₹' . number_format($shipping, 2) : 'Free';
          ?>
        </span>
      </div>
      <div class="summary-row total">
        <span>Total</span>
        <span id="cart-total">₹<?= number_format($total + $shipping, 2) ?></span>
      </div>
      
      <?php if ($shipping > 0): ?>
        <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
          <i class="fas fa-info-circle"></i> Add ₹<?= number_format(500 - $total, 2) ?> more to get free shipping!
        </p>
      <?php endif; ?>
      
      <!-- Direct link to checkout instead of form submission -->
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="checkout.php" class="btn" style="display: block; margin-top: 1.5rem; text-align: center;">
          <i class="fas fa-check"></i> Proceed to Checkout
        </a>
      <?php else: ?>
        <a href="login.php?redirect=checkout.php" class="btn" style="display: block; margin-top: 1.5rem; text-align: center;">
          <i class="fas fa-sign-in-alt"></i> Login to Checkout
        </a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  
  <!-- Recommended Products Section -->
  <div class="recommended-products">
    <h2 class="section-title">Recommended Products</h2>
    <div class="product-grid">
      <?php
      // Get recommended products
      $recommendedProducts = [];
      
      if (isset($conn)) {
          $result = $conn->query("SELECT id, name, price, discount_price, image FROM products ORDER BY RAND() LIMIT 4");
          if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $recommendedProducts[] = $row;
              }
          }
      }
      
      // Fallback data if no products from database
      if (empty($recommendedProducts)) {
          $recommendedProducts = [
              ['id' => 101, 'name' => 'Organic Tomato Seeds', 'price' => 299, 'image' => 'featured1.jpg'],
              ['id' => 102, 'name' => 'Premium Garden Tool Set', 'price' => 1499, 'discount_price' => 1299, 'image' => 'featured2.jpg'],
              ['id' => 103, 'name' => 'Drip Irrigation Kit', 'price' => 2999, 'image' => 'featured3.jpg'],
              ['id' => 104, 'name' => 'Organic Fertilizer', 'price' => 599, 'discount_price' => 499, 'image' => 'featured4.jpg']
          ];
      }
      
      foreach ($recommendedProducts as $product):
          $hasDiscount = isset($product['discount_price']) && $product['discount_price'] > 0;
          $displayPrice = $hasDiscount ? $product['discount_price'] : $product['price'];
      ?>
        <div class="product-card">
          <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-card-image">
          <div class="product-card-info">
            <h3 class="product-card-name"><?= htmlspecialchars($product['name']) ?></h3>
            <div class="product-card-price">
              <?php if ($hasDiscount): ?>
                <span style="text-decoration: line-through; color: #888; margin-right: 0.5rem;">₹<?= number_format($product['price'], 2) ?></span>
              <?php endif; ?>
              ₹<?= number_format($displayPrice, 2) ?>
            </div>
            <div class="product-card-actions">
              <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary btn-sm">View</a>
              <form action="cart.php" method="post" style="display: inline;">
                <input type="hidden" name="add_to_cart" value="1">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <button type="submit" class="btn btn-sm">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>


<?php include 'footer.php'; ?>


<script>
// Add to cart functionality for recommended products
document.addEventListener('DOMContentLoaded', function() {
  // Get all "Add to Cart" buttons
  const addToCartButtons = document.querySelectorAll('.product-card-actions a:nth-child(2)');
  
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      // Prevent default link behavior
      event.preventDefault();
      
      // Get product ID from href
      const href = this.getAttribute('href');
      const productId = href.split('id=')[1];
      
      // Create form data for AJAX request
      const formData = new FormData();
      formData.append('add_to_cart', '1');
      formData.append('product_id', productId);
      formData.append('quantity', '1');
      
      // Send AJAX request
      fetch('cart.php', {
        method: 'POST',
        body: formData
      })
      .then(response => {
        // Reload the page to show updated cart
        window.location.reload();
      })
      .catch(error => {
        console.error('Error adding product to cart:', error);
        alert('Failed to add product to cart. Please try again.');
      });
    });
  });
  
  // Dynamic subtotal calculation when quantity changes
  const quantityInputs = document.querySelectorAll('.quantity-input');
  quantityInputs.forEach(input => {
    input.addEventListener('change', function() {
      updateCartTotals();
    });
    
    input.addEventListener('input', function() {
      updateCartTotals();
    });
  });
  
  // Function to update cart totals dynamically
  function updateCartTotals() {
    let totalItems = 0;
    let subtotal = 0;
    
    document.querySelectorAll('.cart-item').forEach(item => {
      const price = parseFloat(item.dataset.price);
      const quantityInput = item.querySelector('.quantity-input');
      const quantity = parseInt(quantityInput.value);
      const subtotalCell = item.querySelector('.product-subtotal');
      
      if (!isNaN(quantity) && quantity > 0) {
        const itemSubtotal = price * quantity;
        subtotalCell.textContent = '₹' + itemSubtotal.toFixed(2);
        subtotal += itemSubtotal;
        totalItems += quantity;
      }
    });
    
    // Update the item count and subtotal
    document.getElementById('item-count').textContent = totalItems;
    document.getElementById('cart-subtotal').textContent = '₹' + subtotal.toFixed(2);
    
    // Calculate shipping
    const shipping = subtotal < 500 ? 50 : 0;
    const shippingElement = document.getElementById('shipping-cost');
    shippingElement.textContent = shipping > 0 ? '₹' + shipping.toFixed(2) : 'Free';
    
    // Update total
    const total = subtotal + shipping;
    document.getElementById('cart-total').textContent = '₹' + total.toFixed(2);
    
    // Update shipping message if present
    const shippingMsg = document.querySelector('.cart-summary p');
    if (shippingMsg && shipping > 0) {
      const amountForFreeShipping = 500 - subtotal;
      shippingMsg.innerHTML = `<i class="fas fa-info-circle"></i> Add ₹${amountForFreeShipping.toFixed(2)} more to get free shipping!`;
    } else if (shippingMsg) {
      shippingMsg.style.display = 'none';
    }
  }
});
</script>

</body>
</html>