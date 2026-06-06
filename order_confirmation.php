<?php
session_start();
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Check if order was successful
if (!isset($_SESSION['order_success']) || !$_SESSION['order_success']) {
    header('Location: index.php');
    exit;
}

// Get order details
$orderId = $_SESSION['order_id'];
$order = getOrderDetails($orderId);
$orderItems = getOrderItems($orderId);

// Clear the success flag
unset($_SESSION['order_success']);
unset($_SESSION['order_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Agri-Ecommerce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #45a049;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .order-confirmation {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }

        .success-message {
            text-align: center;
            padding: 30px 0;
            border-bottom: 2px solid var(--light-gray);
            margin-bottom: 30px;
        }

        .success-message i {
            color: var(--primary-color);
            font-size: 60px;
            margin-bottom: 20px;
        }

        .success-message h2 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .order-details, .order-items, .shipping-address {
            margin-bottom: 30px;
            padding: 20px;
            background: var(--light-gray);
            border-radius: 8px;
        }

        .order-details h3, .order-items h3, .shipping-address h3 {
            color: var(--primary-color);
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-color);
        }

        .order-details p {
            margin: 10px 0;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }

        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .items-list {
            display: grid;
            gap: 15px;
        }

        .order-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .order-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 15px;
        }

        .item-details {
            flex: 1;
        }

        .item-details h4 {
            margin-bottom: 5px;
            color: var(--text-color);
        }

        .item-details p {
            color: #666;
            margin: 5px 0;
        }

        .shipping-address p {
            margin: 10px 0;
            padding-left: 20px;
            position: relative;
        }

        .shipping-address p:before {
            content: "•";
            position: absolute;
            left: 0;
            color: var(--primary-color);
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-secondary:hover {
            background-color: var(--light-gray);
        }

        @media (max-width: 768px) {
            .order-item {
                flex-direction: column;
                text-align: center;
            }

            .order-item img {
                margin-right: 0;
                margin-bottom: 15px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="order-confirmation">
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <h2>Thank you for your order!</h2>
                <p>Your order has been placed successfully.</p>
            </div>

            <div class="order-details">
                <h3>Order Details</h3>
                <p><strong>Order Number:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                <p><strong>Status:</strong> <span class="status pending"><?php echo ucfirst($order['status']); ?></span></p>
                <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
            </div>

            <div class="order-items">
                <h3>Order Items</h3>
                <div class="items-list">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <img src="<?php echo $baseUrl . 'assets/images/' . $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <p>Quantity: <?php echo $item['quantity']; ?></p>
                                <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="shipping-address">
                <h3>Shipping Address</h3>
                <?php if (!empty($order['address_line1'])): ?>
                    <p><?php echo htmlspecialchars($order['address_line1']); ?></p>
                <?php endif; ?>
                <?php if (!empty($order['address_line2'])): ?>
                    <p><?php echo htmlspecialchars($order['address_line2']); ?></p>
                <?php endif; ?>
                <?php if (!empty($order['city']) || !empty($order['state']) || !empty($order['postal_code'])): ?>
                    <p>
                        <?php 
                        $addressParts = [];
                        if (!empty($order['city'])) $addressParts[] = htmlspecialchars($order['city']);
                        if (!empty($order['state'])) $addressParts[] = htmlspecialchars($order['state']);
                        if (!empty($order['postal_code'])) $addressParts[] = htmlspecialchars($order['postal_code']);
                        echo implode(', ', $addressParts);
                        ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($order['country'])): ?>
                    <p><?php echo htmlspecialchars($order['country']); ?></p>
                <?php endif; ?>
                <?php if (empty($order['address_line1']) && empty($order['city']) && empty($order['state']) && empty($order['postal_code']) && empty($order['country'])): ?>
                    <p>No shipping address provided.</p>
                <?php endif; ?>
            </div>

            <div class="actions">
                <a href="home.php" class="btn btn-primary">Continue Shopping</a>
                <a href="my_orders.php" class="btn btn-secondary">View All Orders</a>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html> 