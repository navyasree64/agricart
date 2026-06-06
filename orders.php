<?php
/**
 * Admin Orders Management Page
 * 
 * This page allows administrators to view and update order statuses
 */
session_start();
require_once('db.php');

// Check admin authentication
checkAdminAuth();

// Process status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    handleStatusUpdate($_POST);
}

// Handle order filtering
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Orders per page
$offset = ($page - 1) * $limit;

// Get orders with filtering
list($orders, $total_orders) = getFilteredOrders($status_filter, $date_from, $date_to, $search, $limit, $offset);
$total_pages = ceil($total_orders / $limit);

// Get order statistics
$order_stats = getOrderStatistics();

// Page title for template
$pageTitle = "Manage Orders";
include('admin_header.php'); // Create this file for admin header
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include('includes/admin_sidebar.php'); // Create this file for admin sidebar navigation ?>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1><?= $pageTitle ?></h1>
        </div>
        
        <?php include('notifications.php'); // Create this file for displaying session messages ?>
        
        <div class="order-stats">
            <div class="stat-card">
                <div class="stat-value"><?= $order_stats['total'] ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-value"><?= $order_stats['pending'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card shipped">
                <div class="stat-value"><?= $order_stats['shipped'] ?></div>
                <div class="stat-label">Shipped</div>
            </div>
            <div class="stat-card delivered">
                <div class="stat-value"><?= $order_stats['delivered'] ?></div>
                <div class="stat-label">Delivered</div>
            </div>
        </div>
        
        <!-- Recent Orders Section -->
        <div class="recent-orders">
            <h2>Recent Orders</h2>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_orders_sql = "SELECT o.*, u.name 
                                            FROM orders o 
                                            JOIN users u ON o.user_id = u.id 
                                            ORDER BY o.created_at DESC 
                                            LIMIT 5";
                        $recent_orders_result = $conn->query($recent_orders_sql);
                        
                        if ($recent_orders_result->num_rows > 0) {
                            while ($order = $recent_orders_result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>#" . $order['id'] . "</td>";
                                echo "<td>" . htmlspecialchars($order['name']) . "</td>";
                                echo "<td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>";
                                echo "<td>₹" . number_format($order['total_amount'], 2) . "</td>";
                                echo "<td><span class='status-badge status-" . strtolower($order['status']) . "'>" . $order['status'] . "</span></td>";
                                echo "<td><a href='order_details.php?id=" . $order['id'] . "' class='btn btn-sm btn-view'>View</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No recent orders found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filters-container">
            <form action="orders.php" method="GET" class="filters-form">
                <div class="filter-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status">
                        <option value="">All Statuses</option>
                        <?php foreach (['Pending', 'Shipped', 'Delivered'] as $status): ?>
                            <option value="<?= $status ?>" <?= $status_filter === $status ? 'selected' : '' ?>>
                                <?= $status ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date_from">From:</label>
                    <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">To:</label>
                    <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                </div>
                
                <div class="filter-group search-group">
                    <input type="text" name="search" placeholder="Search order or customer..." 
                           value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn btn-search">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn btn-filter">Apply Filters</button>
                <a href="orders.php" class="btn btn-reset">Reset</a>
            </form>
        </div>
        
        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="data-table orders-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="order-id">
                                <a href="order_details.php?id=<?= $order['id'] ?>" class="order-link">
                                    #<?= $order['id'] ?>
                                </a>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <span class="customer-name"><?= htmlspecialchars($order['name']) ?></span>
                                    <span class="customer-email"><?= htmlspecialchars($order['email']) ?></span>
                                </div>
                            </td>
                            <td><?= $order['item_count'] ?> items</td>
                            <td class="order-total">₹<?= number_format($order['total_amount'], 2) ?></td>
                            <td>
                                <div class="order-date">
                                    <span class="date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                    <span class="time"><?= date('h:i A', strtotime($order['created_at'])) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                    <?= htmlspecialchars($order['status']) ?>
                                </span>
                            </td>
                            <td>
                                 <div class="action-buttons">
                                    <a href="order_details.php?id=<?= $order['id'] ?>" 
                                       class="btn btn-sm btn-view" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a> 
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-update" 
                                            data-toggle="modal" 
                                            data-target="#updateModal<?= $order['id'] ?>"
                                            title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                                
                                <!-- Status Update Modal -->
                                <div class="modal" id="updateModal<?= $order['id'] ?>">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h3>Update Order #<?= $order['id'] ?> Status</h3>
                                            <button type="button" class="close-modal" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="orders.php" class="status-form">
                                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                
                                                <div class="form-group">
                                                    <label for="status<?= $order['id'] ?>">Status:</label>
                                                    <select name="status" id="status<?= $order['id'] ?>" class="form-control" required>
                                                        <?php foreach (['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'] as $status): ?>
                                                            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>>
                                                                <?= $status ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="notes<?= $order['id'] ?>">Notes (Optional):</label>
                                                    <textarea name="notes" id="notes<?= $order['id'] ?>" class="form-control"></textarea>
                                                </div>
                                                
                                                <div class="form-actions">
                                                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="no-results">No orders found matching your criteria.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?><?= buildQueryParams($status_filter, $date_from, $date_to, $search) ?>" class="page-link prev">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?= $i ?><?= buildQueryParams($status_filter, $date_from, $date_to, $search) ?>" 
                       class="page-link <?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page+1 ?><?= buildQueryParams($status_filter, $date_from, $date_to, $search) ?>" class="page-link next">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include helper functions -->
<?php
function checkAdminAuth() {
    if (!isset($_SESSION['admin_id'])) {
        $_SESSION['error'] = "You must be logged in as an administrator to access this page.";
        header("Location: adminlogin.php");
        exit;
    }
}

function handleStatusUpdate($post_data) {
    global $conn;
    
    $order_id = intval($post_data['order_id']);
    $status = $post_data['status'];
    $notes = isset($post_data['notes']) ? $post_data['notes'] : '';
    
    // Valid status options
    $valid_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $_SESSION['error'] = "Invalid status selected.";
        header("Location: orders.php");
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Update order status
        $update_sql = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        
        // Add status history
        if (!empty($notes)) {
            $history_sql = "INSERT INTO order_status_history (order_id, status, notes, created_by) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($history_sql);
            $admin_id = $_SESSION['admin_id'];
            $stmt->bind_param("issi", $order_id, $status, $notes, $admin_id);
            $stmt->execute();
        }
        
        // If status is shipped, send notification email
        if ($status === 'Shipped') {
            sendShippingNotification($order_id);
        }
        
        $conn->commit();
        $_SESSION['success'] = "Order #$order_id status updated to $status.";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Failed to update order status: " . $e->getMessage();
    }
    
    header("Location: orders.php");
    exit;
}

function getFilteredOrders($status, $date_from, $date_to, $search, $limit, $offset) {
    global $conn;
    
    $where_clauses = ["1=1"]; // Always true condition to start
    $params = [];
    $types = "";
    
    // Add status filter
    if (!empty($status)) {
        $where_clauses[] = "orders.status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    // Add date range filter
    if (!empty($date_from)) {
        $where_clauses[] = "DATE(orders.created_at) >= ?";
        $params[] = $date_from;
        $types .= "s";
    }
    
    if (!empty($date_to)) {
        $where_clauses[] = "DATE(orders.created_at) <= ?";
        $params[] = $date_to;
        $types .= "s";
    }
    
    // Add search filter
    if (!empty($search)) {
        $where_clauses[] = "(orders.id LIKE ? OR users.name LIKE ? OR users.email LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "sss";
    }
    
    // Build the WHERE clause
    $where_clause = implode(" AND ", $where_clauses);
    
    // Count total filtered orders
    $count_sql = "SELECT COUNT(*) as total 
                  FROM orders 
                  JOIN users ON orders.user_id = users.id
                  WHERE $where_clause";
    
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_orders = $count_result->fetch_assoc()['total'];
    
    // Get filtered orders with pagination
    $sql = "SELECT orders.id, orders.user_id, orders.total_amount, orders.status, orders.created_at, 
                   users.name, users.email,
                   (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as item_count
            FROM orders 
            JOIN users ON orders.user_id = users.id
            WHERE $where_clause
            ORDER BY orders.created_at DESC
            LIMIT ? OFFSET ?";
    
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    
    return [$orders, $total_orders];
}

function getOrderStatistics() {
    global $conn;
    
    $stats = [
        'total' => 0,
        'pending' => 0,
        'shipped' => 0,
        'delivered' => 0
    ];
    
    $sql = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
    $result = $conn->query($sql);
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($stats[strtolower($row['status'])])) {
                $stats[strtolower($row['status'])] = $row['count'];
            }
            $stats['total'] += $row['count'];
        }
    }
    
    return $stats;
}

