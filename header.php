<?php
// Configuration and helper functions
require_once __DIR__ . '/config.php';
$siteName = "AgriCart";
$baseUrl = BASE_URL;

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $siteName; ?> - Agricultural Marketplace</title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="<?php echo $baseUrl; ?>favicon.ico" type="image/x-icon">
    <style>
      /* Reset and base styles */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: #333;
  background-color: #f9f9f9;
}

/* Header Styles */
.site-header {
  background-color: #ffffff;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h1 {
  font-size: 1.8rem;
  margin: 0;
}

.logo a {
  color: #4a8f29;
  text-decoration: none;
  font-weight: 700;
  transition: color 0.3s ease;
}

.logo a:hover {
  color: #3a7020;
}

/* Navigation */
.main-navigation ul {
  display: flex;
  list-style: none;
  gap: 1.5rem;
}

.main-navigation a {
  color: #555;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0;
  position: relative;
  transition: color 0.3s ease;
}

.main-navigation a:hover {
  color: #4a8f29;
}

.main-navigation a::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 0;
  background-color: #4a8f29;
  transition: width 0.3s ease;
}

.main-navigation a:hover::after {
  width: 100%;
}

/* Cart count badge */
.cart-count {
  background-color: #e74c3c;
  color: white;
  border-radius: 50%;
  padding: 0.15rem 0.4rem;
  font-size: 0.7rem;
  position: relative;
  top: -8px;
  margin-left: 2px;
}

/* Mobile menu toggle */
.mobile-menu-toggle {
  display: none;
  flex-direction: column;
  justify-content: space-between;
  width: 30px;
  height: 21px;
  cursor: pointer;
}

.mobile-menu-toggle span {
  display: block;
  height: 3px;
  width: 100%;
  background-color: #4a8f29;
  border-radius: 3px;
  transition: all 0.3s ease;
}

/* Responsive Design */
@media (max-width: 768px) {
  .mobile-menu-toggle {
    display: flex;
  }
  
  .main-navigation {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: white;
    padding: 1rem;
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    transform: scaleY(0);
    transform-origin: top;
    transition: transform 0.3s ease;
    opacity: 0;
    visibility: hidden;
  }
  
  .main-navigation.active {
    transform: scaleY(1);
    opacity: 1;
    visibility: visible;
  }
  
  .main-navigation ul {
    flex-direction: column;
    gap: 1rem;
  }
  
  .header-container {
    padding: 1rem;
  }
}

/* Additional styles for active page */
.main-navigation a.active {
  color: #4a8f29;
}

.main-navigation a.active::after {
  width: 100%;
}
</style>
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <div class="logo">
                <h1><a href="<?php echo $baseUrl; ?>home.php"><?php echo $siteName; ?></a></h1>
            </div>
            
            <nav class="main-navigation">
                <ul>
                    <li><a href="<?php echo $baseUrl; ?>home.php">Home</a></li>
                    <li><a href="<?php echo $baseUrl; ?>category.php">Categories</a></li>
                    <li><a href="<?php echo $baseUrl; ?>cart.php">
                        Cart 
                        <?php 
                        // Optional: Display cart item count
                        $cartItemCount = (isset($_SESSION['user_id']) && isset($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;
                        if ($cartItemCount > 0) {
                            echo "<span class='cart-count'>$cartItemCount</span>";
                        }
                        ?>
                    </a></li>

                    <?php if ($isLoggedIn): ?>
                        <li><a href="<?php echo $baseUrl; ?>profile.php">Profile</a></li>
                        <li><a href="<?php echo $baseUrl; ?>logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $baseUrl; ?>login.php">Login</a></li>
                        <li><a href="<?php echo $baseUrl; ?>register.php">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  const mainNavigation = document.querySelector('.main-navigation');
  
  mobileMenuToggle.addEventListener('click', function() {
    mainNavigation.classList.toggle('active');
  });
});
</script>
