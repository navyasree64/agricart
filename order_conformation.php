<?php
session_start();
include('functions.php');

// Redirect if not coming from a successful order
if (!isset($_SESSION['order_success'])) {
  header("Location: home.php");
  exit;
}

// Clear the success flag
unset($_SESSION['order_success']);

// Base URL for assets
$baseUrl = "./"; // Update this to match your project structure
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Confirmation - Agri E-Commerce</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    :root {
      --primary-color: #28a745;
      --primary-hover: #218838;
      --secondary-color: #6c757d;
      --accent-color: #ffc107;
      --light-bg: #f8f9fa;
      --border-color: #dee2e6;
    }
    
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body { 
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2f2f2;
      color: #333;
      line-height: 1.6;
    }
    
    header {
      background-color: var(--primary-color);
      padding: 1rem;
      color: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .header-content {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .header-title {
      font-size: 1.5rem;
      font-weight: bold;
    }
    
    nav {
      display: flex;
      align-items: center;
    }
    
    nav a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    nav a:hover {
      color: var(--accent-color);
    }
    
    .container {
      max-width: 800px;
      margin: 3rem auto;
      background: white;
      padding: 3rem 2rem;
      border-radius: 8px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .success-icon {
      font-size: 5rem;
      color: var(--primary-color);
      margin-bottom: 1.5rem;
      animation: bounceIn 0.8s ease;
    }
    
    @keyframes bounceIn {
      0% {
        opacity: 0;
        transform: scale(0.3);
      }
      50% {
        opacity: 1;
        transform: scale(1.05);
      }
      70% {
        transform: scale(0.9);
      }
      100% {
        transform: scale(1);
      }
    }
    
    .page-title {
      font-size: 2.5rem;
      margin-bottom: 1rem;
      color: #333;
    }
    
    .confirmation-message {
      font-size: 1.1rem;
      color: #666;
      margin-bottom: 2rem;
    }
    
    .order-info {
      background-color: var(--light-bg);
      padding: 1.5rem;
      border-radius: 8px;
      margin: 2rem 0;
      text-align: left;
    }
    
    .info-row {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
      border-bottom: 1px solid var(--border-color);
    }
    
    .info-row:last-child {
      border-bottom: none;
    }
    
    .btn {
      display: inline-block;
      padding: 0.75rem 1.5rem;
      background: var(--primary-color);
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      text-decoration: none;
      font-weight: 500;
      transition: background 0.3s ease;
      text-align: center;
      margin: 0.5rem;
    }
    
    .btn:hover {
      background: var(--primary-hover);
    }
    
    .btn-secondary {
      background: var(--secondary-color);
    }
    
    .btn-secondary:hover {
      background: #5a6268;
    }
    
    .actions {
      margin-top: 2rem;
    }
    
    footer {
      background: #333;
      color: white;
      text-align: center;
      padding: 1.5rem;
      margin-top: 3rem;
    }
    
    @media (max-width: 576px) {
      .container {
        padding: 2rem 1rem;
        margin: 2rem 1rem;
      }
      
      .page-title {
        font-size: 2rem;
      }
      
      .success-icon {
        font-size: 4rem;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="header-content">
    <div class="header-title">Agri E-Commerce</div>
    <nav>
      <a href="home.php">Home</a>
      <a href="category.php?type=seeds">Seeds</a>
      <a href="category.php?type=tools">Tools</a>
      <a href="category.php?type=fertilizer">Fertilizer</a>
      <a href="category.php?type=feed">Animal Feed</a>
      <a href="<?= isset($_SESSION['user_id']) ? 'profile.php' : 'login.php' ?>">
        <i class="fas fa-user"></i> <?= isset($_SESSION['user_id']) ? 'Profile' : 'Login' ?>
      </a>
    </nav>
  </div>
</header>

<div class="container">
  <div class="success-icon">
    <i class="fas fa-check-circle"></i>
  </div>
  
  <h1 class="page-title">Order Confirmed!</h1>
  <p class="confirmation-message">
    Thank you for your order. We've received your purchase and will begin processing it soon.
  </p>
  
  <div class="order-info">
    <div class="info-row">
      <span>Order Number:</span>
      <span><?= 'ORD-' . strtoupper(substr(md5(time()), 0, 8)) ?></span>
    </div>
    <div class="info-row">
      <span>Order Date:</span>
      <span><?= date('F j, Y') ?></span>
    </div>
    <div class="info-row">
      <span>Estimated Delivery:</span>
      <span><?= date('F j, Y', strtotime('+3 days')) ?></span>
    </div>
  </div>
  
  <p>
    A confirmation email has been sent to your registered email address. 
    You can track your order status in your account dashboard.
  </p>
  
  <div class="actions">
    <a href="home.php" class="btn">Continue Shopping</a>
    <a href="profile.php?tab=orders" class="btn btn-secondary">View Your Orders</a>
  </div>
</div>

<footer>
  <div>&copy; <?= date("Y") ?> Agri E-Commerce. All rights reserved.</div>
</footer>

</body>
</html>