function buildQueryParams($status, $date_from, $date_to, $search) {
    $params = [];
    
    if (!empty($status)) {
        $params[] = "status=" . urlencode($status);
    }
    
    if (!empty($date_from)) {
        $params[] = "date_from=" . urlencode($date_from);
    }
    
    if (!empty($date_to)) {
        $params[] = "date_to=" . urlencode($date_to);
    }
    
    if (!empty($search)) {
        $params[] = "search=" . urlencode($search);
    }
    
    return !empty($params) ? '&' . implode('&', $params) : '';
}

function sendShippingNotification($order_id) {
    global $conn;
    
    // This is a placeholder for email notification functionality
    // In a real application, you'd fetch customer details and send an email
    
    // Example code:
    $order_query = "SELECT orders.*, users.email, users.name 
                    FROM orders 
                    JOIN users ON orders.user_id = users.id 
                    WHERE orders.id = ?";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($order = $result->fetch_assoc()) {
        // Here you would integrate with your email sending library
        // mail($order['email'], "Your order #$order_id has shipped!", "Dear {$order['name']}, your order is on its way!");
        
        // Log the notification
        $log_sql = "INSERT INTO notification_log (user_id, type, message) VALUES (?, 'shipping', ?)";
        $stmt = $conn->prepare($log_sql);
        $message = "Shipping notification sent for order #$order_id";
        $stmt->bind_param("is", $order['user_id'], $message);
        $stmt->execute();
    }
}
?>

