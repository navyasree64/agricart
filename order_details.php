<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    
    // Redirect to prevent form resubmission
    header("Location: order_details.php?id=" . $order_id);
    exit;
}

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, u.name as customer_name, u.email as customer_email,
           a.full_name as shipping_name, a.phone as shipping_phone,
           a.address_line1 as shipping_address, a.address_line2, a.city, a.state, a.postal_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN user_addresses a ON o.address_id = a.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Get order items
$stmt = $conn->prepare("
    SELECT oi.*, p.name as product_name, p.image, p.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Include admin header
include('admin_header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include('includes/admin_sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Order Details #<?php echo $order_id; ?></h1>
            <a href="orders.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
        
        <?php if ($order): ?>
            <div class="order-details-container">
                <div class="order-info">
                    <div class="info-card">
                        <h3>Order Information</h3>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y H:i', strtotime($order['created_at'])); ?></p>
                        <p><strong>Payment Method:</strong> <span class="status-badge" style="background: #e3f2fd; color: #0d47a1; padding: 2px 6px; border-radius: 4px; font-size: 13px; font-weight: bold;"><?php echo htmlspecialchars($order['payment_method'] ?? 'COD'); ?></span></p>
                        <p><strong>Order Status:</strong> 
                            <form action="" method="post" class="status-form">
                                <select name="status" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                    <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </p>
                        <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3>Customer Information</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3>Shipping Address</h3>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($order['shipping_name'] ?? $order['customer_name']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?>
                           <?php if (!empty($order['address_line2'])) echo ", " . htmlspecialchars($order['address_line2']); ?></p>
                        <p><strong>City/State:</strong> 
                            <?php 
                            $address_parts = [];
                            if (!empty($order['city'])) $address_parts[] = $order['city'];
                            if (!empty($order['state'])) $address_parts[] = $order['state'];
                            if (!empty($order['postal_code'])) $address_parts[] = $order['postal_code'];
                            echo htmlspecialchars(implode(', ', $address_parts));
                            ?>
                        </p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['shipping_phone'] ?? ''); ?></p>
                    </div>
                </div>
                
                <div class="order-items">
                    <h3>Order Items</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                 class="product-image">
                                            <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>₹<?php echo number_format($item['price'], 2); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                Order not found.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.order-details-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-info {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.info-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.info-card h3 {
    margin-bottom: 15px;
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

.status-form {
    display: inline-block;
    margin-left: 10px;
}

.status-form select {
    padding: 5px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.text-right {
    text-align: right;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php include('admin_footer.php'); ?> 