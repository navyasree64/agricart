<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Handle User deletion
if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Begin transaction for safe cascade deletion
    $conn->begin_transaction();
    try {
        // Delete reviews
        $stmt = $conn->prepare("DELETE FROM product_reviews WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete order status history belonging to user's orders
        $stmt = $conn->prepare("DELETE h FROM order_status_history h JOIN orders o ON h.order_id = o.id WHERE o.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete order items
        $stmt = $conn->prepare("DELETE oi FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE o.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete notifications
        $stmt = $conn->prepare("DELETE FROM notification_log WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete orders
        $stmt = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete addresses
        $stmt = $conn->prepare("DELETE FROM user_addresses WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $conn->commit();
        $_SESSION['success'] = "User and all associated data deleted successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
    header("Location: users.php");
    exit;
}

// Fetch all users with order counts and spend summary
$sql = "SELECT u.id, u.name, u.email, u.phone, u.created_at,
               (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as total_orders,
               (SELECT IFNULL(SUM(total_amount), 0) FROM orders WHERE user_id = u.id AND status != 'cancelled') as total_spent
        FROM users u
        ORDER BY u.id DESC";
$result = $conn->query($sql);

// Include admin header
include('admin_header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <?php include('includes/admin_sidebar.php'); ?>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Manage Users</h1>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="notification success">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="notification error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered At</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></td>
                            <td><?= date('Y-m-d H:i', strtotime($row['created_at'])) ?></td>
                            <td><span class="badge badge-info"><?= $row['total_orders'] ?> orders</span></td>
                            <td><strong>₹<?= number_format($row['total_spent'], 2) ?></strong></td>
                            <td>
                                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this user? This will permanently delete their account, addresses, reviews, cart items, and order history.')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="no-results">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.data-table-container {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.data-table th,
.data-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.data-table th {
    background: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
}

.data-table tr:hover {
    background: #f8f9fa;
}

.notification {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 4px;
    font-weight: 500;
}

.notification.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.notification.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Badges & Buttons */
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
    display: inline-block;
}

.badge-info {
    background: #e8f4fd;
    color: #0b5ed7;
    border: 1px solid #b6d4fe;
}

.btn-delete {
    background: #dc3545;
    color: white !important;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: background-color 0.2s;
}

.btn-delete:hover {
    background: #bb2d3b;
}

.no-results {
    text-align: center;
    padding: 30px;
    color: #666;
    font-style: italic;
}
</style>

<?php include('admin_footer.php'); ?>
