<?php
session_start();
require_once 'functions.php';
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
  exit;
}

// Get user's saved addresses
$addresses = getUserAddresses($_SESSION['user_id']);

// Get cart items
$cartItems = getCartItems();
if (empty($cartItems)) {
    header('Location: cart.php');
  exit;
}

// Calculate totals
$totals = getCartTotals();

// Set disabled attribute based on validation
$disabled = empty($addresses) ? 'disabled' : '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Invalid request";
        header('Location: checkout.php');
      exit;
    }

    // Validate selected address
    if (!isset($_POST['address_id']) || empty($_POST['address_id'])) {
        $_SESSION['error'] = "Please select a delivery address";
        header('Location: checkout.php');
        exit;
    }

    // Get selected address
    $selectedAddress = null;
    foreach ($addresses as $address) {
        if ($address['id'] == $_POST['address_id']) {
            $selectedAddress = $address;
            break;
        }
    }

    if (!$selectedAddress) {
        $_SESSION['error'] = "Invalid address selected";
        header('Location: checkout.php');
        exit;
    }

    // Create order
    $orderId = createOrder($_SESSION['user_id'], $selectedAddress['id'], $totals['total']);
    if ($orderId) {
        // Add items to order
        foreach ($cartItems as $item) {
            addOrderItem($orderId, $item['product']['id'], $item['quantity'], $item['product']['price']);
        }
        
        // Clear cart
        clearCart();
        
        // Set success flag
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $orderId;
        
        // Redirect to order confirmation
        header("Location: order_confirmation.php");
        exit;
    } else {
        $_SESSION['error'] = "Failed to create order";
        header('Location: checkout.php');
        exit;
    }
}

// Generate CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

include 'header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2>Checkout</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="checkout.php">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="checkout-section">
                    <h3>Delivery Address</h3>
                    
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-warning">
                            No saved addresses found. Please <a href="profile.php#addresses">add an address</a> to continue.
                        </div>
                    <?php else: ?>
                        <div class="addresses-grid">
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-card">
                                    <div class="address-header">
                                        <input type="radio" name="address_id" value="<?= $address['id'] ?>" 
                                               id="address_<?= $address['id'] ?>" required>
                                        <label for="address_<?= $address['id'] ?>">
                                            <h4><?= htmlspecialchars($address['full_name']) ?></h4>
                                        </label>
                                        <?php if ($address['is_default']): ?>
                                            <span class="default-badge">Default</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="address-details">
                                        <p><?= htmlspecialchars($address['address_line1']) ?></p>
                                        <?php if (!empty($address['address_line2'])): ?>
                                            <p><?= htmlspecialchars($address['address_line2']) ?></p>
                                        <?php endif; ?>
                                        <p><?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?> - <?= htmlspecialchars($address['postal_code']) ?></p>
                                        <p>Phone: <?= htmlspecialchars($address['phone']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="checkout-section">
                    <h3>Order Summary</h3>
                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₹<?= number_format($totals['subtotal'], 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>₹<?= number_format($totals['shipping'], 2) ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span>₹<?= number_format($totals['total'], 2) ?></span>
                        </div>
                    </div>
                </div>

                <div class="checkout-section">
                    <h3>Payment Method</h3>
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="COD" required>
                            <span>Cash on Delivery</span>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="UPI" required>
                            <span>UPI / Google Pay / PhonePe</span>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="Card" required>
                            <span>Credit / Debit Card</span>
                        </label>
                    </div>
                </div>
                
                <div class="checkout-actions">
                    <a href="cart.php" class="btn btn-outline">Back to Cart</a>
                    <button type="submit" class="place-order-btn" <?php echo $disabled; ?>>
                        <i class="fas fa-shopping-cart"></i> Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.checkout-section {
    background: #fff;
      padding: 2rem;
      border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
    }
    
.checkout-section h3 {
      margin-bottom: 1.5rem;
      color: #333;
    }
    
.addresses-grid {
      display: grid;
    gap: 1rem;
}

.address-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #eee;
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-card:hover {
    border-color: #4a8f29;
}

.address-header {
    display: flex;
    align-items: center;
    gap: 1rem;
      margin-bottom: 1rem;
}

.address-header input[type="radio"] {
    margin: 0;
}

.address-header label {
    flex: 1;
    margin: 0;
    cursor: pointer;
}

.address-details {
    margin-left: 2rem;
}

.address-details p {
    margin: 0.25rem 0;
    color: #555;
    }
    
    .order-summary {
    background: #f8f9fa;
      padding: 1.5rem;
      border-radius: 8px;
    }
    
    .summary-row {
      display: flex;
      justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #eee;
    }
    
    .summary-row:last-child {
      border-bottom: none;
    }
    
    .summary-row.total {
      font-weight: bold;
    font-size: 1.1rem;
    margin-top: 1rem;
}

.checkout-actions {
      display: flex;
    justify-content: space-between;
    margin-top: 2rem;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    }
    
    .payment-methods {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    .payment-method {
      display: flex;
      align-items: center;
      padding: 0.75rem;
    border: 1px solid #eee;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s;
    }
    
    .payment-method:hover {
    background-color: rgba(74, 143, 41, 0.05);
    border-color: #4a8f29;
    }
    
    .payment-method input[type="radio"] {
      margin-right: 0.75rem;
    }
    
    .payment-method.selected {
    background-color: rgba(74, 143, 41, 0.1);
    border-color: #4a8f29;
}

.place-order-btn {
    background-color: #4a8f29;
      color: white;
    padding: 12px 24px;
      border: none;
      border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
      cursor: pointer;
    transition: all 0.3s ease;
    display: inline-block;
      text-align: center;
      width: 100%;
    max-width: 300px;
    margin: 20px auto;
}

.place-order-btn:hover {
    background-color: #3a7a1f;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.place-order-btn:disabled {
    background-color: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.place-order-btn i {
    margin-right: 8px;
    }
  </style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Highlight selected payment method
  const paymentMethods = document.querySelectorAll('.payment-method');
  paymentMethods.forEach(method => {
    const radio = method.querySelector('input[type="radio"]');
    radio.addEventListener('change', function() {
      // Remove selected class from all methods
      paymentMethods.forEach(m => m.classList.remove('selected'));
      // Add selected class to the chosen method
      if (this.checked) {
        this.closest('.payment-method').classList.add('selected');
      }
    });
  });
});
</script>

<?php include 'footer.php'; ?>