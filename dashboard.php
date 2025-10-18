<?php
session_start();
include('db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit;
}

// Include admin header
include('admin_header.php');
?>

<div class="admin-container">
    <div class="admin-sidebar">
        <div class="logo">
            <h2>Admin Panel</h2>
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="productadmin.php"><i class="fas fa-box"></i> Manage Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="categories.php"><i class="fas fa-tags"></i> Manage Categories</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    
    <div class="admin-content">
        <div class="admin-header">
            <h1>Dashboard Overview</h1>
            <button class="mobile-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Products</h3>
                    <p><?php
                        $sql = "SELECT COUNT(*) as total FROM products";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                    ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Orders</h3>
                    <p><?php
                        $sql = "SELECT COUNT(*) as total FROM orders";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                    ?></p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Users</h3>
                    <p><?php
                        $sql = "SELECT COUNT(*) as total FROM users";
                        $result = $conn->query($sql);
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                    ?></p>
                </div>
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
    </div>
</div>

<style>
/* Dashboard Styles */
.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.stat-icon i {
    color: white;
    font-size: 24px;
}

.stat-info h3 {
    font-size: 16px;
    color: #666;
    margin-bottom: 5px;
}

.stat-info p {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
}

.recent-orders {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.recent-orders h2 {
    margin-bottom: 20px;
    color: #2c3e50;
}

.status {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status.pending {
    background: #fff3cd;
    color: #856404;
}

.status.completed {
    background: #d4edda;
    color: #155724;
}

.status.cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-view {
    background: #3498db;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
}

.btn-view:hover {
    background: #2980b9;
}
</style>

<?php include('admin_footer.php'); ?>
