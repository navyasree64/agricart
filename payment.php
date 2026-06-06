<?php
session_start();
require_once 'functions.php';
require_once 'db.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Redirect back if checkout session details are missing
if (!isset($_SESSION['checkout_address_id']) || !isset($_SESSION['checkout_payment_method'])) {
    header('Location: checkout.php');
    exit;
}

$addressId = $_SESSION['checkout_address_id'];
$paymentMethod = $_SESSION['checkout_payment_method'];

// Get cart items and totals
$cartItems = getCartItems();
if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

$totals = getCartTotals();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process simulated payment success
    $orderId = createOrder($_SESSION['user_id'], $addressId, $totals['total'], $paymentMethod);
    if ($orderId) {
        // Add items to order
        foreach ($cartItems as $item) {
            addOrderItem($orderId, $item['product']['id'], $item['quantity'], $item['product']['price']);
        }
        
        // Clear cart
        clearCart();
        
        // Clear checkout session variables
        unset($_SESSION['checkout_address_id']);
        unset($_SESSION['checkout_payment_method']);
        
        // Set success flag and order ID
        $_SESSION['order_success'] = true;
        $_SESSION['order_id'] = $orderId;
        
        header("Location: order_confirmation.php");
        exit;
    } else {
        $error = "Failed to place order. Please try again.";
    }
}

$pageTitle = "Secure Payment Gateway";
include 'header.php';
?>