<!-- Add your CSS styles here -->
<style>
    /* Root Variables for Color Consistency */
    :root {
        --primary-color: #2e7d32;
        --primary-light: #60ad5e;
        --primary-dark: #005005;
        --secondary-color: #ffa000;
        --text-on-primary: #ffffff;
        --text-primary: #212121;
        --text-secondary: #757575;
        --background-light: #f5f5f5;
        --border-color: #e0e0e0;
        --success-color: #4caf50;
        --warning-color: #ff9800;
        --error-color: #f44336;
        --info-color: #2196f3;
    }

    /* Global Styles */
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--background-light);
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.6;
    }

    /* Admin Layout */
    .admin-container {
        display: flex;
        min-height: 100vh;
    }

    .admin-sidebar {
        width: 250px;
        background: #263238;
        color: white;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
    }

    .admin-content {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
    }

    .admin-header h1 {
        margin: 0;
        color: var(--primary-dark);
        font-weight: 500;
    }

    .admin-actions {
        display: flex;
        gap: 10px;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s, color 0.3s, border-color 0.3s;
        border: none;
        gap: 8px;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
    }

    .btn-secondary {
        background-color: var(--secondary-color);
        color: white;
    }

    .btn-secondary:hover {
        background-color: #f57c00;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 0.85rem;
    }

    .btn-view {
        background-color: var(--info-color);
        color: white;
    }

    .btn-update {
        background-color: var(--warning-color);
        color: white;
    }

    /* Order Statistics Cards */
    .order-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
        border-top: 4px solid var(--primary-color);
    }

    .stat-card.pending {
        border-top-color: var(--warning-color);
    }

    .stat-card.shipped {
        border-top-color: var(--info-color);
    }

    .stat-card.delivered {
        border-top-color: var(--success-color);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
        color: var(--text-primary);
    }

    .stat-label {
        font-size: 1rem;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Filters */
    .filters-container {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: flex-end;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        min-width: 150px;
    }

    .filter-group label {
        margin-bottom: 5px;
        font-weight: 500;
    }

    .filter-group select,
    .filter-group input {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        background: white;
    }

    .search-group {
        position: relative;
        flex-grow: 1;
    }

    .search-group input {
        width: 100%;
        padding-right: 40px;
    }

    .btn-search {
        position: absolute;
        right: 0;
        top: 0;
        height: 100%;
        background: transparent;
        border: none;
        padding: 0 12px;
        color: var(--text-secondary);
    }

    .btn-filter {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-reset {
        background-color: #757575;
        color: white;
    }

    /* Orders Table Styles */
    .orders-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
        margin-top: 20px;
    }

    .orders-table thead th {
        background-color: var(--primary-color);
        color: var(--text-on-primary);
        padding: 15px;
        text-align: left;
        font-weight: 500;
    }

    .orders-table tbody tr {
        background-color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .orders-table tbody td {
        padding: 20px 15px;
        vertical-align: middle;
    }

    .orders-table tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }

    /* Customer Info Styles */
    .customer-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .customer-name {
        font-weight: 500;
        color: var(--text-primary);
    }

    .customer-email {
        font-size: 0.9em;
        color: var(--text-secondary);
    }

    /* Order Date Styles */
    .order-date {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .order-date .date {
        font-weight: 500;
    }

    .order-date .time {
        font-size: 0.9em;
        color: var(--text-secondary);
    }

    /* Status Badge Styles */
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: 500;
        text-align: center;
        min-width: 100px;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-processing {
        background-color: #cce5ff;
        color: #004085;
    }

    .status-shipped {
        background-color: #d4edda;
        color: #155724;
    }

    .status-delivered {
        background-color: #d1ecf1;
        color: #0c5460;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }

    /* Action Buttons Styles */
    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .btn-sm {
        padding: 8px 12px;
        font-size: 0.9em;
    }

    .btn-view {
        background-color: var(--info-color);
        color: white;
    }

    .btn-update {
        background-color: var(--warning-color);
        color: white;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: white;
        padding: 30px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }

    .modal-header h3 {
        margin: 0;
        color: var(--text-primary);
    }

    .close-modal {
        background: none;
        border: none;
        font-size: 1.5em;
        cursor: pointer;
        color: var(--text-secondary);
    }

    .modal-body {
        margin-bottom: 20px;
    }

    /* Form Styles */
    .status-form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-weight: 500;
        color: var(--text-primary);
    }

    .form-control {
        padding: 10px;
        border: 1px solid var(--border-color);
        border-radius: 4px;
        font-size: 1em;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 5px;
        margin-top: 30px;
    }

    .page-link {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 4px;
        background: white;
        color: var(--text-primary);
        text-decoration: none;
        border: 1px solid var(--border-color);
        transition: all 0.3s;
    }

    .page-link.active {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    .page-link:hover:not(.active) {
        background: var(--border-color);
    }

    .page-link.prev,
    .page-link.next {
        font-weight: 500;
        background: #f8f9fa;
        color: var(--primary-color);
        border-color: var(--primary-color);
        gap: 6px;
    }

    .page-link.prev:hover,
    .page-link.next:hover {
        background: var(--primary-color) !important;
        color: white !important;
    }

    /* Notifications */
    .message {
        padding: 12px 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        border-left: 4px solid;
        display: flex;
        align-items: center;
    }

    .message:before {
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        margin-right: 10px;
        font-size: 1.2rem;
    }

    .success {
        background-color: #e8f5e9;
        color: #2e7d32;
        border-left-color: #2e7d32;
    }

    .success:before {
        content: "\f00c";
    }

    .error {
        background-color: #ffebee;
        color: #c62828;
        border-left-color: #c62828;
    }

    .error:before {
        content: "\f00d";
    }

    /* No Results */
    .no-results {
        text-align: center;
        padding: 30px 0;
        color: var(--text-secondary);
    }

    /* Small Screen Adjustments */
    @media (max-width: 1024px) {
        .admin-sidebar {
            width: 60px;
        }
        
        .admin-content {
            margin-left: 60px;
        }
        
        .admin-sidebar span {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .order-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filters-form {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        
        .filter-group {
            min-width: auto;
        }
        
        .orders-table {
            display: block;
            overflow-x: auto;
        }
        
        .orders-table tbody tr {
            display: block;
            margin-bottom: 20px;
        }
        
        .orders-table tbody td {
            display: block;
            text-align: right;
            padding: 10px 15px;
            position: relative;
        }
        
        .orders-table tbody td::before {
            content: attr(data-label);
            float: left;
            font-weight: bold;
            color: var(--text-primary);
        }
        
        .action-buttons {
            justify-content: flex-end;
        }
    }

    @media (max-width: 480px) {
        .order-stats {
            grid-template-columns: 1fr;
        }
        
        .admin-content {
            padding: 10px;
        }
    }
</style>

<!-- JavaScript for order management page -->
<script>
    // Show modal when update button is clicked
    document.addEventListener('DOMContentLoaded', function() {
        // Open modals
        var updateButtons = document.querySelectorAll('[data-toggle="modal"]');
        updateButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var target = this.getAttribute('data-target').substring(1);
                document.getElementById(target).style.display = 'flex';
            });
        });
        
        // Close modals
        var closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var modal = this.closest('.modal');
                modal.style.display = 'none';
            });
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });
    });
</script>

<?php include('admin_footer.php'); ?>
