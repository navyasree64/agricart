<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="logo">
    <h2>Admin Panel</h2>
</div>
<ul class="nav-menu">
    <li><a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
    <li><a href="productadmin.php" class="<?= $current_page === 'productadmin.php' ? 'active' : '' ?>"><i class="fas fa-box"></i> Manage Products</a></li>
    <li><a href="orders.php" class="<?= $current_page === 'orders.php' ? 'active' : '' ?>"><i class="fas fa-shopping-cart"></i> Manage Orders</a></li>
    <li><a href="users.php" class="<?= $current_page === 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Manage Users</a></li>
    <li><a href="categories.php" class="<?= $current_page === 'categories.php' ? 'active' : '' ?>"><i class="fas fa-tags"></i> Manage Categories</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
</ul>