<style>
.payment-wrapper {
    max-width: 800px;
    margin: 3rem auto;
    padding: 2.5rem;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.payment-header {
    text-align: center;
    margin-bottom: 2.5rem;
}

.payment-header h2 {
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.payment-amount {
    font-size: 2rem;
    color: #4a8f29;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

/* Credit Card Mockup CSS */
.card-container {
    perspective: 1000px;
    width: 380px;
    height: 220px;
    margin: 0 auto 2rem;
}

.credit-card {
    width: 100%;
    height: 100%;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 0.6s ease;
    border-radius: 15px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

.credit-card.flipped {
    transform: rotateY(180deg);
}

.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    color: #fff;
    background: linear-gradient(135deg, #1d3557, #457b9d);
}

.card-back {
    transform: rotateY(180deg);
    background: linear-gradient(135deg, #2b2d42, #8d99ae);
    padding: 1.5rem 0;
}

.card-logo {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-style: italic;
    font-weight: bold;
    font-size: 1.2rem;
}

.card-chip {
    width: 50px;
    height: 35px;
    background: linear-gradient(135deg, #ffd700, #ffa500);
    border-radius: 5px;
    margin-top: 10px;
}

.card-number-display {
    font-size: 1.4rem;
    letter-spacing: 3px;
    margin: 1.5rem 0 0.5rem;
    font-family: 'Courier New', Courier, monospace;
}

.card-info-row {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    text-transform: uppercase;
}

.card-black-stripe {
    width: 100%;
    height: 45px;
    background: #000;
    margin-top: 10px;
}

.card-cvv-stripe {
    width: 80%;
    height: 35px;
    background: #fff;
    margin: 15px auto 0;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding-right: 10px;
    color: #333;
    font-weight: bold;
    font-family: 'Courier New', Courier, monospace;
}

/* Form Styling */
.payment-form {
    max-width: 500px;
    margin: 0 auto;
}

.form-row {
    display: flex;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.25rem;
    flex: 1;
}

.form-group label {
    display: block;
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #4a8f29;
    box-shadow: 0 0 0 3px rgba(74, 143, 41, 0.1);
    outline: none;
}

.pay-btn {
    width: 100%;
    padding: 1rem;
    background-color: #4a8f29;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 1.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.pay-btn:hover {
    background-color: #3a7020;
}

/* UPI CSS */
.upi-container {
    text-align: center;
    padding: 1.5rem;
}

.qr-code-wrapper {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    display: inline-block;
    border: 2px dashed #ddd;
    margin-bottom: 1.5rem;
}

.qr-code-placeholder {
    width: 200px;
    height: 200px;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.timer {
    font-size: 1.2rem;
    font-weight: 600;
    color: #e74c3c;
    margin-bottom: 1.5rem;
}

.upi-apps {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.upi-app {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 0.85rem;
    color: #666;
}

.upi-app i {
    font-size: 2.2rem;
    color: #555;
    margin-bottom: 0.5rem;
}

/* Loading Overlay */
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #4a8f29;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1.5rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
    <h3>Processing Secure Payment...</h3>
    <p>Please do not refresh the page or click back.</p>
</div>

<div class="payment-wrapper">
    <div class="payment-header">
        <h2>Complete Your Payment</h2>
        <p>Order Summary Total Amount</p>
        <div class="payment-amount">₹<?= number_format($totals['total'], 2) ?></div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background: #fff5f5; border: 1px solid #ffd6d6; color: #dc3545; border-radius: 6px;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($paymentMethod === 'Card'): ?>
        <!-- CARD PAYMENT OPTION -->
        <div class="card-container">
            <div class="credit-card" id="creditCard">
                <div class="card-front">
                    <div class="card-logo">
                        <span>Secure Card</span>
                        <i class="fab fa-cc-visa"></i>
                    </div>
                    <div class="card-chip"></div>
                    <div class="card-number-display" id="cardNumberDisplay">•••• •••• •••• ••••</div>
                    <div class="card-info-row">
                        <div>
                            <span style="font-size: 0.6rem; display: block; color: rgba(255,255,255,0.7);">Card Holder</span>
                            <strong id="cardHolderDisplay">YOUR NAME</strong>
                        </div>
                        <div>
                            <span style="font-size: 0.6rem; display: block; color: rgba(255,255,255,0.7);">Expires</span>
                            <strong id="cardExpiryDisplay">MM/YY</strong>
                        </div>
                    </div>
                </div>
                <div class="card-back">
                    <div class="card-black-stripe"></div>
                    <div class="card-cvv-stripe" id="cardCvvDisplay">***</div>
                    <div style="padding: 1.5rem; font-size: 0.6rem; color: rgba(255,255,255,0.7); text-align: center;">
                        This is a secure card transaction mockup for testing.
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="payment.php" class="payment-form" id="cardForm">
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" class="form-control" placeholder="4111 2222 3333 4444" maxlength="19" required>
            </div>
            
            <div class="form-group">
                <label for="cardName">Cardholder Name</label>
                <input type="text" id="cardName" class="form-control" placeholder="John Doe" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="cardExpiry">Expiration Date</label>
                    <input type="text" id="cardExpiry" class="form-control" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="form-group">
                    <label for="cardCvv">CVV / CVC</label>
                    <input type="password" id="cardCvv" class="form-control" placeholder="123" maxlength="3" required>
                </div>
            </div>

            <button type="submit" class="pay-btn">
                <i class="fas fa-lock"></i> Pay ₹<?= number_format($totals['total'], 2) ?>
            </button>
        </form>

    <?php elseif ($paymentMethod === 'UPI'): ?>
        <!-- UPI PAYMENT OPTION -->
        <div class="upi-container">
            <div class="qr-code-wrapper">
                <div class="qr-code-placeholder">
                    <!-- Real-looking mock QR code -->
                    <svg width="150" height="150" viewBox="0 0 100 100" style="shape-rendering: crispedges;">
                        <!-- QR Code Mock Elements -->
                        <path d="M0,0h30v30h-30z M10,10h10v10h-10z M70,0h30v30h-30z M80,10h10v10h-10z M0,70h30v30h-30z M10,80h10v10h-10z" fill="#333"/>
                        <path d="M40,0h10v10h-10z M55,5h5v5h-5z M45,20h10v10h-10z M60,40h10v10h-10z M40,50h5v15h-5z M50,60h15v5h-15z M35,80h5v15h-5z M80,80h20v20h-20z" fill="#333"/>
                        <path d="M45,45h10v10h-10z M55,75h10v10h-10z M75,55h15v10h-15z M65,15h10v20h-10z M85,45h10v10h-10z" fill="#333"/>
                    </svg>
                </div>
                <p style="margin-top: 10px; font-weight: bold; color: #555;">Scan QR Code to Pay</p>
            </div>

            <div class="timer">QR Code expires in: <span id="timeLeft">03:00</span></div>

            <div class="upi-apps">
                <div class="upi-app">
                    <i class="fab fa-google-pay" style="color: #4285F4;"></i>
                    <span>Google Pay</span>
                </div>
                <div class="upi-app">
                    <i class="fas fa-wallet" style="color: #5f259f;"></i>
                    <span>PhonePe</span>
                </div>
                <div class="upi-app">
                    <i class="fas fa-qrcode" style="color: #00baf2;"></i>
                    <span>Paytm / Any UPI</span>
                </div>
            </div>

            <form method="POST" action="payment.php" id="upiForm">
                <button type="submit" class="pay-btn">
                    <i class="fas fa-check-circle"></i> Confirm Simulated Payment
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Handle Form Submissions with Simulated Delay
    const handleFormSubmit = function(e) {
        e.preventDefault();
        loadingOverlay.style.display = 'flex';
        
        // Simulate loading check and submit to PHP
        setTimeout(() => {
            e.target.submit();
        }, 2500);
    };

    // Card functionality details
    if (document.getElementById('cardForm')) {
        const cardForm = document.getElementById('cardForm');
        cardForm.addEventListener('submit', handleFormSubmit);

        const creditCard = document.getElementById('creditCard');
        const cardNumber = document.getElementById('cardNumber');
        const cardName = document.getElementById('cardName');
        const cardExpiry = document.getElementById('cardExpiry');
        const cardCvv = document.getElementById('cardCvv');

        const cardNumberDisplay = document.getElementById('cardNumberDisplay');
        const cardHolderDisplay = document.getElementById('cardHolderDisplay');
        const cardExpiryDisplay = document.getElementById('cardExpiryDisplay');
        const cardCvvDisplay = document.getElementById('cardCvvDisplay');

        // Spacing/formatting for credit card input
        cardNumber.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formattedValue += ' ';
                formattedValue += value[i];
            }
            e.target.value = formattedValue;
            cardNumberDisplay.textContent = formattedValue || '•••• •••• •••• ••••';
        });

        cardName.addEventListener('input', function(e) {
            cardHolderDisplay.textContent = e.target.value.toUpperCase() || 'YOUR NAME';
        });

        // Slash formatting for expiry date input
        cardExpiry.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                e.target.value = value.substring(0,2) + '/' + value.substring(2,4);
            } else {
                e.target.value = value;
            }
            cardExpiryDisplay.textContent = e.target.value || 'MM/YY';
        });

        // Flip card when focusing CVV
        cardCvv.addEventListener('focus', function() {
            creditCard.classList.add('flipped');
        });
        cardCvv.addEventListener('blur', function() {
            creditCard.classList.remove('flipped');
        });
        cardCvv.addEventListener('input', function(e) {
            cardCvvDisplay.textContent = e.target.value || '***';
        });
    }

    // UPI Timer functionality
    if (document.getElementById('upiForm')) {
        const upiForm = document.getElementById('upiForm');
        upiForm.addEventListener('submit', handleFormSubmit);

        let timeLimit = 180; // 3 minutes in seconds
        const timerSpan = document.getElementById('timeLeft');

        const countdown = setInterval(function() {
            let minutes = Math.floor(timeLimit / 60);
            let seconds = timeLimit % 60;
            
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerSpan.textContent = `0${minutes}:${seconds}`;

            if (timeLimit <= 0) {
                clearInterval(countdown);
                alert("Payment session expired. Redirecting back to checkout.");
                window.location.href = "checkout.php";
            }
            timeLimit--;
        }, 1000);
    }
});
</script>

<?php include 'footer.php'; ?>
