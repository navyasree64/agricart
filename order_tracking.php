<?php
session_start();
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, u.name as customer_name, u.email as customer_email,
           ua.full_name as shipping_name, ua.phone as shipping_phone, 
           ua.address_line1, ua.address_line2, ua.city, ua.state, ua.postal_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN user_addresses ua ON o.address_id = ua.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
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

// Include header
include('header.php');
?>

<div class="container">
    <div class="order-tracking-container">
        <div class="order-header">
            <h1>Order Tracking #<?php echo $order_id; ?></h1>
            <a href="profile.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
        
        <?php if ($order): ?>
            <div class="order-info">
                <div class="info-card">
                    <h3>Order Information</h3>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y H:i', strtotime($order['created_at'])); ?></p>
                    <p><strong>Total Amount:</strong> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                </div>
                
                <div class="info-card">
                    <h3>Shipping Address</h3>
                    <?php if (!empty($order['address_line1'])): ?>
                        <p><strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong></p>
                        <p><?php echo htmlspecialchars($order['address_line1']); ?></p>
                        <?php if (!empty($order['address_line2'])): ?>
                            <p><?php echo htmlspecialchars($order['address_line2']); ?></p>
                        <?php endif; ?>
                        <p><?php echo htmlspecialchars($order['city'] . ', ' . $order['state'] . ' - ' . $order['postal_code']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($order['shipping_phone']); ?></p>
                    <?php else: ?>
                        <p>No shipping address provided</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Order Status Timeline -->
            <div class="timeline-container">
                <h3>Order Status</h3>
                <div class="timeline">
                    <div class="timeline-item <?php echo $order['status'] === 'pending' ? 'active' : 'completed'; ?>">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h4>Order Placed</h4>
                            <p>Your order has been received</p>
                            <span class="timeline-date"><?php echo date('F j, Y H:i', strtotime($order['created_at'])); ?></span>
                        </div>
                    </div>
                    
                    <div class="timeline-item <?php echo $order['status'] === 'processing' ? 'active' : ($order['status'] === 'pending' ? 'pending' : 'completed'); ?>">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h4>Processing</h4>
                            <p>Your order is being prepared</p>
                            <?php if ($order['status'] === 'processing'): ?>
                                <span class="timeline-date"><?php echo date('F j, Y H:i'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="timeline-item <?php echo $order['status'] === 'shipped' ? 'active' : ($order['status'] === 'delivered' ? 'completed' : 'pending'); ?>">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h4>Shipped</h4>
                            <p>Your order is on its way</p>
                            <?php if ($order['status'] === 'shipped'): ?>
                                <span class="timeline-date"><?php echo date('F j, Y H:i'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="timeline-item <?php echo $order['status'] === 'delivered' ? 'active' : 'pending'; ?>">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <h4>Delivered</h4>
                            <p>Your order has been delivered</p>
                            <?php if ($order['status'] === 'delivered'): ?>
                                <span class="timeline-date"><?php echo date('F j, Y H:i'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
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
                                        <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" 
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
        <?php else: ?>
            <div class="alert alert-danger">
                Order not found.
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.order-tracking-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
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

.timeline-container {
    margin: 30px 0;
}

.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 30px;
}

.timeline-dot {
    position: absolute;
    left: 14px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #e0e0e0;
    z-index: 1;
}

.timeline-item.active .timeline-dot {
    background: #3498db;
    box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.2);
}

.timeline-item.completed .timeline-dot {
    background: #2ecc71;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.timeline-content h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
}

.timeline-content p {
    margin: 0;
    color: #666;
}

.timeline-date {
    display: block;
    font-size: 0.8rem;
    color: #888;
    margin-top: 5px;
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

<?php include('footer.php'); ?> 