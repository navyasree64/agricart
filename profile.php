<?php
/**
 * User Profile Page
 * 
 * Displays user information and account options
 */
session_start();
require_once('db.php');
require_once('functions.php'); // Create this file for helper functions

// Authentication check
if (!isLoggedIn()) {
    redirect('login.php', 'Please log in to view your profile');
}

// Get user data with error handling
$user = getUserProfile($_SESSION['user_id']);
if (!$user) {
    redirect('error.php', 'Unable to retrieve user information');
}

// Get order history
$orders = getUserOrders($_SESSION['user_id']);

// Page title for template
$pageTitle = "My Profile";
include('header.php'); // Move header to separate file
?>

<style>
/* Profile Page Styles */
.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.row {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
}

.col-md-4 {
    flex: 0 0 calc(33.333% - 2rem);
    max-width: calc(33.333% - 2rem);
}

.col-md-8 {
    flex: 0 0 calc(66.666% - 2rem);
    max-width: calc(66.666% - 2rem);
}

.profile-sidebar {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.profile-nav {
    list-style: none;
    padding: 0;
    margin: 0 0 2rem 0;
}

.profile-nav li {
    margin-bottom: 0.5rem;
}

.profile-nav a {
    display: block;
    padding: 0.75rem 1rem;
    color: #555;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.profile-nav a:hover,
.profile-nav li.active a {
    background: #4a8f29;
    color: white;
}

.profile-stats {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.stat-item {
    flex: 1;
    text-align: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #4a8f29;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.profile-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.profile-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.profile-header h1 {
    color: #333;
    margin-bottom: 0.5rem;
}

.last-login {
    color: #666;
    font-size: 0.9rem;
}

.profile-section {
    margin-bottom: 2rem;
}

.profile-section h3 {
    color: #333;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #f0f0f0;
}

.info-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 4px;
}

.info-label {
    font-weight: 500;
    color: #555;
}

.info-value {
    color: #333;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    background: #4a8f29;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn:hover {
    background: #3a7020;
}

.btn-primary {
    background: #4a8f29;
}

.btn-danger {
    background: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

.orders-table th,
.orders-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

.orders-table th {
    background: #f8f9fa;
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.btn-outline {
    background: transparent;
    border: 1px solid #4a8f29;
    color: #4a8f29;
}

.btn-outline:hover {
    background: #4a8f29;
    color: white;
}

.btn-link {
    color: #4a8f29;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-link:hover {
    text-decoration: underline;
}

.profile-actions {
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

@media (max-width: 768px) {
    .col-md-4,
    .col-md-8 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .profile-stats {
        flex-direction: column;
    }
    
    .orders-table-responsive {
        overflow-x: auto;
    }
}

/* Additional Styles for Profile Page */
.addresses-grid {
    display: grid;
    gap: 1rem;
    margin-bottom: 2rem;
}

.address-card {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    border: 1px solid #eee;
}

.address-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.default-badge {
    background: #4a8f29;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
}

.address-details {
    margin-bottom: 1rem;
}

.address-details p {
    margin: 0.25rem 0;
    color: #555;
}

.address-actions {
    display: flex;
    gap: 0.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #555;
}

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 1.5rem;
}

.password-form {
    max-width: 500px;
}

.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 4px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<div class="container">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-4">
            <div class="profile-sidebar">
                <ul class="profile-nav">
                    <li class="active"><a href="#profile">Profile Information</a></li>
                    <li><a href="#orders">Order History</a></li>
                    <li><a href="#addresses">Saved Addresses</a></li>
                    <li><a href="#password">Change Password</a></li>
                </ul>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value"><?= count($orders) ?></span>
                        <span class="stat-label">Total Orders</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value"><?= count(getUserAddresses($_SESSION['user_id'])) ?></span>
                        <span class="stat-label">Saved Addresses</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Profile Information Section -->
            <div class="profile-content" id="profile">
                <div class="profile-header">
                    <h1>Profile Information</h1>
                    <p class="last-login">Last login: <?= date('M d, Y H:i', strtotime($user['updated_at'])) ?></p>
                </div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= htmlspecialchars($user['name']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?= htmlspecialchars($user['phone'] ?? 'Not set') ?></span>
                    </div>
                </div>
                
                <a href="edit_profile.php" class="btn">Edit Profile</a>
            </div>

            <!-- Order History Section -->
            <div class="profile-content" id="orders" style="display: none;">
                <h3>Order History</h3>
                <?php if (!empty($orders)): ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                    <td><?= $order['item_count'] ?> items</td>
                                    <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                                    <td>
                                        <span class="status-badge status-<?= strtolower($order['status']) ?>">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order_tracking.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline">Track Order</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No orders found.</p>
                <?php endif; ?>
            </div>

            <!-- Saved Addresses Section -->
            <div class="profile-content" id="addresses" style="display: none;">
                <h3>Saved Addresses</h3>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <?php
                $addresses = getUserAddresses($_SESSION['user_id']);
                if (!empty($addresses)):
                ?>
                    <div class="addresses-grid">
                        <?php foreach ($addresses as $address): ?>
                            <div class="address-card">
                                <div class="address-header">
                                    <h4><?= htmlspecialchars($address['full_name']) ?></h4>
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
                                <div class="address-actions">
                                    <button class="btn btn-sm btn-outline edit-address" 
                                            data-id="<?= $address['id'] ?>"
                                            data-full-name="<?= htmlspecialchars($address['full_name']) ?>"
                                            data-phone="<?= htmlspecialchars($address['phone']) ?>"
                                            data-address1="<?= htmlspecialchars($address['address_line1']) ?>"
                                            data-address2="<?= htmlspecialchars($address['address_line2']) ?>"
                                            data-city="<?= htmlspecialchars($address['city']) ?>"
                                            data-state="<?= htmlspecialchars($address['state']) ?>"
                                            data-postal="<?= htmlspecialchars($address['postal_code']) ?>"
                                            data-default="<?= $address['is_default'] ?>">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-address" data-id="<?= $address['id'] ?>">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No saved addresses found.</p>
                <?php endif; ?>
                
                <button class="btn" id="add-address-btn">Add New Address</button>
                
                <!-- Add/Edit Address Form (Hidden by default) -->
                <div id="address-form" style="display: none;">
                    <form method="post" action="save_address.php">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="address_id" id="address_id">
                        
                        <div class="form-group">
                            <label for="full_name">Full Name *</label>
                            <input type="text" id="full_name" name="full_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address_line1">Address Line 1 *</label>
                            <input type="text" id="address_line1" name="address_line1" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address_line2">Address Line 2</label>
                            <input type="text" id="address_line2" name="address_line2">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="state">State *</label>
                                <input type="text" id="state" name="state" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="postal_code">Postal Code *</label>
                                <input type="text" id="postal_code" name="postal_code" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_default" id="is_default">
                                Set as default address
                            </label>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn">Save Address</button>
                            <button type="button" class="btn btn-outline" id="cancel-address">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="profile-content" id="password" style="display: none;">
                <h3>Change Password</h3>
                <form method="post" action="change_password.php" class="password-form">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabLinks = document.querySelectorAll('.profile-nav a');
    const tabContents = document.querySelectorAll('.profile-content');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active class from all links and contents
            tabLinks.forEach(l => l.parentElement.classList.remove('active'));
            tabContents.forEach(c => c.style.display = 'none');
            
            // Add active class to clicked link
            link.parentElement.classList.add('active');
            
            // Show corresponding content
            const targetId = link.getAttribute('href').substring(1);
            document.getElementById(targetId).style.display = 'block';
        });
    });
    
    // Address form handling
    const addAddressBtn = document.getElementById('add-address-btn');
    const addressForm = document.getElementById('address-form');
    const cancelAddressBtn = document.getElementById('cancel-address');
    const form = addressForm.querySelector('form');
    
    if (addAddressBtn && addressForm && cancelAddressBtn) {
        addAddressBtn.addEventListener('click', () => {
            addressForm.style.display = 'block';
            document.getElementById('address_id').value = '';
            form.reset();
            document.getElementById('is_default').checked = false;
        });
        
        cancelAddressBtn.addEventListener('click', () => {
            addressForm.style.display = 'none';
            form.reset();
        });
        
        // Edit address buttons
        document.querySelectorAll('.edit-address').forEach(btn => {
            btn.addEventListener('click', () => {
                const addressId = btn.getAttribute('data-id');
                document.getElementById('address_id').value = addressId;
                document.getElementById('full_name').value = btn.getAttribute('data-full-name');
                document.getElementById('phone').value = btn.getAttribute('data-phone');
                document.getElementById('address_line1').value = btn.getAttribute('data-address1');
                document.getElementById('address_line2').value = btn.getAttribute('data-address2');
                document.getElementById('city').value = btn.getAttribute('data-city');
                document.getElementById('state').value = btn.getAttribute('data-state');
                document.getElementById('postal_code').value = btn.getAttribute('data-postal');
                document.getElementById('is_default').checked = btn.getAttribute('data-default') === '1';
                addressForm.style.display = 'block';
            });
        });
        
        // Delete address buttons
        document.querySelectorAll('.delete-address').forEach(btn => {
            btn.addEventListener('click', () => {
                if (confirm('Are you sure you want to delete this address?')) {
                    const addressId = btn.getAttribute('data-id');
                    // Here you would typically send a delete request
                    window.location.href = `delete_address.php?id=${addressId}`;
                }
            });
        });
    }
});
</script>

<?php
// Include helper functions file
function getUserProfile($userId) {
    global $conn;
    
    $stmt = $conn->prepare("
        SELECT u.id, u.name, u.email, u.created_at, u.updated_at,
               DATEDIFF(NOW(), u.created_at) as days_member
        FROM users u
        WHERE u.id = ?
    ");
    
    if (!$stmt) {
        logError("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        logError("Execute failed: " . $stmt->error);
        return false;
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Set default values if fields are null
    $user['name'] = $user['name'] ?? 'Guest';
    $user['email'] = $user['email'] ?? '';
    $user['updated_at'] = $user['updated_at'] ?? date('Y-m-d H:i:s');
    $user['created_at'] = $user['created_at'] ?? date('Y-m-d H:i:s');
    $user['days_member'] = $user['days_member'] ?? 0;
    
    $stmt->close();
    return $user;
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatDateTime($date) {
    return date('d M Y, h:i A', strtotime($date));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirect($page, $message = '') {
    if (!empty($message)) {
        $_SESSION['message'] = $message;
    }
    header("Location: $page");
    exit;
}

function logError($message) {
    error_log($message, 0);
}

include('footer.php'); // Move footer to separate file
?>
